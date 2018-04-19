<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('Totoro')) {
    $zbp->ShowError(48);
    die();
}
Totoro_init();
$blogtitle = 'Totoro反垃圾评论';
require $blogpath . 'zb_system/admin/admin_header.php';
?>
<style type="text/css">
    .text-config {
        width: 95%
    }
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';

?>

<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle; ?></div>
    <div class="SubMenu"><?php echo $Totoro->export_submenu('main'); ?></div>
    <div id="divMain2">
        <form id="edit" name="edit" method="post" action="save_setting.php">
            <?php if (function_exists('CheckIsRefererValid')) {
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
}?>
            <input id="reset" name="reset" type="hidden" value="" />
            <div class="content-box">
                <!-- Start Content Box -->

                <div class="content-box-header">
                    <ul class="content-box-tabs">
                        <li><a href="#tab1" class="default-tab"><span>加分减分细则设置</span></a></li>
                        <li><a href="#tab2"><span>过滤列表设置</span></a></li>
                        <li><a href="#tab3"><span>提示语设置</span></a></li>
                        <li><a href="#tab4"><span>内容审查细则设置</span></a></li>
                        <li><a href="#tab5"><span>相似判断设置</span></a></li>
                        <li><a href="#tab-about"><span>关于TotoroⅢ</span></a></li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <!-- End .content-box-header -->
                <div class="content-box-content" id="totorobox">
                    <div class="tab-content default-tab" style="border:none;padding:0px;margin:0;" id="tab1">
                        <table border="1" class="tableFull tableBorder table_hover table_striped">

                            <thead>
                                <tr>
                                    <th style="width:5%">
                                        <p align="left"><b>序号</b></p>
                                    </th>
                                    <th style="width:15%">
                                        <p align="left"><b>规则</b></p>
                                    </th>
                                    <th style="width:15%">
                                        <p align="left"><b>分数</b></p>
                                    </th>
                                    <th>
                                        <p align="left"><b>信息</b></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($Totoro->config_array['SV_RULE'] as $name => $value) {
                                    ?>
                                                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td>
                                        <p align="left"><b><?php echo $value['NAME'] ?></b></p>
                                    </td>
                                    <td>
                                        <input type="text" class="text-config" name="TOTORO_SV_RULE_<?php echo $name ?>" value="<?php echo $Totoro->output_config('SV_RULE', $name) ?>" />
                                    </td>
                                    <td>(默认：<?php echo $value['DEFAULT'] ?>) <?php echo $value['DESC'] ?></td>
                                </tr>
                                                                <?php
                                                                $i++;
                                }
                                foreach ($Totoro->config_array['SV_SETTING'] as $name => $value) {
                                    ?>
                                                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td>
                                        <p align="left"><b><?php echo $value['NAME'] ?></b></p>
                                    </td>
                                    <td>
                                        <input type="text" class="text-config" name="TOTORO_SV_SETTING_<?php echo $name ?>" value="<?php echo $Totoro->output_config('SV_SETTING', $name) ?>" />
                                    </td>
                                    <td>(默认：<?php echo $value['DEFAULT'] ?>) <?php echo $value['DESC'] ?></td>
                                </tr>
                                                                <?php
                                                                $i++;
                                }
?>
                            </tbody>
                        </table>
                    </div>
                    <!-- line -->
                    <div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab2">
                        <table border="1" class="tableFull tableBorder table_hover table_striped">
                            <thead>
                                <tr>
                                    <th style="width:25%">
                                        <p align="left"><b>内容</b></p>
                                    </th>
                                    <th>
                                        <p align="left"><b>值</b></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($Totoro->config_array['BLACK_LIST'] as $name => $value) {
                                    ?>
                                                                <tr>
                                    <td>
                                        <p align="left"><b><?php echo $value['NAME'] ?></b>
                                            <br/> · <?php echo $value['DESC'] ?></p>
                                    </td>
                                    <td>
                                        <textarea class="escape-textarea" name="TOTORO_BLACK_LIST_<?php echo $name ?>" style="display:none" id="TOTORO_BLACK_LIST_<?php echo $name ?>" data-tag="TOTORO_BLACK_LIST_UNESCAPE_<?php echo $name ?>"><?php echo urlencode($Totoro->output_config('BLACK_LIST', $name, false)) ?></textarea>
                                        <textarea class="unescape-textarea" rows="6" style="width:95%" data-tag="TOTORO_BLACK_LIST_<?php echo $name ?>" id="TOTORO_BLACK_LIST_UNESCAPE_<?php echo $name ?>">数据读取中</textarea>
                                    </td>
                                </tr>
                                                                <?php
                                }
