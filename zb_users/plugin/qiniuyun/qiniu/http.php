<?php

require_once "auth_digest.php";
require_once "conf.php";

// --------------------------------------------------------------------------------
// class Qiniu_Error

class Qiniu_Error {
	public $Err; // string
	public $Reqid; // string
	public $Details; // []string
	public $Code; // int

	public function __construct($code, $err) {
		$this->Code = $code;
		$this->Err = $err;
	}
}

// --------------------------------------------------------------------------------
// class Qiniu_Response

class Qiniu_Response {
	public $StatusCode;
	public $Header;
	public $ContentLength;
	public $Body;

	public function __construct($code, $body) {
		$this->StatusCode = $code;
		$this->Header = array();
		$this->Body = $body;
		$this->ContentLength = strlen($body);
	}
}

function Qiniu_Header_Get($header, $key) // => $val
{
	$val = @$header[$key];
	if (isset($val)) {
		if (is_array($val)) {
			return $val[0];
		}
		return $val;
	} else {
		return '';
	}
}

function Qiniu_ResponseError($resp) // => $error
{
	$header = $resp->Header;
	$details = Qiniu_Header_Get($header, 'X-Log');
	$reqId = Qiniu_Header_Get($header, 'X-Reqid');
	$err = new Qiniu_Error($resp->StatusCode, null);

	if ($err->Code > 299) {
		if ($resp->ContentLength !== 0) {
			if (Qiniu_Header_Get($header, 'Content-Type') === 'application/json') {
				$ret = json_decode($resp->Body, true);
				$err->Err = $ret['error'];
			}
		}
	}
	$err->Reqid = $reqId;
	$err->Details = $details;
	return $err;
}

// --------------------------------------------------------------------------------
// class Qiniu_Client

function Qiniu_Client_incBody($ajax) // => $incbody
{
	$body = $ajax->postdata;
	if (count($body) === 0) {
		return false;
	}
	if (isset($ajax->httpheader['Content-Type'])) {
		$ct = $ajax->httpheader['Content-Type'];
		if ($ct === 'application/x-www-form-urlencoded') {
			return true;
		}
	}

	return false;
}

function Qiniu_Client_do($ajax) // => ($resp, $error)
{

	$httpHeader = $req->Header;
	if (!empty($httpHeader)) {
		$header = array();
		foreach ($httpHeader as $key => $parsedUrlValue) {
			$ajax->setRequestHeader($key, $parsedUrlValue);
		}
	}
	$ajax->send();

	$responseArray = explode("\r\n\r\n", $result);
	$responseArraySize = sizeof($responseArray);
	$respHeader = $ajax->getAllResponseHeaders();
	$respBody = $ajax->responseText;

	list($reqid, $xLog) = getReqInfo($respHeader);

	$resp = new Qiniu_Response($ajax->status, $respBody);
	$resp->Header['Content-Type'] = $contentType;
	$resp->Header["X-Reqid"] = $reqid;
	return array($resp, null);
}

function getReqInfo($headerContent) {
	$headers = explode("\r\n", $headerContent);
	$reqid = null;
	$xLog = null;
	foreach ($headers as $header) {
		$header = trim($header);
		if (strpos($header, 'X-Reqid') !== false) {
			list($k, $v) = explode(':', $header);
			$reqid = trim($v);
		} elseif (strpos($header, 'X-Log') !== false) {
			list($k, $v) = explode(':', $header);
			$xLog = trim($v);
		}
	}
	return array($reqid, $xLog);
}

class Qiniu_HttpClient {
	public $ajax;
	public function RoundTrip() // => ($resp, $error)
	{
		return Qiniu_Client_do($this->ajax);
	}

	public function __construct() {
		$this->ajax = Network::Create('fsockopen');
	}
}

class Qiniu_MacHttpClient {
	public $Mac;
	public $ajax;

	public function __construct($mac) {
		$this->Mac = Qiniu_RequireMac($mac);
	}

	public function RoundTrip() // => ($resp, $error)
	{
		$incbody = Qiniu_Client_incBody();
		$token = $this->Mac->SignRequest($this->ajax, $incbody);
		$ajax->setReuqestHeader('Authorization', "QBox $token");
		return Qiniu_Client_do();
	}
}

// --------------------------------------------------------------------------------

function Qiniu_Client_ret($resp) // => ($data, $error)
{
	$code = $resp->StatusCode;
	$data = null;
	if ($code >= 200 && $code <= 299 || $code == 100) {
		if ($resp->ContentLength !== 0) {
			$data = json_decode($resp->Body, true);
			if ($data === null) {
				$err_msg = function_exists('json_last_error_msg') ? json_last_error_msg() : "error with content:" . $resp->Body;
				$err = new Qiniu_Error(0, $err_msg);
				return array(null, $err);
			}
		}
		if ($code === 200 || $code === 100) {
			return array($data, null);
		}
	}
	return array($data, Qiniu_ResponseError($resp));
}

function Qiniu_Client_Call($self, $url) // => ($data, $error)
{
	$resp = $self->RoundTrip();
	return Qiniu_Client_ret($resp);
}

function Qiniu_Client_CallNoRet($self, $url) // => $error
{
	$resp = $self->RoundTrip();
	return Qiniu_ResponseError($resp);
}

function Qiniu_Client_CallWithForm(
	$self, $contentType = 'application/x-www-form-urlencoded') // => ($data, $error)
{

	if ($contentType !== 'multipart/form-data') {
		$self->ajax->setRequestHeader('Content-Type', $contentType);
	}
	list($resp, $err) = $self->RoundTrip();
	if ($err !== null) {
		return array(null, $err);
	}
	return Qiniu_Client_ret($resp);
}

function Qiniu_escapeQuotes($str) {
	$find = array("\\", "\"");
	$replace = array("\\\\", "\\\"");
	return str_replace($find, $replace, $str);
}

// --------------------------------------------------------------------------------
