<?php
namespace app\controller;

use support\Request;

class Index
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

        //\ZBlogPHP::ThrowException('index');

        ClearFilterPlugin('Filter_Plugin_Zbp_ShowError');
        ob_start();
        ViewAuto();
        $r = ob_get_clean();
        return $r;

    }

}
