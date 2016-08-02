<?php
/**
 * 用户类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Member 类库
 */
class Member extends Base {

    private $nonSetter = array('Url', 'Avatar', 'LevelName', 'EmailMD5', 'StaticName', 'PassWord_MD5Path', 'IsGod');
    /**
     * @var string 头像图片地址
     */
    private $_avatar = '';

    /**
     * @var boolean 创始id
     */
    private $_isgod = null;

    /**
     * 构造函数，默认用户设为anonymous
     */
    public function __construct() {
        global $zbp;
        parent::__construct($zbp->table['Member'], $zbp->datainfo['Member'], __CLASS__);

        $this->Name = $zbp->lang['msg']['anonymous'];
    }

    /**
     * 自定义函数
     * @api Filter_Plugin_Member_Call
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args) {
        $plugin = TriggerPlugin('Filter_Plugin_Member_Call', array($this, $method, $args));
        if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];
    }

    /**
     * 自定义参数及值
     * @param $name
     * @param $value
     * @return null|string
     */
    public function __set($name, $value) {
        global $zbp;
        if (in_array($name, $this->nonSetter)) {
            return null;
        }
        elseif ($name == 'Template') {
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
            $plugin = TriggerPlugin('Filter_Plugin_Member_Url', array($this));
            if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];
            $u = new UrlRule($zbp->option['ZC_AUTHOR_REGEX']);
            $u->Rules['{%id%}'] = $this->ID;
            $u->Rules['{%alias%}'] = $this->Alias == '' ? rawurlencode($this->Name) : $this->Alias;

            return $u->Make();
        }
        if ($name == 'Avatar') {
            $plugin = TriggerPlugin('Filter_Plugin_Member_Avatar', array($this));
            if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];
            if ($this->_avatar) {
                return $this->_avatar;
            }

            $s = $zbp->usersdir . 'avatar/' . $this->ID . '.png';
            if (is_readable($s)) {
                $this->_avatar = $zbp->host . 'zb_users/avatar/' . $this->ID . '.png';

                return $this->_avatar;
            }
            $this->_avatar = $zbp->host . 'zb_users/avatar/0.png';

            return $this->_avatar;
        }
        if ($name == 'LevelName') {
            return $zbp->lang['user_level_name'][$this->Level];
        }
        if ($name == 'EmailMD5') {
            return md5($this->Email);
        }
        if ($name == 'StaticName') {
            if ($this->Alias) {
                return $this->Alias;
            }

            return $this->Name;
        }
        if ($name == 'Template') {
            $value = $this->data[$name];
            if ($value == '') {
                $value = $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
            }

            return $value;
        }
        if ($name == 'PassWord_MD5Path') {
            return md5($this->Password . $zbp->guid);
        }
        if ($name == 'IsGod') {
            if ($this->_isgod === true || $this->_isgod === false) {
                return $this->_isgod;
            } else {
                $sql = $zbp->db->sql->Select($zbp->table['Member'], '*', array(array('=', 'mem_Level', 1)), 'mem_ID ASC', 1, null);
                $am = $zbp->GetListType('Member', $sql);
                if ($am[0]->ID == $this->ID) {
                    $this->_isgod = true;
                } else {
                    $this->_isgod = false;
                }

                return $this->_isgod;
            }
        }

        return parent::__get($name);
    }

    /**
     * 获取加盐及二次加密的密码
     * @param string $ps 明文密码
     * @param string $guid 用户唯一码
     * @return string
     */
    public static function GetPassWordByGuid($ps, $guid) {

        return md5(md5($ps) . $guid);

    }

    /**
     * 保存用户数据
     * @return bool
     */
    public function Save() {
        global $zbp;
        if ($this->Template == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
            $this->data['Template'] = '';
        }

        $plugin = TriggerPlugin('Filter_Plugin_Member_Save', array($this));
    if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];

        return parent::Save();
    }

    /**
     * @return bool
     */
    public function Del() {
        $plugin = TriggerPlugin('Filter_Plugin_Member_Del', array($this));
    if ($plugin['signal'] == PLUGIN_EXITSIGNAL_RETURN) return $plugin['return'];

        return parent::Del();
    }

}
