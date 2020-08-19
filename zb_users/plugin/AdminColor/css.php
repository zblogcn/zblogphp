<?php

require '../../../zb_system/function/c_system_base.php';

$id = (int) $zbp->Config('AdminColor')->ColorID;
$c = '/*admincolor*/';
$c .= '
  .ui-tooltip, .arrow_leftmenu:after {
    background: #3a6ea5;
    border: 2px solid white;
  }
  .ui-tooltip {
    color: white;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-transform: uppercase;
    box-shadow: 0 0 7px black;
  }
  .arrow_leftmenu {
	width: 12px;
	height: 30px;
    overflow: hidden;
    position: absolute;
    margin-left: -35px;
    bottom: -16px;
    top: 3px;
	left: 23px;
	bottom: auto;
	background-color: transparent;
  }
  .arrow_leftmenu:after {
    content: "";
    position: absolute;
    left: 6px;
    top: 0px;
    width: 25px;
    height: 25px;
    box-shadow: 9px -9px 6px 5px black;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }';

$c .= "header,.header{background-color:#3a6ea5;}" . PHP_EOL;
$c .= "input.button,input[type='submit'],input[type='button'] {background-color:#3a6ea5;}" . PHP_EOL;
$c .= "div.theme-now .betterTip img{box-shadow: 0 0 10px #3a6ea5;}" . PHP_EOL;

$c .= "#divMain a,#divMain2 a{color:#1d4c7d;}" . PHP_EOL;

$c .= ".menu ul li a{background-color: rgb(255,255,255,0.95);}" . PHP_EOL;
$c .= ".menu ul li.on a {background-color: #fff;}" . PHP_EOL;
$c .= ".menu ul li a:hover {background-color: #b0cdee;}" . PHP_EOL;
$c .= "#leftmenu a:hover {background-color: #b0cdee!important;}" . PHP_EOL;
$c .= "div.theme-now{background-color:#b0cdee;border-color:#3399cc;}" . PHP_EOL;
$c .= "div.theme-other .betterTip img:hover{border-color:#b0cdee;}" . PHP_EOL;
$c .= ".SubMenu a:hover {background-color:#b0cdee;}" . PHP_EOL;
$c .= ".siderbar-header:hover {background-color:#b0cdee;}" . PHP_EOL;

$c .= "#leftmenu .on a,#leftmenu #on a:hover {background-color:#3399cc!important;}" . PHP_EOL;
$c .= "input.button,input[type=\"submit\"],input[type=\"button\"] { border-color:#3399cc;}" . PHP_EOL;
$c .= "input.button:hover {background-color: #3399cc;}" . PHP_EOL;
$c .= "div.theme-other .betterTip img:hover{box-shadow: 0 0 10px #3399cc;}" . PHP_EOL;
$c .= ".SubMenu{border-bottom-color:#3399cc;}" . PHP_EOL;
$c .= ".SubMenu span.m-now{background-color:#3399cc;}" . PHP_EOL;
$c .= "div #BT_title {background-color: #3399cc;border-color:#3399cc;}" . PHP_EOL;

$c .= "a:hover { color:#d60000;}" . PHP_EOL;
$c .= "#divMain a:hover,#divMain2  a:hover{color:#d60000;}" . PHP_EOL;

