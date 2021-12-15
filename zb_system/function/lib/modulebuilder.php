<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 模块创建类.
 */
class ModuleBuilder
{

    //需要重建的module list
    private static $Ready = array(); //'filename';

    public static $List = array(); //array('filename'=>,'function' => '', 'paramters' => '');

    public static function Build()
    {
        global $zbp;
        foreach (self::$Ready as $m) {
            if (isset($zbp->modulesbyfilename[$m])) {
                $zbp->modulesbyfilename[$m]->Build();
                $zbp->modulesbyfilename[$m]->Save();
            }
        }
    }

    /**
     * 将模块注册进Ready重建列表.
     *
     * @param string $modfilename 模块名
     * @param string $userfunc    用户函数
     */
    public static function Reg($modfilename, $userfunc)
    {
        self::$List[$modfilename]['filename'] = $modfilename;
        self::$List[$modfilename]['function'] = $userfunc;
        self::$List[$modfilename]['parameters'] = array();
    }

    /**
     * 添加进Ready List模块.
     *
     * @param string $modfilename 模块名
     * @param null   $parameters  模块参数
     */
    public static function Add($modfilename, $parameters = null)
    {
        $p = func_get_args();
        self::$Ready[$modfilename] = $modfilename;
        array_shift($p);
        $p = is_array($p) ? $p : array();
        self::$List[$modfilename]['parameters'] = $p;
    }

    /**
     * 删除进Ready List模块.
     *
     * @param string $modfilename 模块名
     */
    public static function Del($modfilename)
    {
        unset(self::$Ready[$modfilename]);
    }

    /**
     * 导出网站分类模块数据.
     *
     * @throws Exception
     *
     * @return string 模块内容
     *
     *
     */
    public static function Catalog($type = 0)
    {
        global $zbp;

        $template = $zbp->template;
        $tags = array();

        $tags['style'] = $zbp->option['ZC_MODULE_CATALOG_STYLE'];
        $tags['maxLi'] = $zbp->modulesbyfilename['catalog']->MaxLi;
        $tags['catalogs'] = $zbp->categoriesbyorder_type[$type];

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-catalog');

        return $ret;
    }

