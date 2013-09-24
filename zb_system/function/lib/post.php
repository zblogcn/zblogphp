<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

 
class Post extends Base{


	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['Post'];	
		$this->datainfo=&$zbp->datainfo['Post'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->ID = 0;
		$this->Title	= $GLOBALS['lang']['msg']['unnamed'];
		$this->PostTime	= time();
	}


	function __call($method, $args) {
		foreach ($GLOBALS['Filter_Plugin_Post_Call'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this,$method,$args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
	}


	public function Time($s='Y-m-d H:i:s'){
		return date($s,(int)$this->PostTime);
	}

	function TagsToNameString(){
		global $zbp;
		$s=$this->Tag;
		if($s=='')return '';
		$s=str_replace('}{', '|', $s);
		$s=str_replace('{', '', $s);
		$s=str_replace('}', '', $s);
		$b=explode('|', $s);
		$b=array_unique($b);

		$a=$zbp->LoadTagsByIDString($this->Tag);
		$s='';
		$c='';
		foreach ($b as $key) {
			if(isset($zbp->tags[$key])){
				$c[] = $zbp->tags[$key]->Name;
			}
		}
		if(!$c)return '';
		$s=implode(',', $c);
		return $s;
	}

	public function __set($name, $value) 
	{
        global $zbp;
		switch ($name) {
			case 'Category':
			case 'Author':
			case 'TypeName':
			case 'Url':
			case 'Tags':
			case 'TagsName':
			case 'TagsCount':
			case 'CommentPostUrl':
			case 'Prev':
			case 'Next':
				return null;
				break;
			case 'Template':
				if($value==$zbp->option['ZC_POST_DEFAULT_TEMPLATE'])$value='';
				return $this->Data[$name]  =  $value;
				break;
			default:
				parent::__set($name, $value);
				break;
		}
	}

	public function __get($name) 
	{
        global $zbp;
		switch ($name) {
			case 'Category':
				return $zbp->GetCategoryByID($this->CateID);
				break;
			case 'Author':
				return $zbp->GetMemberByID($this->AuthorID);
				break;
			case 'StatusName':
				return $zbp->lang['post_status_name'][$this->Status];
				break;
			case 'Url':
				if($this->Type==ZC_POST_TYPE_ARTICLE){
					$u = new UrlRule($zbp->option['ZC_ARTICLE_REGEX']);
				}else{
					$u = new UrlRule($zbp->option['ZC_PAGE_REGEX']);
				}
				$u->Rules['{%id%}']=$this->ID;
				if($this->Alias){
					$u->Rules['{%alias%}']=$this->Alias;
				}else{
					$u->Rules['{%alias%}']=urlencode($this->Title);
				}
				$u->Rules['{%year%}']=$this->Time('Y');
				$u->Rules['{%month%}']=$this->Time('m');
				$u->Rules['{%day%}']=$this->Time('d');
				if($this->Category->Alias){
					$u->Rules['{%category%}']=$this->Category->Alias;
				}else{
					$u->Rules['{%category%}']=urlencode($this->Category->Name);
				}
				if($this->Author->Alias){
					$u->Rules['{%author%}']=$this->Author->Alias;
				}else{
					$u->Rules['{%author%}']=urlencode($this->Author->Name);
				}
				return $u->Make();
				break;
			case 'Tags':
				return $zbp->LoadTagsByIDString($this->Tag);
				break;
			case 'TagsCount':
				return substr_count($this->Tag, '{');
				break;				
			case 'TagsName':
				return $this->TagsToNameString;
			case 'Template':
				$value=$this->Data[$name];
				if($value==''){
					$value=GetValueInArray($this->Category->GetDataArray(),'LogTemplate');
					if($value==''){
						$value=$zbp->option['ZC_POST_DEFAULT_TEMPLATE'];
					}
				}
				return $value;
			case 'CommentPostUrl':
				$key='&amp;key=' . md5($zbp->guid . $this->ID . date('Y-m-d'));
				return $zbp->host . 'zb_system/cmd.php?act=cmt&amp;postid=' . $this->ID . $key;
				break;
			case 'Prev':
				static $_prev=null;
				if($_prev!==null)return $_prev;
				$articles=$zbp->GetPostList(
					array('*'),
					array(array('=','log_Type',0),array('=','log_Status',0),array('<','log_PostTime',$this->PostTime)),
					array('log_PostTime'=>'DESC'),
					array(1),
					null
				);
				if(count($articles)==1){
					$_prev=$articles[0];
				}else{
					$_prev='';
				}
				return $_prev;
				break;
			case 'Next':
				static $_next=null;
				if($_next!==null)return $_next;
				$articles=$zbp->GetPostList(
					array('*'),
					array(array('=','log_Type',0),array('=','log_Status',0),array('>','log_PostTime',$this->PostTime)),
					array('log_PostTime'=>'DESC'),
					array(1),
					null
				);
				if(count($articles)==1){
					$_next=$articles[0];
				}else{
					$_next='';
				}
				return $_next;
				break;
			default:
				return parent::__get($name);
				break;
		}

	}

	function Save(){
        global $zbp;
		if($this->Template==$zbp->option['ZC_POST_DEFAULT_TEMPLATE'])$this->Data['Template'] = '';
		return parent::Save();
	}
	
}

?>