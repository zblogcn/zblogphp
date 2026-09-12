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
     * @param array $options
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
        if (!$options['db_port'] && in_array($options['db_type'], ['mysql', 'mysqli', 'pdo_mysql', 'postgresql', 'pdo_postgresql'], true) && substr_count($options['db_server'], ':') === 1) {
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
            $options['db_name']
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

        $module = new Module();
        $module->Name = $zbp->lang['msg']['module_navbar'];
        $module->FileName = 'navbar';
        $module->Source = 'system';
        $module->SidebarID = 0;
        $module->Content = '<li id="navbar-item-index"><a href="{#ZC_BLOG_HOST#}">' . $zbp->lang['zb_install']['index'] . '</a></li><li id="navbar-page-2"><a href="{#ZC_BLOG_HOST#}?id=2">' . $zbp->lang['zb_install']['guestbook'] . '</a></li>';
        $module->HtmlID = 'divNavBar';
        $module->Type = 'ul';
        $module->Save();

        foreach (['calendar', 'catalog', 'comments', 'archives', 'statistics', 'favorite', 'link', 'authors', 'previous', 'tags'] as $fileName) {
            $module = new Module();
            $module->Name = $fileName;
            $module->FileName = $fileName;
            $module->Source = 'system';
            $module->SidebarID = in_array($fileName, ['statistics', 'authors', 'previous', 'tags'], true) ? 0 : 1;
            $module->Content = '';
            $module->HtmlID = 'div' . ucfirst($fileName);
            $module->Type = 'ul';
            $module->Save();
        }

        self::InsertPost($zbp->lang['zb_install']['hello_zblog'], $zbp->lang['zb_install']['hello_zblog_content'], ZC_POST_TYPE_ARTICLE, 1);
        self::InsertPost($zbp->lang['zb_install']['guestbook'], $zbp->lang['zb_install']['guestbook_content'], ZC_POST_TYPE_PAGE, 0);
        $zbp->LoadMembers(0);
        if (!count($zbp->members)) {
            throw new RuntimeException('插入初始数据失败。');
        }
        $messages[] = '管理员和初始数据创建成功。';
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
