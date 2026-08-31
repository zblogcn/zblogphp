<?php

require '../../../zb_system/function/c_system_base.php';

$id = (int) $zbp->Config('AdminColor')->ColorID;
$fontSize = (int) $zbp->Config('AdminColor')->FontSize;

if ($fontSize < 12 || $fontSize > 14) {
    $fontSize = 14;
}
$fontSizePlus2 = $fontSize + 2;

$css = [];

$css[] = '/*admincolor*/';
$css[] = <<<CSS

body {
  font-size: {$fontSize}px;
}

.ui-tooltip,
.arrow_leftmenu:after {
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
}

header,
.header {
  background-color: #3a6ea5;
}
input.button,
input[type='submit'],
input[type='button'] {
  background-color: #3a6ea5;
}
div.theme-now .betterTip img {
  box-shadow: 0 0 10px #3a6ea5;
}
#divMain a,
#divMain2 a {
  color: #1d4c7d;
}
.menu ul li a {
  background-color: rgba(255, 255, 255, 0.85);
}
.menu ul li.on a {
  background-color: #fff;
}
.menu ul li a:hover {
  background-color: #b0cdee;
}
#leftmenu a:hover {
  background-color: #b0cdee !important;
}
div.theme-now {
  background-color: #b0cdee;
  border-color: #3399cc;
}
div.theme-other .betterTip img:hover {
  border-color: #b0cdee;
}
.SubMenu a:hover {
  background-color: #b0cdee;
}
.siderbar-header:hover {
  background-color: #b0cdee;
}
#leftmenu .on a,
#leftmenu #on a:hover {
  background-color: #3399cc !important;
}
input.button,
input[type="submit"],
input[type="button"] {
  border-color: #3399cc;
}
input.button:hover {
  background-color: #3399cc;
}
div.theme-other .betterTip img:hover {
  box-shadow: 0 0 10px #3399cc;
}
.SubMenu {
  border-bottom-color: #3399cc;
}
.SubMenu span.m-now {
  background-color: #3399cc;
}
div #BT_title {
  background-color: #3399cc;
  border-color: #3399cc;
}
a:hover {
  color: #d60000;
}
#divMain a:hover,
#divMain2 a:hover {
  color: #d60000;
}
.imgcheck-on:before {
  background: #3a6ea5;
}
.radio:checked + label:before {
  border: 1px solid #3a6ea5;
}
.radio + label:after {
  background: #3a6ea5;
}
.left #leftmenu span.bgicon {
  background-size: {$fontSize}px;
}
#divMain2 [class^='icon-'] {
  font-size: {$fontSizePlus2}px;
}
CSS;

/*
// appcenter
$css[] = <<<CSS
.tabs {
  border-bottom-color: #3a6ea5 !important;
}
.tabs li a.selected {
  background-color: #3a6ea5 !important;
}
div.heart-vote {
  background-color: #3a6ea5 !important;
}
div.heart-vote ul {
  border-color: #3a6ea5 !important;
}
.install {
  background-color: #3a6ea5 !important;
}
.install:hover {
  background-color: #3399cc !important;
}
input.button {
  background-color: #3a6ea5 !important;
  border-color: #3399cc !important;
}
input.button:hover {
  background-color: #3399cc !important;
}
.themes_body ul li img:hover,
.plugin_body ul li img:hover,
.main_plugin ul li img:hover,
.main_theme ul li img:hover {
  box-shadow: 0 0 10px #3399cc !important;
}
.left_nav h2,
.text h2 {
  color: #3a6ea5 !important;
}
.pagebar span {
  background: #3399cc !important;
  border-color: #3399cc !important;
  color: #fff;
}
.pagebar span.now-page,
.pagebar span:hover {
  background: #fff !important;
  border-color: #fff !important;
  color: #3399cc !important;
}
CSS;
*/

// zbdk
$css[] = <<<'CSS'
#divMain .DIVBlogConfignav ul li a:hover {
  background-color: #3399cc !important;
}
#divMain .DIVBlogConfignav ul li a.clicked {
  background-color: #b0cdee !important;
}
.DIVBlogConfignav {
  background-color: #ededed !important;
}
#divMain .DIVBlogConfigtop {
  background-color: #3399cc !important;
}
#divMain .DIVBlogConfig {
  background-color: #ededed !important;
}
div.bg {
  background: #3a6ea5;
}
div.bg input[type="text"],
input[type="password"] {
  border-color: #3a6ea5;
}

