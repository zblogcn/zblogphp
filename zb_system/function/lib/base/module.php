<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 模块类.
 *
 * @property string FileName
 * @property int|string ID
 * @property string Source 模块来源
 * @property string Content
 * @property string Type 模块显示类型（div / ul）
 * @property bool NoRefresh 拒绝系统刷新该模块
 */
abstract class Base__Module extends Base
{
    public $private_links = [];

    /**
     * 构造函数.
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Module'], $zbp->datainfo['Module'], __CLASS__);
        $this->Type = 'ul';
    }

    /**
     * 设置参数值
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        global $zbp;
        if ('SourceType' == $name) {
            return;
        }
        if ('NoRefresh' == $name) {
            if ((bool) $value) {
                $this->Metas->norefresh = (bool) $value;
            } else {
                $this->Metas->Del('norefresh');
            }

            return;
        }
        if ('Links' == $name) {
            $this->private_links = $value;
            if (!is_array($this->private_links)) {
                $this->private_links = [];
            }
            $this->Metas->system_links = json_encode($this->private_links, JSON_UNESCAPED_UNICODE);
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Set'] as $fpname => &$fpsignal) {
            $fpname($this, $name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * 获取参数值
     *
     * @param $name
     *
     * @return bool|mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        if ('SourceType' == $name) {
            if ('system' == $this->Source) {
                return 'system';
            }
            if ('user' == $this->Source) {
                return 'user';
            }
            if (0 === stripos($this->Source, 'themeinclude_')) {
                return 'themeinclude';
            }
            if ('theme' == $this->Source) {
                return 'theme';
            }
            if (0 === stripos($this->Source, 'theme_')) {
                return 'theme';
            }
            if (0 === stripos($this->Source, 'plugin_')) {
                //如果是plugin_主题名，还是判断为theme，修正历史遗留问题
                $ts = $zbp->LoadThemes();
                foreach ($ts as $t) {
                    if ($this->Source == 'plugin_' . $t->id) {
                        return 'theme';
                    }
                }

                return 'plugin';
            }

            return 'plugin';
        }
        if ('NoRefresh' == $name) {
            return (bool) $this->Metas->norefresh;
        }
        if ('Links' == $name) {
            if (isset($this->Metas->system_links)) {
                $this->private_links = json_decode($this->Metas->system_links, false);
            } else {
                $this->ParseLink();
            }

            return $this->private_links;
        }
        if ('ContentWithoutId' == $name) {
            $s = preg_replace("/(id=\"[^\\s]*\"|id='[^\\s]*')/i", '', $this->Content);

            return $s;
        }
        if ('AutoContent' == $name) {
            if (in_array($this->FileName, ['catalog', 'calendar', 'comments', 'previous', 'archives', 'tags', 'statistics', 'authors'])) {
                return true;
            }

            return false;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Get'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name);
            if (PLUGIN_EXITSIGNAL_RETURN == $fpsignal) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::__get($name);
    }

    /**
     * @return bool
     */
    public function Save()
    {
        global $zbp;

        //强制处理转小写的遗留问题
        if ($zbp->option['ZC_FIX_MODULE_MIXED_FILENAME']) {
            if ($this->FileName !== strtolower($this->FileName)) {
                $list = SerializeString2Array($zbp->cache->module_mixed_filename_list);
                if (!in_array($this->FileName, $list)) {
                    $list[] = $this->FileName;
                    $zbp->cache->module_mixed_filename_list = serialize($list);
                    $zbp->SaveCache();
                }
            }
        }
        $this->FileName = strtolower($this->FileName);

        if (empty($this->HtmlID)) {
            $this->HtmlID = $this->FileName;
        }
        if (!empty($this->private_links)) {
            $this->Metas->system_links = json_encode($this->private_links, JSON_UNESCAPED_UNICODE);
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Save'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if (PLUGIN_EXITSIGNAL_RETURN == $fpsignal) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        if ('themeinclude' == $this->SourceType) {
            if (empty($this->FileName)) {
                return true;
            }

            $c = RemovePHPCode($this->Content);
            $d = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/';
            $f = $d . $this->FileName . '.htm';
            if (!file_exists($d)) {
                @mkdir($d, 0755);
            }
            @file_put_contents($f, $c);

            $fp = $d . $this->FileName . '.php';
            if (file_exists($fp)) {
                @unlink($fp);
            }

            if (0 == $this->ID) {
                $this->ID = (0 - (int) crc32($this->Source . $this->FileName));
            }

            return true;
        }

        //防Module重复保存的机制
        $m = $zbp->GetListType(
            'Module',
            $zbp->db->sql->get()->select($zbp->table['Module'])->where(['=', $zbp->datainfo['Module']['FileName'][0], $this->FileName])->sql,
        );
        if (count($m) >= 1 && 0 == $this->ID) {//如果已有同名，且新ID为0就不存
            return false;
        }

        return parent::Save();
    }

