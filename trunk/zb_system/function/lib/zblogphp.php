<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class ZBlogPHP{

	static private $_zbp=null;

	public $db = null;
	public $option = array();
	public $lang = array();
	public $path = null;
	public $host = null;
	public $cookiespath=null;
	public $guid=null;

	public $members=array();
	public $membersbyname=array();
	public $categorys=array();
	public $categorysbyorder=array();	
	public $tags=array();
	public $tagsbyname=array();	
	public $modules=array();
	public $modulesbyfilename=array();
	public $templates=array();
	public $configs=array();

	public $templatetags=array();	

	public $title=null;
	public $name=null;
	public $subname=null;
	public $theme = null;
	public $style = null;	

	public $user=null;
	public $cache=null;

	public $table=null;
	public $datainfo=null;

	public $isinitialize=false;
	public $isconnect=false;

	public $template = null;

	public $themes = array();
	public $plugins = array();

	public $managecount = 50;
	public $pagebarcount = 10;

	public $sidebar =array();
	public $sidebar2=array();
	public $sidebar3=array();
	public $sidebar4=array();
	public $sidebar5=array();

	public $usersdir = null;
	public $comments = array();

	static public function GetInstance(){
		if(!isset(self::$zbp)){
			self::$_zbp=new ZBlogPHP;
		}
		return self::$_zbp;
	}
	
	function __construct() {

		//基本配置加载到$zbp内
		$this->option = &$GLOBALS['option'];
		$this->lang = &$GLOBALS['lang'];
		$this->path = &$GLOBALS['blogpath'];
		$this->host = &$GLOBALS['bloghost'];
		$this->cookiespath = &$GLOBALS['cookiespath'];
		$this->usersdir = &$GLOBALS['usersdir'];

		$this->table=&$GLOBALS['table'];
		$this->datainfo=&$GLOBALS['datainfo'];

		if (trim($this->option['ZC_BLOG_CLSID'])==''){
			$this->option['ZC_BLOG_CLSID']=GetGuid();
		}
		$this->guid=&$this->option['ZC_BLOG_CLSID'];


		$this->option['ZC_BLOG_HOST']=&$GLOBALS['bloghost'];
		//define();

		$this->title=&$GLOBALS['blogtitle'];
		$this->name=&$GLOBALS['blogname'];
		$this->subname=&$GLOBALS['blogsubname'];
		$this->theme=&$GLOBALS['blogtheme'];
		$this->style=&$GLOBALS['blogstyle'];

		$this->managecount=$this->option['ZC_MANAGE_COUNT'];
		$this->pagebarcount=$this->option['ZC_PAGEBAR_COUNT'];
	}


	function __destruct(){
		$this->Terminate();
	}

	function __call($method, $args) {
		throw new Exception('zbp不存在方法：'.$method);
	}





################################################################################################################
#权限及验证类




	function CheckRights($action){

		foreach ($GLOBALS['Filter_Plugin_Zbp_CheckRights'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($action);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}

		if(is_int($action)){
			if ($GLOBALS['zbp']->user->Level > $action) {
				return false;
			} else {
				return true;
			}
		}

		if ($GLOBALS['zbp']->user->Level > $GLOBALS['actions'][$action]) {
			return false;
		} else {
			return true;
		}	

	}

	public function Verify(){
		return $this->Verify_MD5(GetVars('username','COOKIE'),GetVars('password','COOKIE'));
	}

	public function Verify_MD5($name,$md5pw){
		if (isset($this->membersbyname[$name])){
			$m=$this->membersbyname[$name];
			return $this->Verify_Final($name,md5($md5pw . $m->Guid));
		}else{
			return false;
		}
	}

	public function Verify_Original($name,$originalpw){
		return $this->Verify_MD5($name,md5($originalpw));
	}

	public function Verify_Final($name,$password){
		if (isset($this->membersbyname[$name])){
			$m=$this->membersbyname[$name];
			if($m->Password == $password){
				$this->user=$m;
				return true;
			}else{
				return false;
			}
		}
	}





################################################################################################################


	#初始化连接
	public function Initialize(){

		if(!$this->OpenConnect())return false;


		$this->LoadConfigs();
		$this->LoadCache();
		$this->LoadOption();

		if($this->option['ZC_DEBUG_MODE']==true){
			error_reporting(-1);
			@ini_set("display_errors",1);
		}

		$this->option['ZC_BLOG_PRODUCT_FULL']=$this->option['ZC_BLOG_PRODUCT'] . ' ' . $this->option['ZC_BLOG_VERSION'];
		$this->option['ZC_BLOG_PRODUCT_FULLHTML']='<a href="http://www.rainbowsoft.org/" title="RainbowSoft Z-BlogPHP">' . $this->option['ZC_BLOG_PRODUCT_FULL'] . '</a>';

		date_default_timezone_set($this->option['ZC_TIME_ZONE_NAME']);
		header('Product:' . $this->option['ZC_BLOG_PRODUCT_FULL']);

		$this->lang = require($this->usersdir . 'language/' . $this->option['ZC_BLOG_LANGUAGEPACK'] . '.php');

		#创建User类
		$this->user=new Member();

		$this->isinitialize=true;

	}


	public function Load(){
		if(!$this->isconnect)return false;

		$this->LoadMembers();
		$this->LoadCategorys();
		#$this->LoadTags();
		$this->LoadModules();


		$this->Verify();

		$this->MakeTemplatetags();

	}


	#终止连接，释放资源
	public function Terminate(){
		if($this->isinitialize){
			$this->CloseConnect();
		}
	}

	
	function InitializeDB($type){
		if(!trim($type))return false;
		$newtype='Db'.trim($type);
		$this->db=new $newtype();
		$this->db->sql=new DbSql;
	}

	public function OpenConnect(){

		if($this->isconnect)return false;
		if(!$this->option['ZC_DATABASE_TYPE'])return false;
		switch ($this->option['ZC_DATABASE_TYPE']) {
		case 'mysql':
		case 'pdo_mysql':
			if($this->InitializeDB($this->option['ZC_DATABASE_TYPE']))return false;
			if($this->db->Open(array(
					$this->option['ZC_MYSQL_SERVER'],
					$this->option['ZC_MYSQL_USERNAME'],
					$this->option['ZC_MYSQL_PASSWORD'],
					$this->option['ZC_MYSQL_NAME'],
					$this->option['ZC_MYSQL_PRE'],
					$this->option['ZC_MYSQL_PORT']					
				))==false){
				$zbp->ShowError(67);
			}
			break;
		case 'sqlite':
		case 'sqlite3':
			$this->CreateDB($this->option['ZC_DATABASE_TYPE']);
			if($this->db->Open(array(
				$this->usersdir . 'data/' . $this->option['ZC_SQLITE_NAME'],
				$this->option['ZC_SQLITE_PRE']
				))==false){
				$zbp->ShowError(69);
			}
			break;
		}
		$this->isconnect=true;
		return true;

	}

	public function CloseConnect(){
		if($this->isconnect){
			$this->db->Close();
		}
	}





################################################################################################################
#插件用Configs表相关设置函数




	public function LoadConfigs(){

		$sql = $this->db->sql->Select('Config',array('*'),'','','','');
		$array=$this->db->Query($sql);
		foreach ($array as $c) {
			$m=new Metas;
			$m->Unserialize($c['conf_Value']);
			$this->configs[$c['conf_Name']]=$m;	
		}
	}

	public function DelConfig($name){
		$sql = $this->db->sql->Delete('Config',array(array('=','conf_Name',$name)));
		logs($sql);
		$this->db->Delete($sql);
	}

	public function SaveConfig($name){

		if(!isset($this->configs[$name]))return false;

		$kv=array('conf_Name'=>$name,'conf_Value'=>$this->configs[$name]->Serialize());
		$sql = $this->db->sql->Select('Config',array('*'),array(array('=','conf_Name',$name)),'','','');
		$array=$this->db->Query($sql);

		if(count($array)==0){
			$k=array('conf_Name','conf_Value');
			$v=array($name,$this->configs[$name]->Serialize());		
			$sql = $this->db->sql->Insert('Config',$kv);
			$this->db->Insert($sql);
		}else{
			array_shift($kv);
			$sql = $this->db->sql->Update('Config',$kv,array(array('=','conf_Name',$name)));
			$this->db->Update($sql);
		}
	}

	public function Config($name){
		if(!isset($this->configs[$name])){
			$m=new Metas;
			$this->configs[$name]=$m;
		}
		return $this->configs[$name];
	}






################################################################################################################
#Cache相关


	public function SaveCache(){

		#$s=$this->usersdir . 'cache/' . $this->guid . '.cache';
		#$c=serialize($this->cache);
		#@file_put_contents($s, $c);

		$this->configs['cache']=$this->cache;
		$this->SaveConfig('cache');

	}

	public function LoadCache(){
		#$s=$this->usersdir . 'cache/' . $this->guid . '.cache';
		#if (file_exists($s))
		#{
		#	$this->cache=unserialize(@file_get_contents($s));
		#}
		$this->cache=$this->Config('cache');
	}







################################################################################################################
#保存zbp设置函数



	public function SaveOption(){

		$this->option['ZC_BLOG_CLSID']=$this->guid;

		$this->option['ZC_BLOG_NAME'] = $this->name;
		$this->option['ZC_BLOG_SUBNAME'] = $this->subname;
		$this->option['ZC_BLOG_THEME'] = $this->theme;
		$this->option['ZC_BLOG_CSS'] = $this->style;

		$this->option['ZC_BLOG_HOST'] = $this->host;
		if(!$this->option['ZC_YUN_SITE']){
			$s="<?php\r\n";
			$s.="return ";
			$s.=var_export($this->option,true);
			$s.="\r\n?>";
			@file_put_contents($this->usersdir . 'c_option.php',$s);
		}

		foreach ($this->option as $key => $value) {
			$this->Config('system')->$key = $value;
		}
		$this->SaveConfig('system');

	}	



	public function LoadOption(){

		$array=$this->Config('system')->Data;

		if(empty($array))return false;
		if(!is_array($array))return false;
		foreach ($array as $key => $value) {
			$this->option[$key]=$value;
		}

	}






################################################################################################################
#加载函数




	public function LoadMembers(){

		$array=$this->GetMemberList();
		foreach ($array as $m) {
			$this->members[$m->ID]=$m;
			$this->membersbyname[$m->Name]=&$this->members[$m->ID];
		}
	}

	public function LoadCategorys(){

		$lv0=array();
		$lv1=array();
		$lv2=array();
		$lv3=array();	
		$array=$this->GetCategoryList(null,null,array('cate_Order'=>'ASC'),null,null);
		if(count($array)==0)return false;
		foreach ($array as $c) {
			$this->categorys[$c->ID]=$c;
		}
		foreach ($this->categorys as $id=>$c) {
			$l='lv' . $c->Level;
			${$l}[$c->ParentID][]=$id;
		}


		foreach ($lv0[0] as $id0) {
			$this->categorysbyorder[$id0]=&$this->categorys[$id0];
			if(!isset($lv1[$id0])){continue;}
			foreach ($lv1[$id0] as $id1) {
				if($this->categorys[$id1]->ParentID==$id0){
					$this->categorysbyorder[$id1]=&$this->categorys[$id1];
					if(!isset($lv2[$id1])){continue;}
					foreach ($lv2[$id1] as $id2) {
						if($this->categorys[$id2]->ParentID==$id1){
							$this->categorysbyorder[$id2]=&$this->categorys[$id2];
							if(!isset($lv3[$id2])){continue;}
							foreach ($lv3[$id2] as $id3) {
								if($this->categorys[$id3]->ParentID==$id2){
									$this->categorysbyorder[$id3]=&$this->categorys[$id3];
								}
							}
						}
					}		
				}
			}
		}
	}

	public function LoadTags(){

		$array=$this->GetTagList();
		foreach ($array as $t) {
			$this->tags[$t->ID]=$t;
			$this->tagsbyname[$t->Name]=&$this->tags[$t->ID];
		}

	}

	public function LoadModules(){

		$array=$this->GetModuleList();
		foreach ($array as $m) {
			$this->modules[]=$m;

			$this->modulesbyfilename[$m->FileName]=$m;
		}

		$dir=$this->usersdir . 'theme/' . $this->theme . '/include/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$m=new Module();
			$m->FileName=$sortname;
			$m->Content=file_get_contents($fullname);
			$m->Type='div';
			$m->Source='theme';
			$this->modules[]=$m;

			$this->modulesbyfilename[$m->FileName]=$m;
		}

	}

	public function LoadThemes(){
		$dirs=GetDirsInDir($this->usersdir . 'theme/');

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('theme',$id)==true){
				$this->themes[]=$app;
			}
		}

	}

	public function LoadPlugins(){
		$dirs=GetDirsInDir($this->usersdir . 'plugin/');

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('plugin',$id)==true){
				$this->plugins[]=$app;
			}
		}

	}





