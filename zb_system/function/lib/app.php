<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

/**
* App
*/
class App
{
	#type='plugin'|'theme'
	public $type='';
	public $id;
	public $name;
	public $url;
	public $note;

	public $path;
	public $include;
	public $level;

	public $author_name;
	public $author_email;
	public $author_url;

	public $source_name;
	public $source_email;
	public $source_url;


	public $adapted;
	public $version;
	public $pubdate;
	public $modified;
	public $description;
	public $price;

	public $advanced_dependency;
	public $advanced_rewritefunctions;
	public $advanced_conflict;

	public function CanDel(){
		global $zbp;
		return false;
	}
	public function CanManage(){
		if($this->path){return true;}
		return false;
	}
	public function IsUsed(){
		global $zbp;

		if($this->type=='plugin'){
			$s='|' . $zbp->option['ZC_USING_PLUGIN_LIST'] . '|';
			$t='|' . $this->id. '|';
			if(stripos($s,$t)===false){
				return false;
			}else{
				return true;
			}
		}else{
			if($zbp->theme==$this->id){
				return true;
			}else{
				return false;
			}
		}

	}
	public function HasPlugin(){
		if($this->path || $this->include){return true;}
		return false;
	}

	public function GetHash(){
		global $zbp;
		return crc32($this->id);
	}

	public function GetManageUrl(){
		global $zbp;
		return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/' . $this->path;
	}

	public function GetLogo(){
		global $zbp;
		if($this->type=='plugin'){
			return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/logo.png';
		}else{
			return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/screenshot.png';
		}
	}
	public function GetScreenshot(){
		global $zbp;
		return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/screenshot.png';
	}
	public function GetCssFiles(){
		global $zbp;
		$dir = $zbp->usersdir . 'theme/' . $this->id . '/style/';
		return GetFilesInDir($dir,'css');
	}
	public function LoadInfoByXml($type,$id){
		global $zbp;
		$path=$zbp->usersdir . $type . '/' . $id . '/' . $type . '.xml';
		if(!file_exists($path)){return;}
		$xml = simplexml_load_file($path);
		$appver = $xml->attributes();
		if($appver <> 'php'){return false;}

		$this->type = $type;

		$this->id = (string)$xml->id;
		$this->name = (string)$xml->name;

		$this->url = (string)$xml->url;
		$this->note = (string)$xml->note;

		$this->path = (string)$xml->path;
		$this->include = (string)$xml->include;
		$this->level = (string)$xml->level;

		$this->author_name = (string)$xml->author->name;
		$this->author_email = (string)$xml->author->email;
		$this->author_url = (string)$xml->author->url;

		$this->source_name = (string)$xml->source->name;
		$this->source_email = (string)$xml->source->email;
		$this->source_url = (string)$xml->source->url;

		$this->adapted = (string)$xml->adapted;
		$this->version = (string)$xml->version;
		$this->pubdate = (string)$xml->pubdate;
		$this->modified = (string)$xml->modified;
		$this->description = (string)$xml->description;
		$this->price = (string)$xml->price;

		$this->advanced_dependency = (string)$xml->advanced->dependency;
		$this->advanced_rewritefunctions = (string)$xml->advanced->rewritefunctions;
		$this->advanced_conflict = (string)$xml->advanced->conflict;

		return true;
	}


	static public function ZipApp($type,$name){
		$s=null;
		return $s;
	}

	static public function UnzipApp($xml){
		return null;
	}

	public function SaveXml(){

	}
}

?>