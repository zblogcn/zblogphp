<?php

require '../function/c_system_base.php';

// 这里不需要其他文件引入，设置 ismanage 即可
$zbp->ismanage = true;

$zbp->Load();
if ($zbp->CheckRights('admin')) {
    Redirect302("{$zbp->cmdurl}?act=admin");
}

$zbp->template_admin->SetTags('lang', $lang);
$zbp->template_admin->SetTags('blogname', $blogname);
$zbp->template_admin->Display('login');

RunTime();
