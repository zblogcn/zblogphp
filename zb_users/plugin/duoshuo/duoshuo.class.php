<?php
class duoshuo_class {
	public $cfg;
	public $is_init = false;
	public $version = '1.0';
	public $system_version = '';
	public $duoshuo_path = '';
	public $token = '';
	public $db = array();
	public $cc_thread_key = '';
	public $url = array();
	public $api = null;

	function init() {
		if ($this->is_init) {
			return true;
		}

		global $zbp;
		global $blogversion;
		$this->system_version = $blogversion;
		$this->duoshuo_path = $zbp->host . 'zb_users/plugin/duoshuo/';
		$this->cfg = &$zbp->configs['duoshuo'];

		if (isset($this->cfg)) {
			if (!class_exists('JWT')) {
				require_once DUOSHUO_PATH . '/jwt.php';
			}

			$this->token = JWT::encode(array(
				'short_name' => $this->cfg->short_name,
				'user_key' => $zbp->user->ID,
				'name' => $zbp->user->Name,
			), $this->cfg->secret, 'HS256');
		} else {

		}
		$this->db['comment'] = '%pre%plugin_duoshuo_comment';
		$this->db['members'] = '%pre%plugin_duoshuo_members';
		$this->url = array(
			'posts' => array(
				'import' => 'posts/import.json',
				'create' => 'posts/create.json',
			),
			'threads' => array(
				'import' => 'threads/import.json',
				'count' => 'threads/counts.json',
			),
			'users' => array(
				'import' => 'users/import.json',
				'profile' => 'users/profile.json',
			),
			'sites' => array(
				'join' => 'sites/join.json',
				'listTopThreads' => 'sites/listTopThreads.json',
			),
			'log' => array(
				'list' => 'log/list.json',
			),

		);
		$is_init = true;
		$this->api = new duoshuo_api();
		return true;
	}

	function export_admin($type) {
		return 'http://' . $this->cfg->short_name . '.duoshuo.com/admin' . ($type != '' ? '/' . $type : '') . '/?jwt=' . $this->token;
	}

	function export_submenu($id) {
		if (!$this->is_init) {
			$this->init();
		}

		$submenu = new duoshuo_submenu();
		$submenu->add(array('id' => '', 'url' => 'main.php', 'float' => 'left', 'title' => '评论管理', 'target' => 'self'));
		$submenu->add(array('id' => 'users', 'url' => 'main.php?act=users', 'float' => 'left', 'title' => '用户管理', 'target' => 'self'));
		$submenu->add(array('id' => 'statistics', 'url' => 'main.php?act=statistics', 'float' => 'left', 'title' => '数据统计', 'target' => 'self'));
		$submenu->add(array('id' => 'settings', 'url' => 'main.php?act=settings', 'float' => 'left', 'title' => '多说设置', 'target' => 'self'));
		$submenu->add(array('id' => 'setting', 'url' => 'main.php?act=setting', 'float' => 'left', 'title' => '高级选项', 'target' => 'self'));
		$submenu->add(array('id' => 'export', 'url' => 'export.php', 'float' => 'left', 'title' => '导入导出', 'target' => 'self'));
		$submenu->add(array('id' => 'manage', 'url' => 'http://' . ($this->cfg->short_name != '' ? $this->cfg->short_name : 'www') . '.duoshuo.com', 'float' => 'right', 'title' => '多说后台', 'target' => 'blank'));
		return $submenu->export($id);
	}

	function export_connect_url() {
		if (!$this->is_init) {
			$this->init();
		}

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

		foreach ($param as $t_name => $t_value) {
			$str .= $t_name . '=' . urlencode($t_value) . '&';
		}
		return 'http://duoshuo.com/connect-site/?' . $str;
	}

	function check_spider() {
		if (!$this->is_init) {
			$this->init();
		}

		if (!$this->cfg->seo_enabled) {
			return false;
		}

		$spider = "/(baidu|google|bing|soso|360|Yahoo|msn|Yandex|youdao|Jike|Ahrefs|ezooms|Easou|sogou)(bot|spider|Transcoder|slurp)/i";
		return (bool) preg_match($spider, GetVars("HTTP_USER_AGENT", "SERVER"));
	}

	function get_footer_js() {
		if (!$this->is_init) {
			$this->init();
		}

		$data = '';
		if ($this->cfg->cron_sync_enabled == 'async') {
			$data .= '<script language="javascript" type="text/javascript" src="' . $this->duoshuo_path . 'event.php?act=api_async&' . rand() . '"></script>';
		}

		if ($this->cc_thread_key != "") {
			$data .= '<script type="text/javascript" src="http://api.duoshuo.com/threads/counts.jsonp';
			$data .= '?short_name=' . urlencode($this->cfg->short_name) . '&threads=' . urlencode($this->cc_thread_key);
			$data .= '&callback=duoshuo_callback&rnd=' . rand() . '"></script>';
			//评论数修正		 批量获取
		}

		$data = '<script type="text/javascript">var duoshuo_callback = function(data){if(data.response){jQuery.each(data.response, function(id, object){jQuery("[duoshuo-id="+id+"]").html(object.comments);})}};var duoshuoQuery = {short_name:"' . $this->cfg->short_name . '"};</script><script type="text/javascript" src="http://static.duoshuo.com/embed.js"></script>' . $data;

		return $data;
	}
}

class duoshuo_submenu {

	public static $html = '';
	private $actions = array();
	private $template = '';

	function __construct() {
		global $zbp;
		$this->template = '<a href="$url" target="_$target"><span class="m-$float$light">$title</span></a>';
	}

	function add(array $ary) {
		global $zbp;
		if (!isset($ary['url'])) {
			throw new Exception('Param \'url\' is empty!');
		}

		if (!isset($ary['float'])) {
			throw new Exception('Param \'float\' is empty!');
		}

		if (!isset($ary['title'])) {
			throw new Exception('Param \'title\' is empty!');
		}

		if (!isset($ary['id'])) {
			throw new Exception('Param \'id\' is empty!');
		}

		if (!isset($ary['target'])) {
			throw new Exception('Param \'target\' is empty!');
		}

		$ary['url'] = (strpos($ary['url'], 'http') < 0 ? $zbp->host . 'zb_users/plugin/duoshuo/' . $ary['url'] : $ary['url']);
		$this->actions[] = $ary;
	}

	function export($id) {
		$html = '';
		$temp = '';
		for ($i = 0; $i < count($this->actions); $i++) {
			$temp = $this->template;
			$temp = str_replace('$url', $this->actions[$i]['url'], $temp);
			$temp = str_replace('$float', $this->actions[$i]['float'], $temp);
			$temp = str_replace('$title', $this->actions[$i]['title'], $temp);
			$temp = str_replace('$id', $this->actions[$i]['id'], $temp);
			$temp = str_replace('$target', $this->actions[$i]['target'], $temp);
			$temp = str_replace('$light', ($this->actions[$i]['id'] == $id ? ' m-now' : ''), $temp);
			$html .= $temp;
		}
		return $html;
	}

}
