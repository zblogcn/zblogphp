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
	public $_configs=array();	

	public $templatetags=array();	
	public $title=null;
	public $name=null;
	public $subname=null;

	public $user=null;
	public $cache=array();
	#cache={name,value,time}

	public $table=null;
	public $datainfo=null;

	public $isconnect=false;
	public $isdelay_savecache=false;	

	public $template = null;

	public $themes = array();
	public $plugins = array();

	public $theme = null;
	public $style = null;
	
	static public function GetInstance(){
		if(!isset(self::$_zbp)){
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

		if (trim($this->option['ZC_BLOG_CLSID'])=='')
		{
			$this->guid=GetGuid();
		}
		else
		{
			$this->guid=&$this->option['ZC_BLOG_CLSID'];
		}

		$this->option['ZC_BLOG_HOST']=&$GLOBALS['bloghost'];
		//define();

		$this->title=&$GLOBALS['blogtitle'];
		$this->name=&$GLOBALS['blogname'];
		$this->subname=&$GLOBALS['blogsubname'];

		$this->theme=&$GLOBALS['blogtheme'];
		$this->style=&$GLOBALS['blogstyle'];

	}


	function __destruct(){
		$db = null;
	}

	function __call($method, $args) {
		throw new Exception('zbp不存在方法：'.$method);
	}

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

		//创建模板类
		$this->template = new Template();
		$this->template->path = $this->path . 'zb_users/' . $this->option['ZC_TEMPLATE_DIRECTORY'] . '/';
		$this->template->tags = &$this->templatetags;

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

	public function Verify(){
		if (isset($this->membersbyname[GetVars('username','COOKIE')]))
		{
			$m=$this->membersbyname[GetVars('username','COOKIE')];
			if($m->Password == md5(GetVars('password','COOKIE') . $m->Guid))
			{
				$this->user=$m;
			}
		}
	}

	public function GetCache($name){
		if(array_key_exists($name,$this->cache))
		{
			return $this->cache[$name];
		}
	}

	public function GetCacheValue($name){
		if(array_key_exists($name,$this->cache))
		{

			return $this->cache[$name]['value'];
		}
	}

	public function GetCacheTime($name){
		if(array_key_exists($name,$this->cache))
		{
			return $this->cache[$name]['time'];
		}
	}

	public function SetCache($name,$value){
		$time=time();
		$this->cache[$name]=array('value'=>$value,'time'=>$time);
	}

	public function DelCache($name){
		unset($this->cache[$name]);
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
			file_put_contents($s, $c);
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
				$this->path . $this->option['ZC_SQLITE3_NAME'],
				$this->option['ZC_SQLITE3_PRE']
				))==false){
				throw new Exception($this->lang['error'][69]);
			}
			break;
		}
		$this->isconnect=true;	
	}


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
	}	

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

	public function LoadConfigs(){

		$s='SELECT * FROM %pre%Config';
		$array=$this->db->Query($s);
		foreach ($array as $c) {
			$this->configs[$c['conf_Name']]=$c['conf_Value'];
			$this->_configs[$c['conf_Name']]=$c['conf_Value'];			
		}
	}

	public function SaveConfigs(){

		foreach ($this->configs as $name => $value) {
			if(isset($this->_configs[$name])){
				#update
			}else{
				#insert
			}
		}

		$this->_configs=$this->configs;
	}


	function MakeTemplatetags(){

		$option=$this->option;
		unset($option['ZC_BLOG_CLSID']);
		unset($option['ZC_SQLITE_NAME']);
		unset($option['ZC_SQLITE3_NAME']);
		unset($option['ZC_MYSQL_USERNAME']);
		unset($option['ZC_MYSQL_PASSWORD']);
		unset($option['ZC_MYSQL_NAME']);

		$this->templatetags['option']=$option;
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
		$this->templatetags['modules']=&$this->modulesbyfilename;

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
		$dir = $this->path . 'zb_users/' . $this->option['ZC_TEMPLATE_DIRECTORY'] . '/';
		$files = GetFilesInDir($dir,'php');
		foreach ($files as $fullname) {
			@unlink($fullname);
		}
		
		
		//编译&Save模板
		if($this->template == null){
			$this->template = new Template();
			$this->template->path = $dir;
		}

		$this->template->CompileFiles($this->templates);


	}

	function AddTagsString($s=''){
		static $tagstring;
		$tagstring .= $s;
		return $tagstring;
	}
	function LoadTagsByString($s){
		if($s=='')return array();
		$s=str_replace('}{', '|', $s);
		$s=str_replace('{', '', $s);
		$s=str_replace('}', '', $s);
		$a=explode('|', $s);
		$t=array_unique($a);

		if(count($t)==0)return array();

		$a=array();
		foreach ($t as $v) {
			if(isset($this->tags[$v])==false){
				$a[]=array('tag_ID',$v);
			}
		}

		if(count($a)==0){
			$a=array();
			foreach ($t as $v) {
				$a[$v]=&$this->tags[$v];
			}
			return $a;
		}else{
			$t=array();
			$array=$this->GetTagList('',array('array'=>$a),'','','');
			foreach ($array as $v) {
				$this->tags[$v->ID]=$v;
				$this->tagsbyname[$v->Name]=&$this->tags[$v->ID];
				$t[$v->ID]=&$this->tags[$v->ID];
			}
			return $t;
		}
	}

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
		$where += array('='=>array('log_Type','0'));
		$sql = $this->db->sql->Select('Post',$select,$where,$order,$limit,$option);
		$array = $this->GetList('Post',$sql);
		foreach ($array as $a) {
			$this->AddTagsString($a->Tag);
		}

		$this->LoadTagsByString($this->AddTagsString());

		return $array;
	}

	function GetPageList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		$where += array('='=>array('log_Type','1'));
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



	function CountCategory($postid){

	}
	function CountComment($postid){

	}
	function CountTag($postid){

	}
	function CountAuthor($postid){

	}

}

?>