/*AdminColor*/
#admin_color {
  float: left;
  line-height: 2.5em;
  font-size: 0.5em;
  letter-spacing: -0.1em;
}

.pane,
.theme,
form.search {
  padding: 1em;
  position: relative;
  background: #fff;
  margin: 1em 0;
  border-radius: 0.1em;
}
div.theme {
  height: auto;
  margin: 0 2em 2em 0;
}
div.theme-other {
  background: #fff;
}
form.search p {
  padding: 0;
}
td,
th {
  border: none;
  border-right: 1px solid #efefef;
  padding: 0.6em;
}
table {
  border-collapse: collapse;
  background: #ffffff;
  line-height: 120%;
  margin: 0.5em 0 0.5em 0;
  border: none;
  line-height: 1.5em;
}

.blodcolor {
  color: #1d4c7d;
}
.normalcolor {
  color: #3a6ea5;
}
.lightcolor {
  color: #b0cdee;
}
.highcolor {
  color: #3399cc;
}
.anticolor {
  color: #d60000;
}
.bg-blodcolor {
  background-color: #1d4c7d;
}
.bg-normalcolor {
  background-color: #3a6ea5;
}
.bg-lightcolor {
  background-color: #b0cdee;
}
.bg-highcolor {
  background-color: #3399cc;
}
.bg-anticolor {
  background-color: #d60000;
}
CSS;

if (true == $zbp->Config('AdminColor')->HeaderPathUse) {
    if ($zbp->Config('AdminColor')->HeaderPath) {
        $headerPath = $zbp->Config('AdminColor')->HeaderPath;
        $css[] = <<<CSS
header,
.header {
  background: url({$headerPath}) no-repeat center center;
  background-size: cover;
}
div.bg {
  background: url({$headerPath}) no-repeat center center;
  background-size: cover;
}
CSS;
    }
}

if (false == $zbp->Config('AdminColor')->HeaderPathUse && 10 == $id) {
    $css[] = <<<'CSS'
header,
.header {
  background-image: none;
}
CSS;
}

if ($zbp->Config('AdminColor')->LogoPath) {
    $logoPath = $zbp->Config('AdminColor')->LogoPath;
    $css[] = <<<CSS
.logo img {
  background: url({$logoPath}) no-repeat center center;
}
CSS;
}

if ($zbp->Config('AdminColor')->TableShadow) {
    $css[] = <<<'CSS'
table,
.pane,
.theme,
form.search {
  box-shadow: 0 0 0.5em rgba(0, 0, 0, 0.2);
}
CSS;
} else {
    $css[] = <<<'CSS'
table,
.pane,
.theme,
form.search {
  box-shadow: 0 0 0.1em rgba(0, 0, 0, 0.3);
}
CSS;
}

if (9 == $id) {
    $css[] = <<<'CSS'

header,
.header {
  background-color: #17365d;
}
.left #leftmenu li {
  background: #ededed;
}
table.tableBorder,
table.tableFull,
table.table_hover,
table.table_striped {
  background-color: #e3eaf3;
}
table > tbody > tr:nth-of-type(odd) {
  background-color: #b8cce4;
}
td,
th {
  border: none;
  padding: 0.5em;
  border-right: 1px solid #d3e1f2;
}
.content-box .content-box-tabs a.current {
  background-color: #b8cce4;
}
form.search {
  background-color: #e3eaf3;
}
.left #leftmenu li {
  background: #e3eaf3;
}
body {
  background-color: #edf2f8;
}
CSS;
}

if (10 == $id) {
    $css[] = <<<'CSS'

.header .menu {
  height: 60px;
  position: absolute;
  float: left;
  left: 56px;
  top: 0px;
  overflow: hidden;
}
.header .menu ul {
  float: left;
}
header div.logo {
  border-right: 1px solid #444;
}
.header .menu ul li {
  border-right: 1px solid #444;
  line-height: 60px;
  height: 60px;
  margin: 0;
}
.header .menu ul li a {
  float: none;
  padding: 0px 22px;
  font-size: 1.1em;
  color: #fff;
  background: none;
  vertical-align: middle;
}
.header .user {
  z-index: 2;
  background-color: #262f3e;
  width: 220px;
  top: 0;
  height: 60px;
  padding-top: 10px;
}
.header {
  height: 60px;
  margin-bottom: 0px;
}
header,
.header {
  background-color: #262f3e;
}
.left {
  padding-top: 0px;
  float: left;
  height: auto;
  background-position: -30px -2px;
  background-color: #333333;
}
.left #leftmenu {
  border-top: 10px solid #333;
}
.left #leftmenu a {
  color: #fff;
  height: 40px;
  padding-right: 2em;
}
.left #leftmenu li {
  background-color: #333;
  color: #fff;
  height: 40px;
  overflow: hidden;
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
  margin-left: 32px;
}
div.hint {
  margin-top: 0.5em;
}