//appcenter
/*
$c .= ".tabs { border-bottom-color:#3a6ea5!important;}" . PHP_EOL;
$c .= ".tabs li a.selected {background-color:#3a6ea5!important;}" . PHP_EOL;
$c .= "div.heart-vote {background-color:#3a6ea5!important;}" . PHP_EOL;
$c .= "div.heart-vote ul {border-color:#3a6ea5!important;}" . PHP_EOL;
$c .= ".install {background-color:#3a6ea5!important;}" . PHP_EOL;
$c .= ".install:hover{background-color: #3399cc!important;}" . PHP_EOL;
$c .= "input.button{background-color:#3a6ea5!important;border-color:#3399cc!important;}" . PHP_EOL;
$c .= "input.button:hover{background-color:#3399cc!important;}" . PHP_EOL;
$c .= ".themes_body ul li img:hover,.plugin_body ul li img:hover,.main_plugin ul li img:hover,.main_theme ul li img:hover{box-shadow: 0 0 10px #3399cc!important;}" . PHP_EOL;
$c .= ".left_nav h2,.text h2 {color: #3a6ea5!important;}" . PHP_EOL;
$c .= ".pagebar span{ background:#3399cc!important; border-color:#3399cc!important;color:#fff;}" . PHP_EOL;
$c .= ".pagebar span.now-page,.pagebar span:hover{ background:#fff!important;border-color:#fff!important; color:#3399cc!important;}" . PHP_EOL;
*/
//zbdk
$c .= "#divMain .DIVBlogConfignav ul li a:hover {background-color: #3399cc!important;}" . PHP_EOL;
$c .= "#divMain .DIVBlogConfignav ul li a.clicked{background-color: #b0cdee!important;}" . PHP_EOL;
$c .= ".DIVBlogConfignav {background-color: #ededed!important;}" . PHP_EOL;
$c .= "#divMain .DIVBlogConfigtop {background-color: #3399cc!important;}" . PHP_EOL;
$c .= "#divMain .DIVBlogConfig {background-color: #ededed!important;}" . PHP_EOL;
$c .= "div.bg {background: #3a6ea5;}" . PHP_EOL;
$c .= "div.bg input[type=\"text\"], input[type=\"password\"] {border-color:#3a6ea5}" . PHP_EOL;

$c .= PHP_EOL . "/*AdminColor*/" . PHP_EOL . "#admin_color{float:left;line-height: 2.5em;font-size: 0.5em;letter-spacing: -0.1em;}";

if ($zbp->Config('AdminColor')->HeaderPathUse == true || $id == 9) {
    $c .= 'header,.header{background:url(' . $zbp->Config('AdminColor')->HeaderPath . ') no-repeat center center;background-size:cover}' . PHP_EOL;
    $c .= 'div.bg{background:url(' . $zbp->Config('AdminColor')->HeaderPath . ') no-repeat center center;background-size:cover}' . PHP_EOL;
}
if ($zbp->Config('AdminColor')->LogoPath) {
    $c .= '.logo img{background:url(' . $zbp->Config('AdminColor')->LogoPath . ') no-repeat center center;}';
}

