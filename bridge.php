<?php
require './zb_system/function/c_system_base.php';
$method = $_SERVER['REQUEST_METHOD'];
$network = Network::Create();
$network->open($method, $_GET['url']);
$network->setRequestHeader("auth_bizcode", $_SERVER['HTTP_AUTH_BIZCODE']);
$network->setRequestHeader("auth_guid", $_SERVER['HTTP_AUTH_GUID']);
$network->setRequestHeader("auth_timestamp", $_SERVER['HTTP_AUTH_TIMESTAMP']);
$network->setRequestHeader("auth_token", $_SERVER['HTTP_AUTH_TOKEN']);
if (strtoupper($method) == "POST") {
	$network->setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	$network->send(http_build_query($_POST));
} else {
	$network->send();
}
echo $network->responseText;