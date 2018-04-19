<?php

return 'hyperlink_value';
function hyperlink_value($author, $content, $orig_content, &$sv, $config_sv, $config_array)
{
    $matches = array();
    preg_match_all("/https?:\/\/(?!www|ftp)|ftp|www./si", $orig_content, $matches);

    //var_dump($matches[0]);
    $count = count($matches[0]);

    if ($count > 0) {
        $sv += $config_sv * (pow(2, $count - 1));
    }

    //Totoro_SV=Totoro_SV+TOTORO_HYPERLINK_VALUE*(2^matches.count-1)
}
