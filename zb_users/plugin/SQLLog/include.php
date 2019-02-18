<?php

RegisterPlugin('SQLLog', 'ActivePlugin_SQLLog');
define('SQLLOG_PATH', dirname(__FILE__));
define('SQLLOG_LOGPATH', SQLLOG_PATH . '/logs/');

function ActivePlugin_SQLLog()
{
    Add_Filter_Plugin('Filter_Plugin_DbSql_Filter', 'SQLLog_DbSql_Filter');
}

function InstallPlugin_SQLLog()
{
}

function UninstallPlugin_SQLLog()
{
}

function SQLLog_getip()
{
    $ip = GetVars('HTTP_CLIENT_IP', 'SERVER');
    $ip = ($ip == '') ? GetVars('HTTP_X_FORWARDED_FOR', 'SERVER') : $ip;
    $ip = ($ip == '') ? GetVars('REMOTE_ADDR', 'SERVER') : $ip;

    return $ip;
}

function SQLLog_DbSql_Filter($sql)
{
    global $zbp;
    $log_data = array(
        time(),
        date('H:i:s(e)', time()),
        $zbp->user->ID,
        SQLLog_getip(),
        $_SERVER['PHP_SELF'],
    );
    //	$time =
    $template = "【TIME=$log_data[0]】【FTIME=$log_data[1]】【UID=$log_data[2]】【PAGE=$log_data[4]】【IP=$log_data[3]】$sql" . PHP_EOL;
    $file_name = SQLLOG_LOGPATH . date('Ymd', time()) . '_zbp_' . $zbp->guid . '.php';
    if (!file_exists($file_name)) {
        $file_pointer = fopen($file_name, 'w');
        fwrite($file_pointer, '<?php\nerror_reporting(false);\n\nERROR!\n?>\n<!--begin-->\n' . PHP_EOL);
    } else {
        $file_pointer = fopen($file_name, 'a');
    }

    fwrite($file_pointer, $template);
    fclose($file_pointer);
}
