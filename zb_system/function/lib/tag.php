<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Tag类.
 *
 * @property string Template
 * @property string Name
 * @property string ID
 * @property string Alias
 * @property string Url
 * @property int|string Count 文章数量
 */
class Tag extends Base
{

    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Tag'], $zbp->datainfo['Tag'], __CLASS__);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Call'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $method, $args);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        global $zbp;
        if (in_array($name, array('Url', 'AliasFirst'))) {
            return;
        }
        if ($name == 'Template') {
            if ($value == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
                $value = '';
            }
            $this->data[$name] = $value;

            return;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Set'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     *
     * @return mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'Url') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Url'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($this);
                if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                    return $fpreturn;
                }
            }
            $backAttr = $zbp->option['ZC_ALIAS_BACK_ATTR'];
            $routes = $zbp->GetPostType($this->Type, 'routes');
            if (isset($routes['post_' . $zbp->GetPostType($this->Type, 'name') . '_list_tag'])) {
                $u = new UrlRule($zbp->GetRoute($routes['post_' . $zbp->GetPostType($this->Type, 'name') . '_list_tag']));
            } else {
                $u = new UrlRule($zbp->GetPostType($this->Type, 'list_tag_urlrule'));
            }
            $u->RulesObject = &$this;
            $u->Rules['{%id%}'] = $this->ID;
            $u->Rules['{%alias%}'] = rawurlencode_without_backslash($this->Alias == '' ? $this->$backAttr : $this->Alias);

            return $u->Make();
        }
        if ($name == 'Template') {
            $value = $this->data[$name];
            if ($value == '') {
                $value = $zbp->GetPostType($this->Type, 'tag_template');
            }

            return $value;
        }
        if ($name == 'AliasFirst') {
            if ($this->Alias) {
                return $this->Alias;
            } else {
                return $this->Name;
            }
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Get'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
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
        if ($this->Template == $zbp->GetPostType($this->Type, 'tag_template')) {
            $this->data['Template'] = '';
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Save'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::Save();
    }

    /**
     * @return bool
     */
    public function Del()
    {
        global $zbp;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        $zbp->RemoveCache($this);

        return parent::Del();
    }

}
