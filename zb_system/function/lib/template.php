<?php
/**
 * 模板类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib 类库
 */
class Template {

    protected $tags = array();
    protected $path = null;
    protected $entryPage = null;
    protected $parsedPHPCodes = array();
    public $templates = array();
    public $templatetags = array();
    public $compiledtemplates = array();	

    /**
     *
     */
    public function __construct() {
    }

    /**
     * @param $path
     */
    public function SetPath($path) {
        $this->path = $path;
    }

    /**
     * @return null
     */
    public function GetPath() {
        return $this->path;
    }

    /**
     * @param $name
     * @return boolean
     */
    public function hasTemplate($name) {
		return isset($this->templates[$name]);
	}

    /**
     * @param $name
     * @return string
     */
    public function GetTemplate($name) {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_GetTemplate'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this, $name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
        }

        return $this->path . $name . '.php';
    }

    /**
     * @param $templatename
     */
    public function SetTemplate($templatename) {
        $this->startpage = $templatename;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function &GetTags($name) {
        return $this->tags[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function SetTags($name, $value) {
        $this->tags[$name] = $value;
    }

    /**
     * @return array
     */
    public function &GetTagsAll() {
        return $this->tags;
    }

    /**
     * @param $array
     */
    public function SetTagsAll(&$array) {
        $this->tags = $array;
    }

    /**
     * @param $filesarray
     */
    public function CompileFiles($filesarray) {

        foreach ($filesarray as $name => $content) {
            $s = RemoveBOM($this->Compiling($content));
            @file_put_contents($this->path . $name . '.php', $s);
            //if(function_exists('chmod')){
            //	@chmod($this->path . $name . '.php',0755);
            //}
        }

    }

    /**
     * @param $content
     * @return mixed
     */
    public function Compiling($content) {

        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_Compiling_Begin'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this, $content);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
        }

        // Step 1: 替换<?php块
        $this->replacePHP($content);
        // Step 2: 解析PHP
        $this->parsePHP($content);
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
        // Step N: 解析PHP
        $this->parsePHP2($content);

        foreach ($GLOBALS['hooks']['Filter_Plugin_Template_Compiling_End'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this, $content);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
        }

        return $content;
    }

    /**
     * @param $content
     */
    protected function replacePHP(&$content) {
        $content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
    }

    /**
     * @param $content
     */
    protected function parse_comments(&$content) {
        $content = preg_replace('/\{\*([^\}]+)\*\}/', '{php} /*$1*/ {/php}', $content);
    }

    /**
     * @param $content
     */
    protected function parsePHP(&$content) {
        $this->parsedPHPCodes = array();
        $matches = array();
        if ($i = preg_match_all('/\{php\}([\D\d]+?)\{\/php\}/si', $content, $matches) > 0) {
            if (isset($matches[1])) {
                foreach ($matches[1] as $j => $p) {
                    $content = str_replace($p, '<!--' . $j . '-->', $content);
                    $this->parsedPHPCodes[$j] = $p;
                }
            }
        }}

    /**
     * @param $content
     */
    protected function parsePHP2(&$content) {
        foreach ($this->parsedPHPCodes as $j => $p) {
            $content = str_replace('{php}<!--' . $j . '-->{/php}', '<' . '?php ' . $p . ' ?' . '>', $content);
        }
        $content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<' . '?php $1 ?' . '>', $content);
        $this->parsedPHPCodes = array();
    }

    /**
     * @param $content
     */
    protected function parse_template(&$content) {
        $content = preg_replace('/\{template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
    }

    /**
     * @param $content
     */
    protected function parse_module(&$content) {
        $content = preg_replace('/\{module:([^\}]+)\}/', '{php} if(isset($modules[\'$1\'])){echo $modules[\'$1\']->Content;} {/php}', $content);
    }

    /**
     * @param $content
     */
    protected function parse_option(&$content) {
        $content = preg_replace('#\{\#([^\}]+)\#\}#', '<?php echo $option[\'\\1\']; ?>', $content);
    }

    /**
     * @param $content
     */
    protected function parse_vars(&$content) {
        $content = preg_replace_callback('#\{\$(?!\()([^\}]+)\}#', array($this, 'parse_vars_replace_dot'), $content);
    }

    /**
     * @param $content
     */
    protected function parse_function(&$content) {
        $content = preg_replace_callback('/\{([a-zA-Z0-9_]+?)\((.+?)\)\}/', array($this, 'parse_funtion_replace_dot'), $content);
    }

    /**
     * @param $content
     */
    protected function parse_if(&$content) {
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
     * @return string
     */
    protected function parse_if_sub($matches) {

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
     * @return string
     */
    protected function parse_elseif($matches) {
        $ifexp = str_replace($matches[1], $this->replace_dot($matches[1]), $matches[1]);

        return "{php}}elseif($ifexp) { {/php}";
    }

    /**
     * @param $content
     */
    protected function parse_foreach(&$content) {
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
     * @return string
     */
    protected function parse_foreach_sub($matches) {
        $exp = $this->replace_dot($matches[1]);
        $code = $matches[2];

        return "{php} foreach ($exp) {{/php}$code{php}}  {/php}";
    }

    /**
     * @param $content
     */
    protected function parse_for(&$content) {
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
     * @return string
     */
    protected function parse_for_sub($matches) {
        $exp = $this->replace_dot($matches[1]);
        $code = $matches[2];

        return "{php} for($exp) {{/php} $code{php} }  {/php}";
    }

    /**
     * @param $matches
     * @return string
     */
    protected function parse_vars_replace_dot($matches) {
        if (strpos($matches[1], '=') === false || strpos($matches[1], '=>') !== false) {
            return '{php} echo $' . $this->replace_dot($matches[1]) . '; {/php}';
        } else {
            return '{php} $' . $this->replace_dot($matches[1]) . '; {/php}';
        }
    }

    /**
     * @param $matches
     * @return string
     */
    protected function parse_funtion_replace_dot($matches) {
        return '{php} echo ' . $matches[1] . '(' . $this->replace_dot($matches[2]) . '); {/php}';
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function replace_dot($content) {
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

    #模板入口
    /**
     *
     */
    public function Display($entryPage = "") {
        global $zbp;
        if ($entryPage == "") {
            $entryPage = $this->entryPage;
        }
        $f = $this->path . $this->startpage . '.php';
        if (!is_readable($f)) {
            $zbp->ShowError(86, __FILE__, __LINE__);
        }

        #入口处将tags里的变量提升全局!!!
        foreach ($this->tags as $key => &$value) {
            $$key = &$value;
        }
        include $f;
    }

    /**
     * @return string
     */
    public function Output($entryPage = "") {

        ob_start();
        $this->Display($entryPage);
        $data = ob_get_contents();
        ob_end_clean();

        return $data;

    }

}
