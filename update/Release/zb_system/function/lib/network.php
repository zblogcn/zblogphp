<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

interface iNetwork
{

	
	public function abort();
	public function getAllResponseHeaders();
	public function getResponseHeader($bstrHeader);
	public function open($bstrMethod, $bstrUrl, $varAsync=true, $bstrUser='', $bstrPassword='');
	public function send($varBody='');
	public function setRequestHeader($bstrHeader, $bstrValue);

}

/**
* NetworkFactory
*/
class NetworkFactory 
{

	public $networktype = null;

	public static function Create($type)
	{
		$newtype='Network'.$type;
		$network=New $newtype();
		return $network;
	}
}

?>