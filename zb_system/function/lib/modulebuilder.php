<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 模块创建类.
 */
class ModuleBuilder
{
    public static $List = []; //array('filename'=>,'function' => '', 'paramters' => '');

    //需要重建的module list
    private static $Ready = []; //'filename';

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
        self::$List[$modfilename]['parameters'] = [];
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
        $p = is_array($p) ? $p : [];
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
     * @param mixed $type
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function Catalog($type = 0)
    {
        global $zbp;

        $template = $zbp->GetTemplate();
        $tags = [];

        $tags['style'] = $zbp->option['ZC_MODULE_CATALOG_STYLE'];
        $tags['maxLi'] = $zbp->modulesbyfilename['catalog']->MaxLi;
        $tags['catalogs'] = $zbp->categoriesbyorder_type[$type];

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-catalog');

        $links = [];
        $catalogs = $zbp->categoriesbyorder_type[$type];
        foreach ($catalogs as $catalog) {
            $link = new StdClass();
            $link->href = $catalog->Url;
            $link->title = $catalog->Name;
            $link->content = $catalog->Name;
            $links[] = $link;
        }
        $zbp->modulesbyfilename['catalog']->Links = $links;

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
        $template = $zbp->GetTemplate();
        $tags = [];

        if ('' == $date) {
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
            ['log_ID', 'log_PostTime'],
            [
                ['=', 'log_Type', '0'],
                ['=', 'log_Status', '0'],
                ['BETWEEN', 'log_PostTime', $fdate, $ldate],
            ],
            ['log_PostTime' => 'ASC'],
            null,
            null,
        );
        $array = $zbp->db->Query($sql);
        $arraydate = [];
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
                $arraydate[$key] = [
                    'Date'  => $fullDate,
                    'Url'   => $url->Make(),
                    'Count' => 0,
                ];
            }
            ++$arraydate[$key]['Count'];
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
        $template = $zbp->GetTemplate();
        $tags = [];

        $i = $zbp->modulesbyfilename['comments']->MaxLi;
        if (0 == $i) {
            $i = 10;
        }
        $tags['maxLi'] = $i;
        $comments = $zbp->GetCommentList('*', [['=', 'comm_IsChecking', 0]], ['comm_ID' => 'DESC'], $i, null);
        $tags['comments'] = $comments;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-comments');

        $links = [];
        foreach ($comments as $comment) {
            $link = new StdClass();
            $link->href = $comment->Post->Url;
            $link->title = htmlspecialchars($comment->Author->StaticName . ' @ ' . $comment->Time());
            $link->content = FormatString($comment->Content, '[noenter]');
            $links[] = $link;
        }
        $zbp->modulesbyfilename['comments']->Links = $links;

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
        $template = $zbp->GetTemplate();
        $tags = [];

        $i = $zbp->modulesbyfilename['previous']->MaxLi;
        if (0 == $i) {
            $i = 10;
        }
        $tags['maxLi'] = $i;
        $articles = $zbp->GetArticleList('*', [['=', 'log_Status', 0]], ['log_PostTime' => 'DESC'], $i, null, false);
        $tags['articles'] = $articles;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-previous');

        $links = [];
        foreach ($articles as $article) {
            $link = new StdClass();
            $link->href = $article->Url;
            $link->title = $article->Title;
            $link->content = $article->Title;
            $links[] = $link;
        }
        $zbp->modulesbyfilename['previous']->Links = $links;

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
        $template = $zbp->GetTemplate();
        $tags = [];
        $urls = []; //array(url,name,count);

        $maxli = $zbp->modulesbyfilename['archives']->MaxLi;
        if ($maxli < 0) {
            return '';
        }

        $sql = $zbp->db->sql->Select($zbp->table['Post'], ['log_PostTime'], null, ['log_PostTime' => 'DESC'], [1], null);

        $array = $zbp->db->Query($sql);

        if (0 == count($array)) {
            return '';
        }

        $ldate = [date('Y', $array[0][$zbp->datainfo['Post']['PostTime'][0]]), date('m', $array[0][$zbp->datainfo['Post']['PostTime'][0]])];

        $sql = $zbp->db->sql->Select($zbp->table['Post'], ['log_PostTime'], null, ['log_PostTime' => 'ASC'], [1], null);

        $array = $zbp->db->Query($sql);

        if (0 == count($array)) {
            return '';
        }

        $fdate = [date('Y', $array[0][$zbp->datainfo['Post']['PostTime'][0]]), date('m', $array[0][$zbp->datainfo['Post']['PostTime'][0]])];

        $arraydate = [];

