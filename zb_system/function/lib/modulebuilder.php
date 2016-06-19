<?php
// 兼容代码
function BuildModule_catalog() {
    return ModuleBuilder::Catalog();
}
function BuildModule_calendar($date = '') {
    return ModuleBuilder::Calendar($date);
}
function BuildModule_comments() {
    return ModuleBuilder::Comments();
}
function BuildModule_previous() {
    return ModuleBuilder::LatestArticles();
}
function BuildModule_archives() {
    return ModuleBuilder::Archives();
}
function BuildModule_navbar() {
    return ModuleBuilder::Navbar();
}
function BuildModule_tags() {
    return ModuleBuilder::TagList();
}
function BuildModule_authors($level = 4) {
    return ModuleBuilder::AuthorList($level);
}
function BuildModule_statistics($array = array()) {
    return ModuleBuilder::Statistics($array);
}

class ModuleBuilder {
        
    /**
     * 导出网站分类模块数据
     * @return string 模块内容
     * @todo 必须重写
     */
    public static function Catalog() {
        global $zbp;
        $s = '';

        $i = $zbp->modulesbyfilename['catalog']->MaxLi;
        $j = 0;
        if ($zbp->option['ZC_MODULE_CATALOG_STYLE'] == '2') {
            foreach ($zbp->categorysbyorder as $key => $value) {
                if ($value->Level == 0) {
                    $s .= '<li class="li-cate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li>';
                }
                $j += 1;
                if ($i != 0 && $j >= $i) {
                    break;
                }

            }

            for ($i = 1; $i <= 3; $i++) {
                // 此处逻辑仍要继续修改
                foreach ($zbp->categorysbyorder as $key => $value) {
                    if ($value->Level == $i) {
                        $s = str_replace('<!--' . $value->ParentID . 'end-->', '<li class="li-subcate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li><!--' . $value->ParentID . 'end-->', $s);
                    }
                }
            }

            foreach ($zbp->categorysbyorder as $key => $value) {
                $s = str_replace('<!--' . $value->ID . 'begin--><!--' . $value->ID . 'end-->', '', $s);
            }
            foreach ($zbp->categorysbyorder as $key => $value) {
                $s = str_replace('<!--' . $value->ID . 'begin-->', '<ul class="ul-subcates">', $s);
                $s = str_replace('<!--' . $value->ID . 'end-->', '</ul>', $s);
            }

        } elseif ($zbp->option['ZC_MODULE_CATALOG_STYLE'] == '1') {
            foreach ($zbp->categorysbyorder as $key => $value) {
                $s .= '<li>' . $value->Symbol . '<a href="' . $value->Url . '">' . $value->Name . '</a></li>';
                $j += 1;
                if ($i != 0 && $j >= $i) {
                    break;
                }

            }
        } else {
            foreach ($zbp->categorysbyorder as $key => $value) {
                $s .= '<li><a href="' . $value->Url . '">' . $value->Name . '</a></li>';
                $j += 1;
                if ($i != 0 && $j >= $i) {
                    break;
                }

            }
        }

        return $s;
    }

