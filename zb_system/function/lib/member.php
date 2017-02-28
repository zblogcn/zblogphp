<?php
/**
 * 用户类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Member 类库
 */
class Member extends Base
{

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
    public function __construct()
    {
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
    public function __call($method, $args)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Call'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $method, $args);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;
                return $fpreturn;
            }
        }
    }

    /**
     * 自定义参数及值
     * @param $name
     * @param $value
     * @return null|string
     */
    public function __set($name, $value)
    {
        global $zbp;
        if ($name == 'Url') {
            return null;
        }
        if ($name == 'Avatar') {
            return null;
        }
        if ($name == 'LevelName') {
            return null;
        }
        if ($name == 'EmailMD5') {
            return null;
        }
        if ($name == 'StaticName') {
            return null;
        }
        if ($name == 'Template') {
            if ($value == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
                $value = '';
            }

            return $this->data[$name] = $value;
        }
        if ($name == 'PassWord_MD5Path') {
            return null;
        }
        if ($name == 'IsGod') {
            return null;
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     * @return mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'Url') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Url'] as $fpname => &$fpsignal) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;
                $fpreturn = $fpname($this);
                if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                    return $fpreturn;
                }
            }
            $u = new UrlRule($zbp->option['ZC_AUTHOR_REGEX']);
            $u->Rules['{%id%}'] = $this->ID;
            $u->Rules['{%alias%}'] = $this->Alias == '' ? rawurlencode($this->Name) : $this->Alias;

            return $u->Make();
        }
        if ($name == 'Avatar') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Mebmer_Avatar'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($this);
                if ($fpreturn) {
                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;
                    return $fpreturn;
                }
            }
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
            return $this->GetHashByMD5Path();
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
     * 静态方法，获取加盐及二次散列的,用于保存的最终密码
     * @param string $ps 明文密码
     * @param string $guid 用户唯一码
     * @return string
     */
    public static function GetPassWordByGuid($ps, $guid)
    {

        return md5(md5($ps) . $guid);
    }

    /**
     * 获取有期限的Token密码
     * @param string $wt_id Token的ID
     * @param string $day 时间，按天算 (1分钟就是1/24*60)
     * @return string (sha1字串+unix时间)
     */
    public function GetHashByToken($wt_id = '', $day = 30)
    {
        global $zbp;
        $t = intval( $day * 24 * 3600 ) + time();
        return CreateWebToken($wt_id, $t, $zbp->guid, $this->ID, $this->Password);
    }

    /**
     * 获取加路径盐的Hash密码 (其实并没有用path，而是用zbp->guid替代了)
     * @return string
     */
    public function GetHashByMD5Path()
    {
        global $zbp;
        return md5($this->Password . $zbp->guid);
    }

    /**
     * 保存用户数据
     * @return bool
     */
    public function Save()
    {
        global $zbp;
        if ($this->Template == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
            $this->data['Template'] = '';
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Save'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
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
        if ($this->ID >0) {
            unset($zbp->members[$this->ID]);
        }
        if ($this->Name != '') {
            unset($zbp->membersbyname[$this->Name]);
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Del'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        return parent::Del();
    }
}
