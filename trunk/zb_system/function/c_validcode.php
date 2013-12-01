<?php
require 'c_system_base.php';
$zbp->Load();
ob_clean();

$_vc = new ValidateCode();
$_vc->GetImg();
setcookie("ZBPValidCode", $_vc->GetCode(), time()+60);