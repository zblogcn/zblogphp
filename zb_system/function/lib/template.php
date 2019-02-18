<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 模板类.
 */
class Template
{
    protected $path = null;
    protected $entryPage = null;
    protected $uncompiledCodeStore = array();
    public $theme = "";
    public $templates = array();
    public $compiledTemplates = array();
    public $templateTags = array();
    public $staticTags = array();

    /**
     * @var array 默认侧栏
     */
    public $sidebar = array();
    /**
     * @var array 侧栏2
     */
    public $sidebar2 = array();
    /**
     * @var array 侧栏3
     */
    public $sidebar3 = array();
    /**
     * @var array 侧栏4
     */
    public $sidebar4 = array();
    /**
     * @var array 侧栏5
     */
    public $sidebar5 = array();

    public function __construct()
    {
    }

    /**
     * @param $path
     */
    public function SetPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return null
     */
    public function GetPath()
    {
        return $this->path;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function HasTemplate($name)
    {
        return file_exists($this->path . '/' . $name . '.php');
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function GetTemplate($name)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_GetTemplate'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return $this->path . $name . '.php';
    }

    /**
     * @param $templatename
     */
    public function SetTemplate($templatename)
    {
        $this->entryPage = $templatename;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function &GetTags($name)
    {
        return $this->templateTags[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function SetTags($name, $value)
    {
        $this->templateTags[$name] = $value;
    }

    /**
     * @return array
     */
    public function &GetTagsAll()
    {
        return $this->templateTags;
    }

    /**
     * @param $array
     */
    public function SetTagsAll(&$array)
    {
        if (is_array($array)) {
            $this->templateTags = $array + $this->templateTags;
        }
    }

    /**
     * 编译所有文件.
     */
    public function CompileFiles()
    {
        foreach ($this->templates as $name => $content) {
            $s = RemoveBOM($this->CompileFile($content));
            @file_put_contents($this->path . $name . '.php', $s);
        }
    }

    /**
     * 初始化并编译所有文件.
     *
     * @return bool
     */
    public function BuildTemplate()
    {
        global $zbp;

        // 初始化模板
        if (!file_exists($this->path)) {
            @mkdir($this->path, 0755, true);
        } else {
            foreach (GetFilesInDir($this->path, 'php') as $fullname) {
                @unlink($fullname);
            }
        }
        $this->addNonexistendTags();

        $this->CompileFiles();

        return true;
    }

    protected function addNonexistendTags()
    {
        global $zbp;
        $templates = &$this->templates;

        if (strpos($templates['comments'], 'AjaxCommentBegin') === false) {
            $templates['comments'] = '<label id="AjaxCommentBegin"></label>' . $templates['comments'];
        }

        if (strpos($templates['comments'], 'AjaxCommentEnd') === false) {
            $templates['comments'] = $templates['comments'] . '<label id="AjaxCommentEnd"></label>';
        }

        if (strpos($templates['comment'], 'id="cmt{$comment.ID}"') === false && strpos($templates['comment'], 'id=\'cmt{$comment.ID}\'') === false) {
            $templates['comment'] = '<label id="cmt{$comment.ID}"></label>' . $templates['comment'];
        }

        if (strpos($templates['commentpost'], 'inpVerify') === false && strpos($templates['commentpost'], '=\'verify\'') === false && strpos($templates['commentpost'], '="verify"') === false) {
            $verify = '{template:commentpost-verify}';

            if (strpos($templates['commentpost'], '<!--verify-->') !== false) {
                $templates['commentpost'] = str_replace('<!--verify-->', $verify, $templates['commentpost']);
            } elseif (strpos($templates['commentpost'], '</form>')) {
                $templates['commentpost'] = str_replace('</form>', $verify . '</form>', $templates['commentpost']);
            } else {
                $templates['commentpost'] .= $verify;
            }
        }

        if (strpos($templates['header'], '{$header}') === false) {
            if (strpos($templates['header'], '</head>') !== false) {
                $templates['header'] = str_replace('</head>', '{$header}' . '</head>', $templates['header']);
            } else {
                $templates['header'] .= '{$header}';
            }
        }

        if (strpos($templates['footer'], '{$footer}') === false) {
            if (strpos($templates['footer'], '</body>') !== false) {
                $templates['footer'] = str_replace('</body>', '{$footer}' . '</body>', $templates['footer']);
            } elseif (strpos($templates['footer'], '</html>') !== false) {
                $templates['footer'] = str_replace('</html>', '{$footer}' . '</html>', $templates['footer']);
            } else {
                $templates['footer'] = '{$footer}' . $templates['footer'];
            }
        }
    }

    /**
     * @param $content
     *
     * @return mixed
     */
    public function CompileFile($content)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_Compiling_Begin'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $content);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        // Step 1: 替换<?php块
        $this->remove_php_blocks($content);
        // Step 2: 处理不编译的代码
        $this->parse_uncompile_code($content);
        // Step 3: 引入主题
        $this->parse_template($content);
        // Step 4: 解析module
        $this->parse_module($content);
        // Step 5: 处理注释
        $this->parse_comments($content);
        // Step 6: 替换配置
        $this->parse_option($content);
        // Step 7: 替换标签
        $this->parse_vars($content);
        // Step 8: 替换函数
        $this->parse_function($content);
        // Step 9: 解析If
        $this->parse_if($content);
        // Step 10: 解析foreach
        $this->parse_foreach($content);
        // Step 11: 解析for
        $this->parse_for($content);
        // Step N: 恢复不编译的代码
        $this->parse_back_uncompile_code($content);

        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_Compiling_End'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $content);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return $content;
    }

    /**
     * @param $content
     */
    protected function remove_php_blocks(&$content)
    {
        $content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
    }

    /**
     * @param $content
     */
    protected function parse_comments(&$content)
    {
        $content = preg_replace('/\{\*([^\}]+)\*\}/', '{php} /*$1*/ {/php}', $content);
    }

    /**
     * @param $content
     */
    protected function parse_uncompile_code(&$content)
    {
        $this->uncompiledCodeStore = array();
        $matches = array();
        if ($i = preg_match_all('/\{(php|pre)\}([\D\d]+?)\{\/(php|pre)\}/si', $content, $matches) > 0) {
            if (isset($matches[2])) {
                foreach ($matches[2] as $j => $p) {
                    $content = str_replace($p, '<!-- parse_middle_code' . $j . '-->', $content);
                    $this->uncompiledCodeStore[$j] = array(
                        'type'    => $matches[1][$j],
                        'content' => $p,
                    );
                }
            }
        }
    }

    /**
     * @param $content
     */
    protected function parse_back_uncompile_code(&$content)
    {
        foreach ($this->uncompiledCodeStore as $j => $p) {
            if ($p['type'] == 'php') {
                $content = str_replace('{php}<!-- parse_middle_code' . $j . '-->{/php}', '<' . '?php ' . $p['content'] . ' ?' . '>', $content);
            } else {
                $content = str_replace(
                    '{' . $p['type'] . '}<!-- parse_middle_code' . $j . '-->{/' . $p['type'] . '}',
                    $p['content'],
                    $content
                );
            }
        }

        $content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<' . '?php $1 ?' . '>', $content);
        $this->uncompiledCodeStore = array();
    }

    /**
     * @param $content
     */
    protected function parse_template(&$content)
    {
        $content = preg_replace('/\{template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
    }

    /**
     * @param $content
     */
    protected function parse_module(&$content)
    {
        $content = preg_replace('/\{module:([^\}]+)\}/', '{php} if(isset($modules[\'$1\'])){echo $modules[\'$1\']->Content;} {/php}', $content);
    }

    /**
     * @param $content
     */
    protected function parse_option(&$content)
    {
        $content = preg_replace('#\{\#([^\}]+)\#\}#', '<?php if(defined(trim(\'\\1\'))){echo \\1;}else{echo $option[\'\\1\'];} ?>', $content);
    }

    /**
     * @param $content
     */
    protected function parse_vars(&$content)
    {
        $content = preg_replace_callback('#\{\$(?!\()([^\}]+)\}#', array($this, 'parse_vars_replace_dot'), $content);
    }

    /**
     * @param $content
     */
    protected function parse_function(&$content)
    {
        $content = preg_replace_callback('/\{([a-zA-Z0-9_]+?)\((.*?)\)\}/', array($this, 'parse_funtion_replace_dot'), $content);
    }

    /**
     * @param $content
     */
    protected function parse_if(&$content)
    {
        while (preg_match('/\{if [^\n\}]+\}.*?\{\/if\}/s', $content)) {
            $content = preg_replace_callback(
                '/\{if ([^\n\}]+)\}(.*?)\{\/if\}/s',
                array($this, 'parse_if_sub'),
                $content
            );
        }
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_if_sub($matches)
    {
        $content = preg_replace_callback(
            '/\{elseif ([^\n\}]+)\}/',
            array($this, 'parse_elseif'),
            $matches[2]
        );

        $ifexp = str_replace($matches[1], $this->replace_dot($matches[1]), $matches[1]);

        $content = str_replace('{else}', '{php}}else{ {/php}', $content);

        return "<?php if ($ifexp) { ?>$content<?php } ?>";
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_elseif($matches)
    {
        $ifexp = str_replace($matches[1], $this->replace_dot($matches[1]), $matches[1]);

        return "{php}}elseif($ifexp) { {/php}";
    }

    /**
     * @param $content
     */
    protected function parse_foreach(&$content)
    {
        while (preg_match('/\{foreach(.+?)\}(.+?){\/foreach}/s', $content)) {
            $content = preg_replace_callback(
                '/\{foreach(.+?)\}(.+?){\/foreach}/s',
                array($this, 'parse_foreach_sub'),
                $content
            );
        }
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_foreach_sub($matches)
    {
        $exp = $this->replace_dot($matches[1]);
        $code = $matches[2];

        return "{php} foreach ($exp) {{/php}$code{php}}  {/php}";
    }

    /**
     * @param $content
     */
    protected function parse_for(&$content)
    {
        while (preg_match('/\{for(.+?)\}(.+?){\/for}/s', $content)) {
            $content = preg_replace_callback(
                '/\{for(.+?)\}(.+?){\/for}/s',
                array($this, 'parse_for_sub'),
                $content
            );
        }
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_for_sub($matches)
    {
        $exp = $this->replace_dot($matches[1]);
        $code = $matches[2];

        return "{php} for($exp) {{/php} $code{php} }  {/php}";
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_vars_replace_dot($matches)
    {
        $s = str_replace('=>', '', $matches[1]);
        if (strpos($s, '=') === false) {
            return '{php} echo $' . $this->replace_dot($matches[1]) . '; {/php}';
        } else {
            return '{php} $' . $this->replace_dot($matches[1]) . '; {/php}';
        }
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_funtion_replace_dot($matches)
    {
        return '{php} echo ' . $matches[1] . '(' . $this->replace_dot($matches[2]) . '); {/php}';
    }

    /**
     * @param $content
     *
     * @return mixed
     */
    protected function replace_dot($content)
    {
        $array = array();
        preg_match_all('/".+?"|\'.+?\'/', $content, $array, PREG_SET_ORDER);
        if (count($array) > 0) {
            foreach ($array as $a) {
                $a = $a[0];
                if (strstr($a, '.') != false) {
                    $b = str_replace('.', '{%_dot_%}', $a);
                    $content = str_replace($a, $b, $content);
                }
            }
        }
        $content = str_replace(' . ', ' {%_dot_%} ', $content);
        $content = str_replace('. ', '{%_dot_%} ', $content);
        $content = str_replace(' .', ' {%_dot_%}', $content);
        $content = str_replace('.', '->', $content);
        $content = str_replace('{%_dot_%}', '.', $content);

        return $content;
    }

    /**
     * 显示模板
     *
     * @param string $entryPage
     *
     * @throws Exception
     */
    public function Display($entryPage = "")
    {
        global $zbp;
        if ($entryPage == "") {
            $entryPage = $this->entryPage;
        }
        $f = $this->path . $entryPage . '.php';

        if (!is_readable($f)) {
            $zbp->ShowError(86, __FILE__, __LINE__);
        }

        $ak = array_keys($this->staticTags);
        $av = array_values($this->staticTags);
        foreach ($zbp->modulesbyfilename as &$m) {
            $m->Content = str_replace($ak, $av, $m->Content);
        }
        unset($ak, $av);

        // 入口处将tags里的变量提升全局
        foreach ($this->templateTags as $key => &$value) {
            $$key = &$value;
        }

        include $f;
    }

    /**
     * 获取输出内容.
     *
     * @param string $entryPage
     *
     * @throws Exception
     *
     * @return string
     */
    public function Output($entryPage = "")
    {
        ob_start();
        $this->Display($entryPage);
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    /**
     * 载入已编译模板s.
     */
    public function LoadCompiledTemplates()
    {
        $templates = array();

        // 读取主题模板
        $files = GetFilesInDir($this->path, 'php');
        foreach ($files as $sortname => $fullname) {
            $templates[$sortname] = file_get_contents($fullname);
        }

        $this->compiledTemplates = $templates;
    }

    /**
     * 载入未编译模板s.
     */
    public function LoadTemplates()
    {
        global $zbp;

        $theme = $this->theme;
        $templates = array();

        // 读取预置模板
        $files = GetFilesInDir($zbp->path . 'zb_system/defend/default/', 'php');
        foreach ($files as $sortname => $fullname) {
            $templates[$sortname] = file_get_contents($fullname);
        }

        // 读取主题模板
        $files = GetFilesInDir($zbp->usersdir . 'theme/' . $theme . '/template/', 'php');
        foreach ($files as $sortname => $fullname) {
            $templates[$sortname] = file_get_contents($fullname);
        }

        for ($i = 2; $i <= 5; $i++) {
            if (!isset($templates['sidebar' . $i])) {
                $templates['sidebar' . $i] = str_replace('$sidebar', '$sidebar' . $i, $templates['sidebar']);
            }
        }

        $this->templates = $templates;
    }

    /**
     *解析模板标签.
     */
    public function MakeTemplateTags()
    {
        global $zbp;

        $option = $zbp->option;
        unset($option['ZC_BLOG_CLSID']);
        unset($option['ZC_SQLITE_NAME']);
        unset($option['ZC_MYSQL_USERNAME']);
        unset($option['ZC_MYSQL_PASSWORD']);
        unset($option['ZC_MYSQL_NAME']);
        unset($option['ZC_MYSQL_PORT']);
        unset($option['ZC_MYSQL_SERVER']);
        unset($option['ZC_PGSQL_USERNAME']);
        unset($option['ZC_PGSQL_PASSWORD']);
        unset($option['ZC_PGSQL_NAME']);
        unset($option['ZC_PGSQL_PORT']);
        unset($option['ZC_PGSQL_SERVER']);
        unset($option['ZC_DATABASE_TYPE']);

        $this->templateTags['zbp'] = &$zbp;
        $this->templateTags['user'] = &$zbp->user;
        $this->templateTags['option'] = &$option;
        $this->templateTags['lang'] = &$zbp->lang;
        $this->templateTags['version'] = &$zbp->version;
        $this->templateTags['categorys'] = &$zbp->categories;
        $this->templateTags['categories'] = &$zbp->categories;
        $this->templateTags['categorysbyorder'] = &$zbp->categoriesbyorder;
        $this->templateTags['categoriesbyorder'] = &$zbp->categoriesbyorder;
        $this->templateTags['modules'] = &$zbp->modulesbyfilename;
        $this->templateTags['title'] = htmlspecialchars($zbp->title);
        $this->templateTags['host'] = &$zbp->host;
        $this->templateTags['path'] = &$zbp->path;
        $this->templateTags['cookiespath'] = &$zbp->cookiespath;
        $this->templateTags['name'] = htmlspecialchars($zbp->name);
        $this->templateTags['subname'] = htmlspecialchars($zbp->subname);
        $this->templateTags['theme'] = &$zbp->theme;
        $this->templateTags['themeinfo'] = &$zbp->themeinfo;
        $this->templateTags['style'] = &$zbp->style;
        $this->templateTags['language'] = $zbp->option['ZC_BLOG_LANGUAGE'];
        $this->templateTags['copyright'] = $zbp->option['ZC_BLOG_COPYRIGHT'];
        $this->templateTags['zblogphp'] = $zbp->option['ZC_BLOG_PRODUCT_FULL'];
        $this->templateTags['zblogphphtml'] = $zbp->option['ZC_BLOG_PRODUCT_FULLHTML'];
        $this->templateTags['zblogphpabbrhtml'] = $zbp->option['ZC_BLOG_PRODUCT_HTML'];
        $this->templateTags['type'] = '';
        $this->templateTags['page'] = '';
        $this->templateTags['socialcomment'] = &$zbp->socialcomment;
        $this->templateTags['header'] = &$zbp->header;
        $this->templateTags['footer'] = &$zbp->footer;
        $this->templateTags['validcodeurl'] = &$zbp->validcodeurl;
        $this->templateTags['feedurl'] = &$zbp->feedurl;
        $this->templateTags['searchurl'] = &$zbp->searchurl;
        $this->templateTags['ajaxurl'] = &$zbp->ajaxurl;
        $s = array(
            $option['ZC_SIDEBAR_ORDER'],
            $option['ZC_SIDEBAR2_ORDER'],
            $option['ZC_SIDEBAR3_ORDER'],
            $option['ZC_SIDEBAR4_ORDER'],
            $option['ZC_SIDEBAR5_ORDER'],
        );
        foreach ($s as $k => $v) {
            $a = explode('|', $v);
            $ms = array();
            foreach ($a as $v2) {
                if (isset($zbp->modulesbyfilename[$v2])) {
                    $m = $zbp->modulesbyfilename[$v2];
                    $ms[] = $m;
                }
            }
            //reset($ms);
            $s = 'sidebar' . ($k == 0 ? '' : $k + 1);
            $this->$s = $ms;
            $ms = null;
        }
        $this->templateTags['sidebar'] = &$this->sidebar;
        $this->templateTags['sidebar2'] = &$this->sidebar2;
        $this->templateTags['sidebar3'] = &$this->sidebar3;
        $this->templateTags['sidebar4'] = &$this->sidebar4;
        $this->templateTags['sidebar5'] = &$this->sidebar5;

        //foreach ($GLOBALS['hooks']['Filter_Plugin_Template_MakeTemplatetags'] as $fpname => &$fpsignal) {
        //    $fpreturn = $fpname($this->templateTags);
        //}

        $t = array();
        $o = array();
        foreach ($this->templateTags as $k => $v) {
            if (is_string($v) || is_numeric($v) || is_bool($v)) {
                $t['{$' . $k . '}'] = $v;
            }
        }
        foreach ($option as $k => $v) {
            if (is_string($v) || is_numeric($v) || is_bool($v)) {
                $o['{#' . $k . '#}'] = $v;
            }
        }
        $this->staticTags = $t + $o;
    }

    public function ReplaceStaticTags($s)
    {
        $s = str_replace(array_keys($this->staticTags), array_values($this->staticTags), $s);

        return $s;
    }
}