################################################################################################################
#模板相关函数




	function MakeTemplatetags(){

		$this->templatetags=array();

		$option=$this->option;
		unset($option['ZC_BLOG_CLSID']);
		unset($option['ZC_SQLITE_NAME']);
		unset($option['ZC_SQLITE3_NAME']);
		unset($option['ZC_MYSQL_USERNAME']);
		unset($option['ZC_MYSQL_PASSWORD']);
		unset($option['ZC_MYSQL_NAME']);

		$this->templatetags['user']=&$this->user;
		$this->templatetags['option']=&$option;
		$this->templatetags['modules']=&$this->modulesbyfilename;		
		$this->templatetags['title']=$this->title;
		$this->templatetags['host']=$this->host;	
		$this->templatetags['path']=$this->path;
		$this->templatetags['cookiespath']=$this->cookiespath;
		$this->templatetags['name']=$this->name;	
		$this->templatetags['subname']=$this->subname;
		$this->templatetags['theme']=$this->theme;
		$this->templatetags['style']=$this->style;
		$this->templatetags['language']=$this->option['ZC_BLOG_LANGUAGE'];
		$this->templatetags['copyright']=$this->option['ZC_BLOG_COPYRIGHT'];		
		$this->templatetags['zblogphp']=$this->option['ZC_BLOG_PRODUCT_FULL'];
		$this->templatetags['zblogphphtml']=$this->option['ZC_BLOG_PRODUCT_FULLHTML'];
		$this->templatetags['feedurl']=$this->host . 'feed.php';

		$s=array(
			$option['ZC_SIDEBAR_ORDER'],
			$option['ZC_SIDEBAR2_ORDER'],
			$option['ZC_SIDEBAR3_ORDER'],
			$option['ZC_SIDEBAR4_ORDER'],
			$option['ZC_SIDEBAR5_ORDER']
		);
		foreach ($s as $k =>$v) {
			$a=explode('|', $v);
			$ms=array();
			foreach ($a as $v2) {
				if(isset($this->modulesbyfilename[$v2])){
					$m=$this->modulesbyfilename[$v2];
					$ms[]=$m;
				}
			}
			//reset($ms);
			$s='sidebar' . ($k==0?'':$k+1);
			$this->$s=$ms;
			$ms=null;
		}
		$this->templatetags['sidebar']=$this->sidebar;
		$this->templatetags['sidebar2']=$this->sidebar2;
		$this->templatetags['sidebar3']=$this->sidebar3;
		$this->templatetags['sidebar4']=$this->sidebar4;
		$this->templatetags['sidebar5']=$this->sidebar5;

		//创建模板类
		$this->template = new Template();
		$this->template->path = $this->usersdir . 'template/';
		$this->template->tags = $this->templatetags;

	}

	public function LoadTemplates(){
		#先读默认的
		$dir=$this->path .'zb_system/defend/default/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		#再读当前的
		$dir=$this->usersdir .'theme/' . $this->theme . '/template/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		if(!isset($this->templates['sidebar2'])){
			$this->templates['sidebar2']=str_replace('$sidebar', '$sidebar2', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar3'])){
			$this->templates['sidebar3']=str_replace('$sidebar', '$sidebar3', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar4'])){
			$this->templates['sidebar4']=str_replace('$sidebar', '$sidebar4', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar5'])){
			$this->templates['sidebar5']=str_replace('$sidebar', '$sidebar5', $this->templates['sidebar']);
		}
	}

	function BuildTemplate()
	{
		if($this->option['ZC_YUN_SITE'])return false;
		//初始化模板
		$this->LoadTemplates();

		//清空目标目录
		$dir = $this->usersdir . 'template/';
		$files = GetFilesInDir($dir,'php');
		foreach ($files as $fullname) {
			@unlink($fullname);
		}
		
		//创建模板类
		$this->template = new Template();
		$this->template->path = $this->usersdir . 'template/';

		//模板接口
		foreach ($GLOBALS['Filter_Plugin_Zbp_BuildTemplate'] as $fpname => &$fpsignal) {$fpname();}

		$this->template->CompileFiles($this->templates);

		$this->cache->refesh=time();
		$this->SaveCache();

	}





################################################################################################################
#加载数据对像List函数




	function GetList($type,$sql){

		$array=null;
		$list=array();
		$array=$this->db->Query($sql);
		if(!isset($array)){return array();}
		foreach ($array as $a) {
			$l=new $type();
			$l->LoadInfoByAssoc($a);
			$list[]=$l;
		}
		return $list;
	}

	function GetPostList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		$sql = $this->db->sql->Select('Post',$select,$where,$order,$limit,$option);
		return $this->GetList('Post',$sql);

	}

	function GetArticleList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		$where[]= array('=','log_Type','0');
		$sql = $this->db->sql->Select('Post',$select,$where,$order,$limit,$option);
		$array = $this->GetList('Post',$sql);
		foreach ($array as $a) {
			$this->AddTagsIDString($a->Tag);
		}

		$this->LoadTagsByIDString($this->AddTagsIDString());

		return $array;
	}

	function GetPageList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		$where[]= array('=','log_Type','1');
		$sql = $this->db->sql->Select('Post',$select,$where,$order,$limit,$option);
		return $this->GetList('Post',$sql);

	}

	function GetCommentList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select('Comment',$select,$where,$order,$limit,$option);
		$array=$this->GetList('Comment',$sql);
		foreach ($array as $comment) {
			$this->comments[$comment->ID]=$comment;
		}
		return $array;

	}

	function GetMemberList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select('Member',$select,$where,$order,$limit,$option);
		return $this->GetList('Member',$sql);

	}

	function GetTagList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select('Tag',$select,$where,$order,$limit,$option);
		return $this->GetList('Tag',$sql);

	}

	function GetCategoryList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select('Category',$select,$where,$order,$limit,$option);
		return $this->GetList('Category',$sql);

	}

	function GetModuleList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select('Module',$select,$where,$order,$limit,$option);
		return $this->GetList('Module',$sql);
	}

	function GetUploadList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select('Upload',$select,$where,$order,$limit,$option);
		return $this->GetList('Upload',$sql);
	}



