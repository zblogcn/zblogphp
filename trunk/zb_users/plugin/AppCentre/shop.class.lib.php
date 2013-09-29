<?php
/**
 * AppCentre_Shop_API
 * @author im@imzhou.com
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-09-30
 */
class AppCentre_Shop
{
    /**
     * $id,$password,$apiurl
     * @var string
     */
    protected static $id = '';
    protected static $password = '';
	protected static $apiurl = 'http://app.rainbowsoft.org/zb_users/plugin/AppBuy/api/index.php?api=';

    /**
     * 初始化配置
     * @param string $id
     * @param string $password
	 * @param string $apiurl
     */
    public static function init($id,$password)
    {
        self::$id = $id;
        self::$password = $password;
    }

	//核心API
    /**
     * 用户信息
     */
	public static function userinfo()
    {
		$postdata = array(
			'id' => self::$id,
			'password' => self::$password,
		);
		$results = json_decode(self::create_curl('userinfo',$postdata),true);
		if( $results['ret'] == 0 )
			return $results;
		else
			return false;
    }
	
    /**
     * 订单列表
     */
	public static function orderlist()
    {
		$postdata = array(
			'id' => self::$id,
			'password' => self::$password,
		);
		$results = json_decode(self::create_curl('orderlist',$postdata),true);
		if( $results['ret'] == 0 )
			return $results;
		else
			return false;
    }
	
    /**
     * 订单详情
     */
	public static function orderdetail($orderid)
    {
		$postdata = array(
			'id' => self::$id,
			'password' => self::$password,
			'orderid' => $orderid,
		);
		$results = json_decode(self::create_curl('orderdetail',$postdata),true);
		if( $results['ret'] == 0 )
			return $results;
		else
			return false;
    }

	//辅助
    /**
     * curl请求
     * @param string $post
     * @ignore
     */
    public static function create_curl($type, $post)
    {
		$url = self::$apiurl . $type;
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 86400 );
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$ch=curl_exec($ch);
		return $ch;
    }

}