    /**
     * @return bool
     */
    public function Del()
    {
        global $zbp;

        //强制处理转小写的遗留问题
        if ($zbp->option['ZC_FIX_MODULE_MIXED_FILENAME']) {
            $list = SerializeString2Array($zbp->cache->module_mixed_filename_list);
            $has_mixed = false;
            foreach ($list as $key => $value) {
                if (strtolower($value) == strtolower($this->FileName)) {
                    unset($list[$key]);
                    $has_mixed = true;
                }
            }
            if (true == $has_mixed) {
                $zbp->cache->module_mixed_filename_list = serialize($list);
                $zbp->SaveCache();
            }
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if (PLUGIN_EXITSIGNAL_RETURN == $fpsignal) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        if ('themeinclude' == $this->SourceType) {
            if (empty($this->FileName)) {
                return true;
            }

            $f = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $this->FileName . '.htm';
            if (file_exists($f)) {
                @unlink($f);
            }

            $f = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $this->FileName . '.php';
            if (file_exists($f)) {
                @unlink($f);
            }

            $zbp->RemoveCache($this);

            return true;
        }

        return parent::Del();
    }

    public function Build()
    {
        if (true == $this->NoRefresh) {
            return;
        }

        if (isset(ModuleBuilder::$List[$this->FileName])) {
            if (isset(ModuleBuilder::$List[$this->FileName]['function'])) {
                $f = ModuleBuilder::$List[$this->FileName]['function'];
                $p = ModuleBuilder::$List[$this->FileName]['parameters'];
                $p = is_array($p) ? $p : [];

                $this->Content = call_user_func_array(ParseFilterPlugin($f), $p);

                return true;
            }
        }
        if (isset($this->Metas->system_function)) {
            $f = $this->Metas->system_function;
            $p = $this->Metas->system_parameters;
            $p = is_array($p) ? $p : [];

            $this->Content = call_user_func_array(ParseFilterPlugin($f), $p);

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function GetSideBarInUsed()
    {
        global $zbp;
        $array = [];
        $inused = [];
        for ($i = 1; $i <= 9; ++$i) {
            $optionName = 1 === $i ? 'ZC_SIDEBAR_ORDER' : "ZC_SIDEBAR{$i}_ORDER";
            $array[$i] = $zbp->option[$optionName];
        }
        foreach ($array as $id => $s) {
            if (false !== stripos('|' . $s . '|', '|' . $this->FileName . '|')) {
                $inused[] = $id;
            }
        }

        return $inused;
    }

    public function ParseLink()
    {
        $s = $this->Content;
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $s);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $item = [];
        $aNodes = $dom->getElementsByTagName('a');
        if ($aNodes->length > 0) {
            foreach ($aNodes as $a) {
                $href = [];
                $href['content'] = $a->nodeValue;
                $attributes = $a->attributes;
                if ($attributes->length > 0) {
                    // 遍历属性集合
                    for ($i = 0; $i < $attributes->length; ++$i) {
                        $attr = $attributes->item($i);
                        $href[$attr->nodeName] = $attr->nodeValue;
                    }
                }
                $item[] = $href;
            }
        }
        $item = json_encode($item, JSON_UNESCAPED_UNICODE);
        $item = json_decode($item, false);
        $this->private_links = $item;

        return $item;
    }
}
