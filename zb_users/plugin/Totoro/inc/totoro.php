<?php

class Totoro_Class
{

    public $config_array = array();

    public $sv = 0;

    public function __construct()
    {
        $this->config_array = include TOTORO_INCPATH . 'totoro_config.php';
        $this->init_config();
    }

    public function init_config()
    {
        global $zbp;
        $config_save = false;
        foreach ($this->config_array as $type_name => &$type_value) {
            foreach ($type_value as $name => &$value) {
                $config_name = $type_name . '_' . $name;
                $config_value = $zbp->Config('Totoro')->$config_name;
                if (!isset($config_value)) {
                    $zbp->Config('Totoro')->$config_name = $value['DEFAULT'];
                    $config_save = true;
                }
                $value['VALUE'] = $zbp->Config('Totoro')->$config_name;
            }
        }
        if (!isset($zbp->Config('Totoro')->THROW_INT)) {
            $zbp->Config('Totoro')->THROW_INT = 0;
            $config_save = true;
        }
        if (!isset($zbp->Config('Totoro')->CHECK_INT)) {
            $zbp->Config('Totoro')->CHECK_INT = 0;
            $config_save = true;
        }
        if ($config_save) {
            $zbp->SaveConfig('Totoro');
        }

        return true;
    }

    public function output_config($type, $name, $convert = true)
    {
        global $zbp;
        $content = $this->config_array[$type][$name]['VALUE'];

        return $convert ? TransferHTML($content, '[html-format]') : $content;
    }

    public function add_black_list($id)
    {
        global $zbp;
        $comment = $zbp->GetCommentByID($id);
        $content = $comment->HomePage . ' ' . $comment->Content;
        $black_list = $this->config_array['BLACK_LIST']['BADWORD_LIST']['VALUE'];
        $tmp_list = '';
        $matches = array();
        $regex = "/(([\w\d]+\.)+\w{2,})/si";

        if ($this->config_array['BUILD_CONFIG']['AUTOBANURL']) {
            preg_match_all($regex, $content, $matches);
            if (substr($black_list, strlen($black_list) - 1, 1) == '|') {
                $black_list = substr($black_list, 0, strlen($black_list) - 1);
            }

            foreach ($matches[0] as $value) {
                $value = preg_quote($value);
                if ($value != '' && preg_match("/" . $black_list . "/si", $content) == 0) {
                    if (stripos(preg_quote($zbp->host), $value) === false) {
                        $black_list .= '|' . $value;
                        $zbp->SetHint('good', '新黑词被加入：' . $value);
                    }
                }
            }

            $zbp->Config('Totoro')->BLACK_LIST_BADWORD_LIST = $black_list;
        }

        $zbp->SaveConfig('Totoro');
    }

    public function build_content(&$comment)
    {
        $content = '';
        //$content .= $comment->Name . ' ';
        //$content .= $comment->Email . ' ';
        $content .= $comment->Content . ' ';

        foreach ($this->config_array['BUILD_CONFIG'] as $name => $value) {
            if ($value['VALUE']) {
                $low_name = strtolower($name);
                $file = TOTORO_INCPATH . 'build_' . $low_name . '.php';
                if (file_exists($file)) {
                    $func = Totoro_Include($file);
                    $func($content);
                }
            }
        }

        return array(
            'author' => array(
                'id'    => $comment->AuthorID,
                'name'  => $comment->Name,
                'ip'    => $comment->IP,
                'email' => $comment->Email,
                'url'   => $comment->HomePage,
            ),
            'content' => $content,
        );
    }

    public function get_score(&$comment, $debug = false)
    {
        $build = $this->build_content($comment);
        if ($debug) {
            echo 'BUILD COMMENT: ' . $build['content'] . "\n";
        }

        foreach ($this->config_array['SV_RULE'] as $name => $value) {
            $low_name = strtolower($name);
            $file = TOTORO_INCPATH . 'rule_' . $low_name . '.php';
            if (file_exists($file) && $value['VALUE'] > 0) {
                $func = Totoro_Include($file);
                $func($build['author'], $build['content'], $comment->Content, $this->sv, $value['VALUE'], $this->config_array);
                if ($debug) {
                    echo 'AFTER ' . $value['NAME'] . ': ' . $this->sv . "\n";
                }
            }
        }

        return $this->sv;
    }

    public function check_comment(&$comment)
    {
        global $zbp;
        $zbp->lang['error'][53] = $this->config_array['STRING_BACK']['CHECKSTR']['VALUE'];
        $zbp->lang['error'][14] = $this->config_array['STRING_BACK']['THROWSTR']['VALUE'];

        if ($this->check_ip($comment->IP)) {
            $comment->IsThrow = true;
            $zbp->lang['error'][14] = $this->config_array['STRING_BACK']['KILLIPSTR']['VALUE'];
        }

        if (!$comment->IsThrow) {
            $this->get_score($comment);
            if ($this->sv >= $this->config_array['SV_SETTING']['SV_THRESHOLD']['VALUE']) {
                if ($this->sv < $this->config_array['SV_SETTING']['SV_THRESHOLD2']['VALUE']
                    ||
                    $this->config_array['SV_SETTING']['SV_THRESHOLD2']['VALUE'] <= 0
                ) {
                    $comment->IsChecking = true;
                    $zbp->Config('Totoro')->CHECK_INT = (int) $zbp->Config('Totoro')->CHECK_INT + 1;
                    $zbp->SaveConfig('Totoro');
                    $this->filter_ip($comment->IP, false);
                } elseif ($this->config_array['SV_SETTING']['SV_THRESHOLD2']['VALUE'] <= $this->sv) {
                    $comment->IsThrow = true;
                    $zbp->Config('Totoro')->THROW_INT = (int) $zbp->Config('Totoro')->THROW_INT + 1;
                    $zbp->SaveConfig('Totoro');
                    $this->filter_ip($comment->IP, true);
                }
            }
        }
        //if ($this->sv >=)
    }

