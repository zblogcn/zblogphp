<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);exit();}
if (!$zbp->CheckPlugin('changyan')) {$zbp->ShowError(48);exit();}
$blogtitle='畅言评论系统';
require $blogpath . 'zb_system/admin/admin_header.php';
include_once dirname(__FILE__) . '/scripts.html';
include_once dirname(__FILE__) . '/header.html';
?>

<?php
require $blogpath . 'zb_system/admin/admin_top.php';

?>

<div id="divMain">
  <div class="divHeader2"><?php echo $blogtitle;?></div>
  <div class="SubMenu"><?php echo '';?></div>
  <div id="divMain2">
<?php
if($changyanPlugin->getOption('changyan_script')){
?>
<div id="divMain3" class="margin" style="width: 839px">
    <iframe id="rightBar_1" 
            name="rightBar_1" marginwidth="0" allowtransparency="true"
            src=<?php $script = $changyanPlugin->getOption('changyan_script');
                      $appID = explode("'", $script);
                      $appID = $appID[1];
                      echo "http://changyan.sohu.com/login?type=audit&from=wpplugin&appid=".$appID; ?> frameborder="0"
            scrolling="yes"></iframe>
</div>
<?php
}else{
?>
<div class="margin heiti" style="width: 800px">
    <table class="tableFull tableBorder">
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
    <br />
    <table class="tableFull tableBorder">
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
                            <br/><input type="button" id="appButton"
                                   class="button button-rounded button-primary" value="提交"
                                   onclick="saveAppKey_AppID();"
                                   style="width: 100px; text-align: center; vertical-align: middle" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br />
    <table class="tableFull tableBorder">
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
                        <br/><input type="button" id="appButton"
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
}
?>

    <script type="text/javascript">ActiveLeftMenu("a<?php echo !isset($_GET['act'])?'Comment':'Plugin' ?>Mng");</script> 
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/changyan/logo.png';?>");</script> 
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
