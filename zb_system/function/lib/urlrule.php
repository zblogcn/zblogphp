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
		$this->Url=htmlspecialchars($s);
		return $this->Url;
	}
}

?>