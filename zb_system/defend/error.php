<?php
defined('ZBP_ERRORPROCESSING') || define('ZBP_ERRORPROCESSING', true);
if (!isset($GLOBALS['zbp'])) {
    //exit;
    $GLOBALS['zbp'] = new stdClass();
    $GLOBALS['zbp']->isdebug = (defined('ZBP_DEBUGMODE')) ? true : false;
}
$post_data = $_COOKIE;
foreach ($post_data as $key => $value) {
    if (stripos($key, 'username') !== false) {
        unset($post_data[$key]);
    }
    if (stripos($key, 'password') !== false) {
        unset($post_data[$key]);
    }
    if (stripos($key, 'token') !== false) {
        unset($post_data[$key]);
    }
}
unset($post_data['username']);
unset($post_data['password']);
unset($post_data['token']);
unset($post_data['addinfo']);
?>
<!doctype html>
<html lang="<?php echo $GLOBALS['lang']['lang_bcp47']; ?>">
<head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <meta name="generator" content="<?php echo $GLOBALS['option']['ZC_BLOG_PRODUCT_FULL']; ?>"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width,viewport-fit=cover" />
    <title><?php echo $GLOBALS['blogname'] . '-' . $GLOBALS['lang']['msg']['error']; ?></title>
    <link rel="stylesheet" href="<?php echo $GLOBALS['bloghost']; ?>zb_system/css/admin.css?<?php echo $GLOBALS['blogversion']; ?>" type="text/css" media="screen"/>
    <script src="<?php echo $GLOBALS['bloghost']; ?>zb_system/script/common.js?<?php echo $GLOBALS['blogversion']; ?>"></script>
    <?php
    foreach ($GLOBALS['hooks']['Filter_Plugin_Other_Header'] as $fpname => &$fpsignal) {
        $fpname();
    }
    ?>