$c .= '
.pane,.theme,form.search{padding:1em;position:relative;background:#fff;margin:1em 0;border-radius:0.1em;}
div.theme{height:340px;margin:0 2em 2em 0;}
div.theme-other{background:#fff;}
form.search p{padding:0;}
td,th{border:none;border-right: 1px solid #efefef;padding:0.6em;}
table{border-collapse: collapse;background: #ffffff;line-height: 120%;margin:0.5em 0 0.5em 0;border:none;line-height:1.5em;}
';

if ($id == 9) {
    $c .= '
header, .header {background-color:#17365d;}
.left #leftmenu li {background: #ededed;}
table.tableBorder,table.tableFull,table.table_hover,table.table_striped{background-color: #e3eaf3;}
table > tbody > tr:nth-of-type(odd) {background-color: #b8cce4;}
td,th{border:none;padding:0.5em; border-right: 1px solid #d3e1f2;}
.content-box .content-box-tabs a.current {background-color:#b8cce4;}
form.search{background-color: #e3eaf3;}
.left #leftmenu li {background: #e3eaf3;}
body{background-colo1r: #edf2f8;}
';
}

if ($id == 10) {
    $c .= '
.header .menu {
    height: 60px;
    position: relative;
    float:left;
    left:0px;
}
.header .menu ul li a {
    float: left;
    line-height: 60px;
    height: 60px;
    padding: 0px 25px;
    font-size:1.2em;
    color: #fff;
    background: none;
    border-right:1px solid  #3f474f;
}

.header {
    height:60px;
    margin-bottom: 0px;
}
header, .header {
    background-color: #262f3e;
}
  .left{
padding-top:0px;
float:left;
height:100%;
background-position: -30px -2px;
width:160px;
background-color:#333333;
  }
.left #leftmenu{
    border-top: 10px solid #333;
}
.left #leftmenu a{
color:#fff;
width: 160px;
height: 40px;
}
.left #leftmenu li{
background-color:#333;
color:#fff;
width: 160px;
height: 40px;
}
.main {
padding-left: 170px;
padding-right: 10px;
}
#leftmenu li span {
    background-repeat: no-repeat;
    background-position: 0px 12px;
}
.left #leftmenu span {
    float: left;
    width: auto;
    height: 40px;
    line-height: 40px;
    text-align: left;
    cursor: pointer;
    margin-left: 25px;
    padding-left: 29px;
}
.divHeader {
    padding: 10px 0 45px 0;
    background-position:1px 13px!important;
}
div.hint {margin-top:0.5em;}

.left #leftmenu #nav_admincolor2 {
background-color:#444;
color:#fff;
width: 160px;
height: 40px;
}

.left #leftmenu #nav_admincolor2 span {
    float: left;
    width: auto;
    height: 36px;
    line-height: 36px;
    text-align: left;
    cursor: pointer;
    margin-left: 25px;
    padding-left: 60px;
    background-position: 40px 12px;
}
.left #leftmenu #nav_admincolor2 a:hover { background-color: Transparent!important;}
.left #leftmenu li span {color:black;filter: invert(0.8);font-weight:bold;}
.left #leftmenu li.on span {color:white;filter: none;font-weight:bold;}
.left #leftmenu li a:hover span {color:black;filter: invert(0.1);font-weight:bold;}
.left #leftmenu li.on a:hover span {color:white;filter: invert(0.1);font-weight:bold;}
body{background:url("images/l.png") repeat-y 0 top;}
body[class~=login] {background:none;}
header div.logo{
    background-color: Transparent;
    width: 62px;
    height: 60px;
    padding: 0 0 0 0;
    left: 0px;
    float:left;
}
header div.logo img{
    width: 60px;
    height: 60px;
    background-position: -5px -5px;
}
.pagebar a{border:1px solid white;}
body[class~=login],body[class~=error],body[class~=short]{background:none;}
body[class~=login] div.bg,body[class~=error] div.bg,body[class~=short] div.bg {background: #3399cc;}
body[class~=login] input[type="text"], body[class~=login] input[type="password"] {border-color:#3399cc}
body[class~=login] input.button, input[type="submit"], input[type="button"] {border-color: #3399cc;}
body[class~=login] input.button, body[class~=login] input[type="submit"], body[class~=login] input[type="button"] {background-color: #3399cc;}
body[class~=login] input[type="text"], body[class~=login] input[type="password"] {border-color: #3399cc;}
body[class~=login] input.button:hover {background-color: #3a6ea5;}
';
    if ($GLOBALS['blogversion'] < 162090 && stripos($_SERVER['HTTP_REFERER'], 'login.php')) {
        $c .= 'body{background:none;}';
    }
	//if ($GLOBALS['blogversion'] < 172360) {
		$c .= 'header div.logo img{background:url("images/logo.svg")}';
	//}
}
if ($zbp->Config('AdminColor')->TableShadow) {
    $c .= 'table,.pane,.theme,form.search{box-shadow:0 0 0.5em rgba(0,0,0,0.2);}';
} else {
    $c .= 'table,.pane,.theme,form.search{box-shadow:0 0 0.1em rgba(0,0,0,0.3);}';
}

$c1 = "#1d4c7d";
$c2 = "#3a6ea5";
$c3 = "#b0cdee";
$c4 = "#3399cc";
$c5 = "#d60000";

$AdminColor_Colors = array();

$AdminColor_Colors['Blod'] = $zbp->Config('AdminColor')->BlodColor;
$AdminColor_Colors['Normal'] = $zbp->Config('AdminColor')->NormalColor;
$AdminColor_Colors['Light'] = $zbp->Config('AdminColor')->LightColor;
$AdminColor_Colors['High'] = $zbp->Config('AdminColor')->HighColor;
$AdminColor_Colors['Anti'] = $zbp->Config('AdminColor')->AntiColor;

foreach ($GLOBALS['hooks']['Filter_Plugin_AdminColor_CSS_Pre'] as $fpname => &$fpsignal) {
    $fpname($AdminColor_Colors, $c);
}

$c = str_ireplace($c1, $AdminColor_Colors['Blod'], $c);
$c = str_ireplace($c2, $AdminColor_Colors['Normal'], $c);
$c = str_ireplace($c3, $AdminColor_Colors['Light'], $c);
$c = str_ireplace($c4, $AdminColor_Colors['High'], $c);
$c = str_ireplace($c5, $AdminColor_Colors['Anti'], $c);

$m = 'W/' . md5($c);

header('Content-Type: text/css; Charset=utf-8');
header('Etag: ' . $m);

if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] == $m) {
    SetHttpStatusCode(304);
    die;
}

echo $c;

die();
