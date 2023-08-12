<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('Scheduler')) {$zbp->ShowError(48);die();}

if (count($_POST) > 0) {
    CheckIsRefererValid();
}

$blogtitle='计划任务';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if ($handle = GetVars('handle')) {
    CheckIsRefererValid();
    switch ($handle) {
        case 'disable':
            $zbp->Config('Scheduler')->disabled_jobs = AddNameInString($zbp->Config('Scheduler')->disabled_jobs ?: '', md5(GetVars('job')));
            break;
        case 'enable':
            $zbp->Config('Scheduler')->disabled_jobs = DelNameInString($zbp->Config('Scheduler')->disabled_jobs ?: '', md5(GetVars('job')));
            break;
        case 'reset_token':
            $zbp->Config('Scheduler')->token = Scheduler_RandomChars(32);
            break;
    }
    $zbp->SaveConfig('Scheduler');
    $zbp->SetHint('good');
    Redirect('./main.php');
}
?>
<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle;?></div>
    <div class="SubMenu">
    </div>
    <div id="divMain2">
        <table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
            <tr>
                <th colspan="4">任务列表</th>
            </tr>
            <tr>
                <td>编号</td>
                <td>名称</td>
                <td>描述</td>
                <td></td>
            </tr>
            <?php foreach(Scheduler::getJobs() as $key => $job): ?>
                <tr>
                    <td><?php echo $key+1 ?></td>
                    <td><?php echo $job->getName() ?></td>
                    <td><?php echo $job->getDescription() ?></td>
                    <td class="td10 tdCenter">
                        <?php if (!HasNameInString($zbp->Config('Scheduler')->disabled_jobs, md5($job->getName()))): ?>
                            <a href="./main.php?handle=disable&job=<?php echo $job->getName() ?>&csrfToken=<?php echo $zbp->GetCSRFToken() ?>" title="停用" class="btn-icon btn-disable"><i class="icon-cancel on"></i></a>
                        <?php else: ?>
                            <a href="./main.php?handle=enable&job=<?php echo $job->getName() ?>&csrfToken=<?php echo $zbp->GetCSRFToken() ?>" title="停用" class="btn-icon btn-enable"><i class="icon-power off"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
            <tr>
                <th colspan="2">运行调度程序</th>
            </tr>
            <tr>
                <th colspan="2">方式一（PHP CLI 调用）【推荐】</th>
            </tr>
            <tr>
                <td>运行文件</td>
                <td>
                    <?php echo $filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'run.php' ?>
                </td>
            </tr>
            <tr>
                <td>调用方式</td>
                <td>
                    在您的服务器 crontab 中增加一条：<br/>
                    * * * * * <?php echo $filepath ?> >> /dev/null 2>&1
                </td>
            </tr>
            <tr><td colspan="2"></td></tr>
            <tr>
                <th colspan="2">方式二（定时访问）</th>
            </tr>
            <tr>
                <td>访问网页</td>
                <td>
                    <?php echo $bloghost . 'zb_users/plugin/Scheduler/run.php?token=<span style="color:red">' . $zbp->Config('Scheduler')->token . '</span>' ?><br/>
                    <a href="./main.php?handle=reset_token&csrfToken=<?php echo $zbp->GetCSRFToken() ?>">重置 Token</a>
                </td>
            </tr>
            <tr>
                <td>调用方式</td>
                <td>
                    您可以通过相关服务（如云函数等）定期访问该网页触发
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
