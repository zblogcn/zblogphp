<?php
require 'jwt.php';
require 'duoshuo.class.php';

RegisterPlugin("duoshuo","ActivePlugin_duoshuo");
function ActivePlugin_duoshuo()
{
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','duoshuo_view_post_template');
}
function InstallPlugin_duoshuo()
{
}
function UninstallPlugin_duoshuo()
{
}

$duoshuo = new duoshuo_class();

function duoshuo_view_post_template(&$template)
{

	$r = '<!-- Duoshuo Comment BEGIN -->';
	$r .= '<div class="ds-thread" data-category="<#article/category/id#>" data-thread-key="<#article/id#>" ';
	$r .= 'data-title="<#article/title#>" data-author-key="<#article/author/id#>" data-url=""></div>';
	$r .= '<!-- Duoshuo Comment END -->';

	$post=&$template->GetTags('article');

	$r=str_replace('<#article/category/id#>',$post->Category->ID,$r);
	$r=str_replace('<#article/id#>',$post->ID,$r);
	$r=str_replace('<#article/title#>',htmlspecialchars($post->Title),$r);
	$r=str_replace('<#article/author/id#>',$post->Author->ID,$r);
	
	$template->SetTags('socialcomment',$r);
}
?>