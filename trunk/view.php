<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {Redirect('./zb_install');}

$zbp->LoadData();

ViewPost(GetVars('id','GET'),GetVars('alias','GET'));

RunTime();
?>