.left #leftmenu #nav_admincolor2 {
  background-color: #444;
  color: #fff;
  height: 40px;
}

.left #leftmenu #nav_admincolor2 span {
  float: left;
  width: auto;
  height: 36px;
  line-height: 36px;
  text-align: left;
  cursor: pointer;
  margin-left: 60px;
}
.left #leftmenu #nav_admincolor2 a:hover {
  background-color: transparent !important;
}
.left #leftmenu li span {
  color: black;
  filter: invert(0.8);
  font-weight: bold;
}
.left #leftmenu li.on span {
  color: white;
  filter: none;
  font-weight: bold;
}
.left #leftmenu li a:hover span {
  color: black;
  filter: invert(0.1);
  font-weight: bold;
}
.left #leftmenu li.on a:hover span {
  color: white;
  filter: invert(0.1);
  font-weight: bold;
}
body {
  background: url("images/color10bg.png") repeat-y 0 top;
}
body[class~=body-login] {
  background: none;
}
header div.logo {
  background-color: transparent;
  width: 62px;
  height: 60px;
  padding: 0 0 0 0;
  left: 0px;
  float: left;
}
header div.logo img {
  width: 60px;
  height: 60px;
  background-position: -5px -5px;
}
.pagebar a {
  border: 1px solid white;
}
body[class~=body-login],
body[class~=body-error],
body[class~=short] {
  background: none;
}
body[class~=body-login] div.bg,
body[class~=body-error] div.bg,
body[class~=short] div.bg {
  background: #3399cc;
}
body[class~=body-login] input[type="text"],
body[class~=body-login] input[type="password"] {
  border-color: #3399cc;
}
body[class~=body-login] input.button,
input[type="submit"],
input[type="button"] {
  border-color: #3399cc;
}
body[class~=body-login] input.button,
body[class~=body-login] input[type="submit"],
body[class~=body-login] input[type="button"] {
  background-color: #3399cc;
}
body[class~=body-login] input[type="text"],
body[class~=body-login] input[type="password"] {
  border-color: #3399cc;
}
body[class~=body-login] input.button:hover {
  background-color: #3a6ea5;
}

header div.logo img { background: url("images/logo.svg"); }

.left #leftmenu span.bgicon { background-position: 0px 13px; }
CSS;
}

$AdminColor_Old_Colors = $AdminColor_Colors = [];

$AdminColor_Old_Colors['Blod'] = '#1d4c7d';
$AdminColor_Old_Colors['Normal'] = '#3a6ea5';
$AdminColor_Old_Colors['Light'] = '#b0cdee';
$AdminColor_Old_Colors['High'] = '#3399cc';
$AdminColor_Old_Colors['Anti'] = '#d60000';

$AdminColor_Colors['Blod'] = $zbp->Config('AdminColor')->BlodColor;
$AdminColor_Colors['Normal'] = $zbp->Config('AdminColor')->NormalColor;
$AdminColor_Colors['Light'] = $zbp->Config('AdminColor')->LightColor;
$AdminColor_Colors['High'] = $zbp->Config('AdminColor')->HighColor;
$AdminColor_Colors['Anti'] = $zbp->Config('AdminColor')->AntiColor;

$c = implode('', $css);

foreach ($GLOBALS['hooks']['Filter_Plugin_AdminColor_CSS_Pre'] as $fpname => &$fpsignal) {
    $fpname($AdminColor_Colors, $c);
}

$c = str_ireplace($AdminColor_Old_Colors, $AdminColor_Colors, $c);

$m = 'W/'.md5($c);

header('Content-Type: text/css; Charset=utf-8');
header('Etag: '.$m);

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $m) {
    if (isset($zbp->option['ZC_JS_304_ENABLE']) && $zbp->option['ZC_JS_304_ENABLE']) {
        SetHttpStatusCode(304);

        exit;
    }
}

echo $c;

exit();
