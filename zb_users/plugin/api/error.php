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
	const CUSTOM = 65535;

	public static $errorCode = array(
		self::OK => '',
		self::NON_ACCEPT => '无法发送指定数据结构'
	);

}