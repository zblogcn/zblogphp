<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

/**
* UrlRule
*/
class UrlRule
{
	public $Rules=array();
	public $Url='';
	private $PreUrl='';

	public function __construct($url){
		$this->PreUrl=$url;
	}

	public function Make(){
		global $zbp;

		$this->Rules['{%host%}']=$zbp->host;
		if(isset($this->Rules['{%page%}'])){
			if($this->Rules['{%page%}']=='1'||$this->Rules['{%page%}']=='0'){
				$this->Rules['{%page%}']='%page%';
			}
		}else{
			$this->Rules['{%page%}']='%page%';
		}
		$this->Rules['%page%']=$this->Rules['{%page%}'];
		$s=$this->PreUrl;
		foreach ($this->Rules as $key => $value) {
			//$s=preg_replace($key, $value, $s);
			$s=str_replace($key, $value, $s);
		}
		$s2=$s;
		preg_match('/\{.*%page%.*\}/i', $s2, $matches);
		if(isset($matches[0])){
			$s=str_replace($matches[0],'',$s);
		}
		$s=str_replace('{', '', $s);
		$s=str_replace('}', '', $s);
		$s=
		$this->Url=htmlspecialchars($s);
		return $this->Url;
	}


	public function Make_htaccess(){
		global $zbp;
		$s='RewriteEngine On' . "\r\n";
		$s .= "RewriteBase " . $zbp->cookiespath . "\r\n";

		$s .= $this->Rewrite_htaccess($zbp->option['ZC_ARTICLE_REGEX'],'article') . "\r\n";
		$s .= $this->Rewrite_htaccess($zbp->option['ZC_PAGE_REGEX'],'page') . "\r\n";
		$s .= $this->Rewrite_htaccess($zbp->option['ZC_INDEX_REGEX'],'index') . "\r\n";
		$s .= $this->Rewrite_htaccess($zbp->option['ZC_CATEGORY_REGEX'],'cate') . "\r\n";
		$s .= $this->Rewrite_htaccess($zbp->option['ZC_TAGS_REGEX'],'tags') . "\r\n";
		$s .= $this->Rewrite_htaccess($zbp->option['ZC_DATE_REGEX'],'date') . "\r\n";
		$s .= $this->Rewrite_htaccess($zbp->option['ZC_AUTHOR_REGEX'],'auth') . "\r\n";
		return $s;
	}

	public function Rewrite_htaccess($url,$type){
		$url='RewriteRule ' . $url;
		$url=str_replace('{%host%}', '^', $url);
		$url=str_replace('.', '\\.', $url);
		if($type=='index'){
			$url = $url . '$ index.php\?page=$1';
			$url=str_replace('%page%', '([0-9]+)', $url);
		}
		if($type=='cate'||$type=='tags'||$type=='date'||$type=='auth'){
			$url = $url . '$ index.php\?'. $type .'=$1&page=$2';
			$url=str_replace('%page%', '([0-9]+)', $url);
			$url=str_replace('%id%', '([0-9]+)', $url);
			$url=str_replace('%alias%', '(.+)', $url);
			$url=str_replace('%date%', '([0-9\-]+)', $url);
		}
		if($type=='page'||$type=='article'){
			if(strpos($url, '%alias%')===false){
				$url = $url . '$ view.php\?id=$1';
				$url=str_replace('%id%', '([0-9]+)', $url);				
			}else{
				$url = $url . '$ view.php\?alias=$1';
				$url=str_replace('%alias%', '(.+)', $url);
			}
			$url=str_replace('%category%', '.+', $url);
			$url=str_replace('%author%', '.+', $url);
			$url=str_replace('%year%', '[0-9]{4}', $url);
			$url=str_replace('%month%', '[0-9]{1,2}', $url);	
			$url=str_replace('%day%', '[0-9]{1,2}', $url);
		}
		$url=str_replace('{', '', $url);
		$url=str_replace('}', '', $url);
		return $url . ' [NC]';
	}

	public function Rewrite_httpini($url,$type){

	}

	public function Make_webconfig(){
		global $zbp;

		$s  ='<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
		$s .='<configuration>'. "\r\n";
		$s .=' <system.webServer>' . "\r\n";

		$s .='  <rewrite>' . "\r\n";

		$s .= $this->Rewrite_webconfig($zbp->option['ZC_ARTICLE_REGEX'],'article') . "\r\n";
		$s .= $this->Rewrite_webconfig($zbp->option['ZC_PAGE_REGEX'],'page') . "\r\n";
		$s .= $this->Rewrite_webconfig($zbp->option['ZC_INDEX_REGEX'],'index') . "\r\n";
		$s .= $this->Rewrite_webconfig($zbp->option['ZC_CATEGORY_REGEX'],'cate') . "\r\n";
		$s .= $this->Rewrite_webconfig($zbp->option['ZC_TAGS_REGEX'],'tags') . "\r\n";
		$s .= $this->Rewrite_webconfig($zbp->option['ZC_DATE_REGEX'],'date') . "\r\n";
		$s .= $this->Rewrite_webconfig($zbp->option['ZC_AUTHOR_REGEX'],'auth') . "\r\n";

		$s .='  </rewrite>' . "\r\n";
		$s .=' </system.webServer>' . "\r\n";
		$s .='</configuration>' . "\r\n";

		return $s;
	}


	public function Rewrite_webconfig($url,$type){
		$s ='     <rule name="Imported Rule {$0}" stopProcessing="true">' . "\r\n";
		$s.='       <match url="{$1}" ignoreCase="false" />' . "\r\n";
		$s.='       <action type="Rewrite" url="{$2}" />' . "\r\n";
		$s.='     </rule>';


		$url=str_replace('{%host%}', '^', $url);
		$url=str_replace('.', '\\.', $url);
		if($type=='index'){
			$s2 = 'index.php\?page={R:1}';
			$url=str_replace('%page%', '([0-9]+)', $url);
		}
		if($type=='cate'||$type=='tags'||$type=='date'||$type=='auth'){
			$s2 = 'index.php\?'. $type .'={R:1}&page={R:2}';
			$url=str_replace('%page%', '([0-9]+)', $url);
			$url=str_replace('%id%', '([0-9]+)', $url);
			$url=str_replace('%alias%', '(.+)', $url);
			$url=str_replace('%date%', '([0-9\-]+)', $url);			
		}
		if($type=='page'||$type=='article'){
			if(strpos($url, '%alias%')===false){
				$s2 = 'view.php\?id={R:1}';
				$url=str_replace('%id%', '([0-9]+)', $url);				
			}else{
				$s2 = 'view.php\?alias={R:1}';
				$url=str_replace('%alias%', '(.+)', $url);
			}
			$url=str_replace('%category%', '.+', $url);
			$url=str_replace('%author%', '.+', $url);
			$url=str_replace('%year%', '[0-9]{4}', $url);
			$url=str_replace('%month%', '[0-9]{1,2}', $url);	
			$url=str_replace('%day%', '[0-9]{1,2}', $url);
		}
		$url=str_replace('{', '', $url);
		$url=str_replace('}', '', $url);

		return str_replace(array('{$0}','{$1}','{$2}'), array($type,htmlentities($url),htmlentities($s2)), $s);
	}





}

?>