    /**
     * 导出日历模块数据
     * @param string $date 日期
     * @return string 模块内容
     */
    public static function Calendar($date = '') {
        global $zbp;

        if ($date == '') {
            $date = date('Y-m', time());
        }

        $s = '<table id="tbCalendar"><caption>';

        $url = new UrlRule($zbp->option['ZC_DATE_REGEX']);
        $value = strtotime('-1 month', strtotime($date));
        $url->Rules['{%date%}'] = date('Y-n', $value);
        $url->Rules['{%year%}'] = date('Y', $value);
        $url->Rules['{%month%}'] = date('n', $value);

        $url->Rules['{%day%}'] = 1;
        $s .= '<a href="' . $url->Make() . '">«</a>';

        $value = strtotime($date);
        $url->Rules['{%date%}'] = date('Y-n', $value);
        $url->Rules['{%year%}'] = date('Y', $value);
        $url->Rules['{%month%}'] = date('n', $value);
        $s .= '&nbsp;&nbsp;&nbsp;<a href="' . $url->Make() . '">' . str_replace(array('%y%', '%m%'), array(date('Y', $value), date('n', $value)), $zbp->lang['msg']['year_month']) . '</a>&nbsp;&nbsp;&nbsp;';

        $value = strtotime('+1 month', strtotime($date));
        $url->Rules['{%date%}'] = date('Y-n', $value);
        $url->Rules['{%year%}'] = date('Y', $value);
        $url->Rules['{%month%}'] = date('n', $value);
        $s .= '<a href="' . $url->Make() . '">»</a></caption>';

        $s .= '<thead><tr>';
        for ($i = 1; $i < 8; $i++) {
            $s .= '<th title="' . $zbp->lang['week'][$i] . '" scope="col"><small>' . $zbp->lang['week_abbr'][$i] . '</small></th>';
        }

        $s .= '</tr></thead>';
        $s .= '<tbody>';
        $s .= '<tr>';

        $a = 1;
        $b = date('t', strtotime($date));
        $j = date('N', strtotime($date . '-1'));
        $k = 7 - date('N', strtotime($date . '-' . date('t', strtotime($date))));

        if ($j > 1) {
            $s .= '<td class="pad" colspan="' . ($j - 1) . '"> </td>';
        } elseif ($j = 1) {
            $s .= '';
        }

        $l = $j - 1;
        for ($i = $a; $i < $b + 1; $i++) {
            $s .= '<td>' . $i . '</td>';

            $l = $l + 1;
            if ($l % 7 == 0) {
                $s .= '</tr><tr>';
            }

        }

        if ($k > 1) {
            $s .= '<td class="pad" colspan="' . ($k) . '"> </td>';
        } elseif ($k = 1) {
            $s .= '';
        }

        $s .= '</tr></tbody>';
        $s .= '</table>';
        $s = str_replace('<tr></tr>', '', $s);

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
        $arrayid = array();
        foreach ($array as $key => $value) {
            $arraydate[date('j', $value[$zbp->datainfo['Post']['PostTime'][0]])] = $value[$zbp->datainfo['Post']['ID'][0]];
        }
        if (count($arraydate) > 0) {
            foreach ($arraydate as $key => $value) {
                $arrayid[] = array('log_ID', $value);
            }
            $articles = $zbp->GetArticleList('*', array(array('array', $arrayid)), null, null, null, false);
            foreach ($arraydate as $key => $value) {
                $a = $zbp->GetPostByID($value);
                $s = str_replace('<td>' . $key . '</td>', '<td><a href="' . $a->Url . '">' . $key . '</a></td>', $s);
            }
        }

        return $s;

    }

    /**
     * 导出最新留言模块数据
     * @return string 模块内容
     */
    public static function Comments() {
        global $zbp;

        $i = $zbp->modulesbyfilename['comments']->MaxLi;
        if ($i == 0) {
            $i = 10;
        }

        $comments = $zbp->GetCommentList('*', array(array('=', 'comm_IsChecking', 0)), array('comm_PostTime' => 'DESC'), $i, null);

        $s = '';
        foreach ($comments as $comment) {
            $s .= '<li><a href="' . $comment->Post->Url . '#cmt' . $comment->ID . '" title="' . htmlspecialchars($comment->Author->StaticName . ' @ ' . $comment->Time()) . '">' . TransferHTML($comment->Content, '[noenter]') . '</a></li>';
        }

        return $s;
    }

    /**
     * 导出最近发表文章模块数据
     * @return string 模块内容
     */
    public static function LatestArticles() {
        global $zbp;

        $i = $zbp->modulesbyfilename['previous']->MaxLi;
        if ($i == 0) {
            $i = 10;
        }

        $articles = $zbp->GetArticleList('*', array(array('=', 'log_Type', 0), array('=', 'log_Status', 0)), array('log_PostTime' => 'DESC'), $i, null, false);
        $s = '';
        foreach ($articles as $article) {
            $s .= '<li><a href="' . $article->Url . '">' . $article->Title . '</a></li>';
        }

        return $s;
    }

