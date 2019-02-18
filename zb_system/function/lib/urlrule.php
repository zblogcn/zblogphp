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
    /**
     * @var bool
     */
    public $MakeReplace = true;
    /**
     * @var bool
     */
    public $IsIndex = false; //指示是否为首页的规则

    public static $categoryLayer = '-1';

    /**
     * @param $url
     */
    public function __construct($url)
    {
        $this->PreUrl = $url;
    }

    /**
     * @return string
     */
    private function Make_Preg()
    {
        global $zbp;

        $this->Rules['{%host%}'] = $zbp->host;
        if (isset($this->Rules['{%page%}'])) {
            if ($this->Rules['{%page%}'] == '1' || $this->Rules['{%page%}'] == '0') {
                $this->Rules['{%page%}'] = '';
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
    private function Make_Replace()
    {
        global $zbp;
        $s = $this->PreUrl;

        if (isset($this->Rules['{%page%}'])) {
            if ($this->Rules['{%page%}'] == '1' || $this->Rules['{%page%}'] == '0') {
                $this->Rules['{%page%}'] = '';
            }
        } else {
            $this->Rules['{%page%}'] = '';
        }
        if ($this->Rules['{%page%}'] == '') {
            if ($this->IsIndex == true) {
                //if (substr_count($s, '{%page%}') == 1 && substr_count($s, '{') == 2 && substr_count($s, '&') == 0) {
                $s = $zbp->host;
            }
            if (stripos($s, '_{%page%}') !== false) {
                $s = str_replace('_{%page%}', '{%page%}', $s);
            } elseif (stripos($s, '/{%page%}') !== false) {
                $s = str_replace('/{%page%}', '{%page%}', $s);
            } elseif (stripos($s, '-{%page%}') !== false) {
                $s = str_replace('-{%page%}', '{%page%}', $s);
            } else {
                preg_match('/(?<=\})[^\{\}%\/&]+(?=\{%page%\})/i', $s, $matches);
                if (isset($matches[0])) {
                    $s = str_replace($matches[0], '', $s);
                } else {
                    preg_match('/(?<=&)[^\{\}%\/&]+(?=\{%page%\})/i', $s, $matches);
                    if (isset($matches[0])) {
                        $s = str_replace($matches[0], '', $s);
                    }
                }
            }
            //if (substr($this->PreUrl, -10) != '_{%page%}/' && substr($s, -9) == '{%page%}/') {
            //    $s = substr($s, 0, strlen($s) - 1);
            //}
        }

        $this->Rules['{%host%}'] = $zbp->host;
        foreach ($this->Rules as $key => $value) {
            if (!is_array($value)) {
                $s = str_replace($key, $value, $s);
            }
        }

        if (substr($this->PreUrl, -1) != '/' && substr($s, -1) == '/' && $s != $zbp->host) {
            $s = substr($s, 0, strlen($s) - 1);
        }
        if (substr($s, -1) == '&') {
            $s = substr($s, 0, strlen($s) - 1);
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
            return $this->Make_Replace();
        } else {
            return $this->Make_Preg();
        }
    }

    /**
     * @param $url
     * @param $type
     * @param $haspage boolean
     *
     * @return string
     */
    public static function OutputUrlRegEx($url, $type, $haspage = false)
    {
        global $zbp;

        $s = $url;
        $s = str_replace('%page%', '%poaogoe%', $s);
        $url = str_replace('{%host%}', '^', $url);
        $url = str_replace('.', '\\.', $url);
        if ($type == 'index') {
            $url = str_replace('%page%', '%poaogoe%', $url);
            preg_match('/[^\{\}]+(?=\{%poaogoe%\})/i', $s, $matches);
            if (isset($matches[0])) {
                $url = str_replace($matches[0], '(?:' . $matches[0] . ')<:1:>', $url);
            }
            $url = $url . '$';
            $url = str_replace('%poaogoe%', '(?P<page>[0-9]*)', $url);
        }
        if ($type == 'cate' || $type == 'tags' || $type == 'date' || $type == 'auth' || $type == 'list') {
            $url = str_replace('%page%', '%poaogoe%', $url);
            preg_match('/(?<=\})[^\{\}]+(?=\{%poaogoe%\})/i', $s, $matches);
            if (isset($matches[0])) {
                if ($haspage) {
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
            $url = $url . '$';
            if ($haspage) {
                $url = str_replace('%poaogoe%', '(?P<page>[0-9]*)', $url);
            } else {
                $url = str_replace('%poaogoe%', '', $url);
            }
            $url = str_replace('%id%', '(?P<id>[0-9]+)', $url);
            $url = str_replace('%date%', '(?P<date>[0-9\-]+)', $url);
            if ($type == 'cate') {
                if (self::$categoryLayer == -1) {
                    foreach ($zbp->categorys as $c) {
                        if ($c->Level > self::$categoryLayer && strpos($c->Alias, '/') !== false) {
                            self::$categoryLayer = $c->Level;
                        }
                    }
                    if (self::$categoryLayer == -1) {
                        self::$categoryLayer = 0;
                    }
                }
                switch (self::$categoryLayer) {
                    case 3:
                        $fullcategory = '[^\./_]*|[^\./_]*/[^\./_]*|[^\./_]*/[^\./_]*/[^\./_]*|[^\./_]+/[^\./_]*/[^\./_]*/[^\./_]*';
                        break;
                    case 2:
                        $fullcategory = '[^\./_]*|[^\./_]*/[^\./_]*|[^\./_]*/[^\./_]*/[^\./_]*';
                        break;
                    case 1:
                        $fullcategory = '[^\./_]*|[^\./_]*/[^\./_]*';
                        break;
                    default:
                        $fullcategory = '[^\./_]*';
                        break;
                }
                $url = str_replace('%alias%', '(?P<alias>(' . $fullcategory . ')+?)', $url);
            } else {
                $url = str_replace('%alias%', '(?P<alias>[^\./_]+?)', $url);
            }
        }
        if ($type == 'page' || $type == 'article' || $type == 'post') {
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
            $url = $url . '$';
            if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                $url = str_replace('%category%', '(?P<category>(([^\./_]*/?)<:1,4:>))', $url);
            } else {
                $url = str_replace('%category%', '(?P<category>(([^\./_]*/?)<:1,3:>))', $url);
            }
            $url = str_replace('%author%', '(?P<author>[^\./_]+)', $url);
            $url = str_replace('%year%', '(?P<year>[0-9]<:4:>)', $url);
            $url = str_replace('%month%', '(?P<month>[0-9]<:1,2:>)', $url);
            $url = str_replace('%day%', '(?P<day>[0-9]<:1,2:>)', $url);
        }
        $url = str_replace('{', '', $url);
        $url = str_replace('}', '', $url);
        $url = str_replace('<:', '{', $url);
        $url = str_replace(':>', '}', $url);
        $url = str_replace('/', '\/', $url);
        //$url = str_replace('\/$', '$', $url);

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

        $s .= '	<rule name="' . $zbp->cookiespath . ' Z-BlogPHP Imported Rule" stopProcessing="true">' . "\r\n";
        $s .= '	 <match url="^.*?" ignoreCase="false" />' . "\r\n";
        $s .= '	  <conditions logicalGrouping="MatchAll">' . "\r\n";
        $s .= '	   <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />' . "\r\n";
        $s .= '	   <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />' . "\r\n";
        $s .= '	  </conditions>' . "\r\n";
        $s .= '	 <action type="Rewrite" url="index.php/{R:0}" />' . "\r\n";
        $s .= '	</rule>' . "\r\n";

        $s .= '	<rule name="' . $zbp->cookiespath . ' Z-BlogPHP Imported Rule index.php" stopProcessing="true">' . "\r\n";
        $s .= '	 <match url="^index.php/.*?" ignoreCase="false" />' . "\r\n";
        $s .= '	  <conditions logicalGrouping="MatchAll">' . "\r\n";
        $s .= '	   <add input="{REQUEST_FILENAME}" matchType="IsFile" />' . "\r\n";
        $s .= '	  </conditions>' . "\r\n";
        $s .= '	 <action type="Rewrite" url="index.php/{R:0}" />' . "\r\n";
        $s .= '	</rule>' . "\r\n";

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
        $s .= '	rewrite (.*) $1/index.html break;' . "\r\n";
        $s .= '}' . "\r\n";
        $s .= 'if (-f $request_filename/index.php){' . "\r\n";
        $s .= '	rewrite (.*) $1/index.php;' . "\r\n";
        $s .= '}' . "\r\n";
        $s .= 'if (!-f $request_filename){' . "\r\n";
        $s .= '	rewrite (.*) ' . $zbp->cookiespath . 'index.php;' . "\r\n";
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
        global $zbp;

        $s = '[ISAPI_Rewrite]' . "\r\n";
        $s .= "\r\n";

        $s .= $this->Rewrite_httpdini($zbp->option['ZC_INDEX_REGEX'], 'index') . "\r\n";
        $s .= $this->Rewrite_httpdini($zbp->option['ZC_DATE_REGEX'], 'date') . "\r\n";
        $s .= $this->Rewrite_httpdini($zbp->option['ZC_AUTHOR_REGEX'], 'auth') . "\r\n";
        $s .= $this->Rewrite_httpdini($zbp->option['ZC_TAGS_REGEX'], 'tags') . "\r\n";
        $s .= $this->Rewrite_httpdini($zbp->option['ZC_CATEGORY_REGEX'], 'cate') . "\r\n";
        $s .= $this->Rewrite_httpdini($zbp->option['ZC_ARTICLE_REGEX'], 'article') . "\r\n";
        $s .= $this->Rewrite_httpdini($zbp->option['ZC_PAGE_REGEX'], 'page') . "\r\n";

        return $s;
    }

    /**
     * @param $url
     * @param $type
     *
     * @return string
     */
    public function Rewrite_httpdini($url, $type)
    {
        global $zbp;

        if (self::$categoryLayer == -1) {
            foreach ($zbp->categorys as $c) {
                if ($c->Level > self::$categoryLayer && strpos($c->Alias, '/') !== false) {
                    self::$categoryLayer = $c->Level;
                }
            }
            if (self::$categoryLayer == -1) {
                self::$categoryLayer = 0;
            }
        }
        switch (self::$categoryLayer) {
            case 3:
                $fullcategory = '[^\./_]*|[^\./_]*/[^\./_]*|[^\./_]*/[^\./_]*/[^\./_]*|[^\./_]+/[^\./_]*/[^\./_]*/[^\./_]*';
                break;
            case 2:
                $fullcategory = '[^\./_]*|[^\./_]*/[^\./_]*|[^\./_]*/[^\./_]*/[^\./_]*';
                break;
            case 1:
                $fullcategory = '[^\./_]*|[^\./_]*/[^\./_]*';
                break;
            default:
                $fullcategory = '[^\./_]*';
                break;
        }

        $s = $url;
        $s = str_replace('%page%', '%poaogoe%', $s);
        $url = str_replace('{%host%}', '', $url);
        $url = str_replace('.', '\\.', $url);
        if ($type == 'index') {
            $url = str_replace('%page%', '%poaogoe%', $url);
            preg_match('/[^\{\}]+(?=\{%%poaogoe%%\})/i', $s, $matches);
            if (isset($matches[0])) {
                $url = str_replace($matches[0], '(?:' . $matches[0] . ')<:1:>', $url);
            }
            $url = $url . ' ' . $zbp->cookiespath . 'index\.php\?page=$1&rewrite=1';
            $url = str_replace('%poaogoe%', '([0-9]*)', $url);
        }
        if ($type == 'cate' || $type == 'tags' || $type == 'date' || $type == 'auth' || $type == 'list') {
            $url = str_replace('%page%', '%poaogoe%', $url);
            preg_match('/(?<=\})[^\{\}]+(?=\{%poaogoe%\})/i', $s, $matches);
            if (isset($matches[0])) {
                $url = str_replace($matches[0], '(?:' . $matches[0] . ')?', $url);
            }
            $url = $url . ' ' . $zbp->cookiespath . 'index\.php\?' . $type . '=$1&page=$2&rewrite=1';
            $url = str_replace('%poaogoe%', '([0-9]*)', $url);
            $url = str_replace('%id%', '([0-9]+)', $url);
            $url = str_replace('%date%', '([0-9\-]+)', $url);
            if ($type == 'cate') {
                $url = str_replace('%alias%', '(' . $fullcategory . ')', $url);
            } else {
                $url = str_replace('%alias%', '([^\./_]+?)', $url);
            }
        }
        if ($type == 'page' || $type == 'article' || $type == 'post') {
            if (strpos($url, '%id%') !== false) {
                $url = $url . '(\?.*)? ' . $zbp->cookiespath . 'index\.php\?id=$1&rewrite=1';
                $url = str_replace('%id%', '([0-9]+)', $url);
            }
            if (strpos($url, '%alias%') !== false) {
                $url = $url . '(\?.*)? ' . $zbp->cookiespath . 'index\.php\?alias=$1&rewrite=1';
                if ($type == 'article') {
                    $url = str_replace('%alias%', '(?!zb_)([^/]+)', $url);
                } else {
                    $url = str_replace('%alias%', '(?!zb_)(.+)', $url);
                }
            }
            $url = str_replace('%category%', '(?:' . $fullcategory . ')', $url);
            $url = str_replace('%author%', '(?:[^\./_]+)', $url);
            $url = str_replace('%year%', '(?:[0-9]<:4:>)', $url);
            $url = str_replace('%month%', '(?:[0-9]<:1,2:>)', $url);
            $url = str_replace('%day%', '(?:[0-9]<:1,2:>)', $url);
        }
        $url = str_replace('{', '', $url);
        $url = str_replace('}', '', $url);
        $url = str_replace('<:', '{', $url);
        $url = str_replace(':>', '}', $url);

        return 'RewriteRule ' . $zbp->cookiespath . $url . '&full_uri=$0 [I,L]';
    }
}
