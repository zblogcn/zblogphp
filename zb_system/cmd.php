<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 */

// 标记为 CMD 运行模式
define('ZBP_IN_CMD', true);

if ((isset($_REQUEST['act']) && 'ajax' == $_REQUEST['act']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 0 == strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest'))) {
    define('ZBP_IN_AJAX', true);
}

require 'function/c_system_base.php';

$action = GetVars('act', 'GET');

$zbp->Load();

if (!$zbp->CheckRights($zbp->action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);

    exit();
}

HookFilterPlugin('Filter_Plugin_Cmd_Begin');

switch ($zbp->action) {
    case 'login':
        Redirect_cmd_from_args_with_loggedin(GetVars('redirect', 'GET'));
        if ($zbp->CheckRights('admin')) {
            Redirect_cmd_end('admin/index.php?act=admin');
        }
        if (empty($zbp->user->ID) && GetVars('redirect', 'GET')) {
            setcookie('redirect', GetVars('redirect', 'GET', ''), 0, $zbp->cookiespath);
        }
        Redirect_cmd_end('login.php');

        break;

    case 'logout':
        CheckIsRefererValid();
        Logout();
        Redirect_cmd_end('../');

        break;

    case 'admin':
        Redirect_cmd_end('admin/index.php?act=admin');

        break;

    case 'verify':
        if (VerifyLogin(true, false, false)) {
            Redirect_cmd_from_args_with_loggedin(GetVars('redirect', 'COOKIE'));
            Redirect_cmd_end('admin/index.php?act=admin');
        } else {
            Redirect_cmd_end('../');
        }

        break;

    case 'search':
        Redirect_cmd_to_search();

        break;

    case 'cmt':
        $die = false;
        if (GetVars('isajax', 'POST')) {
            // 兼容老版本的评论前端
            Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'RespondError', PLUGIN_EXITSIGNAL_RETURN);
            $die = true;
        } elseif ('json' == GetVars('format', 'POST')) {
            // 1.5之后的评论以json形式加载给前端
            Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'JsonError4ShowErrorHook', PLUGIN_EXITSIGNAL_RETURN);
            $die = true;
        }
        PostComment();
        $zbp->BuildModule();
        $zbp->SaveCache();

        if ($die) {
            exit;
        }
            Redirect_cmd_end(GetVars('HTTP_REFERER', 'SERVER'));

        break;

    case 'getcmt':
        ViewComments((int) GetVars('postid', 'GET'), (int) GetVars('page', 'GET'));

        break;

    case 'ArticleEdt':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'ArticleDel':
        CheckIsRefererValid();
        DelArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ArticleMng');

        break;

    case 'ArticleMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'ArticlePst':
        $zbp->csrfExpiration = 48;
        CheckIsRefererValid();
        PostArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        echo '<script>localStorage.removeItem("zblogphp_article_" + decodeURIComponent(' . urlencode(GetVars('ID', 'POST')) . '));</script>';
        Redirect_cmd_end_by_script('cmd.php?act=ArticleMng');

        break;

    case 'PageEdt':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'PageDel':
        CheckIsRefererValid();
        DelPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=PageMng');

        break;

    case 'PageMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        // Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'PagePst':
        $zbp->csrfExpiration = 48;
        CheckIsRefererValid();
        PostPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        echo '<script>localStorage.removeItem("zblogphp_article_" + decodeURIComponent(' . urlencode(GetVars('ID', 'POST')) . '));</script>';
        Redirect_cmd_end_by_script('cmd.php?act=PageMng');

        break;

    case 'CategoryMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'CategoryEdt':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/category_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/category_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'CategoryPst':
        CheckIsRefererValid();
        PostCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=CategoryMng');

        break;

    case 'CategoryDel':
        CheckIsRefererValid();
        DelCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=CategoryMng');

        break;

    case 'CommentDel':
        CheckIsRefererValid();
        DelComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER['HTTP_REFERER']);

        break;

    case 'CommentChk':
        CheckIsRefererValid();
        CheckComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER['HTTP_REFERER']);

        break;

    case 'CommentBat':
        CheckIsRefererValid();
        BatchComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER['HTTP_REFERER']);

        break;

    case 'CommentMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'MemberMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'MemberEdt':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'MemberNew':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'MemberPst':
        CheckIsRefererValid();
        $mem = PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
        //判断及提前跳转
        if (isset($_POST['Password'])
            && $mem->ID == $zbp->user->ID
            && !defined('ZBP_IN_AJAX')
            && !defined('ZBP_IN_API')
        ) {
            Redirect_cmd_end($zbp->host . 'zb_system/cmd.php?act=login');
        }
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=MemberMng');

        break;

    case 'MemberDel':
        CheckIsRefererValid();
        if (DelMember()) {
            $zbp->BuildModule();
            $zbp->SaveCache();
            $zbp->SetHint('good');
        } else {
            $zbp->SetHint('bad');
        }
        Redirect_cmd_end('cmd.php?act=MemberMng');

        break;

    case 'UploadMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'UploadPst':
        CheckIsRefererValid();
        if (PostUpload()) {
            $zbp->SetHint('good');
        } else {
            $zbp->SetHint('bad');
        }
        Redirect_cmd_end('cmd.php?act=UploadMng');

        break;

    case 'UploadDel':
        CheckIsRefererValid();
        DelUpload();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=UploadMng');

        break;

    case 'TagMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'TagEdt':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/tag_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/tag_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'TagPst':
        CheckIsRefererValid();
        PostTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=TagMng');

        break;

    case 'TagDel':
        CheckIsRefererValid();
        DelTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=TagMng');

        break;

    case 'PluginMng':
        if (GetVars('install', 'GET')) {
            InstallPlugin(GetVars('install', 'GET'));
            $zbp->BuildModule();
            $zbp->SaveCache();
        }
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        // Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'PluginDis':
        CheckIsRefererValid();
        $disableResult = DisablePlugin(GetVars('name', 'GET'));
        if (false == $disableResult) {
            $zbp->SetHint('bad');
        } else {
            $zbp->BuildModule();
            $zbp->SaveCache();
            $zbp->SetHint('good');
        }
        Redirect_cmd_end('cmd.php?act=PluginMng');

        break;

    case 'PluginEnb':
        CheckIsRefererValid();
        $install = '&install=';
        $install .= EnablePlugin(GetVars('name', 'GET'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=PluginMng' . $install);

        break;

    case 'ThemeMng':
        if (GetVars('install', 'GET')) {
            InstallPlugin(GetVars('install', 'GET'));
        }
        if (null !== GetVars('install', 'GET')) {
            $zbp->BuildTemplate();
        }
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        // Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'ThemeSet':
        CheckIsRefererValid();
        $install = '&install=';
        $install .= SetTheme(GetVars('theme', 'POST'), GetVars('style', 'POST'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ThemeMng' . $install);

        break;

    case 'SidebarSet':
        CheckIsRefererValid();
        SetSidebar();
        $zbp->BuildModule();
        $zbp->SaveCache();

        break;

    case 'ModuleEdt':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/module_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/module_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'ModulePst':
        CheckIsRefererValid();
        PostModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ModuleMng');

        break;

    case 'ModuleDel':
        CheckIsRefererValid();
        DelModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ModuleMng');

        break;

    case 'ModuleMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'SettingMng':
        if (2 == $zbp->option['ZC_MANAGE_UI']) {
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        } else {
            Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        //Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;

    case 'SettingSav':
        CheckIsRefererValid();
        $oldHost = $zbp->option['ZC_BLOG_HOST'];
        SaveSetting();
        $zbp->BuildModule();
        $zbp->SaveCache();
        //判断及提前跳转
        if (true == $zbp->option['ZC_PERMANENT_DOMAIN_ENABLE']) {
            if ($oldHost != $zbp->option['ZC_BLOG_HOST']) {
                Redirect_cmd_end($zbp->option['ZC_BLOG_HOST'] . 'zb_system/cmd.php?act=login');
            }
        }
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=SettingMng');

        break;

    case 'PostBat':
        BatchPost(GetVars('type', 'GET'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER['HTTP_REFERER']);

        break;

    case 'RewriteMng':
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            SaveRewrite();
            Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        }
        Redirect_cmd_end('admin2/index.php?' . GetVars('QUERY_STRING', 'SERVER'));

        break;

    case 'misc':
        include './function/c_system_misc.php';
        ob_clean();

        $miscType = GetVars('type', 'GET');
        $miscType = str_replace(['<', '>', '&', ' ', '/', '"', "'"], '', $miscType);
        $miscType = ($miscType === 'php' . 'info') ? 'php_zbp_info' : $miscType;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Misc_Begin'] as $fpname => &$fpsignal) {
            $fpname($miscType);
        }

        $function = 'misc_' . $miscType;
        $function();

        break;

    case 'AiChat':
        if (!$zbp->CheckRights('ArticleEdt')) {
            echo json_encode(['error' => 'No Permission']);

            exit();
        }

        $prompt = GetVars('prompt', 'POST');
        if (empty($prompt)) {
            echo json_encode(['error' => 'Empty Prompt']);

            exit();
        }

        $apiKey = $zbp->option['ZC_TEXT_AI_API_KEY'];
        if (empty($apiKey)) {
            echo json_encode(['error' => 'API Key is missing']);

            exit();
        }

        $url = $zbp->option['ZC_TEXT_AI_API_URL'];

        $systemPrompt = null;
        if (empty($systemPrompt)) {
            $systemPrompt = "1. 系统角色设定 (System Role)\n你是一位资深的内容创作者和写作专家。你的任务是根据用户需求创作高质量的文章。你需要具备良好的文字功底、丰富的知识储备和专业的写作技巧，能够针对不同主题撰写出吸引读者、内容丰富且结构清晰的文章。\n\n2. 文章结构要求 (Output Structure)\n请按照以下通用格式组织文章：\n\n标题：创建一个吸引人的标题，能准确概括文章主题并引起读者兴趣。\n\n引言：简要介绍文章主题，说明其重要性或与读者的相关性。\n\n主体内容：\n- 提供详细的信息、论据或案例支持\n- 保持逻辑清晰，段落间过渡自然\n- 使用小标题或要点来组织复杂信息\n\n结论：总结主要观点，并提供实用的建议或启发性的思考。\n\n3. 语气与风格 (Tone & Style)\n- 根据主题调整语气（正式、轻松、专业、亲和等）\n- 语言清晰易懂，避免不必要的专业术语\n- 内容要有实际价值，对读者有用处\n- 保持客观中立，除非特别要求表达观点\n- 适当使用例子、类比或故事来增强可读性";
        }

        $data = [
            'model' => 'qwen-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
        } else {
            echo $response;
        }

        curl_close($ch);

        exit();

    case 'ajax':
        foreach ($GLOBALS['hooks']['Filter_Plugin_Cmd_Ajax'] as $fpname => &$fpsignal) {
            $fpname(GetVars('src', 'GET'));
        }

        break;

    default:
        // code...
        break;
}

HookFilterPlugin('Filter_Plugin_Cmd_End');
