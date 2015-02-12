<?php
class QINIU {
	private $data = array();
	private $cfg = NULL;
	private $is_init = FALSE;

	public function __construct() {
	}

	public function __set($name, $value) {
		global $zbp;
		if ($name == 'cfg') {
			$this->cfg = $value;
		} elseif (!is_null($this->cfg->$name)) {
			$this->cfg->$name = $value;
		} elseif (in_array($name, $this->data)) {
			$this->data[$name] = $value;
		} else {
			return false;
		}

		if ($name == 'access_token' || $name == 'secret_key') {
			Qiniu_SetKeys($this->access_token, $this->secret_key);
		}

		return true;
	}

	public function __get($name) {
		if ($name == 'cfg') {
			return $this->cfg;
		} elseif ($name == 'domain') {
			return $this->get_domain();
		} elseif (!is_null($this->cfg->$name)) {
			return $this->cfg->$name;
		} elseif (in_array($name, $this->data)) {
			return $this->data[$name];
		} else {
			return '';
		}

	}

	public function initialize() {
		if ($this->is_init) {
			return;
		}

		global $zbp;
		$this->cfg = $zbp->Config('qiniuyun');
		if ($this->cfg->version != '1.0') {
			$this->init_config();
		}

		Qiniu_SetKeys($this->access_token, $this->secret_key);
		$this->is_init = true;
		return true;
	}

	public function init_config() {
		$this->cfg->access_token = '';
		$this->cfg->secret_key = '';
		$this->cfg->bucket = '';
		$this->cfg->domain = '';
		$this->cfg->cloudpath = '';
		$this->cfg->water_enable = FALSE;
		$this->cfg->water_overwrite = FALSE;
		$this->cfg->water_dissolve = '100';
		$this->cfg->water_gravity = 'SouthEast';
		$this->cfg->water_dx = '10';
		$this->cfg->water_dy = '10';
		$this->cfg->thumbnail_quality = '85';
		$this->cfg->thumbnail_longedge = '300';
		$this->cfg->thumbnail_shortedge = '300';
		$this->cfg->thumbnail_cut = FALSE;

		$this->cfg->version = '1.0';
		return $this->save_config();

	}

	public function save_config() {
		return $GLOBALS['zbp']->SaveConfig('qiniuyun');
	}

	private function get_domain() {
		return ($this->cfg->domain == '' ? $this->bucket . '.qiniudn.com' : $this->cfg->domain);
	}

	public function get_url($key, $water = FALSE) {
		$return = Qiniu_RS_MakeBaseUrl($this->domain, $key);
		if ($water && qiniu_test_image($return)) {
			$return = $this->get_waterimage_url($return, QINIU_WATER_URL, $this->water_dissolve, $this->water_gravity, $this->water_dx, $this->water_dy);
		}

		return $return;
	}

	public function delete($key) {
		$client = new Qiniu_MacHttpClient(NULL);
		return Qiniu_RS_Delete($client, $this->bucket, $key);
	}

	public function upload($filepath_cloud, $filepath_local, $watermark = FALSE) {
		$upload_token = $this->get_upload_token();
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($upload_token, $filepath_cloud, $filepath_local, $putExtra);
		if ($watermark && $err == NULL) {
			$url = $this->get_url($ret['key'], TRUE);
			if (!qiniu_test_image($url)) {
				return $ret;
			}

			$local_url = $this->download_waterimage($url);
			$this->delete($ret['key']);
			$return = $this->upload($filepath_cloud, $local_url, FALSE);

			unlink($local_url);
			return $return;
		}

		return $ret;
	}

	private function get_upload_token() {
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		return $putPolicy->Token(NULL);
	}

	private function download_waterimage($url_cloud) {
		global $zbp;
		$local_file = $zbp->usersdir . 'upload/zbp.qiniutmp.water.' . time();
		$ajax = Network::Create();
		if (!$ajax) {
			throw new Exception('主机没有开启网络功能');
		}

		$ajax->open('GET', $url_cloud);
		$ajax->send();
		file_put_contents($local_file, $ajax->responseText);
		return $local_file;
	}

	private function get_waterimage_url($url_cloud, $image_local, $dissolve = 100, $gravity = 'SouthEast', $dx = 10, $dy = 10) {
		$param = array(
			"watermark", "1",
			"image", str_replace(array('+', '/'), array('-', '_'), base64_encode($image_local)),
			"dissolve", $dissolve,
			"gravity", $gravity,
			"dx", $dx,
			"dy", $dy,
		);

		return $url_cloud . '?' . implode($param, '/');
	}

	public function get_thumbnail_url($url_cloud, $quality = 85, $longedge = 300, $shortedge = 300, $cut = FALSE) {
		$param = array(
			"imageView",
			($cut ? 5 : 4), //mode
			"w", $longedge,
			"h", $shortedge,
			"q", $quality,
		);

		return $url_cloud . '?' . implode($param, '/');
	}
}