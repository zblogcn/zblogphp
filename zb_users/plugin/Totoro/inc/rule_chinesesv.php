<?php

return 'chinesesv';
function chinesesv($author, $content, $orig_content, &$sv, $config_sv, $config_array)
{
    $sv += (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $content) == 0) ? $config_sv : 0;
}
