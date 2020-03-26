<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="{$language}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
{php}
$SEOON = $zbp->Config('tpure')->SEOON;
$SEOTITLE = $zbp->Config('tpure')->SEOTITLE;
$SEOKEYWORDS = $zbp->Config('tpure')->SEOKEYWORDS;
$SEODESCRIPTION = $zbp->Config('tpure')->SEODESCRIPTION;
if(isset($SEOON) && $SEOON == '1'){
    if($type == 'index'){
        if($page == '1'){
            if(isset($SEOTITLE) && !empty($SEOTITLE)){
                $ThisTitle = $SEOTITLE;
            }else{
                $ThisTitle = $zbp->name.' - '.$zbp->subname;
            }
        }else{
            if(isset($SEOTitle) && !empty($SEOTitle)){
                $ThisTitle = $SEOTitle.' - '.'第'.$page.'页';
            }else{
                $ThisTitle = $zbp->name.' - '.'第'.$page.'页'.' - '.$zbp->subname;
            }
        }
        if(isset($SEOKEYWORDS)){
            $keywords = $SEOKEYWORDS;
        }else{
            $keywords = '';
        }
        if(isset($SEODESCRIPTION)){
            $description = $SEODESCRIPTION;
        }else{
            $description = '';
        }
    }elseif($type == 'category'){
        if($category->Metas->catetitle){
            if ($page=='1') {
                $ThisTitle = $category->Metas->catetitle;
            }else{
                $ThisTitle = $zbp->title.' - '.'第'.$page.'页'.' - '.$category->Metas->catetitle;
            }
        }else{
            if ($page=='1') {
                $ThisTitle = $zbp->title.' - '.$zbp->name;
            }else{
                $ThisTitle = $zbp->title.' - '.'第'.$page.'页'.' - '.$zbp->name;
            }
        }
        if($category->Metas->catekeywords){
            $keywords = $category->Metas->catekeywords;
        }else{
            $keywords = $category->Name;
        }
        if($category->Metas->catedescription){
            $description = $category->Metas->catedescription;
        }else{
            $description = $category->Intro;
        }
    }elseif($type == 'tag'){
        if($tag->Metas->tagtitle){
            if ($page=='1') {
                $ThisTitle = $tag->Metas->tagtitle;
            }else{
                $ThisTitle = $zbp->title.' - '.'第'.$page.'页'.' - '.$tag->Metas->tagtitle;
            }
        }else{
            if ($page=='1') {
                $ThisTitle = $zbp->title.' - '.$zbp->name;
            }else{
                $ThisTitle = $zbp->title.' - '.'第'.$page.'页'.' - '.$zbp->name;
            }
        }
        if($tag->Metas->tagkeywords){
            $keywords = $tag->Metas->tagkeywords;
        }else{
            $keywords = $tag->Name;
        }
        if($tag->Metas->tagdescription){
            $description = $tag->Metas->tagdescription;
        }else{
            $description = $tag->Intro;
        }
    }elseif($type == 'article'){
        if($article->Metas->singletitle){
            $ThisTitle = $article->Metas->singletitle;
        }else{          
            $ThisTitle = $article->Title.' - '.$article->Category->Name.' - '.$zbp->name;
        }   
            if($article->Metas->singlekeywords){
            $keywords = $article->Metas->singlekeywords;
        }else{
            $aryTags = array();
            foreach($article->Tags as $key){
                $aryTags[] = $key->Name;
            }
            if(count($aryTags)>0){
                $keywords = implode(',',$aryTags);
            }else{
                $keywords = '';
            }
        }
        if($article->Metas->singledescription){
            $description = $article->Metas->singledescription;
        }else{
            $description = preg_replace('/[\r\n\s]+/', '', trim(SubStrUTF8(TransferHTML($article->Content,'[nohtml]'),100)).'...');
        }
    }elseif($type == 'page'){
        if($article->Metas->singletitle){
            $ThisTitle = $article->Metas->singletitle;
        }else{
            $ThisTitle = $article->Title.' - '.$zbp->name;
        }
        if($article->Metas->singlekeywords){
            $keywords = $article->Metas->singlekeywords;
        }else{
            $keywords = '';
        }
        if($article->Metas->singledescription){
            $description = $article->Metas->singledescription;
        }else{
            $description = preg_replace('/[\r\n\s]+/', '', trim(SubStrUTF8(TransferHTML($article->Content,'[nohtml]'),100)).'...');
        }
    }else {
        if($page>'1'){
            $ThisTitle = $zbp->title.' - '.'第'.$page.'页'.' - '.$zbp->name;
        }else{
            $ThisTitle = $zbp->title.' - '.$zbp->name;
        }
        if(isset($SEOKEYWORDS)){
            $keywords = $SEOKEYWORDS;
        }else{
            $keywords = '';
        }
        if(isset($SEODESCRIPTION)){
            $description = $SEODESCRIPTION;
        }else{
            $description = '';
        }
    }
}
{/php}
    <title>{if isset($SEOON) && $SEOON == '1'}{$ThisTitle}{else}{$name} - {$title}{/if}</title>
{if isset($SEOON) && $SEOON == '1'}
{if $keywords}
    <meta name="keywords" content="{$keywords}" />
{/if}
{if $description}
    <meta name="description" content="{$description}"/>
{/if}
{/if}
    {if $zbp->Config('tpure')->PostFAVICONON}<link rel="shortcut icon" href="{$zbp->Config('tpure')->PostFAVICON}" type="image/x-icon" />{/if}
    <meta name="generator" content="{$zblogphp}" />
{if $type=='article'}
    <link rel="canonical" href="{$article.Url}"/>
{/if}
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css" type="text/css" media="all"/>
{if $zbp->Config('tpure')->PostCOLORON == '1'}
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/include/skin.css" type="text/css" media="all"/>
{/if}
    <script src="{$host}zb_system/script/jquery-2.2.4.min.js" type="text/javascript"></script>
    <script src="{$host}zb_system/script/zblogphp.js" type="text/javascript"></script>
    <script src="{$host}zb_system/script/c_html_js_add.php" type="text/javascript"></script>
    <script type="text/javascript" src="{$host}zb_users/theme/{$theme}/script/common.js"></script>
    <script type="text/javascript">window.tpure={{if $zbp->Config('tpure')->PostBANNERDISPLAYON=='1'}bannerdisplay:'on',{/if}{if $zbp->Config('tpure')->PostVIEWALLON=='1'}viewall:'on',{/if}{if $zbp->Config('tpure')->PostVIEWALLSTYLE}viewallstyle:'1',{else}viewallstyle:'0',{/if}{if $zbp->Config('tpure')->PostVIEWALLHEIGHT}viewallheight:'{$zbp->Config('tpure')->PostVIEWALLHEIGHT}',{/if}{if $zbp->Config('tpure')->PostSINGLEKEY=='1'}singlekey:'on',{/if}{if $zbp->Config('tpure')->PostPAGEKEY=='1'}pagekey:'on',{/if}{if $zbp->Config('tpure')->PostREMOVEPON=='1'}removep:'on',{/if}{if $zbp->Config('tpure')->PostBACKTOTOPON=='1'}backtotop:'on'{/if}}</script>
{if $zbp->Config('tpure')->PostBLANKON=='1'}
    <base target="_blank" />
{/if}
{if $zbp->Config('tpure')->PostGREYON=='1'}
<style>html {filter: grayscale(100%);}</style>
{/if}
{if $type=='article'}
    <link rel="canonical" href="{$article.Url}"/>
{/if}
{$header}
{if $type=='index'&&$page=='1'}
    <link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}" />
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="{$host}zb_system/xml-rpc/?rsd" />
    <link rel="wlwmanifest" type="application/wlwmanifest+xml" href="{$host}zb_system/xml-rpc/wlwmanifest.xml" />
{/if}
</head>