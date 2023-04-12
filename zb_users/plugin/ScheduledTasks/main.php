<?php
require dirname(__FILE__) . '/../../../zb_system/function/c_system_base.php';
require dirname(__FILE__) . '/../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('ScheduledTasks')) {
    $zbp->ShowError(48);
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //$zbp->SaveConfig('zbpcache');
    $zbp->SetHint('good');
    Redirect('./main.php');
}

$blogtitle = 'ScheduledTasks-设置';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';


?>
<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle; ?></div>
    <div class="SubMenu">

    </div>
    <div id="divMain2">

        <form class="config" id="config" method="post" action="config.php">
            <table class="tableFull tableBorder tableBorder-thcenter">
                <tr>
                    <th colspan="2">&nbsp;计划任务列表</th>
                </tr>
<?php
//先加载所有tasks目录下的文件
foreach (glob(__DIR__ . "/tasks/*.php") as $filename) {
    include($filename);
}

foreach ($ScheduledTasks_Data as $task) {
    echo '<tr>';
    echo '<td class="td30"><b>';
    echo $task['id'];
    echo '</b></td>';
    echo '<td>';
    echo '名称：'.$task['name'].'<br/>';
    echo '执行函数：'.$task['function'].'<br/>';
    echo '间隔时间：'.$task['interval'].'分钟<br/>';
    echo '</td>';
    echo '</tr>';
}
?>
            </table>
        </form>

            <table class="tableFull tableBorder tableBorder-thcenter">
                <tr>
                    <th colspan="2">&nbsp;run.php的Token</th>
                </tr>
                <tr>
                    <td>
                        <b>Token</b>
                    </td>
                    <td>
                        <?php echo ScheduledTasks_GetToken();?>
                    </td>
                </tr>
            </table>

            <table class="tableFull tableBorder tableBorder-thcenter">
                <tr>
                    <th>&nbsp;使用说明</th>
                </tr>
                <tr>
                    <td>
                        <p>
<b>注册新任务</b><br/>

在tasks目录下复制task_test.php为一个新的php文件，并修改其中的关键内容。<br/>

<b>定时运行</b><br/>

在linux下执行crontab并加入<br/>

* * * * * curl "http://你的网站/zb_users/plugin/ScheduledTasks/run.php?token={token}" -s -o /dev/null<br/>

{token} 为 $zbp->Config('ScheduledTasks')->Token的值或是本页显示的Token值<br/>

也可以由其它的定时器触发，比如各种云函数定时访问。<br/>
                        </p>
                    </td>
                </tr>
            </table>

            <script type="text/javascript">
                ActiveLeftMenu("azbpcache");
            </script>
            <script type="text/javascript">
                AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ScheduledTasks/logo.png'; ?>");
            </script>
    </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>