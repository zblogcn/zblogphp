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

$zbp->ShowValidCode(GetVars('id','GET'));