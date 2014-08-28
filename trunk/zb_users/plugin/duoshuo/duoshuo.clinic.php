<?php
/**
 * Z-BlogPHP Clinic check template permission
 * @package template-permission
 * @subpackage template-permission.php
 */

class duoshuo_sync extends clinic {
	
	/**
	 * Build queue
	 * @return null
	 */
	public function get_queue() {
		$this->set_queue('check_shortname', '');	
		$this->set_queue('check_table', '');
		$this->set_queue('check_network', 'api.duoshuo.com');		
		$this->set_queue('check_network', 'api.duoshuo.org');		
		$this->set_queue('check_network', '118.144.80.201');		
		$this->set_queue('success', '');		
	}

	public function check_shortname() {
		global $zbp;

		if (!isset($zbp->configs['duoshuo'])) {
			$this->output('error' ,'多说配置未写入，将重新安装多说插件');
			$this->set_queue('reintall_duoshuo', '');
			return;
		}

		if (!$zbp->Config('duoshuo')->HasKey('short_name')) {
			$this->output('error' ,'多说配置未写入，将重新安装多说插件');
			$this->set_queue('reintall_duoshuo', '');
			return;
		}

		if ($zbp->Config('duoshuo')->short_name == '') {
			$this->output('error', '请进入后台重新配置多说插件');
			return;
		}

		$this->output('success', '配置检测一切正常');
		return;

	}

	public function check_table() {

		global $zbp;

		$sql = $zbp->db->sql->Select(
			$GLOBALS['table']['plugin_duoshuo_comment'],
			array('ds_cmtid'),
			null,
			null,
			1,
			null
		);
		$array = $zbp->db->Query($sql);
		if (count($array) > 0) {
			$this->output('success', 'Comment表一切正常');
		}
		else {
			$this->output('error' ,'Comment表出错，将重新安装多说插件');
			$this->set_queue('reintall_duoshuo', '');
			return;
		}


		$sql = $zbp->db->sql->Select(
			$GLOBALS['table']['plugin_duoshuo_members'],
			array('ds_memid'),
			null,
			null,
			1,
			null
		);
		$array = $zbp->db->Query($sql);
		if (count($array) > 0) {
			$this->output('success', 'Member表一切正常');
		}
		else {
			$this->output('error' ,'Member表出错，将重新安装多说插件');
			$this->set_queue('reintall_duoshuo', '');
			return;
		}


	}

	public function check_network($url) {

		global $zbp;
		$ajax = Network::Create();

		if(!$ajax) {
			$this->output('error' ,'您的主机不能联网，不能使用同步功能');
			return;
		}
		$start = $this->get_time();
		$ajax->open('GET', 'http://' . $url . '/threads/counts.jsonp?short_name=' . $zbp->Config('duoshuo')->short_name . '&callback=duoshuo_callback');
		@$ajax->send();
		$end = $this->get_time();
		$val = round(($end - $start) * 1000, 2);
		
		if ($ajax->status == 200) {
			$this->output('success', '服务器连接'. $url . '成功，用时' . $val . '毫秒');
		}
		else {
			$this->output('error', '服务器连接'. $url . '失败，用时' . $val . '毫秒');
		}


	}

	private function get_time() {
		$time = microtime(); 
		list($s1, $s2) = explode(" ", $time);
		return (float)$s1 + (float)$s2;
	}
	
	public function reintall_duoshuo() {
		InstallPlugin_duoshuo();
		$this->output('success', '修复完成；如果还有问题，请完全删除插件，并重新到应用中心下载并安装最新版的多说插件，谢谢。');
	}

	public function success() {
		$this->output('success', '诊断完成；如果还有问题，请完全删除插件，并重新到应用中心下载并安装最新版的多说插件，谢谢。');
	}
	
}

