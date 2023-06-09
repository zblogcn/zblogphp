<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 文章类.
 *
 * @property int|string ID 文章ID
 * @property string Title 文章标题
 * @property string Intro 文章摘要
 * @property string Content 文章内容
 * @property int Top
 * @property int Type 文章类型
 * @property string Template 文章模板
 * @property int|string AuthorID 文章作者ID
 * @property Member Author 文章作者类
 * @property int|string CateID 文章分类ID
 * @property Category Category 文章分类
 * @property int|string Status 文章状态
 * @property int PostTime 发布时间
 * @property int CreateTime 创建时间
 * @property int UpdateTime 更新时间
 * @property int IsTop 文章置顶状态
 * @property string Tag 文章标签
 * @property string Alias 文章别名
 * @property string Url 文章地址
 * @property bool IsLock 是否锁定
 * @property string TypeName 文章类型的具体信息
 * @property string StatusName 文章状态的详细信息
 * @property int|string CommNums 评论数量
 * @property int ImageCount 图片数量
 * @property array AllImages 所有图片
 */
abstract class Base__Post extends Base
{

    private $private_prev = '';

    private $private_next = '';

    protected $allImages;

    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Post'], $zbp->datainfo['Post'], __CLASS__);

        $this->Title = $zbp->lang['msg']['unnamed'];
        $this->PostTime = time();
        $this->CreateTime = $this->PostTime;
        $this->UpdateTime = $this->PostTime;
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Call'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $method, $args);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
    }

    /**
     * @param string $s
     * @param type|string 1.7.2后可以与$s前后调换
     *
     * @return null|string
     */
    public function Time($s = 'Y-m-d H:i:s', $type = 'PostTime')
    {
        //1.7.2改回了1.6的顺序, $type放在第2参数
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
            list($type, $s) = array($s, 'Y-m-d H:i:s');
        }
        if (array_key_exists($type, $this->data)) {
            return date($s, (int) $this->$type);
        } else {
            return date($s, (int) $this->PostTime);
        }
    }

    /**
     * @return array|int|mixed|null|string
     */
    public function TagsToNameString()
    {
        global $zbp;
        $s = $this->Tag;
        if ($s == '') {
            return '';
        }

        $s = str_replace('}{', '|', $s);
        $s = str_replace('{', '', $s);
        $s = str_replace('}', '', $s);
        $b = explode('|', $s);
        $b = array_unique($b);

        $a = $zbp->LoadTagsByIDString($this->Tag);
        $s = '';
        $c = array();
        foreach ($b as $key) {
            if (isset($zbp->tags[$key])) {
                $c[] = $zbp->tags[$key]->Name;
            }
        }
        if (!$c) {
            return '';
        }

        $s = implode(',', $c);

        return $s;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        global $zbp;
        switch ($name) {
            case 'Category':
            case 'Author':
            case 'StatusName':
            case 'Url':
            case 'Tags':
            case 'TagsName':
            case 'TagsCount':
            case 'FirstTag':
            case 'CommentPostUrl':
            case 'CommentPostKey':
            case 'ValidCodeUrl':
            case 'Prev':
            case 'Next':
            case 'RelatedList':
            case 'TypeName':
            case 'TypeActions':
            case 'PostDate':
            case 'CreateDate':
            case 'UpdateDate':
            case 'AliasFirst':
                return;
            case 'Template':
                if ($value == $zbp->GetPostType($this->Type, 'template')) {
                    $value = '';
                }
                $this->data[$name] = $value;

                return;
            case 'TopType':
                if ($value == 'global') {
                    $this->IsTop = 1;
                } elseif ($value == 'index') {
                    $this->IsTop = 2;
                } elseif ($value == 'categorys') {
                    $this->IsTop = 4;
                } elseif ($value == 'category') {
                    $this->IsTop = 8;
                } elseif ($value == '' || $value == null) {
                    $this->IsTop = 0;
                }

                return;
            default:
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Set'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this, $name, $value);
                }
                parent::__set($name, $value);
                break;
        }
    }

    /**
     * @param $name
     *
     * @return array|int|mixed|null|string
     */
    public function __get($name)
    {
        global $zbp;
        switch ($name) {
            case 'Category':
                return $zbp->GetCategoryByID($this->CateID);
            case 'Author':
                return $zbp->GetMemberByID($this->AuthorID);
            case 'StatusName':
                return $zbp->lang['post_status_name'][$this->Status];
            case 'Url':
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Url'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this);
                    if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                        $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                        return $fpreturn;
                    }
                }
                $routes = $zbp->GetPostType($this->Type, 'routes');
                $routename = 'post_' . $zbp->GetPostType($this->Type, 'name') . '_single';
                if (isset($routes[$routename]) && !is_null($zbp->GetRoute($routes[$routename]))) {
                    $u = new UrlRule($zbp->GetRoute($routes[$routename]));
                } else {
                    $u = new UrlRule($zbp->GetPostType($this->Type, 'single_urlrule'));
                }
                $u->Rules['{%id%}'] = $this->ID;
                $u->RulesObject = &$this;
                if ($this->Alias) {
                    $u->Rules['{%alias%}'] = $this->Alias;
                } else {
                    if ($zbp->option['ZC_POST_ALIAS_USE_ID_NOT_TITLE'] == false) {
                        $u->Rules['{%alias%}'] = rawurlencode($this->Title);
                    } else {
                        $u->Rules['{%alias%}'] = $this->ID;
                    }
                }
                    $u->Rules['{%year%}'] = $this->Time('Y');
                    $u->Rules['{%month%}'] = $this->Time('m');
                    $u->Rules['{%day%}'] = $this->Time('d');
                if ($this->Category->Alias) {
                    $u->Rules['{%category%}'] = $this->Category->Alias;
                } else {
                    $u->Rules['{%category%}'] = rawurlencode($this->Category->Name);
                }
                if ($this->Author->Alias) {
                    $u->Rules['{%author%}'] = $this->Author->Alias;
                } else {
                    $u->Rules['{%author%}'] = rawurlencode($this->Author->Name);
                }

                return $u->Make();
            case 'Tags':
                return $zbp->LoadTagsByIDString($this->Tag);
            case 'TagsCount':
                return substr_count($this->Tag, '{');
            case 'TagsName':
                return $this->TagsToNameString();
            case 'FirstTag':
                $array = $zbp->LoadTagsByIDString($this->Tag);
                if (count($array) == 0) {
                    return new Tag;
                } else {
                    return current($array);
                }
            case 'Template':
                $value = $this->data[$name];
                if ($value == '') {
                    $value = GetValueInArray($this->Category->GetData(), 'LogTemplate');
                    if ($value == '') {
                        $value = $zbp->GetPostType($this->Type, 'template');
                    }
                }
                return $value;
            case 'CommentPostKey':
                return $zbp->GetCmtKey($this->ID);
            case 'CommentPostUrl':
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_CommentPostUrl'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this);
                    if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                        $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                        return $fpreturn;
                    }
                }
                $key = '&amp;key=' . $this->CommentPostKey;

                return $zbp->host . 'zb_system/cmd.php?act=cmt&amp;postid=' . $this->ID . $key;
            case 'ValidCodeUrl':
                return $zbp->validcodeurl . '?id=cmt';
            case 'Prev':
                if ($this->private_prev !== '') {
                    return $this->private_prev;
                }

                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Prev'] as $fpname => &$fpsignal) {
                    $this->private_prev = $fpname($this);
                    if ($this->private_prev !== '') {
                        return $this->private_prev;
                    }
                }

                $articles = $zbp->GetPostList(
                    array('*'),
                    array(array('=', 'log_Type', 0), array('=', 'log_Status', 0), array('<', 'log_PostTime', $this->PostTime)),
                    array('log_PostTime' => 'DESC'),
                    array(1),
                    null
                );
                if (count($articles) == 1) {
                    $this->private_prev = $articles[0];
                } else {
                    $this->private_prev = null;
                }

                return $this->private_prev;
            case 'Next':
                if ($this->private_next !== '') {
                    return $this->private_next;
                }

                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Next'] as $fpname => &$fpsignal) {
                    $this->private_next = $fpname($this);
                    if ($this->private_next !== '') {
                        return $this->private_next;
                    }
                }

                $articles = $zbp->GetPostList(
                    array('*'),
                    array(array('=', 'log_Type', 0), array('=', 'log_Status', 0), array('>', 'log_PostTime', $this->PostTime)),
                    array('log_PostTime' => 'ASC'),
                    array(1),
                    null
                );
                if (count($articles) == 1) {
                    $this->private_next = $articles[0];
                } else {
                    $this->private_next = null;
                }

                return $this->private_next;
            case 'RelatedList':
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_RelatedList'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this);
                    if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                        $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                        return $fpreturn;
                    }
                }

                return GetList($zbp->option['ZC_RELATEDLIST_COUNT'], null, null, null, null, null, array('is_related' => $this->ID));
            case 'TopType':
                $toptype = '';
                if ($this->IsTop == 1) {
                    $toptype = 'global';
                }
                if ($this->IsTop == 2) {
                    $toptype = 'index';
                }
                if ($this->IsTop == 4) {
                    $toptype = 'categorys';
                }
                if ($this->IsTop == 8) {
                    $toptype = 'category';
                }
                return $toptype;
            case 'TypeName':
                return $zbp->GetPostType($this->Type, 'name');
            case 'TypeActions':
                return $zbp->GetPostType($this->Type, 'actions');
            case 'PostDate':
                return new ZbpDate($this->PostTime);
            case 'CreateDate':
                return new ZbpDate($this->CreateTime);
            case 'UpdateDate':
                return new ZbpDate($this->UpdateTime);
            case 'AliasFirst':
                if ($this->Alias) {
                    return $this->Alias;
                } else {
                    return $this->Title;
                }
            case 'AllImages':
                return is_array($this->allImages) ? $this->allImages : GetImagesFromHtml($this->Content);
            case 'ImageCount':
                return count($this->AllImages);
            default:
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Get'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this, $name);
                    if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                        $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                        return $fpreturn;
                    }
                }

                return parent::__get($name);
        }
    }

    /**
     * 获取缩略图.
     *
     * @param integer $width
     * @param integer $height
     * @param integer $count
     * @param boolean $clip
     * @return array
     */
    public function Thumbs($width = 200, $height = 150, $count = 1, $clip = true)
    {
        $all_images = $this->AllImages;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Thumbs'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $all_images, $width, $height, $count, $clip);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;
                return $fpreturn;
            }
        }

        return Thumb::Thumbs($all_images, $width, $height, $count, $clip);
    }

    /**
     * @return bool
     */
    public function Save()
    {
        if ($this->Type == ZC_POST_TYPE_ARTICLE) {
            if ($this->Template == GetValueInArray($this->Category->GetData(), 'LogTemplate')) {
                $this->data['Template'] = '';
            }
        }
        if ($this->Template == $this->GetType('template')) {
            $this->data['Template'] = '';
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Save'] as $fpname => &$fpsignal) {
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
        foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::Del();
    }

    /**
     * @return array|string
     */
    public function GetType($key)
    {
        global $zbp;
        return $zbp->GetPostType($this->Type, $key);
    }

}
