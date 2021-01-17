<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * Url规则类.
 */
class UrlRule
{

    /**
     * @var array
     */
    public $Rules = array();

    /**
     * @var string
     */
    public $Url = '';

    private $PreUrl = '';

    private $Route = array();

    /**
     * @var bool MakeReplace(变量名义意不明，以后要换)为真就进行Make_Rewrite_Replace 为假为进行Make_Active_Replace
     */
    public $MakeReplace = true;

    /**
     * @var bool
     */
    public $forceDisplayPage = false;//强制显示page参数

    public static $categoryLayer = '-1';

    /**
     * @param $url
     */
    public function __construct($url)
    {
        if (is_array($url)) {
            $this->Route = $url;
            if (isset($url['urlrule_regex']) && $url['urlrule_regex'] != '') {
                $this->PreUrl = $url['urlrule_regex'];
            } else {
                $this->PreUrl = $url['urlrule'];
            }
        } else {
            $this->PreUrl = $url;
        }
    }

    /**
     * @return string
     */
    public function GetPreUrl()
    {
        return $this->PreUrl;
    }

    /**
     * @return string
     */
    private function Make_Active_Replace()
    {
        global $zbp;

        $this->Rules['{%host%}'] = $zbp->host;
        if (isset($this->Rules['{%page%}'])) {
            if ($this->forceDisplayPage == false) {
                if ($this->Rules['{%page%}'] == '1' || $this->Rules['{%page%}'] == '0') {
                    $this->Rules['{%page%}'] = '';
                }
            }
        }
        $s = $this->PreUrl;
        foreach ($this->Rules as $key => $value) {
            $s = preg_replace($key, $value, $s);
        }
        $s = preg_replace('/\{[\?\/&a-z0-9]*=\}/', '', $s);
        $s = preg_replace('/\{\/?}/', '', $s);
        $s = str_replace(array('{', '}'), array('', ''), $s);

        $this->Url = htmlspecialchars($s);

        return $this->Url;
    }

    /**
     * @return string
     */
    private function Make_Rewrite_Replace()
    {
        global $zbp;
        $s = $this->PreUrl;

        if (isset($this->Rules['{%page%}'])) {
            if ($this->forceDisplayPage == false) {
                if ($this->Rules['{%page%}'] == '1' || $this->Rules['{%page%}'] == '0') {
                    $this->Rules['{%page%}'] = '';
                }
            }
        } else {
            $this->Rules['{%page%}'] = '';
        }
        if ($this->Rules['{%page%}'] == '') {
            preg_match('/(?<=\})[^\{\}%&]+(?=\{%page%\})/i', $s, $matches);
            if (isset($matches[0])) {
                $s = str_replace($matches[0], '', $s);
            } else {
                preg_match('/(?<=&)[^\{\}%&]+(?=\{%page%\})/i', $s, $matches);
                if (isset($matches[0])) {
                    $s = str_replace($matches[0], '', $s);
                }
            }
        }

        $prefix = GetValueInArray($this->Route, 'prefix', '');
        if ($prefix != '') {
            $prefix .= '/';
        }
        $this->Rules['{%host%}'] = $zbp->host . $prefix;
        foreach ($this->Rules as $key => $value) {
            if (!is_array($value)) {
                $s = str_replace($key, $value, $s);
            }
        }

        if (substr($this->PreUrl, -1) != '/' && substr($s, -1) == '/') {
            $s = substr($s, 0, (strlen($s) - 1));
        }
        if (substr($s, -2) == '//') {
            $s = substr($s, 0, (strlen($s) - 1));
        }
        if (substr($s, -1) == '&') {
            $s = substr($s, 0, (strlen($s) - 1));
        }

        $this->Url = htmlspecialchars($s);

        return $this->Url;
    }

    /**
     * @return string
     */
    public function Make()
    {
        if ($this->MakeReplace) {
            return $this->Make_Rewrite_Replace();
        } else {
            return $this->Make_Active_Replace();
        }
    }

