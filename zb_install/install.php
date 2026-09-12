<?php

/**
 * Shared installer service for the web installer and CLI installer.
 */
if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

class ZbpInstaller
{
    /**
     * Install Z-BlogPHP from normalized options.
     *
     * @return array{success: bool, messages: array, error: string}
     */
    public static function Install(array $options)
    {
        global $zbp;

        $result = [
            'success' => false,
            'messages' => [],
            'error' => '',
        ];

        try {
            self::Validate($options);
            self::ApplyDatabaseOptions($options);

            if (file_exists($zbp->path . 'zb_users/c_option.php')) {
                throw new RuntimeException('Z-BlogPHP 已经安装，不能重复安装。');
            }

            $sql = self::GetCreateTableSql($options['db_type']);
            self::CreateDatabase($options);
            $zbp->OpenConnect();
            $zbp->ConvertTableAndDatainfo();
            if ($zbp->db->ExistTable($GLOBALS['table']['Config'])) {
                throw new RuntimeException('数据库中已存在 Z-BlogPHP 表，请更换表前缀或数据库。');
            }

            self::CreateTable($sql, $result['messages']);
            self::InsertInfo($options, $result['messages']);
            self::SaveConfig($options, $result['messages']);
            $result['success'] = true;
        } catch (Throwable $exception) {
            $result['error'] = $exception->getMessage();
        } finally {
            if (isset($zbp)) {
                $zbp->CloseConnect();
            }
        }

        return $result;
    }

    public static function Normalize(array $options)
    {
        $options += [
            'db_type' => '',
            'db_server' => '',
            'db_port' => 0,
            'db_name' => '',
            'db_user' => '',
            'db_password' => '',
            'db_prefix' => 'zbp_',
            'db_engine' => 'MyISAM',
            'site_name' => '',
            'admin_user' => '',
            'admin_password' => '',
            'theme' => 'default|default',
        ];

        $options['db_type'] = strtolower(trim($options['db_type']));
        $options['db_prefix'] = trim($options['db_prefix']) ?: 'zbp_';
        $options['db_engine'] = $options['db_engine'] ?: 'MyISAM';
        $options['theme'] = $options['theme'] ?: 'default|default';
        if (!$options['db_port'] && in_array($options['db_type'], ['mysql', 'mysqli', 'pdo_mysql', 'postgresql', 'pdo_postgresql'], true) && 1 === substr_count($options['db_server'], ':')) {
            [$options['db_server'], $options['db_port']] = explode(':', $options['db_server'], 2);
            $options['db_port'] = (int) $options['db_port'];
        }
        if (!$options['db_port']) {
            $options['db_port'] = in_array($options['db_type'], ['postgresql', 'pdo_postgresql'], true) ? 5432 : 3306;
        }

        return $options;
    }

    private static function Validate(array $options)
    {
        global $zbp;

        if (!in_array($options['db_type'], ['sqlite', 'sqlite3', 'pdo_sqlite', 'mysql', 'mysqli', 'pdo_mysql', 'postgresql', 'pdo_postgresql'], true)) {
            throw new InvalidArgumentException('不支持的数据库类型。');
        }
        if (!$options['site_name'] || !$options['admin_user'] || !$options['admin_password']) {
            throw new InvalidArgumentException('网站名称、管理员账号和管理员密码不能为空。');
        }
        if (strlen($options['admin_user']) < $zbp->option['ZC_USERNAME_MIN'] || strlen($options['admin_user']) > $zbp->option['ZC_USERNAME_MAX']) {
            throw new InvalidArgumentException('管理员账号长度不符合要求。');
        }
        if (strlen($options['admin_password']) < $zbp->option['ZC_PASSWORD_MIN'] || strlen($options['admin_password']) > $zbp->option['ZC_PASSWORD_MAX']) {
            throw new InvalidArgumentException('管理员密码长度不符合要求。');
        }
        if (in_array($options['db_type'], ['sqlite', 'sqlite3', 'pdo_sqlite'], true)) {
            if (!$options['db_name']) {
                throw new InvalidArgumentException('SQLite 数据库文件不能为空。');
            }
        } elseif (!$options['db_server'] || !$options['db_name'] || !$options['db_user']) {
            throw new InvalidArgumentException('数据库地址、数据库名和数据库用户名不能为空。');
        }
    }

