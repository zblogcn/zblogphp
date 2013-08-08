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
	#public $modules=array();
	public $modulesbyfilename=array();
	public $templates=array();
	public $configs=array();

	public $templatetags=array();	
	public $title=null;
	public $name=null;
	public $subname=null;

	public $user=null;
	public $cache=array();
	#cache={name,value,time}

	public $table=null;
	public $datainfo=null;

	public $isinitialize=false;
	public $isconnect=false;
	public $isdelay_savecache=false;	

	public $template = null;

	public $themes = array();
	public $plugins = array();

	public $theme = null;
	public $style = null;
	public $managecount = 50;
	public $pagebarcount = 10;
	
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
		$db = null;
	}

	function __call($method, $args) {
		throw new Exception('zbp不存在方法：'.$method);
	}





################################################################################################################
#权限及验证类




	function CheckRights($action){

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

		ActivePlugin();

		$this->LoadCache();

		$this->OpenConnect();
		$this->LoadMembers();
		$this->LoadCategorys();
		#$this->LoadTags();
		$this->LoadModules();
		$this->LoadConfigs();

		$this->Verify();

		$this->MakeTemplatetags();

		$this->isinitialize=true;

	}



	#终止连接，释放资源
	public function Terminate(){
		if($this->isconnect)
		{
			$this->db->Close();
		}
		if($this->isdelay_savecache)
		{
			$this->SaveCache();
		}		

	}


	public function OpenConnect(){

		if($this->isconnect){return;}

		switch ($this->option['ZC_DATABASE_TYPE']) {
		case 'mysql':
		case 'pdo_mysql':
			$db=DbFactory::Create($this->option['ZC_DATABASE_TYPE']);
			$this->db=&$db;
			if($db->Open(array(
					$this->option['ZC_MYSQL_SERVER'],
					$this->option['ZC_MYSQL_USERNAME'],
					$this->option['ZC_MYSQL_PASSWORD'],
					$this->option['ZC_MYSQL_NAME'],
					$this->option['ZC_MYSQL_PRE']
				))==false){
				throw new Exception($this->lang['error'][67]);
			}

			break;
		case 'sqlite':
			$db=DbFactory::Create('sqlite');
			$this->db=&$db;
			if($db->Open(array(
				$this->path . $this->option['ZC_SQLITE_NAME'],
				$this->option['ZC_SQLITE_PRE']
				))==false){
				throw new Exception($this->lang['error'][68]);
			}
			break;
		case 'sqlite3':
			$db=DbFactory::Create('sqlite3');
			$this->db=&$db;
			if($db->Open(array(
				$this->path . $this->option['ZC_SQLITE_NAME'],
				$this->option['ZC_SQLITE_PRE']
				))==false){
				throw new Exception($this->lang['error'][69]);
			}
			break;
		}
		$this->isconnect=true;	
	}





