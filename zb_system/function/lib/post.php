<?php
/**
 * 文章类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Post 类库
 */
class Post extends Base
{

    private $_prev = '';
    private $_next = '';

    /**
     *
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Post'], $zbp->datainfo['Post'], __CLASS__);

        $this->Title = $zbp->lang['msg']['unnamed'];
        $this->PostTime = time();
    }

    /**
     * @param $method
     * @param $args
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
     * @return bool|string
     */
    public function Time($s = 'Y-m-d H:i:s')
    {
        return date($s, (int) $this->PostTime);
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
     * @return null|string
     */
    public function __set($name, $value)
    {
        global $zbp;
        switch ($name) {
            case 'Category':
            case 'Author':
            case 'TypeName':
            case 'Url':
            case 'Tags':
            case 'TagsName':
            case 'TagsCount':
            case 'CommentPostUrl':
            case 'Prev':
            case 'Next':
            case 'RelatedList':
                return null;
            break;
            case 'Template':
                if ($value == $zbp->GetPostType_Template($this->Type)) {
                    $value = '';
                }

                return $this->data[$name] = $value;
            break;
            case 'TopType':
                if ($value == 'global' || $value == 'category') {
                    $this->Metas->toptype = $value;
                } elseif ($value == '' || $value == null) {
                    $this->Metas->Del('toptype');
                } else {
                    $this->Metas->toptype = 'index';
                }

                return null;
            break;
            default:
                parent::__set($name, $value);
                break;
        }
    }

    /**
     * @param $name
     * @return array|int|mixed|null|string
     */
    public function __get($name)
    {
        global $zbp;
        switch ($name) {
            case 'Category':
                return $zbp->GetCategoryByID($this->CateID);
            break;
            case 'Author':
                return $zbp->GetMemberByID($this->AuthorID);
            break;
            case 'StatusName':
                return $zbp->lang['post_status_name'][$this->Status];
            break;
            case 'Url':
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Url'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this);
                    if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
                        return $fpreturn;
                    }
                }
                $u = new UrlRule($zbp->GetPostType_UrlRule($this->Type));
                $u->Rules['{%id%}'] = $this->ID;
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
            break;
            case 'Tags':
                return $zbp->LoadTagsByIDString($this->Tag);
            break;
            case 'TagsCount':
                return substr_count($this->Tag, '{');
            break;
            case 'TagsName':
                return $this->TagsToNameString();
            case 'Template':
                $value = $this->data[$name];
                if ($value == '') {
                    $value = GetValueInArray($this->Category->GetData(), 'LogTemplate');
                    if ($value == '') {
                        $value = $zbp->GetPostType_Template($this->Type);
                    }
                }

                return $value;
            case 'CommentPostUrl':
                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_CommentPostUrl'] as $fpname => &$fpsignal) {
                    $fpreturn = $fpname($this);
                    if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                        $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                        return $fpreturn;
                    }
                }
                $key = '&amp;key=' . $zbp->GetCmtKey($this->ID);

                return $zbp->host . 'zb_system/cmd.php?act=cmt&amp;postid=' . $this->ID . $key;
            break;
            case 'ValidCodeUrl':
                return $zbp->validcodeurl . '?id=cmt';
            break;
            case 'Prev':
                if ($this->_prev !== '') {
                    return $this->_prev;
                }

                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Prev'] as $fpname => &$fpsignal) {
                    $this->_prev = $fpname($this);
                    if ($this->_prev !== '') {
                        return $this->_prev;
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
                    $this->_prev = $articles[0];
                } else {
                    $this->_prev = null;
                }

                return $this->_prev;
            break;
            case 'Next':
                if ($this->_next !== '') {
                    return $this->_next;
                }

                foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Next'] as $fpname => &$fpsignal) {
                    $this->_prev = $fpname($this);
                    if ($this->_prev !== '') {
                        return $this->_prev;
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
                    $this->_next = $articles[0];
                } else {
                    $this->_next = null;
                }

                return $this->_next;
            break;
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
                $toptype = $this->Metas->toptype;
                if ($this->IsTop == true && $toptype == null) {
                    $toptype = 'index';
                }

                return $toptype;
            case 'TypeName':
                return $zbp->GetPostType_Name($this->Type);
            default:
                return parent::__get($name);
            break;
        }
    }

    /**
     * @return bool
     */
    public function Save()
    {
        global $zbp;
        if ($this->Type == ZC_POST_TYPE_ARTICLE) {
            if ($this->Template == GetValueInArray($this->Category->GetData(), 'LogTemplate')) {
                $this->data['Template'] = '';
            }
        }
        if ($this->Template == $zbp->GetPostType_Template($this->Type)) {
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
        global $zbp;
        if ($this->ID > 0) {
            unset($zbp->posts[$this->ID]);
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Post_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;
                return $fpreturn;
            }
        }

        return parent::Del();
    }
}
