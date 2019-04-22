<?php

return 'interval_value';
function interval_value($author, $content, $orig_content, &$sv, $config_sv, $config_array)
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
            array('>', 'comm_PostTime', time() - 3600),
        ),
        null,
        null,
        null
    );

    $ary = $zbp->db->Query($sql);

    if (count($ary) > 0) {
        $count = (int) $ary[0]['c'];
    }
    if ($count > 0) {
        //		if ($count <= 30) {
        $sv += $config_sv * ((int) ($count / 5) + (($count % 5 > 0) ? 1 : 0)) / 5;
        //		}

        // = TOTORO_INTERVAL_VALUE * (0-5=1||5-10=2||.....)/10
        //		else {
        //			$sv += $config_sv * 8 / 5;
        //		}
    }
}
