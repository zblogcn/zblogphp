<?php
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
class duoshuo_class
{
	public $cfg;
	public $is_init = false;
	public $version = '1.0';
	public $system_version = '';
	public $duoshuo_path = '';
	
	function init()
	{
		global $zbp;
		global $blogversion;
		$this->system_version = $blogversion;
		$this->duoshuo_path = $zbp->host . 'zb_users/plugin/duoshuo/';
		//$cfg = $zbp->config('duoshuo');
		$is_init = true;
	}
	
	
	function export_connect_url()
	{
		if (!$this->is_init) $this->init();
		
		global $zbp;
		
		$param = array();
		$param['name'] = $zbp->name;
		$param['description'] = $zbp->subname;
		$param['url'] = $zbp->host;
		$param['siteurl'] = $zbp->host;
		$param['system_version'] = $this->system_version;
		$param['plugin_version'] = $this->version;
		$param['system'] = 'zblogphp';
		$param['callback'] = $this->duoshuo_path . 'event.php?act=callback';
		$param['user_key'] = $zbp->user->ID;
		$param['user_name'] = $zbp->user->Name;
		$param['admin_email'] = $zbp->user->Email;
		$param['local_api_url'] = $this->duoshuo_path . 'event.php?act=api';
		
		$str = '';
		
		foreach($param as $t_name => $t_value)
		{
			$str .= $t_name . '=' . urlencode($t_value) . '&';
		}

		return 'http://duoshuo.com/connect-site/?' . $str;
	}
	
	//plugin interface
	function view_post_template(&$template)
	{
		if (!$this->is_init) $this->init();
		$s = $zbp->config('duoshuo')->commoncode;
		$template->SetTags('socialcomment',$s);
	}
}


?>