<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 用户基类.
 *
 * @property int|string ID
 * @property int|string Level 用户等级
 * @property string Name
 * @property string Password
 * @property string Template
 * @property string Email
 * @property string LevelName 等级详细信息
 * @property string HomePage
 * @property string Guid
 * @property string Url
 * @property string Alias
 * @property int|string Status 用户状态
 * @property string PassWord_MD5Path
 * @property string StaticName 对外展示的名字（推荐用于替代Name和Alias）
 * @property bool IsGod 是否最高管理员
 * @property int|string Articles 文章数量
 * @property int|string Pages 页面数量
 * @property int|string Comments 评论数量
 * @property int|string Uploads 上传数量
 */
abstract class Base__Member extends Base
{

    /**
     * @var string 头像图片地址
     */
    private $private_avatar = '';

    /**
     * @var bool 创始id
     */
    private $private_isgod = null;

    /**
     * 构造函数，默认用户设为anonymous.
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Member'], $zbp->datainfo['Member'], __CLASS__);

        $this->Name = $zbp->lang['msg']['anonymous'];
        $this->Level = ZC_MEMBER_LEVER_LOWEST;

        $this->CreateTime = time();
        $this->UpdateTime = $this->CreateTime;
    }

    /**
     * 自定义函数.
     *
     * @api Filter_Plugin_Member_Call
     *
     * @param $method
     * @param $args
     *
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
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        global $zbp;
        if (in_array($name, array('Url', 'Avatar', 'LevelName', 'EmailMD5', 'StaticName', 'PassWord_MD5Path', 'IsGod', 'AliasFirst'))) {
            return;
        } elseif ($name == 'Template') {
            if ($value == $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']) {
                $value = '';
            }
            $this->data[$name] = $value;

            return;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Set'] as $fpname => &$fpsignal) {
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
            foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Url'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($this);
                if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                    return $fpreturn;
                }
            }
            $routes = $zbp->GetPostType(0, 'routes');
            if (isset($routes['post_' . $zbp->GetPostType(0, 'name') . '_list_author'])) {
                $u = new UrlRule($zbp->GetRoute($routes['post_' . $zbp->GetPostType(0, 'name') . '_list_author']));
            } else {
                $u = new UrlRule($zbp->GetPostType(0, 'list_author_urlrule'));
            }
            $u->RulesObject = &$this;
            $u->Rules['{%id%}'] = $this->ID;
            $u->Rules['{%alias%}'] = $this->Alias == '' ? rawurlencode($this->Name) : $this->Alias;

            return $u->Make();
        }
        if ($name == 'Avatar') {
            if ($this->private_avatar) {
                return $this->private_avatar;
            }
            foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Avatar'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($this);
                if ($fpreturn) {
                    $this->private_avatar = $fpreturn;

                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                    return $fpreturn;
                }
            }
            $s = $zbp->usersdir . 'avatar/' . $this->ID . '.png';
            if (is_readable($s)) {
                $this->private_avatar = $zbp->host . 'zb_users/avatar/' . $this->ID . '.png';

                return $this->private_avatar;
            }
            $this->private_avatar = $zbp->host . 'zb_users/avatar/0.png';

            return $this->private_avatar;
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
                $value = $zbp->GetPostType(0, 'author_template');
            }

            return $value;
        }
        if ($name == 'PassWord_MD5Path') {
            return $this->GetHashByMD5Path();
        }
        if ($name == 'IsGod') {
            $evn = GetVarsFromEnv('ZBP_PRESET_DISABLE_ROOT');
            if ($evn == true) {
                return false;
            }
            if ($this->private_isgod === true || $this->private_isgod === false) {
                return $this->private_isgod;
            } else {
                if ($this->Level != 1) {
                    $this->private_isgod = false;
                    return $this->private_isgod;
                }

                $sql = $zbp->db->sql->Select($zbp->table['Member'], '*', array(array('=', 'mem_Level', 1)), 'mem_ID ASC', 1, null);
                $am = $zbp->GetListType('Member', $sql);
                if ($am[0]->ID == $this->ID) {
                    $this->private_isgod = true;
                } else {
                    $this->private_isgod = false;
                }

                return $this->private_isgod;
            }
        }
        if ($name == 'AliasFirst') {
            if ($this->Alias) {
                return $this->Alias;
            } else {
                return $this->Name;
            }
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Get'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::__get($name);
    }

    /**
     * 静态方法，获取加盐及二次散列的,用于保存的最终密码
     *
     * @param string $ps   明文密码
     * @param string $guid 用户唯一码
     *
     * @return string
     */
    public static function GetPassWordByGuid($ps, $guid)
    {
        return md5(md5($ps) . $guid);
    }

    /**
     * 获取有期限的Token密码
     *
     * @param string $wt_id Token的ID
     * @param int    $day   时间，按天算 (1分钟就是1/24*60)
     *
     * @return string (sha1字串+unix时间)
     */
    public function GetHashByToken($wt_id = '', $day = 30)
    {
        global $zbp;
        $t = (intval($day * 24 * 3600) + time());

        return CreateWebToken($wt_id, $t, $zbp->guid, $this->ID, $this->Password);
    }

    /**
     * 获取加路径盐的Hash密码 (其实并没有用path，而是用zbp->guid替代了).
     *
     * @return string
     */
    public function GetHashByMD5Path()
    {
        global $zbp;

        return md5($this->Password . $zbp->guid);
    }

    /**
     * 保存用户数据.
     *
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
        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Del'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        return parent::Del();
    }

    /**
     * @param string $s
     * @param string $type
     *
     * @return null|string
     */
    public function Time($s = 'Y-m-d', $type = 'PostTime')
    {
        if ($s === 'Post') {
            $s = 'PostTime';
        } elseif ($s === 'Create') {
            $s = 'CreateTime';
        } elseif ($s === 'Update') {
            $s = 'UpdateTime';
        }
        if (func_num_args() == 2) {
            if ($type === 'Post') {
                $type = 'PostTime';
            } elseif ($type === 'Create') {
                $type = 'CreateTime';
            } elseif ($type === 'Update') {
                $type = 'UpdateTime';
            }
        }
        if (func_num_args() == 2 && !array_key_exists($type, $this->data) && array_key_exists($s, $this->data)) {
            list($type, $s) = array($s, $type);
        } elseif (func_num_args() == 1 && array_key_exists($s, $this->data)){
            list($type, $s) = array($s, 'Y-m-d');
        }
        if (array_key_exists($type, $this->data)) {
            return date($s, (int) $this->$type);
        } else {
            return date($s, (int) $this->PostTime);
        }
    }

}
