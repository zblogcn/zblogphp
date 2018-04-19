<?php

function upyun_SubMenu($id)
{
    $arySubMenu = array(
        0 => array('空间设置', 'main.php', 'left', false),
        //1 => array('略缩图设置', 'image.php', 'left', false),
    );

    foreach ($arySubMenu as $k => $v) {
        echo '<a href="' . $v[1] . '" ' . ($v[3] == true ? 'target="_blank"' : '') . '><span class="m-' . $v[2] . ' ' . ($id == $k ? 'm-now' : '') . '">' . $v[0] . '</span></a>';
    }
}