################################################################################################################
#读取对象函数




	function GetCategoryByID($id){
		if(isset($this->categorys[$id])){
			return $this->categorys[$id];
		}else{
			return new Category;
		}
	}

	function GetModuleByID($id){
		$m = new Module;
		if($id>0){
			$m->LoadInfoByID($id);
		}
		return $m;		
	}

	function GetMemberByID($id){
		if(isset($this->members[$id])){
			return $this->members[$id];
		}else{
			$m = new Member;
			$m->Guid=GetGuid();
			return $m;
		}
	}

	function GetCommentByID($id){
		if(isset($this->comments[$id])){
			return $this->comments[$id];
		}else{
			$c = new Comment;
			if($id==0){
				return $c;
			}else{
				$c->LoadInfoByID($id);
				return $c;
			}
		}
	}

	function GetTagByID($id){
		if(isset($this->tags[$id])){
			return $this->tags[$id];
		}else{
			$array=$this->LoadTagsByIDString('{'.$id.'}');
			if(count($array)==0){
				return new Tag;
			}else{
				return $this->tags[$id];
			}

		}
	}

	function AddTagsIDString($s=''){
		static $tagstring;
		$tagstring .= $s;
		return $tagstring;
	}
	function LoadTagsByIDString($s){
		if($s=='')return array();
		$s=str_replace('}{', '|', $s);
		$s=str_replace('{', '', $s);
		$s=str_replace('}', '', $s);
		$a=explode('|', $s);
		$t=array_unique($a);

		if(count($t)==0)return array();

		$a=array();
		$b=array();
		foreach ($t as $v) {
			if(isset($this->tags[$v])==false){
				$a[]=array('tag_ID',$v);
			}else{
				$b[$v]=&$this->tags[$v];
			}
		}

		if(count($a)==0){
			return $b;
		}else{
			$t=array();
			$array=$this->GetTagList('',array(array('array',$a)),'','','');
			foreach ($array as $v) {
				$this->tags[$v->ID]=$v;
				$this->tagsbyname[$v->Name]=&$this->tags[$v->ID];
				$t[$v->ID]=&$this->tags[$v->ID];
			}
			return $b+$t;
		}
	}

	function LoadTagsByNameString($s){
		if($s=='')return array();
		$a=explode(',', $s);
		$t=array_unique($a);

		if(count($t)==0)return array();

		$a=array();
		$b=array();
		foreach ($t as $v) {
			if(isset($this->tagsbyname[$v])==false){
				$a[]=array('tag_Name',$v);
			}else{
				$b[$v]=&$this->tagsbyname[$v];
			}
		}

		if(count($a)==0){
			return $b;
		}else{
			$t=array();
			$array=$this->GetTagList('',array(array('array',$a)),'','','');
			foreach ($array as $v) {
				$this->tags[$v->ID]=$v;
				$this->tagsbyname[$v->Name]=&$this->tags[$v->ID];
				$t[$v->Name]=&$this->tags[$v->ID];
			}
			return $b+$t;
		}
	}

	function CheckUnsetTagAndConvertIDString($tagnamestring){
		$s='';
		$tagnamestring=str_replace(';', ',', $tagnamestring);
		$tagnamestring=str_replace('，', ',', $tagnamestring);
		$tagnamestring=str_replace('、', ',', $tagnamestring);
		$tagnamestring=trim($tagnamestring);
		if($tagnamestring=='')return '';
		if($tagnamestring==',')return '';		
		$a=explode(',', $tagnamestring);
		$b=array_unique($a);
		$b=array_slice($b, 0, 20);
		$c=array();

		$t=$this->LoadTagsByNameString(GetVars('Tag','POST'));
		foreach ($t as $key => $value) {
			$c[]=$key;
		}
		$d=array_diff($b,$c);
		if($this->CheckRights('TagNew')){
			foreach ($d as $key) {
				$tag = new Tag;
				$tag->Name = $key;
				$tag->Save();
				$this->tags[$tag->ID]=$tag;
				$this->tagsbyname[$tag->Name]=&$this->tags[$tag->ID];
			}
		}

		foreach ($a as $key) {
			if(!isset($this->tagsbyname[$key]))continue;
			$s .= '{' . $this->tagsbyname[$key]->ID . '}';
		}
		return $s;
	}





