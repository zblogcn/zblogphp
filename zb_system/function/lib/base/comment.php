<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 评论类.
 *
 * @property string Name
 * @property int|string AuthorID
 * @property string HomePage
 * @property string Email
 * @property int|string RootID
 * @property int|string ParentID
 * @property int|string LogID
 * @property bool IsChecking 审核状态
 * @property int|string Level 评论层级
 * @property int PostTime
 * @property Comment[] Comments 子评论
 * @property string Content
 */
abstract class Base__Comment extends Base
{

    /**
     * @var bool 是否丢弃，如通过插件等判断为垃圾评论则标记为true
     */
    public $IsThrow = false;

    /**
     * @var int 评论层号
     */
    public $FloorID = 0;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Comment'], $zbp->datainfo['Comment'], __CLASS__);
    }

    /**
     * 魔术方法：重载，可通过接口Filter_Plugin_Comment_Call添加自定义函数.
     *
     * @param string $method 方法
     * @param mixed  $args   参数
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Comment_Call'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $method, $args);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
    }

    /**
     * 获取评论楼号.
     *
     * @param int $parentid 父评论ID
     *
     * @return array|int|mixed
     */
    public static function GetRootID($parentid)
    {
        global $zbp;
        if ($parentid == 0) {
            return 0;
        }

        $c = $zbp->GetCommentByID($parentid);
        if ($c->RootID == 0) {
            return $c->ID;
        } else {
            return $c->RootID;
        }
    }

    /**
     * 评论时间.
     *
     * @param string $s 时间格式
     *
     * @return bool|string
     */
    public function Time($s = 'Y-m-d H:i:s')
    {
        return date($s, (int) $this->PostTime);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (in_array($name, array('Author', 'Comments', 'Level', 'Post', 'Parent'))) {
            return;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Comment_Set'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     *
     * @return array|int|Member|mixed
     */
    public function __get($name)
    {
        global $zbp;
        if ($name === 'Author') {
            $m = $zbp->GetMemberByID($this->AuthorID);
            if ($m->ID == null) {
                $m->Name = $this->Name;
                $m->Alias = $this->Name;
                $m->Email = $this->Email;
                $m->HomePage = $this->HomePage;
            }

            return $m;
        } elseif ($name === 'Comments') {
            //此方法并不是从数据库中读取数据而是从缓存中读取，所以可能不准确
            $array = array();
            foreach ($zbp->comments as $comment) {
                if ($comment->ParentID == $this->ID) {
                    $array[] = &$zbp->comments[$comment->ID];
                }
            }

            return $array;
        } elseif ($name === 'Level') {
            return $this->GetDeep($this);
        } elseif ($name === 'Post') {
            return $zbp->GetPostByID($this->LogID);
        } elseif ($name === 'Parent') {
            if ($this->ParentID == 0) {
                return;
            } else {
                return $zbp->GetCommentByID($this->ParentID);
            }
        } else {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Comment_Get'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($this, $name);
                if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                    return $fpreturn;
                }
            }
        }

        return parent::__get($name);
    }

    /**
     * 保存评论数据.
     *
     * @return bool
     */
    public function Save()
    {
        global $zbp;
        foreach ($GLOBALS['hooks']['Filter_Plugin_Comment_Save'] as $fpname => &$fpsignal) {
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
        foreach ($GLOBALS['hooks']['Filter_Plugin_Comment_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::Del();
    }

    /**
     * 得到评论深度.
     *
     * @param object $object
     * @param int    $deep
     *
     * @return int 评论深度
     */
    private function GetDeep(&$object, $deep = 0)
    {
        global $zbp;
        if ($object->ParentID == 0 || $object->ParentID == $object->ID) {
            return $deep;
        }
        $parentComment = $zbp->GetCommentByID($object->ParentID);

        return $this->GetDeep($parentComment, ($deep + 1));
    }

}
