<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
set_error_handler(create_function('', ''));
set_exception_handler(create_function('', ''));
register_shutdown_function(create_function('', ''));
set_error_handler('api_error_handler');
set_exception_handler('api_exception_handler');
register_shutdown_function('api_shutdown_error_handler');

/**
 * api_format_exception.
 *
 * @param ZBlogException &$zbe
 *
 * @return true
 */
function api_format_exception(&$zbe)
{
    $code = $zbe->get_code($zbe->file, $zbe->line);
    $code = $code[$zbe->line - 1];

    $traces = array();
    foreach (debug_backtrace() as $iInt => $sData) {
        if ($iInt <= 1) { // That's error trigger
            continue;
        }
        $trace = array();
        $trace['file'] = isset($sData['file']) ? $sData['file'] : 'Callback';
        $trace['line'] = isset($sData['line']) ? $sData['line'] : '';
        $trace['class'] = isset($sData['class']) ? $sData['class'] . $sData['type'] : "";
        $trace['function'] = $sData['function'];
        if (isset($sData['line'])) {
            $fileContent = $zbe->get_code($sData['file'], $sData['line']);
            $trace['line'] = $fileContent[$sData['line'] - 1];
        } else {
            $trace['line'] = '';
        }
        array_push($traces, $trace);
    }

    $requestData = array(
        'get'    => $_GET,
        'post'   => $_POST,
        'cookie' => $_COOKIE,
    );
    unset($requestData['cookie']['password']);

    API::$IO->type = $zbe->type;
    API::$IO->typename = $zbe->typename;
    API::$IO->message = $zbe->message;
    API::$IO->file = $zbe->file;
    API::$IO->line = $zbe->line;
    API::$IO->code = $code;
    API::$IO->trace = $traces;
    API::$IO->include = get_included_files();
    API::$IO->end(0);
    exit;
}

/**
 * api_error_handler.
 *
 * @param int    $errno
 * @param string $errstr
 * @param string $errfile
 * @param int    $errline
 * @param array  $errcontext
 *
 * @return true
 */
function api_error_handler($errno, $errstr, $errfile, $errline)
{
    $zbe = ZBlogException::GetInstance();
    $zbe->ParseError($errno, $errstr, $errfile, $errline);
    //Http500();
    api_format_exception($zbe);
}

/**
 * api_exception_handler.
 *
 * @param array $exception
 *
 * @return true
 */
function api_exception_handler($exception)
{

    //ob_clean();
    $zbe = ZBlogException::GetInstance();
    $zbe->ParseException($exception);
    //Http500();
    api_format_exception($zbe);
}

/**
 * register_shutdown_function.
 *
 * @return true
 */
function api_shutdown_error_handler()
{
    if ($error = error_get_last()) {
        //ob_clean();
        $zbe = ZBlogException::GetInstance();
        $zbe->ParseShutdown($error);
        //Http500();
        api_format_exception($zbe);
    }
}
