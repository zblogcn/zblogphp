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
	public $tags=array();
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

	}


	function __destruct(){
		$db = null;
	}

	function __call($method, $args) {
		throw new Exception('zbp不存在方法：'.$method);
	}

	#初始化连接
	public function Initialize(){

		ActivePlugin();

		$this->LoadCache();

		$this->OpenConnect();
		$this->LoadMembers();
		$this->LoadCategorys();
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

		$s="<?php\r\n";
		$s.="return ";
		$s.=var_export($this->option,true);
		$s.="\r\n?>";

		file_put_contents($this->path . 'zb_users/c_option.php',$s);
	}	

	public function LoadMembers(){

		$array=$this->GetMemberList();
		foreach ($array as $m) {
			$this->members[$m->ID]=$m;
			$this->membersbyname[$m->Name]=&$this->members[$m->ID];
		}
	}

	public function LoadCategorys(){

		$array=$this->GetCategoryList();
		foreach ($array as $c) {
			$this->categorys[$c->ID]=$c;
		}
	}

	public function LoadModules(){

		$array=$this->GetModuleList();
		foreach ($array as $m) {
			#$this->modules[$m->ID]=$m;
			#$this->modulesbyfilename[$m->FileName]=&$this->modules[$m->ID];
			$this->modulesbyfilename[$m->FileName]=$m;
		}

		$dir=$this->path .'zb_users/theme/' . $this->option['ZC_BLOG_THEME'] . '/include/';
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

		$this->templatetags['option']=&$option;
		$this->templatetags['title']=&$this->title;
		$this->templatetags['host']=&$this->host;	
		$this->templatetags['path']=&$this->path;
		$this->templatetags['cookiespath']=&$this->cookiespath;
		$this->templatetags['name']=&$this->name;	
		$this->templatetags['subname']=&$this->subname;
		$this->templatetags['theme']=&$this->option['ZC_BLOG_THEME'];
		$this->templatetags['style']=&$this->option['ZC_BLOG_STYLE'];
		$this->templatetags['language']=&$this->option['ZC_BLOG_LANGUAGE'];
		$this->templatetags['copyright']=&$this->option['ZC_BLOG_COPYRIGHT'];		
		$this->templatetags['zblogphp']=&$this->option['ZC_BLOG_PRODUCT_FULL'];		
		$this->templatetags['zblogphphtml']=&$this->option['ZC_BLOG_PRODUCT_FULLHTML'];

		$this->templatetags['modules']=&$this->modulesbyfilename;

		$s=array(
			$option['ZC_SIDEBAR_ORDER'],
			$option['ZC_SIDEBAR_ORDER2'],
			$option['ZC_SIDEBAR_ORDER3'],
			$option['ZC_SIDEBAR_ORDER4'],
			$option['ZC_SIDEBAR_ORDER5']
		);
		foreach ($s as $k =>$v) {
			$a=explode(':', $v);
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

	public function LoadTemplates(){
		#先读默认的
		$dir=$this->path .'zb_system/defend/default/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		#再读当前的
		$dir=$this->path .'zb_users/theme/' . $this->option['ZC_BLOG_THEME'] . '/template/';
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
			unlink($fullname);
		}
		
		
		//编译&Save模板
		if($this->template == null){
			$this->template = new Template();
			$this->template->path = $dir;
		}

		$this->template->CompileFiles($this->templates);


	}

	function ParseSql($type,$sql,$where,$order,$limit,$option){

		$sqlw='';
		$sqlo='';
		$sqll='';

		if(!empty($where)) {
			$sqlw .= ' WHERE ';
			$comma = '';
			foreach($where as $w) {

				if($w[0]=='='|$w[0]=='<'|$w[0]=='>'|$w[0]=='LIKE'){
					$w[2] = $this->db->EscapeString($w[2]);
					$sqlw .= $comma . " $w[1] $w[0] '$w[2]' ";
				}

				$comma = 'AND';
			}
		}

		if(!empty($order)) {
			$sqlo .= ' ORDER BY ';
			$comma = '';
			foreach($order as $k=>$v) {
				$sqlo .= $comma ."$k $v";
				$comma = ',';
			}
		}

		if(!empty($limit)){
			if(!isset($limit[1])){
				$sqll .= " LIMIT $limit[0]";
			}else{
				$sqll .= " LIMIT $limit[0], $limit[1]";
			}
		}

		if(!empty($option)){
			if(isset($option['pagebar'])){
				$s2 = "SELECT COUNT({$this->datainfo[$type]['ID'][0]}) AS num FROM {$this->table[$type]} ";
				$s2 .= $sqlw;
				$option['pagebar']->Count = GetValueInArray(current($this->db->Query($s2)),'num');
				$option['pagebar']->make();
			}
		}



		return $sql . $sqlw . $sqlo . $sqll;

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

	function GetArticleList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM {$this->table['Post']} ";
		if(empty($where)){$where = array();}
		$where[] = array('=','log_Type','0');
		$sql = $this->ParseSql('Post',$sql,$where,$order,$limit,$option);
		return $this->GetList('Post',$sql);
	}

	function GetPageList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM {$this->table['Post']} ";
		if(empty($where)){$where = array();}
		$where[] = array('=','log_Type','1');
		$sql = $this->ParseSql('Post',$sql,$where,$order,$limit,$option);
		return $this->GetList('Post',$sql);

	}

	function GetCommentList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM " . $this->table['Comment'] . " ";
		$sql = $this->ParseSql('Comment',$sql,$where,$order,$limit,$option);
		return $this->GetList('Comment',$sql);

	}

	function GetMemberList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM " . $this->table['Member'] . " ";
		$sql = $this->ParseSql('Member',$sql,$where,$order,$limit,$option);
		return $this->GetList('Member',$sql);

	}
	function GetTagList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM " . $this->table['Tag'] . " ";
		$sql = $this->ParseSql('Tag',$sql,$where,$order,$limit,$option);
		return $this->GetList('Tag',$sql);

	}
	function GetCategoryList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM " . $this->table['Category'] . " ";
		$sql = $this->ParseSql('Category',$sql,$where,$order,$limit,$option);
		return $this->GetList('Category',$sql);

	}
	function GetModuleList($where=null,$order=null,$limit=null,$option=null){

		$sql = "SELECT * FROM " . $this->table['Module'] . " ";
		$sql = $this->ParseSql('Module',$sql,$where,$order,$limit,$option);
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

}

?>