    private static function ApplyDatabaseOptions(array $options)
    {
        global $zbp;

        $zbp->option['ZC_DATABASE_TYPE'] = $options['db_type'];
        $zbp->option['ZC_MYSQL_SERVER'] = $options['db_server'];
        $zbp->option['ZC_MYSQL_PORT'] = $options['db_port'];
        $zbp->option['ZC_MYSQL_NAME'] = str_replace(["'", '"'], '', $options['db_name']);
        $zbp->option['ZC_MYSQL_USERNAME'] = $options['db_user'];
        $zbp->option['ZC_MYSQL_PASSWORD'] = $options['db_password'];
        $zbp->option['ZC_MYSQL_PRE'] = $options['db_prefix'];
        $zbp->option['ZC_MYSQL_ENGINE'] = $options['db_engine'];
        $zbp->option['ZC_PGSQL_SERVER'] = $options['db_server'];
        $zbp->option['ZC_PGSQL_PORT'] = $options['db_port'];
        $zbp->option['ZC_PGSQL_NAME'] = strtolower(str_replace(["'", '"'], '', $options['db_name']));
        $zbp->option['ZC_PGSQL_USERNAME'] = $options['db_user'];
        $zbp->option['ZC_PGSQL_PASSWORD'] = $options['db_password'];
        $zbp->option['ZC_PGSQL_PRE'] = $options['db_prefix'];
        $zbp->option['ZC_SQLITE_NAME'] = $options['db_name'];
        $zbp->option['ZC_SQLITE_PRE'] = $options['db_prefix'];
    }

    private static function GetCreateTableSql($type)
    {
        $file = in_array($type, ['sqlite', 'sqlite3', 'pdo_sqlite'], true) ? 'sqlite.sql' : (in_array($type, ['postgresql', 'pdo_postgresql'], true) ? 'pgsql.sql' : 'mysql.sql');
        $sql = file_get_contents(ZBP_PATH . 'zb_system/defend/createtable/' . $file);
        if (false === $sql) {
            throw new RuntimeException('读取数据库建表脚本失败。');
        }
        if (in_array($type, ['sqlite', 'sqlite3', 'pdo_sqlite'], true)) {
            $sql = str_replace(' autoincrement', '', $sql);
        }

        return $sql;
    }

    private static function CreateDatabase(array $options)
    {
        if (in_array($options['db_type'], ['sqlite', 'sqlite3', 'pdo_sqlite'], true)) {
            return;
        }

        $database = ZBlogPHP::InitializeDB($options['db_type']);
        $created = call_user_func(
            [$database, 'CreateDB'],
            $options['db_server'],
            $options['db_port'],
            $options['db_user'],
            $options['db_password'],
            $options['db_name'],
        );
        $database->Close();
        if (!$created) {
            throw new RuntimeException('创建数据库失败。');
        }
    }

    private static function CreateTable($sql, array &$messages)
    {
        global $zbp;

        if (false !== stripos($zbp->option['ZC_DATABASE_TYPE'], 'mysql')) {
            $zbp->option['ZC_MYSQL_CHARSET'] = $zbp->db->charset;
            $zbp->option['ZC_MYSQL_COLLATE'] = $zbp->db->collate;
            $sql = str_ireplace('CHARSET=utf8', 'CHARSET=' . $zbp->option['ZC_MYSQL_CHARSET'], $sql);
            $sql = str_ireplace('COLLATE=utf8_general_ci', 'COLLATE=' . $zbp->option['ZC_MYSQL_COLLATE'], $sql);
        }
        $zbp->db->QueryMulit($zbp->db->sql->ReplacePre($sql));
        if (!$zbp->db->ExistTable($GLOBALS['table']['Config'])) {
            throw new RuntimeException('创建数据库表失败。');
        }
        $messages[] = '数据库表创建成功。';
    }

