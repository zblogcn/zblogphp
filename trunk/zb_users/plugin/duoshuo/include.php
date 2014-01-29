<?php
require 'jwt.php';
require 'duoshuo.class.php';

RegisterPlugin("duoshuo","ActivePlugin_duoshuo");
function ActivePlugin_duoshuo()
{
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','$duoshuo->view_post_template()');
}
function InstallPlugin_duoshuo()
{
}
function UninstallPlugin_duoshuo()
{
}

$duoshuo = new duoshuo_class();


?>