?>
                            </tbody>
                        </table>
                    </div>
                    <!-- line -->
                    <div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab3">
                        <table border="1" class="tableFull tableBorder table_hover table_striped">
                            <thead>
                                <tr>
                                    <th style="width:25%">
                                        <p align="left"><b>内容</b></p>
                                    </th>
                                    <th>
                                        <p align="left"><b>值</b></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($Totoro->config_array['STRING_BACK'] as $name => $value) {
                                    ?>
                                                                <tr>
                                    <td>
                                        <p align="left"><b><?php echo $value['NAME'] ?></b></p>
                                    </td>
                                    <td>
                                        <textarea class="unescape-textarea" name="TOTORO_STRING_BACK_<?php echo $name ?>" rows="6" style="width:95%"><?php echo $Totoro->output_config('STRING_BACK', $name, false) ?></textarea>
                                    </td>
                                </tr>
                                                                <?php
                                }
?>
                            </tbody>
                        </table>
                    </div>
                    <!-- line -->
                    <div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab4">
                        <table border="1" class="tableFull tableBorder table_hover table_striped">
                            <thead>
                                <tr>
                                    <th style="width:25%">
                                        <p align="left"><b>内容</b></p>
                                    </th>
                                    <th>
                                        <p align="left"><b>值</b></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($Totoro->config_array['BUILD_CONFIG'] as $name => $value) {
                                    ?>
                                                                <tr>
                                    <td>
                                        <p align="left"><b><?php echo $value['NAME'] ?></b>
                                            <br/> · <?php echo $value['DESC'] ?></p>
                                    </td>
                                    <td>
                                        <input type="text" class="checkbox" name="TOTORO_BUILD_CONFIG_<?php echo $name ?>" value="<?php echo $Totoro->output_config('BUILD_CONFIG', $name) ?>" />
                                    </td>
                                </tr>
                                                                <?php
                                }
?>
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab5">
                        <table border="1" class="tableFull tableBorder table_hover table_striped">

                            <thead>
                                <tr>
                                    <th style="width:5%">
                                        <p align="left"><b>序号</b></p>
                                    </th>
                                    <th style="width:15%">
                                        <p align="left"><b>规则</b></p>
                                    </th>
                                    <th style="width:15%">
                                        <p align="left"><b>分数</b></p>
                                    </th>
                                    <th>
                                        <p align="left"><b>信息</b></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($Totoro->config_array['SIMILAR_CONFIG'] as $name => $value) {
                                    ?>
                                                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td>
                                        <p align="left"><b><?php echo $value['NAME'] ?></b></p>
                                    </td>
                                    <td>
                                        <input type="text" class="text-config" name="TOTORO_SIMILAR_CONFIG_<?php echo $name ?>" value="<?php echo $Totoro->output_config('SIMILAR_CONFIG', $name) ?>" />
                                    </td>
                                    <td>(默认：<?php echo $value['DEFAULT'] ?>) <?php echo $value['DESC'] ?></td>
                                </tr>
                                                                <?php
                                                                $i++;
                                }
?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab-about">
                        <dl class="totoro">
                            <dd>Totoro是个采用评分机制的防止垃圾留言的插件，原作<a href="http://www.rainbowsoft.org/" target="_blank">zx.asd</a>。
                                <br/> TotoroⅡ是
                                <a href="http://ZxMYS.COM" target="_blank">Zx.MYS</a>在Totoro的基础上修改而成的增强版，加入了诸多新特性，同时修正一些问题。
                                <br/> TotoroⅢ及Totoro For Z-BlogPHP是由<a href="http://www.zsxsoft.com" target="_blank">zsx</a>将TotoroII升级到2.0版本后增添新特性并移植的版本。</dd>
                            <dd>Spam Value(SV)初始值为0，经过相关运算后的SV分值越高Spam嫌疑越大，超过设定的阈值这条评论就进入审核状态或直接被删除。</dd>
                            <dd>配置完成之后，请一定要测试，切记切记！</dd>
                            <dd></dd>
                        </dl>
                    </div>
                    <!--<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab-init">
            <dl class="totoro">
              <dd>若您的配置因为主机的关键词过滤而失效，您可以点击右侧按钮初始化Totoro设置以修复。
                <input style="float:right" class="button" type="button" value="初始化Totoro设置" onClick="if(confirm('您确定要初始化Totoro设置吗？该操作不可逆！')){$('#edit').attr('action','savesetting.asp?act=delall');$('#edit').submit()}"/>
              </dd>
              <dd></dd>
            </dl>
          </div>-->
                </div>
            </div>
            <hr/>
            <p>
                <input type="submit" class="button" value="<?php echo $lang['msg']['submit'] ?>" />
            </p>
        </form>
        <script type="text/javascript">
            $(function() {
                $(".escape-textarea").each(function() {
                    var self = $(this);
                    $("#" + self.attr("data-tag")).text(decodeURIComponent(self.val()));
                });

                $("#edit").submit(function() {
                    $(".unescape-textarea").each(function() {
                        var self = $(this);
                        $("#" + self.attr("data-tag")).text(encodeURIComponent(self.val()));
                    });
                });

            });
        </script>
        <script type="text/javascript">
            ActiveLeftMenu("aPluginMng");
            AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Totoro/logo.png'; ?>");
        </script>
    </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
