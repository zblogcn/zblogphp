<?php

//global $zbp;

$table['appbuybill']='%pre%appbuybill';
$datainfo['appbuybill']=array(
	'bl_ID' => array('bl_ID','integer','',0),
	'bl_BuyID' => array('bl_BuyID','integer','',0),
	'bl_SaleID' => array('bl_SaleID','integer','',0),
	'bl_AppID' => array('bl_AppID','integer','',0),
	'bl_ID' => array('bl_ID','integer','',0),
	'bl_TradeNum' => array('bl_TradeNum','string',32,''),
	'bl_AlipayTredoNum' => array('bl_AlipayTredoNum','string',64,''),
	'bl_Money' => array('bl_Money','double','',''),
	'bl_AppBody' => array('bl_AppBody','text','',''),
	'bl_CreatTime' => array('bl_CreatTime','integer','',0),
	'bl_PayTime' => array('bl_PayTime','integer','',0),
	'bl_BuyAlipayID' => array('bl_BuyAlipayID','string',16,''),
	'bl_SaleAlipayID' => array('bl_SaleAlipayID','string',16,''),
	'bl_BuyAlipayUserName' => array('bl_BuyAlipayUserName','string',64,''),
	'bl_SaleAlipayUserName' => array('bl_SaleAlipayUserName','string',64,''),
	'bl_Status' => array('bl_Status','integer','',''),
);
class AppBuyBill extends Base{

	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['appbuybill'];
		$this->datainfo=&$zbp->datainfo['appbuybill'];
		
		$this->Metas=new Metas;
		
		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->ID = 0;
	}

	public function __set($name, $value)
	{
        global $zbp;
		parent::__set($name, $value);
	}
	
	public function __get($name)
	{
		return parent::__get($name);
	}
}
?>