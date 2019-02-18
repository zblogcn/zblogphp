<?php

return 'number_value';
function number_value($author, $content, $orig_content, &$sv, $config_sv, $config_array)
{
    $matches = array();
    preg_match_all("/\d/si", $content, $matches);

    $count = count($matches[0]);
    if ($count > 10) {
        $sv += $config_sv * ($count - 10);
    }
}
