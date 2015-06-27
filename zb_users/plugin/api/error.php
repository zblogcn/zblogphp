<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/error
 * @php >= 5.2
 */
class API_ERROR {
	
	const OK = 0;
	const NON_ACCEPT = 1;
	const MISSING_PARAMATER = 2;
	const CUSTOM = 65535;

	public static $errorCode = array(
		self::OK => '',
		self::NON_ACCEPT => '无法发送指定数据结构',
		self::MISSING_PARAMATER => '缺少必须参数'
	);

}