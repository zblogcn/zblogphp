<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Member extends Base{

	private $_avatar='';

	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Member'],$zbp->datainfo['Member']);

		$this->Name = $zbp->lang['msg']['anonymous'];
	}

	function __call($method, $args) {
		foreach ($GLOBALS['Filter_Plugin_Member_Call'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this,$method, $args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
	}

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='Url') {
			$u = new UrlRule($zbp->option['ZC_AUTHOR_REGEX']);
			$u->Rules['{%id%}']=$this->ID;
			$u->Rules['{%alias%}']=$this->Alias==''?urlencode($this->Name):$this->Alias;
			return $u->Make();
		}
		if ($name=='Avatar') {
			return null;
		}
		if ($name=='LevelName') {
			return null;
		}
		if ($name=='EmailMD5') {
			return null;
		}
		if ($name=='Template') {
			if($value==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$value='';
			return $this->data[$name]  =  $value;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Url') {
			$u = new UrlRule($zbp->option['ZC_AUTHOR_REGEX']);
			$u->Rules['{%id%}']=$this->ID;
			$u->Rules['{%alias%}']=$this->Alias==''?urlencode($this->Name):$this->Alias;
			return $u->Make();
		}
		if ($name=='Avatar') {
			foreach ($GLOBALS['Filter_Plugin_Mebmer_Avatar'] as $fpname => &$fpsignal) {
				$fpreturn=$fpname($this);
				if($fpreturn){$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
			}
			if($this->_avatar)return $this->_avatar;
			$s=$zbp->usersdir . 'avatar/' . $this->ID . '.png';
			if(is_readable($s)){
				$this->_avatar = $zbp->host . 'zb_users/avatar/' . $this->ID . '.png';
				return $this->_avatar;
			}
			$this->_avatar = $zbp->host . 'zb_users/avatar/0.png';
			return $this->_avatar;
		}
		if ($name=='LevelName') {
			return $zbp->lang['user_level_name'][$this->Level];
		}
		if ($name=='EmailMD5') {
			return md5($this->Email);
		}
		if ($name=='Meta') {
			return $this->Metas->serialize();
		}
		if ($name=='Template') {
			$value=$this->data[$name];
			if($value=='')$value=$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			return $value;
		}
		return parent::__get($name);
	}

	static function GetPassWordByGuid($ps,$guid){

		return md5(md5($ps). $guid);

	}
	
	function Save(){
        global $zbp;
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->data['Template'] = '';
		foreach ($GLOBALS['Filter_Plugin_Member_Save'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		return parent::Save();
	}

}
