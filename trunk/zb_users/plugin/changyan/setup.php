<?php
ini_set('max_execution_time', '0');
require_once CHANGYAN_PLUGIN_PATH . '/Handler.php';
$changyanPlugin = Changyan_Handler::getInstance();

include_once dirname(__FILE__) . '/header.html';
?>

<div class="margin">
    <img src="<?php echo plugin_dir_url(__FILE__) . 'changyan.png'; ?>"
         align="bottom" />
    <HR style="text-align: left; margin-left: 0; margin-right: 15px"
        color="grey" SIZE=1 />
</div>

<div class="margin heiti" style="width: 800px">
    <br />
    <table>
        <tr>
            <td>
                <p class="start">&nbsp;</p>
            </td>
            <td>
                <h3>注册账号</h3>
            </td>
        </tr>
        <tr>
            <td />
            <td>
                <span class="high">请按照提示<a
                        href="http://changyan.sohu.com/register" target="blank">注册</a>账号。
                </span>
            </td>
        </tr>
    </table>
    <br /><br />
    <table>
        <tr>
            <td>
                <p class="start">&nbsp;</p>
            </td>
            <td>
                <h3>账号设置</h3>
            </td>
        </tr>
        <tr>
            <td />
            <td>
                <span class="high">请在<a
                        href="http://changyan.sohu.com/manage"
                        target="blank">畅言站长管理后台</a>依次选择 “站点设置” - “通用设置” 获取APP ID和APP KEY并在下方提交。
                </span>
            </td>
        </tr>

        <tr>
            <td />
            <td>
                <table>
                    <tr>
                        <td style="color: SteelBlue">APP ID:</td>
                    </tr>
                    <tr>
                        <td><input type="text" id="appID" style="width: 210px"
                                   value="<?php
                                   $aScript = $changyanPlugin->getOption('changyan_script');
                                   if (!empty($aScript)) {
                                       $appID = explode("'", $aScript);
                                       $appID = $appID[1];
                                       echo trim($appID);
                                   }
                                   ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="color: SteelBlue">APP KEY:</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="appKey" style="width: 210px"
                                   value="<?php
                                   $appKey = $changyanPlugin->getOption('changyan_appKey');
                                   if (!empty($appKey)) {
                                       echo trim($appKey);
                                   }
                                   ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            <input type="button" id="appButton"
                                   class="button button-rounded button-primary" value="提交"
                                   onclick="saveAppKey_AppID();"
                                   style="width: 100px; text-align: center; vertical-align: middle" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br /><br />
    <table>
        <tr>
            <td>
                <p class="start">&nbsp;</p>
            </td>
            <td>
                <h3>数据同步</h3>
            </td>
        </tr>
        <tr>
            <td />
            <td><span> 将本地数据库中的评论同步到畅言，即刻享受畅言带来的便利。 </span>
            </td>
        </tr>
        <tr>
            <td />
            <td>
                <div id="cyan-WP2cyan">
                    <p class="message-start">
                        <input type="button" id="appButton"
                               class="button button-rounded button-primary" value="同步本地评论到畅言"
                               onclick="sync2Cyan('T');"
                               style="width: 160px; text-align: center; vertical-align: middle" />
                    </p>

                    <p class="status"></p>

                    <p class="message-complete">同步完成</p>
                </div>
            </td>
        </tr>
    </table>
</div>

<?php
include_once dirname(__FILE__) . '/scripts.html';
?>
