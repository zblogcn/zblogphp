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
     * @var object
     */
    public $RulesObject = null;

    /**
     * @var string
     */
    public $Url = '';

    private $PreUrl = '';

    private $Route = array();

    /**
     * @var bool
     */
    public $useAbbr = false; //指示是否是可以精简规则的

    /**
     * @var bool
     */
    public $forceDisplayFirstPage = false;//强制显示page参数

    public static $categoryLayer = '-1';

    /**
     * @param $url
     */
    public function __construct($url)
    {
        if (self::$categoryLayer == -1) {
            self::$categoryLayer = $GLOBALS['zbp']->category_recursion_real_deep;
        }
        if (is_array($url)) {
            $this->Route = $url;
            //if (isset($url['urlrule_regex']) && $url['urlrule_regex'] != '') {
            //    $this->PreUrl = $url['urlrule_regex'];
            //} else {
            if (isset($url['urlrule'])) {
                $this->PreUrl = $url['urlrule'];
            }
            //}
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

    public function SetPreUrl($url)
    {
        $this->PreUrl = $url;
    }

    /**
     * @return array
     */
    public function GetRoute()
    {
        return $this->Route;
    }

    public function SetRoute($array)
    {
        $this->Route = $array;
    }

    /**
     * @return string
     */
    public function Make()
    {
        global $zbp;
        $url = $this->GetPreUrl();
        $route = $this->GetRoute();

        $only_match_page = GetValueInArray($route, 'only_match_page', false);
        $forceDisplayFirstPage = $this->forceDisplayFirstPage;
        $useAbbr = $this->useAbbr;
        if (!empty($route)) {
            if (isset($route['abbr_url'])) {
                $useAbbr = (bool) $route['abbr_url'];
            }
            if (isset($route['force_display_firstpage'])) {
                $forceDisplayFirstPage = (bool) $route['force_display_firstpage'];
            }
        }

        if (isset($this->Rules['{%page%}'])) {
            if ($this->Rules['{%page%}'] == '1' || $this->Rules['{%page%}'] == '0') {
                //如果强制显示第一页为假和只匹配带page参数的条件为假
                if ($forceDisplayFirstPage == false && $only_match_page == false) {
                    $this->Rules['{%page%}'] = '';
                }
            }
        } else {
            $this->Rules['{%page%}'] = '';
        }

        //处理之前Active的过程
        if (strpos($url, '{&') !== false) {
            $url = str_ireplace('{&', '&', $url);
            $url = str_ireplace('=%', '={%', $url);
        }

        //如果没有page页，就删除{%page%}
        if ($this->Rules['{%page%}'] == '' && strpos($url, '{%page%}') !== false) {
            if (stripos($url, '_{%page%}') !== false) {
                $url = str_replace('_{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '/{%page%}') !== false) {
                $url = str_replace('/{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '-{%page%}') !== false) {
                $url = str_replace('-{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '={%page%}') !== false) {
                $url = str_replace('={%page%}', '={%page%}', $url);
            } else {
                $url = preg_replace('/(?<=\})[^\}]+(?=\{%page%\})/i', '', $url, 1);
            }

            //如是精简模式，就把{%page%}之后的全部删除，再删除之前的，一直到}/&为止
            if ($useAbbr) {
                $array = explode('{%page%}', $url);
                if (is_array($array) && isset($array[0])) {
                    $url = substr($url, 0, strpos($url, '{%page%}'));
                    $i = (int) strripos($url, '}');
                    $j = (int) strripos($url, '/');
                    $k = (int) strripos($url, '&');
                    $i = ($j > $i) ? $j : $i;
                    $i = ($k > $i) ? $k : $i;
                    $url = substr($url, 0, ($i + 1));
                }
            }
            $url = str_replace('{%page%}', '', $url);
            $url = str_replace('{%host%}/', '{%host%}', $url);
        }

        //从“Rules数组规则”替换一次
        $prefix = GetValueInArray($route, 'prefix', '');
        $prefix = ($prefix != '') ? ($prefix . '/') : $prefix;
        $this->Rules['{%host%}'] = $zbp->host . $prefix;

        foreach ($this->Rules as $key => $value) {
            if (!is_array($value)) {
                $url = str_replace($key, (string) $value, $url);
            }
        }

        //1.7的魔术戏法：处理路由规则里预先指定好的"关联数据来源"的参数并先替换一次
        $paras = self::ProcessParameters($route);
        foreach ($paras as $key => &$p) {
            if ($p['relate'] && is_object($this->RulesObject)) {
                $object = clone $this->RulesObject;
                $objectArray = explode('.', $p['relate']);
                foreach ($objectArray as $key => $subObject) {
                    //先判断是数组，还是函数，还是对象
                    if (stripos($subObject, '[') !== false) {
                        $i = preg_match_all('/\[.+\]/', $subObject, $m);
                        if ($i > 0) {
                            $arrayName = trim(SplitAndGet($subObject, '[', 0));
                            $a = trim(str_replace(array('[', ']'), '', current($m[0])));
                            $object = $object->$arrayName;
                            if (isset($object[$a])) {
                                $object = $object[$a];
                            } else {
                                $object = null;
                            }
                        }
                    } elseif (stripos($subObject, '(') !== false) {
                        $i = preg_match_all('/\(.+\)/', $subObject, $m);
                        if ($i > 0) {
                            $functionName = trim(SplitAndGet($subObject, '(', 0));
                            $a = trim(str_replace(array('(', ')'), '', current($m[0])));
                            $array = array();
                            if ($a) {
                                $array = explode(',', $a);
                            }
                            $object = call_user_func_array(array($object, $functionName), $array);
                        }
                    } else {
                        $object = $object->$subObject;
                    }
                    //如果是标量就退出
                    if (is_scalar($object) || is_null($object)) {
                        break;
                    }
                }
                if (is_scalar($object)) {
                    $p['relate_value'] = rawurlencode_without_backslash($object);
                }
                if (is_null($object)) {
                    $p['relate_value'] = '';
                }
            }
        }
        foreach ($paras as $key => $p) {
            //首先替换人为指定value的
            if (array_key_exists('value', $p)) {
                $url = str_replace('{%' . $p['name'] . '%}', $p['value'], $url);
            }
            if (isset($p['relate']) && $p['relate'] && array_key_exists('relate_value', $p)) {
                $url = str_replace('{%' . $p['name'] . '%}', $p['relate_value'], $url);
            }
        }

        //处理没有被替换掉的{%abc%}之类的
        $url = preg_replace('/\{%[^%]+%\}/', '', $url);

        //处理之前Active的过程 去掉无用的=&之类的
        if (strpos($url, '?') !== false) {
            $url = $url . '&';
            $j = substr_count($url, '=&');
            for ($i = 0; $i < $j; $i++) {
                $url = preg_replace('/&[^=]+=&/', '&', $url);
            }
        }

        //处理尾巴上的//或是&
        if (substr($url, -2) == '//') {
            $url = substr($url, 0, (strlen($url) - 1));
        }
        $url = rtrim($url, '&');
        $url = rtrim($url, '?');

        $this->Url = htmlspecialchars($url);

        return $this->Url;
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
        
        if (isset($route['args'])) {
            $parameters = $route['args'];
        } else {
            $parameters = array();
        }
        $args = $parameters;
        $default_names = array('name', 'regex', 'alias', 'relate');

        //从$route的args项中读取各种花式设置方法设置的参数
        foreach ($args as $key => $value) {
            if (is_array($value)) {
                //如果是array( array('name3','regex3','relate3','alias3') )
                if (is_int(key($value))) {
                    $array = array();
                    if (count($value) <= 1) {
                        $array['name'] = $value[0];
                    }
                    if (count($value) <= 2) {
                        $array['regex'] = $value[1];
                    }
                    if (count($value) <= 3) {
                        $array['alias'] = $value[2];
                    }
                    if (count($value) <= 4) {
                        $array['relate'] = $value[3];
                    }
                    foreach ($default_names as $key2 => $value2) {
                        if (!array_key_exists($value2, $array)) {
                            $array[$value2] = '';
                        }
                    }
                    $newargs[] = $array;
                } else {
                //如果是array( array('name'=>'name4','regex'=>'regex4','relate'=>'relate4', 'alias'=>'alias4') )
                    foreach ($default_names as $key2 => $value2) {
                        if (!array_key_exists($value2, $value)) {
                            $value[$value2] = '';
                        }
                    }
                    $newargs[] = $value;
                }
            } else {
                if (is_integer($key)) {
                    //如果是  array( 'alias1@name1', 'alias2@name2')
                    if (stripos($value, '@') !== false) {
                        $alias = SplitAndGet($value, '@', 0);
                        $name = SplitAndGet($value, '@', 1);
                        $newargs[] = array('name' => $name, 'regex' => '', 'alias' => $alias, 'relate' => '');
                    } else {
                    //如果是  array( 'name7', 'name8')
                        $newargs[] = array('name' => $value, 'regex' => '', 'alias' => '', 'relate' => '');
                    }
                } else {
                    //如果是  array( 'alias5@name5'=>'regex5')
                    if (stripos($key, '@') !== false) {
                        $alias = SplitAndGet($key, '@', 0);
                        $name = SplitAndGet($key, '@', 1);
                        $newargs[] = array('name' => $name, 'regex' => $value, 'alias' => $alias, 'relate' => '');
                    } else {
                    //如果是  array( 'name6'=>'regex6')
                        $newargs[] = array('name' => $key, 'regex' => $value, 'alias' => '', 'relate' => '');
                    }
                }
            }
        }

        //在$route['urlrule']取出嵌入在规则里的每一个参数的名称
        $route_array = array();
        if (!empty($route) && is_array($route)) {
            $s = $route['urlrule'];
            $s = str_replace('{%host%}', '', $s);
            $marray = array();
            if (preg_match_all('/%[^%]+%/', $s, $m) >= 1) {
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
                $route_array[] = $value;
            }
        }
        foreach ($route_array as $key => $value) {
            //在$newargs 查找$value是否存在，不存在就插入
            $b = false;
            foreach ($newargs as $key2 => $value2) {
                if ($value2['name'] == $value) {
                    $b = true;
                }
            }
            if ($b == false) {
                $newargs[] = array('name' => $value, 'regex' => '', 'alias' => '', 'relate' => '');
            }
        }
        //在$newargs 查找如果有同时设有$alias和$name的参数，不在$route_array中就把它从$newargs删除了
        foreach ($newargs as $key => $value) {
            if ($value['alias'] != '' && $value['name'] != '') {
                if (in_array($value['name'], $route_array) == false) {
                    unset($newargs[$key]);
                }
            }
        }

        foreach ($newargs as $key => &$value) {
            if ($value['name'] == 'id' && $value['regex'] == '') {
                $value['regex'] = '[0-9]+';
            }
            if ($value['name'] == 'alias' && $value['regex'] == '') {
                $value['regex'] = '.+';
            }
            if ($value['name'] == 'category' && $value['regex'] == '') {
                $value['regex'] = '.+';
            }
            if ($value['name'] == 'author' && $value['regex'] == '') {
                $value['regex'] = '[^\.\/_]+';
            }
            if ($value['name'] == 'year' && $value['regex'] == '') {
                $value['regex'] = '[0-9]{4}';
            }
            if ($value['name'] == 'month' && $value['regex'] == '') {
                $value['regex'] = '[0-9]{1,2}';
            }
            if ($value['name'] == 'day' && $value['regex'] == '') {
                $value['regex'] = '[0-9]{1,2}';
            }
            if ($value['name'] == 'page' && $value['regex'] == '') {
                $value['regex'] = '[0-9]+';
            }
            if ($value['name'] == 'date' && $value['regex'] == '') {
                $separator = $GLOBALS['option']['ZC_DATETIME_SEPARATOR'];
                $separator = str_replace('/', '\/', $separator);
                $separator = str_replace('-', '\-', $separator);
                $separator = str_replace('.', '\.', $separator);
                $date = $GLOBALS['option']['ZC_DATETIME_RULE'];
                $letter = (strpos($date, 'F') !== false || strpos($date, 'M') !== false) ? 'a-zA-Z' : '';
                $value['regex'] = '[' . $letter . '0-9' . $separator . ']+';
            }
        }
        return $newargs;
    }

    /**
     * @param $route
     * @param $keepPage 指示规则是否需要匹配{%page%}(如为假将生成一个没有{%page%}的参数) boolean
     *
     * @return string
     */
    public static function OutputUrlRegEx_Route($route, $keepPage = false)
    {
        global $zbp;

        $match_with_page = $keepPage;
        $useAbbr = (bool) GetValueInArray($route, 'abbr_url', false);

        $newargs = self::ProcessParameters($route);
        $orginUrl = $url = $route['urlrule'];

        if ($match_with_page == false && strpos($url, '{%page%}') !== false) {
            if (stripos($url, '_{%page%}') !== false) {
                $url = str_replace('_{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '/{%page%}') !== false) {
                $url = str_replace('/{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '-{%page%}') !== false) {
                $url = str_replace('-{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '={%page%}') !== false) {
                $url = str_replace('={%page%}', '={%page%}', $url);
            } else {
                $url = preg_replace('/(?<=\})[^\}]+(?=\{%page%\})/i', '', $url, 1);
            }

            if ($useAbbr) {
                $array = explode('{%page%}', $url);
                if (is_array($array) && isset($array[0])) {
                    $url = substr($url, 0, strpos($url, '{%page%}'));
                    $i = (int) strripos($url, '}');
                    $j = (int) strripos($url, '/');
                    $k = (int) strripos($url, '&');
                    $i = ($j > $i) ? $j : $i;
                    $i = ($k > $i) ? $k : $i;
                    $url = substr($url, 0, ($i + 1));
                }
            }

            $url = str_replace('{%page%}', '', $url);
        }
        $url = str_replace('{%host%}/', '{%host%}', $url);
        $prefix = GetValueInArray($route, 'prefix', '');
        $prefix = ($prefix != '') ? ($prefix . '/') : $prefix;
        $url = str_replace('{%host%}', $prefix, $url);
        $url = str_replace('.', '\\.', $url);
        $url = str_replace('/', '\\/', $url);
        $url = str_replace('?', '\\?', $url);

        //把page传进$newargs
        $newargs[] = array('name'  => 'page', 'regex' => '[0-9]+');
        //传入{%参数%}的正则
        foreach ($newargs as $key => $value) {
            $url = str_replace('{%' . $value['name'] . '%}', '(?P<' . $value['name'] . '>' . $value['regex'] . ')', $url);
        }

        $url = '^' . $url . '$';
        if ($url == '^$' || $url == '^\/$') {
            return '';
        }

        return '/(?J)' . $url . '/';
    }

    /**
     * 1.7新版本的OutputUrlRegEx (如果需要使用，请用V2新版输出，注意结果有可能是空值)
     *
     * @param $url
     * @param $type
     * @param $keepPage 指示规则是否需要匹配{%page%}(如为假将生成一个没有{%page%}的参数) boolean
     * @param $useAbbr 指示规则可以被缩写为"域名/"或是"域名/目录/"
     *
     * @return string
     */
    public static function OutputUrlRegEx_V2($url, $type, $keepPage = false, $useAbbr = false)
    {
        global $zbp;

        if (is_array($url)) {
            return self::OutputUrlRegEx_Route($url, $keepPage = false);
        }
        $match_with_page = $keepPage;

        if (self::$categoryLayer == -1) {
            self::$categoryLayer = $GLOBALS['zbp']->category_recursion_real_deep;
        }
        $post_type_name = array('post');
        foreach ($zbp->posttype as $key => $value) {
            $post_type_name[] = $value['name'];
        }
        $orginUrl = $url;

        if ($match_with_page == false && strpos($url, '{%page%}') !== false) {
            if (stripos($url, '_{%page%}') !== false) {
                $url = str_replace('_{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '/{%page%}') !== false) {
                $url = str_replace('/{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '-{%page%}') !== false) {
                $url = str_replace('-{%page%}', '{%page%}', $url);
            } elseif (stripos($url, '={%page%}') !== false) {
                $url = str_replace('={%page%}', '={%page%}', $url);
            } else {
                $url = preg_replace('/(?<=\})[^\}]+(?=\{%page%\})/i', '', $url, 1);
            }

            if ($useAbbr) {
                $array = explode('{%page%}', $url);
                if (is_array($array) && isset($array[0])) {
                    $url = substr($url, 0, strpos($url, '{%page%}'));
                    $i = (int) strripos($url, '}');
                    $j = (int) strripos($url, '/');
                    $k = (int) strripos($url, '&');
                    $i = ($j > $i) ? $j : $i;
                    $i = ($k > $i) ? $k : $i;
                    $url = substr($url, 0, ($i + 1));
                }
            }

            $url = str_replace('{%page%}', '', $url);
        }
        $url = str_replace('{%host%}/', '{%host%}', $url);
        $url = str_replace('.', '\\.', $url);
        $url = str_replace('/', '\\/', $url);
        $url = str_replace('?', '\\?', $url);

        $array = array();
        $array[] = array('{%page%}' => '(?P<page>[0-9]+)');
        $array[] = array('{%host%}' => '');

        if ($type == 'date') {
            $array[] = array('{%date%}' => '(?P<date>[0-9\-]+)');
        } elseif ($type == 'cate') {
            $array[] = array('{%id%}' => '(?P<cate>[0-9]+)');
            $array[] = array('{%alias%}' => '(?P<cate>.+)');
        } elseif ($type == 'tags') {
            $array[] = array('{%id%}' => '(?P<tags>[0-9]+)');
            $array[] = array('{%alias%}' => '(?P<tags>[^\.\/_]+)');
        } elseif ($type == 'auth') {
            $array[] = array('{%id%}' => '(?P<auth>[0-9]+)');
            $array[] = array('{%alias%}' => '(?P<auth>[^\.\/_]+)');
        } elseif (in_array($type, $post_type_name)) {
            if (strpos($url, '%id%') !== false) {
                $array[] = array('{%id%}' => '(?P<id>[0-9]+)');
            }
            if (strpos($url, '%alias%') !== false) {
                if ($type == 'article') {
                    $array[] = array('{%alias%}' => '(?P<alias>.+)');
                } else {
                    $array[] = array('{%alias%}' => '(?P<alias>.+)');
                }
            }
            $array[] = array('{%category%}' => '(?P<category>.+)');
            $array[] = array('{%author%}' => '(?P<author>.+)');
            $array[] = array('{%year%}' => '(?P<year>[0-9]{4})');
            $array[] = array('{%month%}' => '(?P<month>[0-9]{1,2})');
            $array[] = array('{%day%}' => '(?P<day>[0-9]{1,2})');
        } else {
            $array[] = array('{%id%}' => '(?P<' . $type . '>[0-9]+)');
            $array[] = array('{%alias%}' => '(?P<' . $type . '>.+)');
            $array[] = array('{' . $type . '}' => '(?P<' . $type . '>.+)');
        }
 
        foreach ($array as $key => $value) {
            $url = str_replace(key($value), current($value), $url);
        }

        $url = '^' . $url . '$';
        if ($url == '^$' || $url == '^\/$') {
            return '';
        }
        return '/(?J)' . $url . '/';

        // 关于J标识符的使用
        // @see https://bugs.php.net/bug.php?id=47456
    }

    /**
     * 旧版本的OutputUrlRegEx (暂时没有删除，给老版本兼容使用)
     *
     * @param $url
     * @param $type
     * @param $keepPage boolean
     *
     * @return string
     */
    public static function OutputUrlRegEx($url, $type, $keepPage = false)
    {
        global $zbp;

        if (is_array($url)) {
            return self::OutputUrlRegEx_Route($url, $keepPage);
        }

        if (self::$categoryLayer == -1) {
            self::$categoryLayer = $GLOBALS['zbp']->category_recursion_real_deep;
        }
        $post_type_name = array('post');
        foreach ($zbp->posttype as $key => $value) {
            $post_type_name[] = $value['name'];
        }

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
                if ($keepPage) {
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
            if ($keepPage) {
                $url = str_replace('%poaogoe%', '(?P<page>[0-9]*)', $url);
            } else {
                $url = str_replace('%poaogoe%', '', $url);
            }

            $url = str_replace('%date%', '(?P<date>[0-9\-]+)', $url);
            if ($type == 'cate') {
                $url = str_replace('%id%', '(?P<id>[0-9]+)', $url);

                $carray = array();
                for ($i = 1; $i <= self::$categoryLayer; $i++) {
                    $carray[$i] = '[^\./_]*';
                    for ($j = 1; $j <= ($i - 1); $j++) {
                        $carray[$i] = '[^\./_]*/' . $carray[$i];
                    }
                }
                $fullcategory = implode('|', $carray);
                $url = str_replace('%alias%', '(?P<alias>(' . $fullcategory . ')+?)', $url);
            }
            if ($type == 'tags') {
                   $url = str_replace('%id%', '(?P<id>[0-9]+)', $url);
                $url = str_replace('%alias%', '(?P<alias>[^\./_]+)', $url);
            }
            if ($type == 'auth') {
                $url = str_replace('%id%', '(?P<id>[0-9]+)', $url);
                $url = str_replace('%alias%', '(?P<alias>[^\./_]+)', $url);
            }
        }
        if (in_array($type, $post_type_name)) {
            $url = str_replace('%page%', '%poaogoe%', $url);
            preg_match('/(?<=\})[^\{\}]+(?=\{%poaogoe%\})/i', $s, $matches);
            if (isset($matches[0])) {
                if ($keepPage) {
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
            if ($keepPage) {
                $url = str_replace('%poaogoe%', '(?P<page>[0-9]*)', $url);
            } else {
                $url = str_replace('%poaogoe%', '', $url);
            }
            if (strpos($url, '%id%') !== false) {
                $url = str_replace('%id%', '(?P<id>[0-9]+)', $url);
            }
            if (strpos($url, '%alias%') !== false) {
                if ($type == 'article') {
                    $url = str_replace('%alias%', '(?P<alias>.+)', $url);
                } else {
                    $url = str_replace('%alias%', '(?P<alias>.+)', $url);
                }
            }
            $url = $url . '$';
            $url = str_replace('%category%', '(?P<category>([^\./_]*/?)<:1,' . self::$categoryLayer . ':>)', $url);
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
