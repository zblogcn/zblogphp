<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Member extends Base{


	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['Member'];	
		$this->datainfo=&$zbp->datainfo['Member'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->Name = $zbp->lang['msg']['anonymous'];

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
		if ($name=='Template') {
			if($value==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$value='';
			return $this->Data[$name]  =  $value;
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
				if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
			}
			static $_avatar = '';
			if($_avatar)return $_avatar;
			$s=$zbp->usersdir . 'avatar/' . $this->ID . '.png';
			if(file_exists($s)){
				$_avatar = $zbp->host . 'zb_users/avatar/' . $this->ID . '.png';
				return $_avatar;
			}
			$_avatar = $zbp->host . 'zb_users/avatar/0.png';
			return $_avatar;
		}
		if ($name=='LevelName') {
			return $zbp->lang['user_level_name'][$this->Level];
		}
		if ($name=='Meta') {
			return $this->Metas->serialize();
		}
		if ($name=='Template') {
			$value=$this->Data[$name];
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
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->Data['Template'] = '';
		parent::Save();
	}

}

?>