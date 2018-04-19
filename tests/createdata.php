<?php
/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) RainbowSoft Studio
 *
 * @version
 */
require '../zb_system/function/c_system_base.php';
error_reporting(0);
ini_set("display_errors", 0);
set_error_handler(create_function('', ''));
set_exception_handler(create_function('', ''));
register_shutdown_function(create_function('', ''));

$zbp->Load();

function article()
{
    for ($i = 0; $i < 100000; $i++) {
        $a = new Post();
        $a->CateID = mt_rand(1, 1000);
        $a->AuthorID = 1;
        $a->Tag = getTagStr(mt_rand(0, 3));
        $a->Status = ZC_POST_STATUS_PUBLIC;
        $a->Type = ZC_POST_TYPE_ARTICLE;
        $a->Alias = '';
        $a->IsTop = false;
        $a->IsLock = false;
        $a->Title = getRandStr(mt_rand(8, 14));
        //$a->Intro=getRandStr(mt_rand(50,150)) . GetGuid();
        $s = getRandStr(mt_rand(500, 1000)) . GetGuid() . '<br/>' . GetGuid();
        $a->Content = $s;
        $a->Intro = substr($s, 0, 250);
        //1418365907
        $a->PostTime = mt_rand(1400000000, 1418360000);
        $a->IP = GetGuestIP();
        //$a->PostTime=time();
        $a->CommNums = 0;
        $a->ViewNums = 0;
        $a->Template = '';
        $a->Meta = '';
        $a->Save();
    }
}

function page()
{
    for ($i = 0; $i < 1000; $i++) {
        $a = new Post();
        $a->CateID = 0;
        $a->AuthorID = 1;
        $a->Tag = '';
        $a->Status = ZC_POST_STATUS_PUBLIC;
        $a->Type = ZC_POST_TYPE_PAGE;
        $a->Alias = '';
        $a->IsTop = false;
        $a->IsLock = false;
        $a->Title = getRandStr(mt_rand(6, 10));
        $a->Intro = '';
        $a->Content = getRandStr(mt_rand(200, 300)) . GetGuid() . '<br/>' . GetGuid();
        $a->IP = GetGuestIP();
        $a->PostTime = time();
        $a->CommNums = 0;
        $a->ViewNums = 0;
        $a->Template = '';
        $a->Meta = '';
        $a->Save();
    }
}

function cate()
{
    for ($i = 0; $i < 1000; $i++) {
        $cate = new Category();
        $cate->Name = getRandStr(mt_rand(2, 4));
        $cate->Save();
    }
}

function tag()
{
    for ($i = 0; $i < 100000; $i++) {
        $tag = new Tag();
        $tag->Name = getRandStr(mt_rand(2, 5));
        $tag->Save();
    }
}

function getChineseChar2()
{
    $unidec = mt_rand(hexdec('4e00'), hexdec('9fa5'));
    $unichr = '&#' . $unidec . ';';
    $zhcnchr = mb_convert_encoding($unichr, "UTF-8", "HTML-ENTITIES");

    return $zhcnchr;
}

function getChineseChar()
{
    $i = mt_rand(0xb0, 0xd7);
    $j = mt_rand(0xa1, 0xfe);
    $s = chr($i) . chr($j);
    $s = @iconv('GB2312', 'UTF-8//IGNORE', $s);

    return $s;
}

function getRandStr($len)
{
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        $str = $str . getChineseChar();
    }

    return $str;
}

function getTagStr($tagcount)
{
    $str = '';
    for ($i = 0; $i < $tagcount; $i++) {
        $str = $str . '{' . mt_rand(1, 10000) . '}';
    }

    return $str;
}

echo "生成文章!<br/>";
article();

/*

echo "生成分类!<br/>";
cate();

echo "生成文章!<br/>";
article();

echo "生成页面!<br/>";
page();

echo "tag!<br/>";
tag();




$zbp->LoadTags();
foreach ($zbp->tags as $key => $value) {
    $value->Count=$zbp->CountTag($key);
    $value->Save();
}

foreach ($zbp->categories as $key => $value) {
    $value->Count=$zbp->CountCategory($key);
    $value->Save();
}

*/
RunTime();