    /**
     * 导出日历模块数据.
     *
     * @param string $date 日期
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Calendar($date = '')
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();

        if ($date == '') {
            $date = date('Y-m', time());
        }
        $tags['date'] = $date;

        $routes = $zbp->GetPostType(0, 'routes');
        if (isset($routes['post_' . $zbp->GetPostType(0, 'name') . '_list_date'])) {
            $url = new UrlRule($zbp->GetRoute($routes['post_' . $zbp->GetPostType(0, 'name') . '_list_date']));
        } else {
            $url = new UrlRule($zbp->GetPostType(0, 'list_date_urlrule'));
        }
        $url->Rules['{%day%}'] = 1;

        $value = strtotime('-1 month', strtotime($date));
        $vdate = new ZbpDate($value);
        $tags['prevMonth'] = date('n', $value);
        $tags['prevYear'] = date('Y', $value);
        $url->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_RULE'], $value);
        $url->Rules['{%year%}'] = $tags['prevYear'];
        $url->Rules['{%month%}'] = $tags['prevMonth'];
        $url->RulesObject = $vdate;
        $tags['prevMonthUrl'] = $url->Make();

        $value = strtotime($date);
        $vdate = new ZbpDate($value);
        $tags['nowMonth'] = date('n', $value);
        $tags['nowYear'] = date('Y', $value);
        $url->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_RULE'], $value);
        $url->Rules['{%year%}'] = $tags['nowYear'];
        $url->Rules['{%month%}'] = $tags['nowMonth'];
        $url->RulesObject = $vdate;
        $tags['nowMonthUrl'] = $url->Make();

        $value = strtotime('+1 month', strtotime($date));
        $vdate = new ZbpDate($value);
        $tags['nextMonth'] = date('n', $value);
        $tags['nextYear'] = date('Y', $value);
        $url->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_RULE'], $value);
        $url->Rules['{%year%}'] = $tags['nextYear'];
        $url->Rules['{%month%}'] = $tags['nextMonth'];
        $url->RulesObject = $vdate;
        $tags['nextMonthUrl'] = $url->Make();

        $fdate = strtotime($date);
        $ldate = (strtotime(date('Y-m-t', strtotime($date))) + 60 * 60 * 24);
        $sql = $zbp->db->sql->Select(
            $zbp->table['Post'],
            array('log_ID', 'log_PostTime'),
            array(
                array('=', 'log_Type', '0'),
                array('=', 'log_Status', '0'),
                array('BETWEEN', 'log_PostTime', $fdate, $ldate),
            ),
            array('log_PostTime' => 'ASC'),
            null,
            null
        );
        $array = $zbp->db->Query($sql);
        $arraydate = array();
        foreach ($array as $value) {
            $key = date('j', $value[$zbp->datainfo['Post']['PostTime'][0]]);
            if (!isset($arraydate[$key])) {
                $fullDate = $tags['nowYear'] . '-' . $tags['nowMonth'] . '-' . $key;
                $vdate = new ZbpDate(strtotime($fullDate));
                $url->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_WITHDAY_RULE'], strtotime($fullDate));
                $url->Rules['{%year%}'] = $tags['nowYear'];
                $url->Rules['{%month%}'] = $tags['nowMonth'];
                $url->Rules['{%day%}'] = $key;
                $url->RulesObject = $vdate;
                $arraydate[$key] = array(
                    'Date'  => $fullDate,
                    'Url'   => $url->Make(),
                    'Count' => 0,
                );
            }
            $arraydate[$key]['Count']++;
        }
        $tags['arraydate'] = $arraydate;
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-calendar');

        return $ret;
    }

    /**
     * 导出最新留言模块数据.
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Comments()
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();

        $i = $zbp->modulesbyfilename['comments']->MaxLi;
        if ($i == 0) {
            $i = 10;
        }
        $tags['maxLi'] = $i;
        $comments = $zbp->GetCommentList('*', array(array('=', 'comm_IsChecking', 0)), array('comm_ID' => 'DESC'), $i, null);
        $tags['comments'] = $comments;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-comments');

        return $ret;
    }

    /**
     * 导出最近发表文章模块数据.
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function LatestArticles()
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();

        $i = $zbp->modulesbyfilename['previous']->MaxLi;
        if ($i == 0) {
            $i = 10;
        }
        $tags['maxLi'] = $i;
        $articles = $zbp->GetArticleList('*', array(array('=', 'log_Status', 0)), array('log_PostTime' => 'DESC'), $i, null, false);
        $tags['articles'] = $articles;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-previous');

        return $ret;
    }

    /**
     * 导出文章归档模块数据.
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Archives()
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();
        $urls = array(); //array(url,name,count);

        $maxli = $zbp->modulesbyfilename['archives']->MaxLi;
        if ($maxli < 0) {
            return '';
        }

        $sql = $zbp->db->sql->Select($zbp->table['Post'], array('log_PostTime'), null, array('log_PostTime' => 'DESC'), array(1), null);

        $array = $zbp->db->Query($sql);

        if (count($array) == 0) {
            return '';
        }

        $ldate = array(date('Y', $array[0][$zbp->datainfo['Post']['PostTime'][0]]), date('m', $array[0][$zbp->datainfo['Post']['PostTime'][0]]));

        $sql = $zbp->db->sql->Select($zbp->table['Post'], array('log_PostTime'), null, array('log_PostTime' => 'ASC'), array(1), null);

        $array = $zbp->db->Query($sql);

        if (count($array) == 0) {
            return '';
        }

        $fdate = array(date('Y', $array[0][$zbp->datainfo['Post']['PostTime'][0]]), date('m', $array[0][$zbp->datainfo['Post']['PostTime'][0]]));

        $arraydate = array();

        for ($i = $fdate[0]; $i < ($ldate[0] + 1); $i++) {
            for ($j = 1; $j < 13; $j++) {
                $arraydate[] = strtotime($i . '-' . $j);
            }
        }

        foreach ($arraydate as $key => $value) {
            if (($value - strtotime($ldate[0] . '-' . $ldate[1])) > 0) {
                unset($arraydate[$key]);
            }

            if (($value - strtotime($fdate[0] . '-' . $fdate[1])) < 0) {
                unset($arraydate[$key]);
            }
        }

        $arraydate = array_reverse($arraydate);
        $s = '';
        $i = 0;

        foreach ($arraydate as $key => $value) {
            if ($i >= $maxli && $maxli > 0) {
                break;
            }

            $routes = $zbp->GetPostType(0, 'routes');
            if (isset($routes['post_' . $zbp->GetPostType(0, 'name') . '_list_date'])) {
                $url = new UrlRule($zbp->GetRoute($routes['post_' . $zbp->GetPostType(0, 'name') . '_list_date']));
            } else {
                $url = new UrlRule($zbp->GetPostType(0, 'list_date_urlrule'));
            }
            $url->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_RULE'], $value);
            $url->Rules['{%year%}'] = date('Y', $value);
            $url->Rules['{%month%}'] = date('n', $value);
            $url->Rules['{%day%}'] = 1;
            $url->RulesObject = new ZbpDate($value);

            $fdate = $value;
            $ldate = (strtotime(date('Y-m-t', $value)) + 60 * 60 * 24);
            $sql = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', '0'), array('=', 'log_Status', '0'), array('BETWEEN', 'log_PostTime', $fdate, $ldate)));
            $n = GetValueInArrayByCurrent($zbp->db->Query($sql), 'num');
            if ($n > 0) {
                //$urls[]=array($url->Make(),str_replace(array('%y%', '%m%'), array(date('Y', $fdate), date('n', $fdate)), $zbp->lang['msg']['year_month']),$n);
                $meta = new Metas();
                $meta->Url = $url->Make();
                $meta->Name = str_replace(array('%y%', '%m%'), array(date('Y', $fdate), date('n', $fdate)), $zbp->lang['msg']['year_month']);
                $meta->Count = $n;
                $urls[] = $meta;
                $i++;
            }
        }

        $tags['urls'] = $urls;
        $tags['style'] = $zbp->option['ZC_MODULE_ARCHIVES_STYLE'];
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-archives');

        return $ret;
    }

    /**
     * 导出导航模块数据.
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Navbar()
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();

        $s = $zbp->modulesbyfilename['navbar']->Content;

        $a = array();
        preg_match_all('/<li id="navbar-(page|category|tag)-(\d+)">/', $s, $a);

        $b = $a[1];
        $c = $a[2];
        foreach ($b as $key => $value) {
            if ($b[$key] == 'page') {
                $type = 'page';
                $id = $c[$key];
                $o = $zbp->GetPostByID($id);
                $url = $o->Url;
                $name = $o->Title;

                $a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
                $s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/a><\/li>/', $a, $s);
            }
            if ($b[$key] == 'category') {
                $type = 'category';
                $id = $c[$key];
                $o = $zbp->GetCategoryByID($id);
                $url = $o->Url;
                $name = $o->Name;

                $a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
                $s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/a><\/li>/', $a, $s);
            }
            if ($b[$key] == 'tag') {
                $type = 'tag';
                $id = $c[$key];
                $o = $zbp->GetTagByID($id);
                $url = $o->Url;
                $name = $o->Name;

                $a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
                $s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/a><\/li>/', $a, $s);
            }
        }

        $tags['content'] = $s;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-navbar');

        return $ret;
    }

    /**
     * 导出tags模块数据.
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function TagList($type = 0)
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();
        $urls = array(); //array(real tag);

        $i = $zbp->modulesbyfilename['tags']->MaxLi;
        if ($i == 0) {
            $i = 25;
        }

        $array = $zbp->GetTagList('*', array(array('=', 'tag_Type', $type)), array('tag_Count' => 'DESC'), $i, null);
        $array2 = array();
        foreach ($array as $tag) {
            $array2[$tag->ID] = $tag;
        }
        ksort($array2);

        foreach ($array2 as $tag) {
            $urls[] = $tag;
        }

        $tags['tags'] = $urls;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-tags');

        return $ret;
    }

    /**
     * 导出用户列表模块数据.
     *
     * @param int $level 要导出的用户最低等级，默认为4（即协作者）
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Authors($level = 4)
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();
        $authors = array();
        $level = $level || $zbp->actions['ArticleEdt'];

        $w = array();
        $w[] = array('<=', 'mem_Level', $level);

        $i = $zbp->modulesbyfilename['authors']->MaxLi;
        if ($i == 0) {
            $i = 10;
        }

        $array = $zbp->GetMemberList('*', $w, array('mem_ID' => 'ASC'), $i, null);

        foreach ($array as $member) {
            $m = $member->Cloned();
            $m->Guid = '';
            $m->Password = '';
            $authors[] = $m;
        }

        $tags['authors'] = $authors;
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-authors');

        return $ret;
    }

    /**
     * 导出网站统计模块数据.
     *
     * @param array $array
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Statistics($array = array())
    {
        global $zbp;
        $template = $zbp->template;
        $tags = array();
        $allinfo = array();

        $all_artiles = 0;
        $all_pages = 0;
        $all_categorys = 0;
        $all_tags = 0;
        $all_views = 0;
        $all_comments = 0;

        if (count($array) == 0) {
            return $zbp->modulesbyfilename['statistics']->Content;
        }

        if (isset($array[0])) {
            $all_artiles = $array[0];
        }

        if (isset($array[1])) {
            $all_pages = $array[1];
        }

        if (isset($array[2])) {
            $all_categorys = $array[2];
        }

        if (isset($array[3])) {
            $all_tags = $array[3];
        }

        if (isset($array[4])) {
            $all_views = $array[4];
        }

        if (isset($array[5])) {
            $all_comments = $array[5];
        }

        $allinfo['all_artiles'] = array('name' => $zbp->lang['msg']['all_artiles'], 'count' => $all_artiles);
        $allinfo['all_pages'] = array('name' => $zbp->lang['msg']['all_pages'], 'count' => $all_pages);
        $allinfo['all_categorys'] = array('name' => $zbp->lang['msg']['all_categorys'], 'count' => $all_categorys);
        $allinfo['all_tags'] = array('name' => $zbp->lang['msg']['all_tags'], 'count' => $all_tags);
        $allinfo['all_comments'] = array('name' => $zbp->lang['msg']['all_comments'], 'count' => $all_comments);
        if (!$zbp->option['ZC_VIEWNUMS_TURNOFF'] || $zbp->option['ZC_LARGE_DATA']) {
            $allinfo['all_views'] = array('name' => $zbp->lang['msg']['all_views'], 'count' => $all_views);
        }

        $zbp->modulesbyfilename['statistics']->Type = "ul";

        $tags['allinfo'] = $allinfo;
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-statistics');

        return $ret;
    }

}