    private static function InsertInfo(array $options, array &$messages)
    {
        global $zbp;

        $zbp->guid = GetGuid();
        $member = new Member();
        $member->Guid = GetGuid();
        $member->Level = 1;
        $member->Name = $options['admin_user'];
        $member->Password = Member::GetPassWordByGuid($options['admin_password'], $member->Guid);
        $member->IP = GetGuestIP();
        $member->PostTime = time();
        FilterMember($member);
        $member->Save();

        $category = new Category();
        $category->Name = $zbp->lang['msg']['uncategory'];
        $category->Alias = 'uncategorized';
        $category->Count = 1;
        $category->Save();

        $moduleDefinitions = [
            [
                'name' => $zbp->lang['msg']['module_navbar'],
                'file_name' => 'navbar',
                'sidebar_id' => 0,
                'html_id' => 'divNavBar',
                'links' => [
                    self::CreateModuleLink('{#ZC_BLOG_HOST#}', $zbp->lang['zb_install']['index'], ['li_id' => 'navbar-item-index']),
                    self::CreateModuleLink('{#ZC_BLOG_HOST#}?id=2', $zbp->lang['zb_install']['guestbook']),
                ],
            ],
            [
                'name' => $zbp->lang['msg']['calendar'],
                'file_name' => 'calendar',
                'sidebar_id' => 1,
                'html_id' => 'divCalendar',
                'type' => 'div',
                'hide_title' => true,
                'build' => true,
            ],
            [
                'name' => $zbp->lang['msg']['control_panel'],
                'file_name' => 'controlpanel',
                'sidebar_id' => 1,
                'content' => '<span class="cp-hello">' . $zbp->lang['zb_install']['wellcome'] . '</span><br/><span class="cp-login"><a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=login">' . $zbp->lang['msg']['admin_login'] . '</a></span>&nbsp;&nbsp;<span class="cp-vrs"><a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=misc&amp;type=vrs">' . $zbp->lang['msg']['view_rights'] . '</a></span>',
                'html_id' => 'divContorPanel',
                'type' => 'div',
            ],
            [
                'name' => $zbp->lang['msg']['module_catalog'],
                'file_name' => 'catalog',
                'sidebar_id' => 1,
                'html_id' => 'divCatalog',
            ],
            [
                'name' => $zbp->lang['msg']['search'],
                'file_name' => 'searchpanel',
                'sidebar_id' => 1,
                'content' => '<form name="search" method="post" action="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=search"><label><span style="position:absolute;color:transparent;z-index:-9999;">Search</span><input type="text" name="q" size="11" /></label> <input type="submit" value="' . $zbp->lang['msg']['search'] . '" /></form>',
                'html_id' => 'divSearchPanel',
                'type' => 'div',
            ],
            ['name' => $zbp->lang['msg']['module_comments'], 'file_name' => 'comments', 'sidebar_id' => 1, 'html_id' => 'divComments'],
            ['name' => $zbp->lang['msg']['module_archives'], 'file_name' => 'archives', 'sidebar_id' => 1, 'html_id' => 'divArchives'],
            ['name' => $zbp->lang['msg']['module_statistics'], 'file_name' => 'statistics', 'sidebar_id' => 0, 'html_id' => 'divStatistics'],
            [
                'name' => $zbp->lang['msg']['module_favorite'],
                'file_name' => 'favorite',
                'sidebar_id' => 1,
                'html_id' => 'divFavorites',
                'links' => [
                    self::CreateModuleLink('https://app.zblogcn.com/', 'Z-Blog应用中心', ['target' => '_blank']),
                    self::CreateModuleLink('https://bbs.zblogcn.com/', 'ZBlogger社区', ['target' => '_blank']),
                    self::CreateModuleLink('https://z5encrypt.com/', 'Z5 PHP加密', ['target' => '_blank', 'title' => '全新的PHP加密方案，致力于PHP源码的保护']),
                ],
            ],
            [
                'name' => $zbp->lang['msg']['module_link'],
                'file_name' => 'link',
                'sidebar_id' => 1,
                'html_id' => 'divLinkage',
                'links' => [
                    self::CreateModuleLink('https://github.com/zblogcn', 'Z-Blog on Github', ['target' => '_blank', 'title' => 'Z-Blog on Github']),
                ],
            ],
            ['name' => $zbp->lang['msg']['module_authors'], 'file_name' => 'authors', 'sidebar_id' => 0, 'html_id' => 'divAuthors'],
            ['name' => $zbp->lang['msg']['module_previous'], 'file_name' => 'previous', 'sidebar_id' => 0, 'html_id' => 'divPrevious'],
            ['name' => $zbp->lang['msg']['module_tags'], 'file_name' => 'tags', 'sidebar_id' => 0, 'html_id' => 'divTags'],
        ];
        foreach ($moduleDefinitions as $moduleDefinition) {
            self::CreateModule($moduleDefinition);
        }

        self::InsertPost($zbp->lang['zb_install']['hello_zblog'], $zbp->lang['zb_install']['hello_zblog_content'], ZC_POST_TYPE_ARTICLE, 1);
        self::InsertPost($zbp->lang['zb_install']['guestbook'], $zbp->lang['zb_install']['guestbook_content'], ZC_POST_TYPE_PAGE, 0);
        $zbp->LoadMembers(0);
        if (!count($zbp->members)) {
            throw new RuntimeException('插入初始数据失败。');
        }
        $messages[] = '管理员和初始数据创建成功。';
    }

