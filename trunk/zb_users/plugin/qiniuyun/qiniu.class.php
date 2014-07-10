<?php
class QINIU
{
	private $data = array();
	private $cfg = NULL;
	private $is_init = FALSE;
	
	public function __construct()
	{
	}
	
	public function __set($name, $value)
	{
		global $zbp;
		if ($name == 'cfg')
			$this->cfg = $value;
		elseif (!is_null($this->cfg->$name))
			$this->cfg->$name = $value;
		elseif (in_array($name, $this->data))
			$this->data[$name] = $value;
		else
			return false;
		
		if ($name == 'access_token' || $name == 'secret_key')
			Qiniu_SetKeys($this->access_token, $this->secret_key);
		
		return true;
	}
	
	public function __get($name)
	{
		if ($name == 'cfg')
			return $this->cfg;
		elseif (!is_null($this->cfg->$name))
			return $this->cfg->$name;
		elseif (in_array($name, $this->data))
			return $this->data[$name];
		else
			return '';
	}
	
	public function initialize()
	{
		if ($this->is_init) return;
		global $zbp;
		$this->cfg = $zbp->Config('qiniuyun');
		if ($this->cfg->version != '1.0') $this->init_config();
		
		Qiniu_SetKeys($this->access_token, $this->secret_key);
		$this->is_init = true;
		return true;
	}
	
	public function init_config()
	{
		$this->cfg->access_token = '';
		$this->cfg->secret_key = '';
		$this->cfg->bucket = '';
		$this->cfg->domain = '';
		$this->cfg->cloudpath = '';
		$this->cfg->version = '1.0';
		return $this->save_config();
		
	}
	
	public function save_config()
	{
		return $GLOBALS['zbp']->SaveConfig('qiniuyun');
	}
	
	
	public function get_url($key)
	{
		$domain = ($this->cfg->domain == '' ? $this->bucket . '.qiniudn.com' : $this->cfg->domain);
		return Qiniu_RS_MakeBaseUrl($domain, $key);
	}
	
	public function delete($key)
	{
		$client = new Qiniu_MacHttpClient(NULL);
		return Qiniu_RS_Delete($client, $this->bucket, $key);
	}
	
	public function upload($filepath_cloud, $filepath_local)
	{
		$upload_token = $this->get_upload_token();
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($upload_token, $filepath_cloud, $filepath_local, $putExtra);
		return $ret;
	}
	
	private function get_upload_token()
	{
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		return $putPolicy->Token(NULL);
	}
}