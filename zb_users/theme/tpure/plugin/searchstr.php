<?php
/**
 *  获取UTF8格式的字符串的子串.
 *
 * @param string $sourcestr 源字符串
 * @param int    $startstr  开始字串子串
 * @param int    $cutlength 子串长度
 *
 * @return string
 */
function tpure_SubStrStartUTF8($sourcestr, $startstr, $cutlength)
{
    global $zbp;
    if (function_exists('mb_substr') && function_exists('mb_internal_encoding') && function_exists('mb_stripos')) {
        mb_internal_encoding('UTF-8');

        return mb_substr($sourcestr, mb_stripos($sourcestr, $startstr), $cutlength);
    }
    if (function_exists('iconv_substr') && function_exists('iconv_set_encoding') && function_exists('iconv_strpos')) {
        iconv_set_encoding("internal_encoding", "UTF-8");
        iconv_set_encoding("output_encoding", "UTF-8");

        return iconv_substr($sourcestr, iconv_strpos($sourcestr, $startstr), $cutlength);
    }
    $returnstr = '';
    $i = stripos($sourcestr, $startstr);
    $n = 0;
    $str_length = strlen($sourcestr); //字符串的字节数
    while (($n < $cutlength) and ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = ord($temp_str); //得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) { //如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } else { //其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数计1个
            $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > $cutlength) {
        $returnstr = $returnstr;
    }

    return $returnstr;
}
