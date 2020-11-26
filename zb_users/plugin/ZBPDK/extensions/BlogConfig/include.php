<?php

$GLOBALS['zbpdk']->add_extension(array(
    'url'         => 'main.php',
    'description' => '可以对zbp_config表里的数据进行管理，用于调试Config配置类。',
    'id'          => 'BlogConfig',
));

$GLOBALS['zbpdk']->submenu->add(array(
    'url'   => 'BlogConfig/main.php',
    'float' => 'left',
    'id'    => 'BlogConfig',
    'title' => 'BlogConfig',
));

function blogconfig_left()
{
    global $zbp;
    $html = '';

    $configs_name = $configs_namevalue = array();
    foreach ($zbp->configs as $n => $c) {
        $configs_name[$n] = $n;
        $configs_namevalue[$n] = $c;
    }
    natcasesort($configs_name);
    $zbp->configs = array();
    foreach ($configs_name as $name) {
        $zbp->configs[$name] = $configs_namevalue[$name];
    }
    unset($configs_name, $configs_namevalue);

    foreach ($zbp->configs as $k => $v) {
        $html .= "<li><a id=\"$k\" href=\"javascript:;\" onclick=\"clk(this);run('open','$k');\">$k</a></li>";
    }

    return $html;
}

function blogconfig_exportlist($id)
{
    global $zbp;
    $html = '';
    $i = 0;
    $html .= '<div class="DIVBlogConfigtop"><span id="name">';
    $html .= $id . '</span><a href="javascript:;" onclick="run2(\'new\',\'' . $id . '\')">新建</a></div>';
    $html .= '<table width="100%" style="padding:0px;" cellspacing="0" cellpadding="0" id="configt">';
    $html .= '<tr height="32"><th width="25%">项</th><th>内容 <a onclick="alert(\'点击表格即可开始编辑。如果标注有array的话则无法编辑。\')" href="javascript:void(0);">？</a> </th><th width="10%"></th></tr>';

    if (isset($zbp->configs[$id]->Data)) {
        $data = $zbp->configs[$id]->Data;
    } else {
        $data = $zbp->configs[$id]->GetData();
    }
    ksort($data);
    foreach ($data as $name => $value) {
        $show_submit_button = true;
        $have_textarea = true;
        $show_in_span_html = '';
        $name = TransferHTML((string) $name, '[html-format]');

        if (gettype($value) == 'array') {
            $value = '[array][' . implode(",", $value) . ']';
            $show_in_span_html = $value;
            $show_submit_button = false;
            $have_textarea = false;
        } elseif (gettype($value) == 'boolean') {
            $value = (int) $value;
            $show_in_span_html = '<input type="text" id="ta' . $i . '" class="checkbox" value="' . (bool) $value . '" />';
            $have_textarea = false;
        } else {
            $value = TransferHTML((string) $value, '[html-format]');
            $show_in_span_html = $value;
        }

        //echo $value;
        //echo "\n";
        $html .= '<tr height="32">';
        $html .= '<td><input type="hidden" value="' . $i . '"/><span id="txt' . $i . '">';
        $html .= $name;
        $html .= '</span></td><td ';

        if ($have_textarea) {
            $html .= 'onclick="$(\'#ta' . $i . '\').show();$(\'#show' . $i . '\').hide()"';
        }

        $html .= '><span id="show' . $i . '">' . $show_in_span_html . '</span>';

        if ($have_textarea) {
            $html .= '<textarea id="ta' . $i . '" style="display:none;width:100%">' . $value . '</textarea></td>';
        }

        $html .= '<td class="tdCenter">';
        if ($show_submit_button) {
            $html .= '<a href="javascript:;" onclick="run2(\'edit\',\'' . $i . '\',\'' . $id . '\')">';
            if($GLOBALS['blogversion']>170000){
                $html .= '<i class="icon-check-circle-fill" style="color:green;" title="提交" ></i>';
            }else{
                $html .= '<img src="../../../../../zb_system/image/admin/ok.png" alt="提交" title="提交" width="16" /></a>';
            }

            $html .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $html .= '<a onclick="if(window.confirm(\'单击“确定”继续。单击“取消”停止。\')){run2(\'del\',\'' . $i . '\',\'' . $id . '\')};"';
        $html .= 'href="javascript:;" onclick="run2(\'del\',\'' . $i . '\',\'' . $id . '\')">';
        if($GLOBALS['blogversion']>170000){
            $html .= '<i class="icon-x-circle-fill" style="color:red;" title="删除"></i>';
        }else{
            $html .= '<img src="../../../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td></tr>';
        }

        $html .= PHP_EOL . PHP_EOL;
        $i++;
    }

    return $html;
}
