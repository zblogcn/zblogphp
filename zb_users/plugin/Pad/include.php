<?php

//注册插件
RegisterPlugin("Pad", "ActivePlugin_Pad");

function ActivePlugin_Pad()
{
    Add_Filter_Plugin('Filter_Plugin_Index_Begin', 'Pad_Main');
    Add_Filter_Plugin('Filter_Plugin_Cmd_Begin', 'Pad_Template');
}

function Pad_Template()
{
    global $zbp, $action;
    if (GetVars('mod', 'GET') == 'pad') {
        $zbp->template->SetPath($zbp->usersdir . 'plugin/Pad/compile/');
    }
}

function Pad_Main()
{
    global $zbp;

    if (GetVars('mod', 'GET') == 'pad') {
        if (GetVars('act', 'GET') == 'logout') {
            Pad_Logout();
        }

        if (GetVars('act', 'GET') == 'login') {
            Pad_Login();
        }

        if (GetVars('act', 'GET') == 'verify') {
            Pad_Verify();
        }

        if (isset($_GET['q'])) {
            Pad_Search();
        }

        Pad_Export();
        die;
    }
    if (GetVars('mod', 'GET') == 'pc') {
        return;
    }

    $Pad_List = '/android|iphone|ipad|windows\sphone|kindle|gt\-p|gt\-n|rim\stablet|opera|meego/i';
    $UA = GetGuestAgent();

    if (CheckRegExp($UA, $Pad_List) == true) {
        Pad_Export();
    }
}

function Pad_Logout()
{
    Logout();
    Redirect('?mod=pad');
    die();
}

function Pad_Verify()
{
    $_POST['username'] = $_POST['username'];
    $_POST['password'] = md5($_POST['password']);
    if (VerifyLogin()) {
        Redirect('?mod=pad');
    }

    die();
}

function Pad_Login()
{
    global $zbp, $lang;

    Pad_Pre();

    $article = new Post();
    $article->Title = '登录';
    $article->IsLock = true;
    $article->Type = ZC_POST_TYPE_PAGE;

    $article->Content .= '<form method="post" action="?mod=pad&act=verify">';

    $article->Content .= '<p>名称：<input type="text" name="username" id="username" value="" /></p>';
    $article->Content .= '<p>密码：<input type="password" name="password" id="password" value="" /></p>';
    $article->Content .= '<input type="hidden" name="savedate" id="savedate" value="0" />';
    $article->Content .= '<p><input type="submit" value="登录" /></p>';
    $article->Content .= '</form>';

    $zbp->template->SetTags('title', $article->Title);
    $zbp->template->SetTags('article', $article);
    $zbp->template->SetTags('type', $article->type = 0 ? 'article' : 'page');
    $zbp->template->SetTemplate($article->Template);

    $zbp->template->Display();

    die();
}