    /**
     * 处理Route参数
     *
     * @param $array
     *
     * @return array
     */
    public static function ProcessParameters($route)
    {
        $newargs = array();
        
        $parameters = $route['parameters'];

        $args = array();
        foreach ($parameters as $key2 => $value2) {
            if (is_integer($key2)) {
                $args[$value2] = $value2;
            } else {
                $args[$key2] = $value2;
            }
        }
        foreach ($args as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key2 => $value2) {
                    if (is_integer($key2)) {
                        $newargs[] = array('name' => $key, 'match' => $value2, 'regex' => '');
                    } else {
                        $newargs[] = array('name' => $key, 'match' => $key2, 'regex' => $value2);
                    }
                }
            } else {
                $newargs[] = array('name' => $key, 'match'  => $value, 'regex' => '');
            }
        }

        if (!empty($route) && is_array($route)) {
            $s = $route['urlrule'];
            $s = str_replace('{%host%}', '', $s);
            $marray = array();
            if (preg_match_all('/%[^\{]+%/', $s, $m) > 1) {
                foreach ($m as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k1 => $v1) {
                            $marray[] = $v1;
                        }
                    }
                }
            }
            foreach ($marray as $key => $value) {
                $value = str_replace('%', '', $value);
                $newargs[] = array('name' => $value, 'match' => $value, 'regex' => '');
            }
        }

        foreach ($newargs as $key => &$value) {
            if ($value['match'] == 'id' && $value['regex'] == '') {
                $value['regex'] = '[0-9]+';
            }
            if ($value['match'] == 'alias' && $value['regex'] == '') {
                $value['regex'] = '[^\./_]+?';
            }
            if ($value['match'] == 'category' && $value['regex'] == '') {
                $value['regex'] = '(([^\./_]*/?)<:1,' . self::$categoryLayer . ':>))';
            }
            if ($value['match'] == 'author' && $value['regex'] == '') {
                $value['regex'] = '[^\./_]+';
            }
            if ($value['match'] == 'year' && $value['regex'] == '') {
                $value['regex'] = '[0-9]{4}';
            }
            if ($value['match'] == 'month' && $value['regex'] == '') {
                $value['regex'] = '[0-9]{1,2}';
            }
            if ($value['match'] == 'day' && $value['regex'] == '') {
                $value['regex'] = '[0-9]{1,2}';
            }
            if ($value['match'] == 'page' && $value['regex'] == '') {
                $value['regex'] = '[0-9]+';
            }
            if ($value['match'] == 'date' && $value['regex'] == '') {
                $value['regex'] = '[0-9\-]+';
            }
        }
        return $newargs;
    }

    /**
     * @param $route
     * @param $showFristPage boolean
     *
     * @return string
     */
    public static function OutputUrlRegEx_Route($route, $showFristPage = false)
    {
        global $zbp;
        self::$categoryLayer = $GLOBALS['zbp']->category_recursion_real_deep;

        $newargs = self::ProcessParameters($route);
        $orginUrl = $url = $route['urlrule'];

        $url = str_replace('{%page%}', '{%poaogoe%}', $url);

        $url = str_replace('{%poaogoe%}', '{%page%}', $url);
        if ($showFristPage == false) {
            $url = preg_replace('/(?<=\})[^\}]+(?=\{%page%\})/i', '', $url, 1);
            $url = str_replace('{%page%}', '', $url);
        }

        foreach ($newargs as $key => $value) {
            $url = str_replace('{%' . $value['match'] . '%}', '(?P<' . $value['name'] . '>' . $value['regex'] . ')', $url);
        }

        $prefix = GetValueInArray($route, 'prefix', '');
        if ($prefix != '') {
            $prefix .= '/';
        }
        $url = str_replace('{%host%}', '{%host%}' . $prefix, $url);

        $arrayReplace = array('{%host%}' => '^', '.' => '\\.', '{%page%}' => '{%poaogoe%}', '/' => '\\/');
        foreach ($arrayReplace as $key => $value) {
            $url = str_replace($key, $value, $url);
        }

        $url = $url . '$';

        if ($url == '^$' || $url == '^\/$') {
            return '';
        }

        return '/(?J)' . $url . '/';
    }

    /**
     * @param $url
     * @param $type
     * @param $showFristPage boolean
     *
     * @return string
     */
    public static function OutputUrlRegEx($url, $type, $showFristPage = false)
    {
        global $zbp;

        if (is_array($url)) {
            return self::OutputUrlRegEx_Route($url);
        }

        self::$categoryLayer = $GLOBALS['zbp']->category_recursion_real_deep;
        $post_type_name = array('post');
        foreach ($zbp->posttype as $key => $value) {
            $post_type_name[] = $value['name'];
        }

        $orginUrl = $url;
        $s = $url;
        $s = str_replace('%page%', '%poaogoe%', $s);
        $url = str_replace('{%host%}', '^', $url);
        $url = str_replace('.', '\\.', $url);

        $url = str_replace('%page%', '%poaogoe%', $url);
        preg_match('/(?<=\})[^\{\}]+(?=\{%poaogoe%\})/i', $s, $matches);
        if (isset($matches[0])) {
            if ($showFristPage) {
                //$url = str_replace($matches[0], '(?:' . $matches[0] . ')', $url);
                $url = preg_replace('/(?<=\})[^\{\}]+(?=\{%poaogoe%\})/i', '(?:' . $matches[0] . ')', $url, 1);
            } else {
                //$url = str_replace($matches[0], '', $url);
                if (stripos($url, '_{%poaogoe%}') !== false) {
                    $url = str_replace('_{%poaogoe%}', '{%poaogoe%}', $url);
                } elseif (stripos($url, '/{%poaogoe%}') !== false) {
                    $url = str_replace('/{%poaogoe%}', '{%poaogoe%}', $url);
                } elseif (stripos($url, '-{%poaogoe%}') !== false) {
                    $url = str_replace('-{%poaogoe%}', '{%poaogoe%}', $url);
                } else {
                    $url = preg_replace('/(?<=\})[^\{\}]+(?=\{%poaogoe%\})/i', '', $url, 1);
                }
            }
        }

        if ($type == 'date') {
            $url = str_replace('%date%', '(?P<date>[0-9\-]+)', $url);
        } elseif ($type == 'cate') {
            $url = str_replace('%id%', '(?P<cate>[0-9]+)', $url);

            $carray = array();
            for ($i = 1; $i <= self::$categoryLayer; $i++) {
                $carray[$i] = '[^\./_]*';
                for ($j = 1; $j <= ($i - 1); $j++) {
                    $carray[$i] = '[^\./_]*/' . $carray[$i];
                }
            }
            $fullcategory = implode('|', $carray);
            $url = str_replace('%alias%', '(?P<cate>(' . $fullcategory . ')+?)', $url);
        } elseif ($type == 'tags') {
               $url = str_replace('%id%', '(?P<tags>[0-9]+)', $url);
            $url = str_replace('%alias%', '(?P<tags>[^\./_]+?)', $url);
        } elseif ($type == 'auth') {
            $url = str_replace('%id%', '(?P<auth>[0-9]+)', $url);
            $url = str_replace('%alias%', '(?P<auth>[^\./_]+?)', $url);
        } elseif (in_array($type, $post_type_name)) {
            if (strpos($url, '%id%') !== false) {
                $url = str_replace('%id%', '(?P<id>[0-9]+)', $url);
            }
            if (strpos($url, '%alias%') !== false) {
                if ($type == 'article') {
                    $url = str_replace('%alias%', '(?P<alias>[^/]+)', $url);
                } else {
                    $url = str_replace('%alias%', '(?P<alias>.+)', $url);
                }
            }
            $url = str_replace('%category%', '(?P<category>(([^\./_]*/?)<:1,' . self::$categoryLayer . ':>))', $url);
            $url = str_replace('%author%', '(?P<author>[^\./_]+)', $url);
            $url = str_replace('%year%', '(?P<year>[0-9]<:4:>)', $url);
            $url = str_replace('%month%', '(?P<month>[0-9]<:1,2:>)', $url);
            $url = str_replace('%day%', '(?P<day>[0-9]<:1,2:>)', $url);
        } else {
            $url = str_replace('%id%', '(?P<' . $type . '>[0-9]+)', $url);
            $url = str_replace('%alias%', '(?P<' . $type . '>[^\./_]+?)', $url);
            $url = str_replace('%' . $type . '%', '(?P<' . $type . '>[^\./_]+?)', $url);
        }
 
        $url = str_replace('{', '', $url);
        $url = str_replace('}', '', $url);
        $url = str_replace('<:', '{', $url);
        $url = str_replace(':>', '}', $url);
        $url = str_replace('/', '\/', $url);

        if ($showFristPage) {
            $url = str_replace('%poaogoe%', '(?P<page>[0-9]*)', $url);
        } else {
            if ($type == 'list') {
                if (isset($matches[0])) {
                    $s = str_replace('{%host%}', '^', $orginUrl);
                    $s = str_replace('.', '\\.', $s);
                    $i = strpos($s, $matches[0]);
                    $url = substr($url, 0, $i);
                } else {
                    $url = str_replace('%poaogoe%', '', $url);
                }
            } else {
                $url = str_replace('%poaogoe%', '', $url);
            }
        }

        $url = $url . '$';
        if ($url == '^$') {
            return '';
        }
        return '/(?J)' . $url . '/';

        // 关于J标识符的使用
        // @see https://bugs.php.net/bug.php?id=47456
    }

    /**
     * @return string
     */
    public function Make_htaccess()
    {
        global $zbp;
        $s = '<IfModule mod_rewrite.c>' . "\r\n";
        $s .= 'RewriteEngine On' . "\r\n";
        $s .= "RewriteBase " . $zbp->cookiespath . "\r\n";

        $s .= 'RewriteCond %{REQUEST_FILENAME} !-f' . "\r\n";
        $s .= 'RewriteCond %{REQUEST_FILENAME} !-d' . "\r\n";
        $s .= 'RewriteRule . ' . $zbp->cookiespath . 'index.php [L]' . "\r\n";
        $s .= '</IfModule>';

        return $s;
    }

    /**
     * @return string
     */
    public function Make_webconfig()
    {
        global $zbp;

        $s = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
        $s .= '<configuration>' . "\r\n";
        $s .= ' <system.webServer>' . "\r\n";

        $s .= '  <rewrite>' . "\r\n";
        $s .= '   <rules>' . "\r\n";

        $s .= ' <rule name="' . $zbp->cookiespath . ' Z-BlogPHP Imported Rule" stopProcessing="true">' . "\r\n";
        $s .= '  <match url="^.*?" ignoreCase="false" />' . "\r\n";
        $s .= '   <conditions logicalGrouping="MatchAll">' . "\r\n";
        $s .= '    <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />' . "\r\n";
        $s .= '    <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />' . "\r\n";
        $s .= '   </conditions>' . "\r\n";
        $s .= '  <action type="Rewrite" url="index.php/{R:0}" />' . "\r\n";
        $s .= ' </rule>' . "\r\n";

        $s .= ' <rule name="' . $zbp->cookiespath . ' Z-BlogPHP Imported Rule index.php" stopProcessing="true">' . "\r\n";
        $s .= '  <match url="^index.php/.*?" ignoreCase="false" />' . "\r\n";
        $s .= '   <conditions logicalGrouping="MatchAll">' . "\r\n";
        $s .= '    <add input="{REQUEST_FILENAME}" matchType="IsFile" />' . "\r\n";
        $s .= '   </conditions>' . "\r\n";
        $s .= '  <action type="Rewrite" url="index.php/{R:0}" />' . "\r\n";
        $s .= ' </rule>' . "\r\n";

        $s .= '   </rules>' . "\r\n";
        $s .= '  </rewrite>' . "\r\n";
        $s .= ' </system.webServer>' . "\r\n";
        $s .= '</configuration>' . "\r\n";

        return $s;
    }

    /**
     * @return string
     */
    public function Make_nginx()
    {
        global $zbp;
        $s = '';
        $s .= 'if (-f $request_filename/index.html){' . "\r\n";
        $s .= ' rewrite (.*) $1/index.html break;' . "\r\n";
        $s .= '}' . "\r\n";
        $s .= 'if (-f $request_filename/index.php){' . "\r\n";
        $s .= ' rewrite (.*) $1/index.php;' . "\r\n";
        $s .= '}' . "\r\n";
        $s .= 'if (!-f $request_filename){' . "\r\n";
        $s .= ' rewrite (.*) ' . $zbp->cookiespath . 'index.php;' . "\r\n";
        $s .= '}' . "\r\n";

        return $s;
    }

    /**
     * @return string
     */
    public function Make_lighttpd()
    {
        global $zbp;
        $s = '';

        //$s .='# Handle 404 errors' . "\r\n";
        //$s .='server.error-handler-404 = "/index.php"' . "\r\n";
        //$s .='' . "\r\n";

        $s .= '# Rewrite rules' . "\r\n";
        $s .= 'url.rewrite-if-not-file = (' . "\r\n";

        $s .= '' . "\r\n";
        $s .= '"^' . $zbp->cookiespath . '(zb_install|zb_system|zb_users)/(.*)" => "$0",' . "\r\n";

        $s .= '' . "\r\n";
        $s .= '"^' . $zbp->cookiespath . '(.*.php)" => "$0",' . "\r\n";

        $s .= '' . "\r\n";
        $s .= '"^' . $zbp->cookiespath . '(.*)$" => "' . $zbp->cookiespath . 'index.php/$0"' . "\r\n";

        $s .= '' . "\r\n";
        $s .= ')' . "\r\n";

        return $s;
    }

    /**
     * @return string
     */
    public function Make_httpdini()
    {
    }

    /**
     * @param $url
     * @param $type
     *
     * @return string
     */
    public function Rewrite_httpdini($url, $type)
    {
    }

}
