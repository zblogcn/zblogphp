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
	public $description;

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
	public $price;

	public $advanced_dependency;
	public $advanced_rewritefunctions;
	public $advanced_conflict;

	public $sidebars_sidebar1;
	public $sidebars_sidebar2;
	public $sidebars_sidebar3;
	public $sidebars_sidebar4;
	public $sidebars_sidebar5;

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
		return $zbp->CheckPlugin($this->id);
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

	public function GetDir(){
		global $zbp;
		return $zbp->path . 'zb_users/' . $this->type . '/' . $this->id . '/';
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

		$this->sidebars_sidebar1 = (string)$xml->sidebars->sidebar1;
		$this->sidebars_sidebar2 = (string)$xml->sidebars->sidebar2;
		$this->sidebars_sidebar3 = (string)$xml->sidebars->sidebar3;
		$this->sidebars_sidebar4 = (string)$xml->sidebars->sidebar4;
		$this->sidebars_sidebar5 = (string)$xml->sidebars->sidebar5;

		return true;
	}

	public function SaveInfoByXml(){
		global $zbp;
		$s='<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
		$s.='<'.$this->type.' version="php">' . "\r\n";

		$s.='<id>'.htmlspecialchars($this->id).'</id>' . "\r\n";
		$s.='<name>'.htmlspecialchars($this->name).'</name>' . "\r\n";
		$s.='<url>'.htmlspecialchars($this->url).'</url>' . "\r\n";
		$s.='<note>'.htmlspecialchars($this->note).'</note>' . "\r\n";
		$s.='<description>'.htmlspecialchars($this->description).'</description>' . "\r\n";

		$s.='<path>'.htmlspecialchars($this->path).'</path>' . "\r\n";
		$s.='<include>'.htmlspecialchars($this->include).'</include>' . "\r\n";
		$s.='<level>'.htmlspecialchars($this->level).'</level>' . "\r\n";

		$s.='<author>' . "\r\n";
		$s.='  <name>'.htmlspecialchars($this->author_name).'</name>' . "\r\n";
		$s.='  <email>'.htmlspecialchars($this->author_email).'</email>' . "\r\n";
		$s.='  <url>'.htmlspecialchars($this->author_url).'</url>' . "\r\n";
		$s.='</author>' . "\r\n";

		$s.='<source>' . "\r\n";
		$s.='  <name>'.htmlspecialchars($this->source_name).'</name>' . "\r\n";
		$s.='  <email>'.htmlspecialchars($this->source_email).'</email>' . "\r\n";
		$s.='  <url>'.htmlspecialchars($this->source_url).'</url>' . "\r\n";
		$s.='</source>' . "\r\n";

		$s.='<adapted>'.htmlspecialchars($this->adapted).'</adapted>' . "\r\n";
		$s.='<version>'.htmlspecialchars($this->version).'</version>' . "\r\n";
		$s.='<pubdate>'.htmlspecialchars($this->pubdate).'</pubdate>' . "\r\n";
		$s.='<modified>'.htmlspecialchars($this->modified).'</modified>' . "\r\n";
		$s.='<price>'.htmlspecialchars($this->price).'</price>' . "\r\n";

		$s.='<advanced>' . "\r\n";
		$s.='  <dependency>'.htmlspecialchars($this->advanced_dependency).'</dependency>' . "\r\n";
		$s.='  <rewritefunctions>'.htmlspecialchars($this->advanced_rewritefunctions).'</rewritefunctions>' . "\r\n";
		$s.='  <conflict>'.htmlspecialchars($this->advanced_conflict).'</conflict>' . "\r\n";
		$s.='</advanced>' . "\r\n";


		$s.='<sidebars>' . "\r\n";
		$s.='  <sidebar1>'.htmlspecialchars($this->sidebars_sidebar1).'</sidebar1>' . "\r\n";
		$s.='  <sidebar2>'.htmlspecialchars($this->sidebars_sidebar2).'</sidebar2>' . "\r\n";
		$s.='  <sidebar3>'.htmlspecialchars($this->sidebars_sidebar3).'</sidebar3>' . "\r\n";
		$s.='  <sidebar4>'.htmlspecialchars($this->sidebars_sidebar4).'</sidebar4>' . "\r\n";
		$s.='  <sidebar5>'.htmlspecialchars($this->sidebars_sidebar5).'</sidebar5>' . "\r\n";
		$s.='</sidebars>' . "\r\n";

		$s.='</'.$this->type.'>';
		
		$path=$zbp->usersdir . $this->type . '/' . $this->id . '/' . $this->type . '.xml';
		
		@file_put_contents($path, $s);

	}

	private $dirs=array();
	private $files=array();

	private function GetAllFileDir($dir){

		if(function_exists('scandir')){
			foreach (scandir($dir) as $d) {
				if (is_dir($dir .  $d)) {
					if( ($d<>'.') && ($d<>'..') ){
						$this->GetAllFileDir($dir . $d . '/');
						$this->dirs[]=$dir . $d . '/';
					}
				}else{
					$this->files[]=$dir . $d;
				}
			}
		}else{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						if (is_dir($dir .  $file)) {
							$this->dirs[]=$dir . $file  . '/';
							$this->GetAllFileDir($dir . $file . '/');
						}else{
							$this->files[]=$dir . $file;
						}
					}
				}
				closedir($handle);
			}
		}

	}


	public function Pack(){
		global $zbp;

		$dir=$this->GetDir();
		$this->GetAllFileDir($dir);

		$s='<?xml version="1.0" encoding="utf-8"?>';
		$s.='<app version="php" type="'.$this->type.'">';

		$s.='<id>'.htmlspecialchars($this->id).'</id>';
		$s.='<name>'.htmlspecialchars($this->name).'</name>';
		$s.='<url>'.htmlspecialchars($this->url).'</url>';
		$s.='<note>'.htmlspecialchars($this->note).'</note>';
		$s.='<description>'.htmlspecialchars($this->description).'</description>';

		$s.='<path>'.htmlspecialchars($this->path).'</path>';
		$s.='<include>'.htmlspecialchars($this->include).'</include>';
		$s.='<level>'.htmlspecialchars($this->level).'</level>';

		$s.='<author>';
		$s.='<name>'.htmlspecialchars($this->author_name).'</name>';
		$s.='<email>'.htmlspecialchars($this->author_email).'</email>';
		$s.='<url>'.htmlspecialchars($this->author_url).'</url>';
		$s.='</author>';

		$s.='<source>';
		$s.='<name>'.htmlspecialchars($this->source_name).'</name>';
		$s.='<email>'.htmlspecialchars($this->source_email).'</email>';
		$s.='<url>'.htmlspecialchars($this->source_url).'</url>';
		$s.='</source>';

		$s.='<adapted>'.htmlspecialchars($this->adapted).'</adapted>';
		$s.='<version>'.htmlspecialchars($this->version).'</version>';
		$s.='<pubdate>'.htmlspecialchars($this->pubdate).'</pubdate>';
		$s.='<modified>'.htmlspecialchars($this->modified).'</modified>';
		$s.='<price>'.htmlspecialchars($this->price).'</price>';

		$s.='<advanced>';
		$s.='<dependency>'.htmlspecialchars($this->advanced_dependency).'</dependency>';
		$s.='<rewritefunctions>'.htmlspecialchars($this->advanced_rewritefunctions).'</rewritefunctions>';
		$s.='<conflict>'.htmlspecialchars($this->advanced_conflict).'</conflict>';
		$s.='</advanced>';


		$s.='<sidebars>';
		$s.='<sidebar1>'.htmlspecialchars($this->sidebars_sidebar1).'</sidebar1>';
		$s.='<sidebar2>'.htmlspecialchars($this->sidebars_sidebar2).'</sidebar2>';
		$s.='<sidebar3>'.htmlspecialchars($this->sidebars_sidebar3).'</sidebar3>';
		$s.='<sidebar4>'.htmlspecialchars($this->sidebars_sidebar4).'</sidebar4>';
		$s.='<sidebar5>'.htmlspecialchars($this->sidebars_sidebar5).'</sidebar5>';
		$s.='</sidebars>';


		foreach ($this->dirs as $key => $value) {
			$d=$this->id .'/'. str_replace($dir,'',$value);
			$s.='<folder><path>'.htmlspecialchars($d).'</path></folder>';
		}
		foreach ($this->files as $key => $value) {
			$d=$this->id .'/'. str_replace($dir,'',$value);
			$c=base64_encode(file_get_contents($value));
			$s.='<file><path>'.$d.'</path><stream>'.$c.'</stream></file>';
		}



		$s.='</app>';
		
		return $s;
	}

	static public function UnPack($xml){
		global $zbp;
		$xml = simplexml_load_string($xml);
		if(!$xml)return false;
		if($xml['version']!='php')return $zbp->ShowError(78);
		$type=$xml['type'];
		$id=$xml->id;
		$dir=$zbp->path . 'zb_users/' . $type . '/';# . $id . '/';

		if(!file_exists($dir . $id . '/'))@mkdir($dir . $id . '/');

		foreach ($xml->folder as $folder) {
			$f=$dir . $folder->path;
			if(!file_exists($f)){
				@mkdir($f,0777,true);
			}
		}

		foreach ($xml->file as $file) {
			$f=$dir . $file->path;
			@file_put_contents($f, base64_decode($file->stream));
		}

		return true;
	}

	public function SaveInfo(){

	}
}


?>