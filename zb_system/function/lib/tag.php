<?php
/**
 * Tag类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Tag 类库
 */
class Tag extends Base {

    /**
     *
     */
    public function __construct() {
        global $zbp;
        parent::__construct($zbp->table['Tag'], $zbp->datainfo['Tag'], __CLASS__);
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args) {
        $plugin = EmitPlugin('Filter_Plugin_Tag_Call', $this, $method, $args);
        if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];
    }

    /**
     * @param $name
     * @param $value
     * @return null|string
     */
    public function __set($name, $value) {
        global $zbp;
        if ($name == 'Url') {
            return null;
        }
        if ($name == 'Template') {
            if ($value == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
                $value = '';
            }

            return $this->data[$name] = $value;
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     * @return mixed|string
     */
    public function __get($name) {
        global $zbp;
        if ($name == 'Url') {
            $plugin = EmitPlugin('Filter_Plugin_Tag_Url', $this);
            if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];
            $backAttr = $zbp->option['ZC_ALIAS_BACK_ATTR'];
            $u = new UrlRule($zbp->option['ZC_TAGS_REGEX']);
            $u->Rules['{%id%}'] = $this->ID;
            $u->Rules['{%alias%}'] = rawurlencode($this->Alias == '' ? $this->$backAttr : $this->Alias);

            return $u->Make();
        }
        if ($name == 'Template') {
            $value = $this->data[$name];
            if ($value == '') {
                $value = $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
            }

            return $value;
        }

        return parent::__get($name);
    }

    /**
     * @return bool
     */
    public function Save() {
        global $zbp;
        if ($this->Template == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
            $this->data['Template'] = '';
        }

        $plugin = EmitPlugin('Filter_Plugin_Tag_Save', $this);
        if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];

        return parent::Save();
    }

    /**
     * @return bool
     */
    public function Del() {
        $plugin = EmitPlugin('Filter_Plugin_Tag_Del', $this);
        if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];

        return parent::Del();
    }

}
