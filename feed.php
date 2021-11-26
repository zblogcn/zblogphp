<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 * @version
 */
require 'zb_system/function/c_system_base.php';

$zbp->action = 'feed';
$zbp->Load();

HookFilterPlugin('Filter_Plugin_Feed_Begin');

ViewIndex();

HookFilterPlugin('Filter_Plugin_Feed_End');
