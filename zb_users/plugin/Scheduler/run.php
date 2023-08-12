<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();

if (!IS_CLI && ($zbp->Config('Scheduler')->run_token != GetVars('run_token', 'GET'))) {
    die('You have no permission to visit this page.');
}

Scheduler::run();