################################################################################################################
#统计函数



	function CountCategory($id){
		$s=$this->db->sql->Count('Post',array('Log_ID'=>'num'),array(array('LIKE','log_CateID',$id)));
		$num=GetValueInArray(current($this->db->Query($s)),'num');
		return $num;
	}
	function CountComment($postid){
		$s=$this->db->sql->Count('Comment',array('comm_ID'=>'num'),array(array('LIKE','comm_LogID','%{'.$id.'}%')));
		$num=GetValueInArray(current($this->db->Query($s)),'num');
		return $num;
	}
	function CountTag($id){
		$s=$this->db->sql->Count('Post',array('Log_ID'=>'num'),array(array('LIKE','log_Tag','%{'.$id.'}%')));
		$num=GetValueInArray(current($this->db->Query($s)),'num');
		return $num;
	}
	function CountAuthor($id){
		$s=$this->db->sql->Count('Post',array('Log_ID'=>'num'),array(array('LIKE','log_AuthID','%{'.$id.'}%')));
		$num=GetValueInArray(current($this->db->Query($s)),'num');
		return $num;
	}






################################################################################################################
#杂项

	function CheckPlugin($name){
		$s=$this->option['ZC_BLOG_THEME'] . '|' . $this->option['ZC_USING_PLUGIN_LIST'];
		return HasNameInString($s,$name);
	}

	function AddItemToNavbar($type,$id,$name,$url){

	}
	function DelItemToNavbar($type,$id){

	}
	function CheckItemToNavbar($type,$id){

	}

	#$signal = good,bad,tips
	function SetHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];				
		}
		setcookie("hint_signal", $signal . '|' . $content,time()+3600,$this->cookiespath);
	}

	function GetHint(){
		$signal=GetVars('hint_signal','COOKIE');
		if($signal){
			$a=explode('|', $signal);
			$this->ShowHint($a[0],$a[1]);
			setcookie("hint_signal", '',time()-3600,$this->cookiespath);
		}
	}

	function ShowHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];				
		}
		echo "<div class='hint'><p class='hint hint_$signal'><font color='blue'>$content</font></p></div>";
	}


	function ShowError($idortext){
		if(is_numeric($idortext))$idortext=$this->lang['error'][$idortext];

		foreach ($GLOBALS['Filter_Plugin_Zbp_ShowError'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($idortext);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}

		throw new Exception($idortext);
	}
}

?>