</head>
<body class="error short">
<div class="bg">
    <div id="wrapper">
        <div class="logo"><img src="<?php echo $GLOBALS['bloghost']; ?>zb_system/image/admin/none.gif" title="Z-BlogPHP"
                               alt="Z-BlogPHP"/></div>
        <div class="login loginw">
            <form id="frmLogin" method="post" action="#">
                <?php
                if (!$GLOBALS['zbp']->isdebug) {
                    ?>
                    <div class="divHeader lessinfo" style="margin-bottom:10px;">
                        <b><?php echo FormatString($error->getMessage(), '[noscript]'); ?></b></div>
                    <div class="content lessinfo">
                        <div>
                            <p style="font-weight: normal;"><?php echo $GLOBALS['lang']['msg']['possible_causes_error']; ?></p>
                            <?php echo $error->possible_causes_of_the_error(); ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
    <?php
    if ($GLOBALS['zbp']->isdebug) {
        ?>
                    <div class="divHeader moreinfo"
                         style="margin-bottom:10px;"><?php echo $GLOBALS['lang']['msg']['error_tips']; ?></div>
                    <div class="content moreinfo">
                        <div>
                            <p><?php echo $GLOBALS['lang']['msg']['error_info']; ?></p>
        <?php
        echo '[' . $error->getType() . '] (' . $error->getCode() . ') :   ' . (FormatString($error->getMessageFull(), '[noscript]'));
        echo ' (' . ZC_VERSION_FULL . ') ';
        if (!in_array('Status: 404 Not Found', headers_list())) {
                echo '(' . GetEnvironment(true) . ') ';
        }
        ?>
                        </div>
        <?php
        $moreinfo = $error->getMoreInfo();
        if (is_array($moreinfo) && !empty($moreinfo)) {
            ?>
                        <div>
                            <p><?php echo 'Debug More Info'; ?></p>

                            <table style="width: 100%" class="table_striped">
                                <tbody>

                    <?php
                    $i = 0;
                    foreach ($moreinfo as $key => $value) {
                        $i += 1;
                        ?>
                                    <tr>
                                        <td style='width:50px;'><?php echo $key; ?></td>
                                        <td><?php echo htmlspecialchars(var_export($value, true)); ?></td>
                                    </tr>
                                    <?php
                    }
                    ?>

                                </tbody>
                            </table>
                        </div>
            <?php
        }
        ?>

                        <div>
                            <p><?php echo $GLOBALS['lang']['msg']['file_line']; ?></p>
                            <i><?php echo $error->getFile(); ?></i><br/>
                            <table style="width: 100%" class="table_striped">
                                <tbody>

                    <?php
                    $aFile = $error->get_code($error->getFile(), $error->getLine());
                    foreach ($aFile as $iInt => $sData) {
                        ?>
                                    <tr>
                                        <td style='width:50px' <?php echo ($iInt + 1) == $error->getLine() ? ' class="bg-lightcolor"' : ''; ?> ><?php echo ($iInt + 1); ?></td>
                                        <td <?php echo ($iInt + 1) == $error->getLine() ? ' class="bg-lightcolor"' : ''; ?> ><?php echo $sData; ?></td>
                                    </tr>
                                    <?php
                    }
                    ?>

                                </tbody>
                            </table>
                        </div>
                        <div>
                            <p><?php echo $GLOBALS['lang']['msg']['debug_backtrace']; ?></p>
                            <table style='width:100%' class="table_striped">
                                <tbody>
                    <?php
                    foreach ($error->getTrace() as $iInt => $sData) {
                        if ($iInt <= 2) { // 不显示错误捕捉部分
                            continue;
                        }
                        ?>
                                    <tr>
                                        <td style="width:50px"><?php echo ($iInt + 1); ?></td>
                                        <td><?php echo isset($sData['file']) ? $sData['file'] : 'Callback'; ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><code>(
                                <?php
                                if (isset($sData['line'])) {
                                            echo $sData['line'];
                                }
                                ?>
                                                )
                                                <?php
                                                echo isset($sData['class']) ? $sData['class'] . $sData['type'] : "";
                                                echo $sData['function'] . '(';
                                                if (isset($sData['args'])) {
                                                    foreach ($sData['args'] as $argKey => $argVal) {
                                                        echo $argKey . ' => ' . (CheckCanBeString($argVal) ? htmlspecialchars((string) $argVal) : 'Object') . ',';
                                                    }
                                                }
                                                echo ')';
                                                ?>
                                    </code></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <code>
                                                <?php
                                                if (isset($sData['line'])) {
                                                    $fileContent = $error->get_code($sData['file'], $sData['line']);
                                                    echo $fileContent[($sData['line'] - 1)];
                                                }
                                                ?>
                                            </code>
                                        </td>
                                    </tr>
                                    <?php
                    }
                    ?>

                                </tbody>
                            </table>
                        </div>
                        <div>
                            <p><?php echo $GLOBALS['lang']['msg']['request_data']; ?></p>
                            <pre><?php echo '$%_GET = ' . call_user_func('print_r', htmlspecialchars_array($_GET), 1); ?></pre>
                            <pre><?php echo '$%_POST = ' . call_user_func('print_r', htmlspecialchars_array($_POST), 1); ?></pre>
                            <pre><?php echo '$%_COOKIE = ' . call_user_func('print_r', htmlspecialchars_array($post_data), 1); ?></pre>
                        </div>
                        <div>
                            <p><?php echo $GLOBALS['lang']['msg']['include_file']; ?></p>
                            <table style='width:100%' class="table_striped">
                                <tbody>
                                <?php
                                foreach (get_included_files() as $iInt => $sData) {
                                    ?>
                                    <tr>
                                        <td style='width:30px'><?php echo $iInt; ?></td>
                                        <td><?php echo $sData; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>

                                </tbody>
                            </table>
                        </div>
                        <div>
                            <p><?php echo 'Error List'; ?></p>
                            <table style="width: 100%" class="table_striped">
                                <tbody>

                    <?php
                    $error_list = ZbpErrorControl::GetErrorList();
                    $i = 0;
                    foreach ($error_list as $key => $value) {
                        $i += 1;
                        ?>
                                    <tr>
                                        <td style='width:50px;'><?php echo $key; ?></td>
                                        <td><?php echo htmlspecialchars(var_export($value, true)); ?></td>
                                    </tr>
                                    <?php
                    }
                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
    }
    ?>

                <div class="goback">
                    <a href="javascript:history.back(-1);"><?php echo $GLOBALS['lang']['msg']['back']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="javascript:location.reload();"><?php echo $GLOBALS['lang']['msg']['refresh']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo $GLOBALS['bloghost']; ?>zb_system/cmd.php?act=login"><?php echo $GLOBALS['lang']['msg']['login']; ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>