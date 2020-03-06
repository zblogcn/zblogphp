<?php

return 'similar_value';
function similar_value($author, $content, $orig_content, &$sv, $config_sv, $config_array)
{
    add_similar_sv(
        array(
            array('=', 'comm_IsChecking', 0),
            array('>', 'comm_PostTime', time() - 24 * 60 * 60),
        ),
        $config_array['SIMILAR_CONFIG']['SIMILAR_PASS_COMMCOUNT']['VALUE'],
        $content,
        $sv,
        $config_sv,
        $config_array
    );

    add_similar_sv(
        array(
            array('=', 'comm_IsChecking', 1),
            array('>', 'comm_PostTime', time() - 24 * 60 * 60),

        ),
        $config_array['SIMILAR_CONFIG']['SIMILAR_AUDIT_COMMCOUNT']['VALUE'],
        $content,
        $sv,
        $config_sv * 2,
        $config_array
    );
}

function add_similar_sv($condition, $count_for_condition, $content, &$sv, $config_sv, $config_array)
{
    global $zbp;
    $sql = $zbp->db->sql->Select(
        '%pre%comment',
        array(
            'comm_Content',
        ),
        $condition,
        null,
        $count_for_condition,
        null
    );
    $result = $zbp->db->Query($sql);
    if (count($result) <= 0) {
        return;
    }
    for ($i = 0; $i < count($result); $i++) {
        if (check_similar($content, $result[$i]['comm_Content']) >= $config_array['SIMILAR_CONFIG']['SIMILAR_PERCENT']['VALUE']) {
            $sv += $config_sv;
        }
    }
}

function check_similar($val1, $val2)
{
    $percent = 0;
    similar_text($val2, $val1, $percent);

    return (int) $percent;
}