    private static function CreateModule(array $definition)
    {
        $module = new Module();
        $module->Name = $definition['name'];
        $module->FileName = $definition['file_name'];
        $module->Source = 'system';
        $module->SidebarID = $definition['sidebar_id'];
        $module->Content = $definition['content'] ?? '';
        $module->HtmlID = $definition['html_id'];
        $module->Type = $definition['type'] ?? 'ul';
        if (!empty($definition['hide_title'])) {
            $module->IsHideTitle = true;
        }
        if (isset($definition['links'])) {
            self::SetModuleLinks($module, $definition['links']);
        }
        if (!empty($definition['build'])) {
            $module->Build();
        }
        $module->Save();

        return $module;
    }

    private static function CreateModuleLink($href, $content, array $attributes = [])
    {
        $link = new stdClass();
        $link->href = $href;
        $link->content = $content;
        foreach ($attributes as $name => $value) {
            $link->{$name} = $value;
        }

        return $link;
    }

    private static function SetModuleLinks(Module $module, array $links)
    {
        if (version_compare(ZC_VERSION, '1.8.0', '>=')) {
            $module->Links = $links;
            call_user_func([$module, 'ConvertLink']);

            return;
        }

        $content = '';
        foreach ($links as $link) {
            $content .= isset($link->li_id) ? '<li id="' . $link->li_id . '">' : '<li>';
            $content .= '<a href="' . $link->href . '"';
            if (!empty($link->target)) {
                $content .= ' target="' . $link->target . '"';
            }
            if (!empty($link->title)) {
                $content .= ' title="' . $link->title . '"';
            }
            $content .= '>' . $link->content . '</a></li>';
        }
        $module->Content = $content;
    }

    private static function InsertPost($title, $content, $type, $categoryId)
    {
        $post = new Post();
        $post->CateID = $categoryId;
        $post->AuthorID = 1;
        $post->Status = ZC_POST_STATUS_PUBLIC;
        $post->Type = $type;
        $post->Title = $title;
        $post->Intro = $content;
        $post->Content = $content;
        $post->IP = GetGuestIP();
        $post->PostTime = time();
        $post->Save();
    }

    private static function SaveConfig(array $options, array &$messages)
    {
        global $zbp;

        $zbp->option['ZC_BLOG_VERSION'] = ZC_BLOG_VERSION;
        $zbp->option['ZC_BLOG_NAME'] = $options['site_name'];
        $zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre|UEditor|Totoro|LinksManage';
        $zbp->option['ZC_BLOG_THEME'] = SplitAndGet($options['theme'], '|', 0);
        $zbp->option['ZC_BLOG_CSS'] = SplitAndGet($options['theme'], '|', 1);
        $zbp->option['ZC_DEBUG_MODE'] = false;
        $zbp->option['ZC_LAST_VERSION'] = ZC_LAST_VERSION;
        $zbp->option['ZC_NOW_VERSION'] = $zbp->version;
        $zbp->LoadCache();
        $zbp->SaveOption();
        $zbp->Config('cache')->templates_md5 = '';
        $zbp->SaveCache();
        $zbp->Config('AppCentre')->enabledcheck = 1;
        $zbp->Config('AppCentre')->checkbeta = 0;
        $zbp->Config('AppCentre')->enabledevelop = 0;
        $zbp->Config('AppCentre')->enablegzipapp = 0;
        $zbp->SaveConfig('AppCentre');
        $zbp->template = $zbp->PrepareTemplate();
        $zbp->BuildTemplate();
        $zbp->LoadCategories();
        $zbp->LoadModules();
        $zbp->RegBuildModules();
        $zbp->modulesbyfilename['calendar']->Build();
        $zbp->modulesbyfilename['calendar']->Save();
        $zbp->modulesbyfilename['catalog']->Build();
        $zbp->modulesbyfilename['catalog']->Save();
        $messages[] = '站点配置和模板保存成功。';
    }
}
