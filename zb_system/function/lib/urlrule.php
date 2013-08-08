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
			if($this->Rules['{%page%}']=='1'){$this->Rules['{%page%}']='';}
		}
		$s=$this->PreUrl;
		foreach ($this->Rules as $key => $value) {
			$s=preg_replace($key, $value, $s);
		}
		$s=preg_replace('/\{[\?\/&a-z0-9]*=\}/', '', $s);
		$s=str_replace(array('{','}'), array('',''), $s);
		$this->Url=htmlspecialchars($s);
		#echo nl2br($s,true);
		return $this->Url;
	}
}

?>