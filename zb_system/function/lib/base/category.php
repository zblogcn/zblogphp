<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 文章分类类.
 *
 * @property int|string ID
 * @property string Name 分类名
 * @property string Alias 别名
 * @property int|string Count 分类下文章数量
 * @property int|string RootID 祖先分类ID
 * @property int|string ParentID 父分类ID
 * @property string Symbol 用于后台分类管理的“层次标识符”，无用处，待改名
 * @property int|string Level 分类层级
 * @property string Template 分类模板
 * @property string LogTemplate 分类下文章模板
 * @property string Url
 * @property int|string Order 分类顺序
 * @property string SymbolName 层次标识符+名字
 * @property int AllCount 本分类及子孙分类下所有文章数量
 */
abstract class Base__Category extends Base
{

    /**
     * @var array 下层分类
     */
    public $SubCategories = array(); //子分类

    /**
     * @deprecated
     *
     * @var array|null
     */
    public $SubCategorys = null; // 拼写错误，保持兼容

    public $ChildrenCategories = array(); //子孙分类
    //private $priChildrenCategories = null; //私有的子孙分类

    /**
     * 构造函数.
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Category'], $zbp->datainfo['Category'], __CLASS__);

        $this->SubCategorys = &$this->SubCategories;
        $this->Name = $zbp->lang['msg']['unnamed'];

        $this->CreateTime = time();
        $this->UpdateTime = $this->CreateTime;
    }

    /**
     * 魔术方法：重载，可通过接口Filter_Plugin_Category_Call添加自定义函数.
     *
     * @api Filter_Plugin_Category_Call
     *
     * @param string $method 方法
     * @param mixed  $args   参数
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Call'] as $fpname => &$fpsignal) {
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
        if (in_array($name, array('Url', 'Symbol', 'Level', 'SymbolName', 'Parent', 'AliasFirst'))) {
            return;
        } elseif ($name == 'Template') {
            if ($value == $zbp->GetPostType($this->Type, 'category_template')) {
                $value = '';
            }
            $this->data[$name] = $value;

            return;
        }
        if ($name == 'LogTemplate') {
            if ($value == $zbp->GetPostType($this->Type, 'template')) {
                $value = '';
            }
            $this->data[$name] = $value;

            return;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Set'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     *
     * @return int|mixed|null|string
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'Url') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Url'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($this);
                if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                    return $fpreturn;
                }
            }
            $backAttr = $zbp->option['ZC_ALIAS_BACK_ATTR'];

            $routes = $zbp->GetPostType($this->Type, 'routes');
            $routename = 'post_' . $zbp->GetPostType($this->Type, 'name') . '_list_category';
            if (isset($routes[$routename]) && !is_null($zbp->GetRoute($routes[$routename]))) {
                $u = new UrlRule($zbp->GetRoute($routes[$routename]));
            } else {
                $u = new UrlRule($zbp->GetPostType($this->Type, 'list_category_urlrule'));
            }
            $u->RulesObject = &$this;
            $u->Rules['{%id%}'] = $this->ID;
            $u->Rules['{%alias%}'] = rawurlencode_without_backslash($this->Alias == '' ? $this->$backAttr : $this->Alias);

            return $u->Make();
        }
        if ($name == 'Symbol') {
            if ($this->ParentID == 0) {
                return '';
            } else {
                $l = $this->Level;

                return str_repeat('&nbsp;', ($l * 2 - 1)) . '└';
            }
        }
        if ($name == 'Level') {
            return $this->GetDeep($this);
        }
        if ($name == 'SymbolName') {
            return $this->Symbol . htmlspecialchars($this->Name);
        }
        if ($name == 'Parent') {
            if ($this->ParentID == 0) {
                return;
            } else {
                return $zbp->categories_all[$this->ParentID];
            }
        }
        if ($name == 'Root') {
            if ($this->RootID == 0) {
                return;
            } else {
                return $zbp->categories_all[$this->RootID];
            }
        }
        if ($name == 'Template') {
            $value = $this->data[$name];
            if ($value == '') {
                $value = $zbp->GetPostType($this->Type, 'category_template');
            }

            return $value;
        }
        if ($name == 'LogTemplate') {
            $value = $this->data[$name];
            if ($value == '') {
                $value = $zbp->GetPostType($this->Type, 'template');
            }

            return $value;
        }
        if ($name == 'AllCount') {
            $i = $this->Count;
            foreach ($this->ChildrenCategories as $c) {
                $i += $c->Count;
            }
            return $i;
        }
        if ($name == 'AliasFirst') {
            if ($this->Alias) {
                return $this->Alias;
            } else {
                return $this->Name;
            }
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Get'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::__get($name);
    }

    /**
     * 保存分类数据.
     *
     * @return bool
     */
    public function Save()
    {
        global $zbp;
        if ($this->Template == $zbp->GetPostType($this->Type, 'category_template')) {
            $this->data['Template'] = '';
        }

        if ($this->LogTemplate == $zbp->GetPostType($this->Type, 'template')) {
            $this->data['LogTemplate'] = '';
        }

        $this->RootID = (int) $this->GetRoot($this->ParentID);

        foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Save'] as $fpname => &$fpsignal) {
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
        foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::Del();
    }

    /**
     * 得到分类深度.
     *
     * @param object $object
     * @param int    $deep
     *
     * @return int 分类深度
     */
    private function GetDeep(&$object, $deep = 0)
    {
        global $zbp;
        if ($object->ParentID == 0) {
            return $deep;
        } elseif (!isset($zbp->categories_all[$object->ParentID])) {
            return 0;
        } else {
            return $this->GetDeep($zbp->categories_all[$object->ParentID], ($deep + 1));
        }
    }

    /**
     * 得到分类RootID.
     *
     * @param int 父分类ID
     *
     * @return int 祖分类ID
     */
    private function GetRoot($parentid)
    {
        global $zbp;
        if ($parentid == 0) {
            return 0;
        }
        if (isset($zbp->categories_all[$parentid])) {
            if ($zbp->categories_all[$parentid]->ParentID > 0) {
                return $this->GetRoot($zbp->categories_all[$parentid]->ParentID);
            }

            return $parentid;
        } else {
            return 0;
        }
    }

    /**
     * 查找父祖分类并写入$parents
     */
    private function FindParents($parentid, &$parents)
    {
        global $zbp;
        if ($parentid == 0) {
            return 0;
        }
        if (isset($zbp->categories_all[$parentid])) {
            $parents[] = $zbp->categories_all[$parentid];
            if ($zbp->categories_all[$parentid]->ParentID > 0) {
                return $this->FindParents($zbp->categories_all[$parentid]->ParentID, $parents);
            }

            return $parentid;
        } else {
            return 0;
        }
    }

    /**
     * 判断$id是否是分类的父祖辈分类
     */
    public function IsParents($id)
    {
        $parents = array();
        $this->FindParents($this->ParentID, $parents);
        foreach ($parents as $c) {
            if ($c->ID == $id) {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断$id是否是分类的父分类
     */
    public function IsParent($id)
    {
        return $this->ParentID == $id;
    }

    /**
     * 判断$id是否是分类的子孙分类
     */
    public function IsGrandChildren($id)
    {
        foreach ($this->ChildrenCategories as $c) {
            if ($c->ID == $id) {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断$id是否是分类的子分类
     */
    public function IsChildren($id)
    {
        foreach ($this->SubCategories as $c) {
            if ($c->ID == $id) {
                return true;
            }
        }
        return false;
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
