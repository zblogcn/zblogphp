<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 模板类.
 */
class Template
{

    /**
     * @var string 编译后的模板php执行路径
     */
    protected $path = null;

    protected $entryPage = null;

    protected $uncompiledCodeStore = array();

    public $theme = "";

    public $templates = array();

    public $templates_Name = array();

    public $templates_Type = array();

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

    /**
     * @var array 侧栏6
     */
    public $sidebar6 = array();

    /**
     * @var array 侧栏77
     */
    public $sidebar7 = array();

    /**
     * @var array 侧栏8
     */
    public $sidebar8 = array();

    /**
     * @var array 侧栏9
     */
    public $sidebar9 = array();

    /**
     * @var bool 是否启用标识模板类型
     */
    public $isuse_nameandtype = false;

    /**
     * @var string 模板目录，方便指定多套模板
     */
    public $template_dirname = 'template';

    /**
     * @var bool 是否已显示过了
     */
    public $isdisplayed = false;

    public function __construct()
    {
    }

    /**
     * 设置路径
     * 1.7改为可以为空，为空就是按系统设置去设path
     *
     * @param $path
     */
    public function SetPath($path = null)
    {
        global $zbp;
        $template_dirname = $this->template_dirname;

        if ($path == null) {
            $path = $zbp->cachedir . 'compiled/' . $this->theme . '/';
        }

        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }
        //针对不同的模板目录创建不同的编译目录
        if (!($template_dirname == '' || $template_dirname == 'template')) {
            $path = substr($path, 0, (strlen($path) - 1)) . '___' . $template_dirname . '/';
        }
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
            $this->templateTags = ($array + $this->templateTags);
        }
    }

    /**
     * 编译所有文件.
     */
    public function CompileFiles()
    {
        global $zbp;

        foreach ($this->dirs as $key => $value) {
            $value = str_ireplace($zbp->usersdir . 'theme/' . $this->theme . '/' . $this->template_dirname . '/', $this->path, $value);
            if (!file_exists($value)) {
                mkdir($value, 0755, true);
            }
        }
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
            foreach (GetFilesInDir($this->path, 'php') as $s) {
                if (file_exists($s)) {
                    @unlink($s);
                }
            }
            foreach ($this->files as $key => $value) {
                $s = $this->path . $key . '.php';
                if (file_exists($s)) {
                    @unlink($s);
                }
            }
            $this->dirs = array_reverse($this->dirs);
            foreach ($this->dirs as $key => $value) {
                $s = str_replace($zbp->usersdir . 'theme/' . $this->theme . '/' . $this->template_dirname . '/', $this->path, $value);
                if (file_exists($s)) {
                    foreach (GetFilesInDir($s, 'php') as $t) {
                        if (file_exists($t)) {
                            @unlink($t);
                        }
                    }
                    @rmdir($s);
                }
            }
        }
        $this->addNonexistentTags();

        $this->CompileFiles();

        return true;
    }

    protected function addNonexistentTags()
    {
        global $zbp;
        $templates = &$this->templates;

        if ($zbp->autofill_template_htmltags == false) {
            return;
        }

        if (strpos($templates['comments'], 'AjaxCommentBegin') === false) {
            $templates['comments'] = '<label id="AjaxCommentBegin"></label>' . $templates['comments'];
        }

        if (strpos($templates['comments'], 'AjaxCommentEnd') === false) {
            $templates['comments'] = $templates['comments'] . '<label id="AjaxCommentEnd"></label>';
        }

        if (strpos($templates['comment'], 'id="cmt{$comment.ID}"') === false && strpos($templates['comment'], 'id=\'cmt{$comment.ID}\'') === false) {
            $templates['comment'] = '<label id="cmt{$comment.ID}"></label>' . $templates['comment'];
        }

        if (strpos($templates['commentpost'], 'commentpost-verify') === false && strpos($templates['commentpost'], 'inpVerify') === false && strpos($templates['commentpost'], '=\'verify\'') === false && strpos($templates['commentpost'], '="verify"') === false) {
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
        // Step 12: 解析switch
        $this->parse_switch($content);
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
        //为了模板更好看
        $content = str_replace('{php}<' . '?php', '{php}', $content);
        $content = str_replace('?' . '>{/php}', '{/php}', $content);
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
     * @param $content
     */
    protected function parse_switch(&$content)
    {
        while (preg_match('/\{switch(.+?)\}(.+?){\/switch}/s', $content)) {
            $content = preg_replace_callback(
                '/\{switch(.+?)\}(.+?){\/switch}/s',
                array($this, 'parse_switch_sub'),
                $content
            );
        }
    }
    
    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_switch_sub($matches)
    {
        $exp = $this->replace_dot($matches[1]);
        
        $code = $this->parse_switch_case($matches[2]);
        $code = preg_replace('/^(\s+?){php}/', '${1}', $code);
        
        return "{php} switch($exp) { $code{php} }  {/php}";
    }
    
    /**
     * @param $code
     *
     * @return string
     */
    protected function parse_switch_case($code)
    {
        $code = preg_replace('/{break;?}/', '{php}break;{/php}', $code);
        $code = preg_replace('/{default:?}/', '{php}default:{/php}', $code);
        
        $code = preg_replace_callback('/{case(.+?)}/', array($this, 'parse_switch_case_repalce'), $code);
        return $code;
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function parse_switch_case_repalce($matches)
    {
        return '{php}case ' . rtrim(trim($matches[1]), ':') . ':{/php}';
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

        foreach ($zbp->modulesbyfilename as $m) {
            $m->Content = $this->ReplaceStaticTags($m->Content);
        }

        if ($entryPage == "") {
            $entryPage = $this->entryPage;
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_Display'] as $fpname => &$fpsignal) {
            $fpname($this, $entryPage);
        }

        $file = $this->path . $entryPage . '.php';

        if (!is_readable($file)) {
            $zbp->ShowError(86, __FILE__, __LINE__, array('lost_file' => $file));
        }

        // 入口处将tags里的变量提升全局
        foreach ($this->templateTags as $key => &$value) {
            $$key = &$value;
        }

        include $file;

        $this->isdisplayed = true;

        return true;
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
        $files = GetFilesInDir($zbp->systemdir . 'defend/default/', 'php');
        foreach ($files as $sortname => $fullname) {
            $s = file_get_contents($fullname);
            if (substr($s, 0, 2) == '{*' && strstr($s, '*}') !== false) {
                $s = strstr($s, '*}');
                $s = substr($s, 2);
            }
            $templates[$sortname] = $s;
            $s = null;
        }

        // 读取主题模板
        $this->dirs = array();
        $this->files = array();
        $this->GetAllFileDir($zbp->usersdir . 'theme/' . $theme . "/{$this->template_dirname}/");

        foreach ($this->files as $key => $value) {
            $templates[$key] = $value;
        }

        for ($i = 2; $i < 10; $i++) {
            if (!isset($templates['sidebar' . $i])) {
                $templates['sidebar' . $i] = str_replace('$sidebar', '$sidebar' . $i, $templates['sidebar']);
            }
        }

        $this->templates = $templates;
        $this->LoadTemplateInfos();

        return true;
    }

    /**
     * 读取模板 Name 及 Type
     */
    public function LoadTemplateInfos()
    {
        $templates_Name = array();
        $templates_Type = array();
        $this->template_json_file = null;
        foreach ($this->templates as $key => $value) {
            $a = $this->GetTemplateNameAndType($key, $value);
            $templates_Name[$key] = $a[0];
            $templates_Type[$key] = $a[1];
        }
        $this->templates_Name = $templates_Name;
        $this->templates_Type = $templates_Type;

        return true;
    }

    //获取 模板的名称和类型
    private function GetTemplateNameAndType($filename, $content)
    {
        $name = '';
        $type = '';
        $t = $content;
        if (stristr($t, 'Template Name:')) {
            $t = stristr($t, 'Template Name:');
            $t = str_ireplace('Template Name:', '', $t);
            $name = trim(strtok($t, '*'));
        }
        $t = $content;
        if (stristr($t, 'Template Type:')) {
            $t = stristr($t, 'Template Type:');
            $t = str_ireplace('Template Type:', '', $t);
            $type = trim(strtok($t, '*'));
        }

        if (is_readable($f = $GLOBALS['blogpath'] . 'zb_users/theme/' . $this->theme . '/template.json')) {
            if (!is_object($this->template_json_file)) {
                $this->template_json_file = json_decode(file_get_contents($f));
            }
        }
        if (is_object($this->template_json_file)) {
            if (is_array($this->template_json_file->templates)) {
                foreach ($this->template_json_file->templates as $key => $value) {
                    if (strtolower($filename) == strtolower($value->filename)) {
                        $name = $value->name;
                        $type = $value->type;
                        break;
                    }
                }
            }
        }
        $name = trim($name);
        $type = trim($type);
        $type = str_replace(array(',', '，', ';', '；', '、'), '|', $type);
        if ($type != null) {
            $this->isuse_nameandtype = true;
        }
        if ($filename == 'index' && $type == null) {
            $type = 'list|index';
        }
        if ($filename == 'single' && $type == null) {
            $type = 'single';
        }
        if ($filename == '404' && $type == null) {
            $type = '404';
        }
        if ($filename == 'search' && $type == null) {
            $type = 'search';
        }
        return array($name, $type);
    }

    private $template_json_file = null;

    private $dirs = array();

    private $files = array();

    private function GetAllFileDir($dir)
    {
        global $zbp;
        if (!file_exists($dir)) {
            return;
        }
        if (function_exists('scandir')) {
            foreach (scandir($dir) as $d) {
                if ($d != "." && $d != "..") {
                    if (is_dir($dir . $d)) {
                        if ((substr($d, 0, 1) != '.')) {
                            $fd = str_replace('\\', '/', $dir . $d . '/');
                            $this->dirs[] = $fd;
                            $this->GetAllFileDir($fd);
                        }
                    } elseif (is_readable($dir . $d)) {
                        $s = $dir . $d;
                        $i = strlen($zbp->usersdir . 'theme/' . $this->theme . "/{$this->template_dirname}/");
                        if (substr($s, -4) == '.php') {
                            $s2 = substr($s, ($i - strlen($s)));
                            $s3 = substr($s2, 0, (strlen($s2) - 4));
                            $s3 = str_replace('\\', '/', $s3);
                            $this->files[$s3] = file_get_contents($s); //$dir . $d;
                        }
                    }
                }
            }
        } else {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $d = str_replace("{$this->template_dirname}//", "{$this->template_dirname}/", str_replace('\\', '/', $dir . '/' . $file));
                        if (is_dir($dir . '/' . $file)) {
                            $d = str_replace('\\', '/', $d);
                            $this->dirs[] = substr($d, -1) == '/' ? $d : ($d . '/');
                            $this->GetAllFileDir($d);
                        } elseif (is_readable($d)) {
                            $s = $d;
                            $i = strlen($zbp->usersdir . 'theme/' . $this->theme . "/{$this->template_dirname}/");
                            if (substr($s, -4) == '.php') {
                                $s2 = substr($s, ($i - strlen($s)));
                                $s3 = substr($s2, 0, (strlen($s2) - 4));
                                $s3 = str_replace('\\', '/', $s3);
                                $this->files[$s3] = file_get_contents($s); //$dir . $d;
                            }
                        }
                    }
                }
                closedir($handle);
            }
        }
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

        //添加template_dirname进zbp->themeinfo
        $zbp->themeinfo['template_dirname'] = &$this->template_dirname;

        $this->templateTags['zbp'] = &$zbp;
        $this->templateTags['user'] = &$zbp->user;
        $this->templateTags['option'] = &$option;
        $this->templateTags['lang'] = &$zbp->lang;
        $this->templateTags['langs'] = &$zbp->langs;
        $this->templateTags['version'] = &$zbp->version;
        $this->templateTags['categorys'] = &$zbp->categories;
        $this->templateTags['categories'] = &$zbp->categories;
        $this->templateTags['categorysbyorder'] = &$zbp->categoriesbyorder;
        $this->templateTags['categoriesbyorder'] = &$zbp->categoriesbyorder;
        $this->templateTags['categories_all'] = &$zbp->categories_all;
        $this->templateTags['categoriesbyorder_type'] = &$zbp->categoriesbyorder_type;
        $this->templateTags['modules'] = &$zbp->modulesbyfilename;
        $this->templateTags['title'] = htmlspecialchars($zbp->title);
        $this->templateTags['host'] = &$zbp->host;
        $this->templateTags['path'] = &$zbp->path;
        $this->templateTags['cookiespath'] = &$zbp->cookiespath;
        $this->templateTags['name'] = htmlspecialchars($zbp->name);
        $this->templateTags['subname'] = htmlspecialchars($zbp->subname);
        $this->templateTags['theme'] = &$zbp->theme;
        $this->templateTags['themeapp'] = &$zbp->themeapp;
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
        $this->templateTags['issearch'] = false;
        $this->templateTags['html_js_hash'] = $zbp->html_js_hash;
        $this->templateTags['admin_js_hash'] = $zbp->admin_js_hash;
        $s = array(
            $option['ZC_SIDEBAR_ORDER'],
            $option['ZC_SIDEBAR2_ORDER'],
            $option['ZC_SIDEBAR3_ORDER'],
            $option['ZC_SIDEBAR4_ORDER'],
            $option['ZC_SIDEBAR5_ORDER'],
            $option['ZC_SIDEBAR6_ORDER'],
            $option['ZC_SIDEBAR7_ORDER'],
            $option['ZC_SIDEBAR8_ORDER'],
            $option['ZC_SIDEBAR9_ORDER'],
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

        for ($i = 1; $i < 10; $i++) {
            $j = ($i == 1) ? '' : $i;
            $this->templateTags['sidebar' . $j] = &$this->{'sidebar' . $j};
        }

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
        $this->staticTags = ($t + $o);
    }

    public function ReplaceStaticTags($s)
    {
        $s = str_replace(array_keys($this->staticTags), array_values($this->staticTags), $s);

        return $s;
    }

    public function GetCurrentTemplate()
    {
        return $this->entryPage;
    }

}
