<?php

return 'level_value';
function level_value($author, $content, $orig_content, &$sv, $config_sv, $config_array)
{
    global $zbp;
    $level_max = 6;

    if ($author['id'] == 0) {
        $level = 6;
    } elseif (isset($zbp->members[$author['id']])) {
        $level = $zbp->members[$author['id']]->Level;
    } else {
        $level = 6;
    }

    $LEVEL_MINUS = $level_max - $level - 1;
    $SV = (($LEVEL_MINUS < 0) ? 0 : pow(2, $LEVEL_MINUS));
    $sv -= $config_sv * $SV;
}
