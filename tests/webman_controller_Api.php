<?php
namespace app\controller;

use support\Request;

class Api
{
    public function __construct() {
        print "ZBlogPHP::Load()\n";
        $zbp = \ZBlogPHP::GetInstance();
        $zbp->Load();
    }

    public function index(Request $request)
    {
        $zbp = \ZBlogPHP::GetInstance();
        http_request_convert_to_global($request, \Webman\App::connection());
        RunTime_Begin();

        ClearFilterPlugin('Filter_Plugin_Zbp_ShowError');

        ApiCheckEnable();

        foreach ($GLOBALS['hooks']['Filter_Plugin_API_Begin'] as $fpname => &$fpsignal) {
            $fpname();
        }

        ApiCheckAuth(false, 'api');

        ApiCheckLimit();

        $GLOBALS['mods'] = array();
        $GLOBALS['mods_allow'] = array();
        $GLOBALS['mods_disallow'] = array();
        $GLOBALS['mod'] = GetVars('mod', 'GET');
        $GLOBALS['act'] = GetVars('act', 'GET');

        // 载入系统和应用的 mod
        ApiLoadMods($GLOBALS['mods']);

        //进行Api白名单和黑名单的检查
        ApiCheckMods($GLOBALS['mods_allow'], $GLOBALS['mods_disallow']);

        ApiLoadPostData();

        ApiVerifyCSRF();

        // 派发 API
        ob_start();
        $r = ApiDispatch($GLOBALS['mods'], $GLOBALS['mod'], $GLOBALS['act']);
        ob_end_clean();

        return $r;

    }

}