################################################################################################################
#Cache相关



	public function HasCache($name){
		if(array_key_exists($name,$this->cache)){
			return true;
		}else{
			return false;
		}
	}

	public function GetCache($name){
		if(array_key_exists($name,$this->cache)){
			return $this->cache[$name];
		}
	}

	public function GetCacheTime($name){
		if(array_key_exists($name,$this->cache)){
			return $this->cache[$name . '_time'];
		}
	}

	public function SetCache($name,$value){
		$this->cache[$name]=$value;
		$this->cache[$name . '_time']=time();		
	}

	public function DelCache($name){
		unset($this->cache[$name]);
		unset($this->cache[$name . '_time']);
	}

	public function SaveCache($delay=false){

		if($delay==true)
		{
			$this->isdelay_savecache=true;
		}
		else
		{
			$s=$this->path . 'zb_users/cache/' . $this->guid . '.cache';
			$c=serialize($this->cache);
			@file_put_contents($s, $c);
			$this->isdelay_savecache=false;
		}

	}

	public function LoadCache(){
		$s=$this->path . 'zb_users/cache/' . $this->guid . '.cache';
		if (file_exists($s))
		{
			$this->cache=unserialize(file_get_contents($s));
		}
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

		$s="<?php\r\n";
		$s.="return ";
		$s.=var_export($this->option,true);
		$s.="\r\n?>";

		@file_put_contents($this->path . 'zb_users/c_option.php',$s);

		$this->SetCache('refesh',time());
		$this->SaveCache(true);

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
			#$this->modules[$m->ID]=$m;
			#$this->modulesbyfilename[$m->FileName]=&$this->modules[$m->ID];
			$this->modulesbyfilename[$m->FileName]=$m;
		}

		$dir=$this->path .'zb_users/theme/' . $this->theme . '/include/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$m=new Module();
			$m->FileName=$sortname;
			$m->Content=file_get_contents($fullname);
			$m->Type='div';
			#$this->template_includes[$sortname]=file_get_contents($fullname);

			#$this->modules[$m->ID]=$m;
			#$this->modulesbyfilename[$m->FileName]=&$this->modules[$m->ID];
			$this->modulesbyfilename[$m->FileName]=$m;
		}

	}

	public function LoadThemes(){
		$dirs=GetDirsInDir($this->path . 'zb_users/theme/');

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('theme',$id)==true){
				$this->themes[]=$app;
			}
		}

	}

	public function LoadPlugins(){
		$dirs=GetDirsInDir($this->path . 'zb_users/plugin/');

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('plugin',$id)==true){
				$this->plugins[]=$app;
			}
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
			$sql = $this->db->sql->Update('Config',$kv,array(array('=','conf_Name',$name)));
			$this->db->Update($sql);
		}
	}

	public function GetConfig($name){
		if(!isset($this->configs[$name])){
			$m=new Metas;
			$this->configs[$name]=$m;
		}
		return $this->configs[$name];
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
			$option['ZC_SIDEBAR_ORDER2'],
			$option['ZC_SIDEBAR_ORDER3'],
			$option['ZC_SIDEBAR_ORDER4'],
			$option['ZC_SIDEBAR_ORDER5']
		);
		foreach ($s as $k =>$v) {
			$a=explode('|', $v);
			$ms=array();
			foreach ($a as $v2) {
				if(isset($this->modulesbyfilename[$v2])){
					$m=$this->modulesbyfilename[$v2];
				}
				$ms[]=$m ;
			}
			reset($ms);
			$this->templatetags['sidebars' . ($k==0?'':$k+1)]=$ms;
			$ms=null;

		}

		//创建模板类
		$this->template = new Template();
		$this->template->path = $this->path . 'zb_users/template/';
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
		$dir=$this->path .'zb_users/theme/' . $this->theme . '/template/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		if(!isset($this->templates['sidebar2'])){
			$this->templates['sidebar2']=str_replace('$sidebars', '$sidebars2', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar3'])){
			$this->templates['sidebar3']=str_replace('$sidebars', '$sidebars3', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar4'])){
			$this->templates['sidebar4']=str_replace('$sidebars', '$sidebars4', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar5'])){
			$this->templates['sidebar5']=str_replace('$sidebars', '$sidebars5', $this->templates['sidebar']);
		}
	}

	function BuildTemplate()
	{
		//初始化模板
		$this->LoadTemplates();

		//清空目标目录
		$dir = $this->path . 'zb_users/template/';
		$files = GetFilesInDir($dir,'php');
		foreach ($files as $fullname) {
			@unlink($fullname);
		}

		$this->template->CompileFiles($this->templates);

		$this->SetCache('refesh',time());
		$this->SaveCache(true);

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
		return $this->GetList('Comment',$sql);

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





################################################################################################################
#读取对象函数




	function GetCategoryByID($id){
		if(isset($this->categorys[$id])){
			return $this->categorys[$id];
		}else{
			return new Category;
		}
	}

	function GetMemberByID($id){
		if(isset($this->members[$id])){
			return $this->members[$id];
		}else{
			return new Member;
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
		if($tagnamestring=='')return array();
		if($tagnamestring==',')return array();		
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




	function CreateOptoinsOfTemplate($default){
		$s=null;
		$s .= '<option value="" >' . $this->lang['msg']['none'] . '</option>';
		foreach ($this->templates as $key => $value) {
			if(substr($key,0,2)=='b_')continue;
			if(substr($key,0,2)=='c_')continue;
			if(substr($key,0,5)=='post-')continue;
			if(substr($key,0,6)=='module')continue;
			if(substr($key,0,6)=='header')continue;
			if(substr($key,0,6)=='footer')continue;	
			if(substr($key,0,7)=='comment')continue;
			if(substr($key,0,7)=='sidebar')continue;
			if(substr($key,0,7)=='pagebar')continue;
			if($default==$key){
				$s .= '<option value="' . $key . '" selected="selected">' . $key . ' ('.$this->lang['msg']['default_template'].')' . '</option>';
			}else{
				$s .= '<option value="' . $key . '" >' . $key . '</option>';
			}
		}

		return $s;
	}


	function AddItemToNavbar($type,$id,$name,$url){

	}
	function DelItemToNavbar($type,$id){

	}

	#$signal = good,bad,tips
	function SetHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];				
		}
		setcookie("hint_signal", $signal . '|' . $content,time()+2,$this->cookiespath);
	}

	function GetHint(){
		$signal=GetVars('hint_signal','COOKIE');
		if($signal){
			$a=explode('|', $signal);
			echo "<div class='hint'><p class='hint hint_$a[0]'><font color='blue'>$a[1]</font></p></div>";
		}
	}

}

?>