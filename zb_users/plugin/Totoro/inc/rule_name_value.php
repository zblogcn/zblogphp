<?php

return 'name_value';
function name_value($author, $content, $orig_content, &$sv, $config_sv, $config_array)
{
    global $zbp;
    $count = 0;
    $sql = $zbp->db->sql->Select(
        '%pre%comment',
        array(
            'COUNT(comm_id) AS c',
        ),
        array(
            array('=', 'comm_IP', $author['ip']),
            array('=', 'comm_IsChecking', '0'),
            array('<', 'comm_PostTime', time() - 3600),
        ),
        null,
        null,
        null
    );

    $ary = $zbp->db->Query($sql);

    if (count($ary) > 0) {
        $count = (int) $ary[0]['c'];
    }

    if ($count > 0 && $count <= 10) {
        $sv += -10;
    } elseif ($count > 10 && $count <= 20) {
        $sv += -10 - 1 * $config_sv;
    } elseif ($count > 20 && $count <= 50) {
        $sv += -10 - 2 * $config_sv;
    } elseif ($count > 50) {
        $sv += -10 - 3 * $config_sv;
    }
}
