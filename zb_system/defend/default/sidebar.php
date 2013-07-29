{php}

foreach($sidebars as $module){
	include $this->GetTemplate('module');
}

{/php}

{foreach $sidebars as $module}
{template:module}
{/foreach}