    /**
     * 导出文章归档模块数据
     * @return string 模块内容
     */
    public static function Archives() {
        global $zbp;

        $maxli = $zbp->modulesbyfilename['archives']->MaxLi;
        if ($maxli < 0) {
            return '';
        }

        $sql = $zbp->db->sql->Select($zbp->table['Post'], array('log_PostTime'), null, array('log_PostTime' => 'DESC'), array(1), null);

        $array = $zbp->db->Query($sql);

        if (count($array) == 0) {
            return '';
        }

        $ldate = array(date('Y', $array[0]['log_PostTime']), date('m', $array[0]['log_PostTime']));

        $sql = $zbp->db->sql->Select($zbp->table['Post'], array('log_PostTime'), null, array('log_PostTime' => 'ASC'), array(1), null);

        $array = $zbp->db->Query($sql);

        if (count($array) == 0) {
            return '';
        }

        $fdate = array(date('Y', $array[0]['log_PostTime']), date('m', $array[0]['log_PostTime']));

        $arraydate = array();

        for ($i = $fdate[0]; $i < $ldate[0] + 1; $i++) {
            for ($j = 1; $j < 13; $j++) {
                $arraydate[] = strtotime($i . '-' . $j);
            }
        }

        foreach ($arraydate as $key => $value) {
            if ($value - strtotime($ldate[0] . '-' . $ldate[1]) > 0) {
                unset($arraydate[$key]);
            }

            if ($value - strtotime($fdate[0] . '-' . $fdate[1]) < 0) {
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

            $url = new UrlRule($zbp->option['ZC_DATE_REGEX']);
            $url->Rules['{%date%}'] = date('Y-n', $value);
            $url->Rules['{%year%}'] = date('Y', $value);
            $url->Rules['{%month%}'] = date('n', $value);
            $url->Rules['{%day%}'] = 1;

            $fdate = $value;
            $ldate = (strtotime(date('Y-m-t', $value)) + 60 * 60 * 24);
            $sql = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', '0'), array('=', 'log_Status', '0'), array('BETWEEN', 'log_PostTime', $fdate, $ldate)));
            $n = GetValueInArrayByCurrent($zbp->db->Query($sql), 'num');
            if ($n > 0) {
                $s .= '<li><a href="' . $url->Make() . '">' . str_replace(array('%y%', '%m%'), array(date('Y', $fdate), date('n', $fdate)), $zbp->lang['msg']['year_month']) . ' (' . $n . ')</a></li>';
                $i++;
            }
        }

        return $s;

    }

    /**
     * 导出导航模块数据
     * @return string 模块内容
     */
    public static function Navbar() {
        global $zbp;

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

        return $s;
    }

    /**
     * 导出tags模块数据
     * @return string 模块内容
     */
    public static function TagList() {
        global $zbp;
        $s = '';
        $i = $zbp->modulesbyfilename['tags']->MaxLi;
        if ($i == 0) {
            $i = 25;
        }

        $array = $zbp->GetTagList('*', '', array('tag_Count' => 'DESC'), $i, null);
        $array2 = array();
        foreach ($array as $tag) {
            $array2[$tag->ID] = $tag;
        }
        ksort($array2);

        foreach ($array2 as $tag) {
            $s .= '<li><a href="' . $tag->Url . '">' . $tag->Name . '<span class="tag-count"> (' . $tag->Count . ')</span></a></li>';
        }

        return $s;
    }

    /**
     * 导出用户列表模块数据
     * @param int $level 要导出的用户最低等级，默认为4（即协作者）
     * @return string 模块内容
     */
    public static function AuthorList($level = 4) {
        global $zbp;
        $s = '';

        $w = array();
        $w[] = array('<=', 'mem_Level', $level);

        $array = $zbp->GetMemberList('*', $w, array('mem_ID' => 'ASC'), null, null);

        foreach ($array as $member) {
            $s .= '<li><a href="' . $member->Url . '">' . $member->Name . '<span class="article-nums"> (' . $member->Articles . ')</span></a></li>';
        }

        return $s;
    }

    /**
     * 导出网站统计模块数据
     * @param array $array
     * @return string 模块内容
     */
    public static function Statistics($array = array()) {
        global $zbp;
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

        $s = "";
        $s .= "<li>{$zbp->lang['msg']['all_artiles']}:{$all_artiles}</li>";
        $s .= "<li>{$zbp->lang['msg']['all_pages']}:{$all_pages}</li>";
        $s .= "<li>{$zbp->lang['msg']['all_categorys']}:{$all_categorys}</li>";
        $s .= "<li>{$zbp->lang['msg']['all_tags']}:{$all_tags}</li>";
        $s .= "<li>{$zbp->lang['msg']['all_comments']}:{$all_comments}</li>";
        if (!$zbp->option['ZC_VIEWNUMS_TURNOFF'] || $zbp->option['ZC_LARGE_DATA']) {
            $s .= "<li>{$zbp->lang['msg']['all_views']}:{$all_views}</li>";
        }

        $zbp->modulesbyfilename['statistics']->Type = "ul";

        return $s;

    }

}
