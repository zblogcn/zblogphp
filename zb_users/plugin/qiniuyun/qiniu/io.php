<?php

require_once "http.php";
require_once "auth_digest.php";

// ----------------------------------------------------------
// class Qiniu_PutExtra

class Qiniu_PutExtra {
	public $Params = null;
	public $MimeType = null;
	public $Crc32 = 0;
	public $CheckCrc = 0;
}

function Qiniu_Put($upToken, $key, $body, $putExtra) // => ($putRet, $err)
{
	global $QINIU_UP_HOST;

	if ($putExtra === null) {
		$putExtra = new Qiniu_PutExtra;
	}

	$client = new Qiniu_HttpClient;
	$client->ajax->open("POST", $QINIU_UP_HOST);
	$client->ajax->addText('token', $upToken);

	if ($key === null) {
		$fname = '?';
	} else {
		$fname = $key;
		$client->ajax->addText('key', $key);
	}
	if ($putExtra->CheckCrc) {
		$client->ajax->addText('crc32', $putExtra->Crc32);
	}
	if ($putExtra->Params) {
		foreach ($putExtra->Params as $k => $v) {
			$client->ajax->addText($k, $v);
		}
	}

	$client->ajax->addBinary('file', $body, $fname, $putExtra->MimeType);

	return Qiniu_Client_CallWithForm($client);
}

function Qiniu_PutFile($upToken, $key, $localFile, $putExtra) // => ($putRet, $err)
{
	global $QINIU_UP_HOST;

	if ($putExtra === null) {
		$putExtra = new Qiniu_PutExtra;
	}

	$client = new Qiniu_HttpClient;
	$client->ajax->open("POST", $QINIU_UP_HOST);
	$client->ajax->addBinary('file', $localFile, $putExtra->MimeType);
	$client->ajax->addText('token', $upToken);

	if ($key === null) {
		$fname = '?';
	} else {
		$fname = $key;
		$client->ajax->addText('key', $key);
	}
	if ($putExtra->CheckCrc) {
		if ($putExtra->CheckCrc === 1) {
			$hash = hash_file('crc32b', $localFile);
			$array = unpack('N', pack('H*', $hash));
			$putExtra->Crc32 = $array[1];
		}
		$client->ajax->addText('crc32', sprintf('%u', $putExtra->Crc32));
	}
	if ($putExtra->Params) {
		foreach ($putExtra->Params as $k => $v) {
			$client->ajax->addText($k, $v);
		}
	}

	return Qiniu_Client_CallWithForm($client, 'multipart/form-data');
}

// ----------------------------------------------------------
