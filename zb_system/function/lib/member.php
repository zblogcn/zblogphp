<?php
/**
 * 用户类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib 类库
 */
class Member extends Base {

	/**
	 * @var string 头像图片地址
	 */
	private $_avatar='';

	/**
	 * @var boolean 创始id
	 */
	private $_isgod=null;

	/**
	 * 构造函数，默认用户设为anonymous
	 */
	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Member'],$zbp->datainfo['Member']);

		$this->Name = $zbp->lang['msg']['anonymous'];
	}

	/**
	 * 自定义函数
	 * @api Filter_Plugin_Member_Call
	 * @param $method
	 * @param $args
	 * @return mixed
	 */
	function __call($method, $args) {
		foreach ($GLOBALS['Filter_Plugin_Member_Call'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this,$method, $args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
	}

	/**
	 * 自定义参数及值
	 * @param $name
	 * @param $value
	 * @return null|string
	 */
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
		if ($name=='StaticName') {
			return null;
		}
		if ($name=='Template') {
			if($value==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$value='';
			return $this->data[$name]  =  $value;
		}
		if ($name=='PassWord_MD5Path') {
			return null;
		}
		if ($name=='IsGod') {
			return null;
		}
		parent::__set($name, $value);
	}

	/**
	 * @param $name
	 * @return mixed|string
	 */
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
		if ($name=='StaticName') {
			if($this->Alias)return $this->Alias;
			return $this->Name;
		}
		if ($name=='Template') {
			$value=$this->data[$name];
			if($value=='')$value=$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			return $value;
		}
		if ($name=='PassWord_MD5Path') {
			return md5($this->Password . $zbp->guid);
		}
		if ($name=='IsGod') {
			if($this->_isgod === true || $this->_isgod === false){
				return $this->_isgod;
			}else{
				$sql = $zbp->db->sql->Select($zbp->table['Member'],'*',array(array('=','mem_Level',1)),'mem_ID ASC',1,null);
				$am = $zbp->GetListType('Member',$sql);
				if($am[0]->ID == $this->ID){
					$this->_isgod = true;
				}else{
					$this->_isgod = false;
				}
				return $this->_isgod;
			}
		}
		return parent::__get($name);
	}

	/**
	 * 获取加盐及二次加密的密码
	 * @param string $ps 明文密码
	 * @param string $guid 用户唯一码
	 * @return string
	*/
	static function GetPassWordByGuid($ps,$guid){

		return md5(md5($ps). $guid);

	}

	/**
	 * 保存用户数据
	 * @return bool
	 */
	function Save(){
		global $zbp;
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->data['Template'] = '';
		foreach ($GLOBALS['Filter_Plugin_Member_Save'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		return parent::Save();
	}

	/**
	 * @return bool
	 */
	function Del(){
		foreach ($GLOBALS['Filter_Plugin_Member_Del'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		return parent::Del();
	}

}