    public function replace_comment(&$comment)
    {
        $replace_str = $this->config_array['SV_SETTING']['REPLACE_KEYWORD']['VALUE'];

        $replace_list = $this->config_array['BLACK_LIST']['REPLACE_LIST']['VALUE'];
        $badword_list = $this->config_array['BLACK_LIST']['BADWORD_LIST']['VALUE'];

        $replace_reg = "/" .
            ($replace_list != '' ? $replace_list : '') .
            (($replace_list != '' && $badword_list != '') ? '|' : '') .
            ($badword_list != '' ? $badword_list : '') .
            "/si";

        if ($replace_reg != "//si") {
            $comment->Content = preg_replace($replace_reg, $replace_str, $comment->Content);
        }
    }

    public function check_ip($ip)
    {
        //如果是ipv6，就跳过
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false){
            return false;
        }
        //如果是ipv4的内网，就跳过
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false){
            return false;
        }
        $ip = ip2long($ip);
        $ip_str = explode('|', $this->config_array['BLACK_LIST']['IPFILTER_LIST']['VALUE']);
        for ($i = 0; $i < count($ip_str); $i++) {
            $ip_begin = ip2long(str_replace('*', '0', $ip_str[$i]));
            $ip_end = ip2long(str_replace('*', '255', $ip_str[$i]));
            if ($ip >= $ip_begin && $ip <= $ip_end) {
                return true;
            }
        }

        return false;
    }

    public function filter_ip($ip, $kill)
    {
        global $zbp;
        if ($this->config_array['SV_SETTING']['KILLIP']['VALUE'] == 0) {
            return;
        }

        if (!$kill) {
            $sql = $zbp->db->sql->Select(
                '%pre%comment',
                array(
                    'COUNT(comm_id) AS c',
                ),
                array(
                    array('=', 'comm_IP', $ip),
                    array('>', 'comm_PostTime', time() - 24 * 60 * 60),
                ),
                null,
                null,
                null
            );
            $result = $zbp->db->Query($sql);
            if (count($result) <= 0) {
                return;
            }

            if ((int) $result[0]['c'] <= $this->config_array['SV_SETTING']['KILLIP']['VALUE']) {
                return;
            }
        }

        if ($kill) {
            if (!$this->check_ip($ip)) {
                $FILTERIP = $this->config_array['BLACK_LIST']['IPFILTER_LIST']['VALUE'];
                $FILTERIP = ($FILTERIP == '' ? $ip : $FILTERIP . '|' . $ip);
                $zbp->Config('Totoro')->BLACK_LIST_IPFILTER_LIST = $FILTERIP;
                $zbp->SaveConfig('Totoro');
            }
        }

        $this->kill_ip($ip);
    }

    public function kill_ip($ip)
    {
        global $zbp;
        $logid = array();
        $cmtid = array();
        $sql = $zbp->db->sql->Select(
            '%pre%comment',
            array(
                'comm_ID',
                'comm_logID',
            ),
            array(
                array('=', 'comm_IP', $ip),
                array('=', 'comm_IsChecking', 0),
                array('>', 'comm_PostTime', time() - 24 * 60 * 60),
            ),
            null,
            null,
            null
        );
        $result = $zbp->db->Query($sql);
        $foundComment = count($result);
        if ($foundComment > 0) {
            for ($i = 0; $i < count($result); $i++) {
                $cmtid[] = $result[$i]['comm_ID'];
                $logid[] = $result[$i]['comm_logID'];
            }
        }

        $zbp->Config('Totoro')->CHECK_INT = (int) $zbp->Config('Totoro')->CHECK_INT + $foundComment;
        $zbp->SaveConfig('Totoro');

        if (function_exists('CountCommentNums')) {
            CountCommentNums(0, +$foundComment);
        }

        CountPostArray($logid);
        $zbp->AddBuildModule('comments');

        $sql = $zbp->db->sql->Update(
            '%pre%comment',
            array(
                'comm_IsChecking' => 1,
            ),
            array(
                array('=', 'comm_IP', $ip),
                array('=', 'comm_IsChecking', 0),
                array('>', 'comm_PostTime', time() - 24 * 60 * 60),
            )
        );
        $zbp->db->Update($sql);
    }

    public function export_submenu($action)
    {
        $array = array(
            array(
                'action' => 'main',
                'url'    => 'main.php',
                'target' => '_self',
                'float'  => 'left',
                'title'  => '设置页面',
            ),
            array(
                'action' => 'regex_test',
                'url'    => 'regex_test.php',
                'target' => '_self',
                'float'  => 'right',
                'title'  => '正则测试',
            ),
            array(
                'action' => 'online_test',
                'url'    => 'online_test.php',
                'target' => '_self',
                'float'  => 'right',
                'title'  => '配置测试',
            ),
        );
        $str = '';
        $template = '<a href="$url" target="$target"><span class="m-$float$light">$title</span></a>';
        for ($i = 0; $i < count($array); $i++) {
            $str .= $template;
            $str = str_replace('$url', $array[$i]['url'], $str);
            $str = str_replace('$target', $array[$i]['target'], $str);
            $str = str_replace('$float', $array[$i]['float'], $str);
            $str = str_replace('$title', $array[$i]['title'], $str);
            $str = str_replace('$light', ($action == $array[$i]['action'] ? ' m-now' : ''), $str);
        }

        return $str;
    }

}