function Pad_Pre()
{
    global $zbp;

    $zbp->option['ZC_STATIC_MODE'] = 'ACTIVE';
    $zbp->option['ZC_ARTICLE_REGEX'] = '{%host%}?mod=pad&id={%id%}';
    $zbp->option['ZC_PAGE_REGEX'] = '{%host%}?mod=pad&id={%id%}';
    $zbp->option['ZC_CATEGORY_REGEX'] = '{%host%}?mod=pad&cate={%id%}&page={%page%}';
    $zbp->option['ZC_AUTHOR_REGEX'] = '{%host%}?mod=pad&auth={%id%}&page={%page%}';
    $zbp->option['ZC_TAGS_REGEX'] = '{%host%}?mod=pad&tags={%id%}&page={%page%}';
    $zbp->option['ZC_DATE_REGEX'] = '{%host%}?mod=pad&date={%date%}&page={%page%}';
    $zbp->option['ZC_INDEX_REGEX'] = '{%host%}?mod=pad&page={%page%}';

    $zbp->template->SetPath($zbp->usersdir . 'plugin/Pad/compile/');

    $sidebar_pad = array();

    $mod = new Module();
    $mod->Name = '控制面板';
    $mod->Type = 'ul';
    $s = '';
    if ($zbp->user->ID > 0) {
        $mod->Name = '欢迎' . $zbp->user->Name;
        $s .= '<li><a href="?mod=pad&amp;act=logout">退出登录</a></li>';
    } else {
        $s .= '<li><a href="?mod=pad&amp;act=login">登录管理</a></li>';
    }
    $mod->Content = $s;
    $sidebar_pad[] = $mod;

    $mod = new Module();
    $s = '';
    foreach ($zbp->categorys as $key => $value) {
        $s .= '<li><a href="' . $value->Url . '">' . $value->Name . '</a></li>';
    }
    $mod->Type = 'ul';
    $mod->Name = '分类';

    $mod->Content = $s;
    $sidebar_pad[] = $mod;

    $mod = new Module();
    $mod->Name = '搜索';
    $mod->Content = '<form name="search" method="get" action="#"><input type="hidden" name="mod" value="pad" /><input type="text" name="q" size="11" /> <input type="submit" value="搜索" /></form>';
    $sidebar_pad[] = $mod;

    $zbp->template->SetTags('sidebar_pad', $sidebar_pad);
}

function Pad_Export()
{
    global $zbp;

    if ($zbp->currenturl == $zbp->cookiespath ||
        $zbp->currenturl == $zbp->cookiespath . 'index.php') {
        Pad_Pre();
        $zbp->template->SetTemplate('index');
        ViewList(null, null, null, null, null);
        die;
    } elseif (isset($_GET['id']) || isset($_GET['alias'])) {
        Pad_Pre();
        $zbp->template->SetTemplate('single');
        ViewPost(GetVars('id', 'GET'), GetVars('alias', 'GET'));
        die;
    } elseif (isset($_GET['page']) || isset($_GET['cate']) || isset($_GET['auth']) || isset($_GET['date']) || isset($_GET['tags'])) {
        Pad_Pre();
        $zbp->template->SetTemplate('index');
        ViewList(GetVars('page', 'GET'), GetVars('cate', 'GET'), GetVars('auth', 'GET'), GetVars('date', 'GET'), GetVars('tags', 'GET'));
        die;
    } elseif (GetVars('mod', 'GET') == 'pad') {
        Pad_Pre();
        $zbp->template->SetTemplate('index');
        ViewList(null, null, null, null, null);
        die;
    }
}

function Pad_Search()
{
    global $zbp, $lang;

    Pad_Pre();

    $action = 'search';

    if (!$zbp->CheckRights($action)) {
        Redirect('?mod=pad');
    }

    $article = new Post();
    $article->Title = $lang['msg']['search'] . '“' . GetVars('q', 'GET') . '”';
    $article->IsLock = true;
    $article->Type = ZC_POST_TYPE_PAGE;

    $w = array();
    $w[] = array('=', 'log_Type', '0');
    $s = trim(GetVars('q', 'GET'));
    if ($s) {
        $w[] = array('search', 'log_Content', 'log_Intro', 'log_Title', $s);
    } else {
        Redirect('./');
    }

    $array = $zbp->GetArticleList(
        '',
        $w,
        array('log_PostTime' => 'DESC'),
        array($zbp->searchcount),
        null
    );

    foreach ($array as $a) {
        $article->Content .= '<p><br/>' . $a->Title . '<br/>';
        $article->Content .= '<a href="' . $a->Url . '">' . $a->Url . '</a></p>';
    }

    $zbp->template->SetTags('title', $article->Title);
    $zbp->template->SetTags('article', $article);
    $zbp->template->SetTags('type', $article->type = 0 ? 'article' : 'page');
    $zbp->template->SetTemplate($article->Template);

    $zbp->template->Display();

    die();
}
