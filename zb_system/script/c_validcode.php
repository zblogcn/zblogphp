<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require '../function/c_system_base.php';
$zbp->Load();
ob_clean();

$_vc = new ValidateCode();
$_vc->GetImg();
setcookie("ZBPValidCode", $_vc->GetCode(), time()+60);