        for ($i = $fdate[0]; $i < ($ldate[0] + 1); ++$i) {
            for ($j = 1; $j < 13; ++$j) {
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
            $sql = $zbp->db->sql->Count($zbp->table['Post'], [['COUNT', '*', 'num']], [['=', 'log_Type', '0'], ['=', 'log_Status', '0'], ['BETWEEN', 'log_PostTime', $fdate, $ldate]]);
            $n = GetValueInArrayByCurrent($zbp->db->Query($sql), 'num');
            if ($n > 0) {
                //$urls[]=array($url->Make(),str_replace(array('%y%', '%m%'), array(date('Y', $fdate), date('n', $fdate)), $zbp->lang['msg']['year_month']),$n);
                $meta = new Metas();
                $meta->Url = $url->Make();
                $meta->Name = str_replace(['%y%', '%m%'], [date('Y', $fdate), date('n', $fdate)], $zbp->lang['msg']['year_month']);
                $meta->Count = $n;
                $urls[] = $meta;
                ++$i;
            }
        }

        $tags['urls'] = $urls;
        $tags['style'] = $zbp->option['ZC_MODULE_ARCHIVES_STYLE'];
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-archives');

        $links = [];
        foreach ($urls as $url) {
            $link = new StdClass();
            $link->href = $url['Url'];
            $link->title = $url['Name'];
            $link->content = $url['Name'];
            $link->data_count = $url['Count'];
            $links[] = $link;
        }
        $zbp->modulesbyfilename['archives']->Links = $links;

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
        $template = $zbp->GetTemplate();
        $tags = [];

        $s = $zbp->modulesbyfilename['navbar']->Content;

        $tags['content'] = $s;
        $tags['links'] = $zbp->modulesbyfilename['navbar']->Links;

        $template->SetTagsAll($tags);
        $ret = $template->Output('module-navbar');

        return $ret;
    }

    /**
     * 导出tags模块数据.
     *
     * @param mixed $type
     *
     * @throws Exception
     *
     * @return string 模块内容
     */
    public static function TagList($type = 0)
    {
        global $zbp;
        $template = $zbp->GetTemplate();
        $tags = [];
        $urls = []; //array(real tag);

        $i = $zbp->modulesbyfilename['tags']->MaxLi;
        if (0 == $i) {
            $i = 25;
        }

        $array = $zbp->GetTagList('*', [['=', 'tag_Type', $type]], ['tag_Count' => 'DESC'], $i, null);
        $array2 = [];
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

        $links = [];
        foreach ($urls as $tag) {
            $link = new StdClass();
            $link->href = $tag->Url;
            $link->title = $tag->Name;
            $link->content = $tag->Name;
            $link->data_count = $tag->Count;
            $links[] = $link;
        }
        $zbp->modulesbyfilename['tags']->Links = $links;

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
        $template = $zbp->GetTemplate();
        $tags = [];
        $authors = [];
        $level = $level || $zbp->actions['ArticleEdt'];

        $w = [];
        $w[] = ['<=', 'mem_Level', $level];

        $i = $zbp->modulesbyfilename['authors']->MaxLi;
        if (0 == $i) {
            $i = 10;
        }

        $array = $zbp->GetMemberList('*', $w, ['mem_ID' => 'ASC'], $i, null);

        foreach ($array as $member) {
            $m = $member->Cloned();
            $m->Guid = '';
            $m->Password = '';
            $authors[] = $m;
        }

        $tags['authors'] = $authors;
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-authors');

        $links = [];
        foreach ($authors as $author) {
            $link = new StdClass();
            $link->href = $author->Url;
            $link->title = $author->StaticName;
            $link->content = $author->StaticName;
            $link->data_count = $author->Articles;
            $link->data_count_articles = $author->Articles;
            $links[] = $link;
        }
        $zbp->modulesbyfilename['authors']->Links = $links;

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
    public static function Statistics($array = [])
    {
        global $zbp;
        $template = $zbp->GetTemplate();
        $tags = [];
        $allinfo = [];

        $all_artiles = 0;
        $all_pages = 0;
        $all_categorys = 0;
        $all_tags = 0;
        $all_views = 0;
        $all_comments = 0;

        if (0 == count($array)) {
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

        $allinfo['all_artiles'] = ['name' => $zbp->lang['msg']['all_artiles'], 'count' => $all_artiles];
        $allinfo['all_pages'] = ['name' => $zbp->lang['msg']['all_pages'], 'count' => $all_pages];
        $allinfo['all_categorys'] = ['name' => $zbp->lang['msg']['all_categorys'], 'count' => $all_categorys];
        $allinfo['all_tags'] = ['name' => $zbp->lang['msg']['all_tags'], 'count' => $all_tags];
        $allinfo['all_comments'] = ['name' => $zbp->lang['msg']['all_comments'], 'count' => $all_comments];
        if (!$zbp->option['ZC_VIEWNUMS_TURNOFF'] || $zbp->option['ZC_LARGE_DATA']) {
            $allinfo['all_views'] = ['name' => $zbp->lang['msg']['all_views'], 'count' => $all_views];
        }

        $tags['allinfo'] = $allinfo;
        $template->SetTagsAll($tags);
        $ret = $template->Output('module-statistics');

        $links = [];
        foreach ($allinfo as $info) {
            $link = new StdClass();
            $link->href = '';
            $link->title = $info['name'];
            $link->content = $info['name'] . ':' . $info['count'];
            $link->data_count = $info['count'];
            $links[] = $link;
        }
        $zbp->modulesbyfilename['statistics']->Links = $links;

        return $ret;
    }
}
