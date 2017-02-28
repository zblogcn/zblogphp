<?php
/**
 * 评论类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Comment 类库
 */
class Comment extends Base
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
     * 构造函数
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Comment'], $zbp->datainfo['Comment'], __CLASS__);
    }

    /**
     * 魔术方法：重载，可通过接口Filter_Plugin_Comment_Call添加自定义函数
     * @param string $method 方法
     * @param mixed $args 参数
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
     * 获取评论楼号
     * @param int $parentid 父评论ID
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
     * 评论时间
     * @param string $s 时间格式
     * @return bool|string
     */
    public function Time($s = 'Y-m-d H:i:s')
    {
        return date($s, (int) $this->PostTime);
    }

    /**
     * @param $name
     * @param $value
     * @return null
     */
    public function __set($name, $value)
    {
        global $zbp;
        if ($name == 'Author') {
            return null;
        }
        if ($name == 'Comments') {
            return null;
        }
        if ($name == 'Level') {
            return null;
        }
        if ($name == 'Post') {
            return null;
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     * @return array|int|Member|mixed
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'Author') {
            $m = $zbp->GetMemberByID($this->AuthorID);
            if ($m->ID == 0) {
                $m->Name = $this->Name;
                $m->Alias = $this->Name;
                $m->Email = $this->Email;
                $m->HomePage = $this->HomePage;
            }

            return $m;
        }
        if ($name == 'Comments') {
            $array = array();
            foreach ($zbp->comments as $comment) {
                if ($comment->ParentID == $this->ID) {
                    $array[] = &$zbp->comments[$comment->ID];
                }
            }

            return $array;
        }
        if ($name == 'Level') {
            return $this->GetDeep($this);
        }
        if ($name == 'Post') {
            $p = $zbp->GetPostByID($this->LogID);

            return $p;
        }

        return parent::__get($name);
    }

    /**
     * 保存评论数据
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
        global $zbp;
        if ($this->ID >0) {
            unset($zbp->comments[$this->ID]);
        }

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
     * 得到评论深度
     * @param object $object
     * @return int 评论深度
     */
    private function GetDeep(&$object, $deep = 0)
    {
        global $zbp;
        if ($object->ParentID == 0 || $object->ParentID == $object->ID) {
            return $deep;
        }
        $parentComment = $zbp->GetCommentByID($object->ParentID);

        return $this->GetDeep($parentComment, $deep + 1);
    }
}
