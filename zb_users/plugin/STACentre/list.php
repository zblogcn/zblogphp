<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('STACentre')) {
    $zbp->ShowError(68);
    die();
}

$blogtitle = '静态管理中心';

if (count($_GET) > 0) {
    if (function_exists('CheckIsRefererValid')) {
        CheckIsRefererValid();
    }
    if (GetVars('mak', 'GET') == '1') {
        @file_put_contents($zbp->path . '.htaccess', show_htaccess());
    } elseif (GetVars('mak', 'GET') == '2') {
        @file_put_contents($zbp->path . 'web.config', show_webconfig());
    } elseif (GetVars('mak', 'GET') == '3') {
        @file_put_contents($zbp->path . 'httpd.ini', show_httpini());
    }

    if (GetVars('del', 'GET') == '1') {
        @unlink($zbp->path . '.htaccess');
    } elseif (GetVars('del', 'GET') == '2') {
        @unlink($zbp->path . 'web.config');
    } elseif (GetVars('del', 'GET') == '3') {
        @unlink($zbp->path . 'httpd.ini');
    }

    $zbp->SetHint('good');

    Redirect('./list.php');
}

function show_htaccess()
{
    $ur = new UrlRule("");

    return $ur->Make_htaccess();
}

function show_httpini()
{
    $ur = new UrlRule("");

    return $ur->Make_httpdini();
}

function show_webconfig()
{
    $ur = new UrlRule("");

    return $ur->Make_webconfig();
}

function show_nginx()
{
    $ur = new UrlRule("");
    if (method_exists('UrlRule', 'Make_nginx')) {
        return $ur->Make_nginx();
    }
}

function show_lighttpd()
{
    $ur = new UrlRule("");
    if (method_exists('UrlRule', 'Make_lighttpd')) {
        return $ur->Make_lighttpd();
    }
}

if (!function_exists('BuildSafeURL')) {
    function BuildSafeURL($url, $appId = '')
    {
        global $zbp;
        if (substr($url, 0, 1) === '/') {
            $url = $zbp->host . $url;
        }

        return $url;
    }
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

$default_tab = strtolower($_SERVER["SERVER_SOFTWARE"]);
if (strpos($default_tab, 'apache') !== false) {
    $default_tab = 1;
} elseif (strpos($default_tab, 'iis/6') !== false) {
    $default_tab = 3;
} elseif (strpos($default_tab, 'nginx') !== false) {
    $default_tab = 4;
} elseif (strpos($default_tab, 'lighttpd') !== false) {
    $default_tab = 5;
} else {
    $default_tab = 2;
}
?>
<div id="divMain">

  <div class="divHeader">
    <?php echo $blogtitle; ?></div>
  <div class="SubMenu">
    <a href="main.php">
      <span class="m-left">配置页面</span>
    </a>
    <a href="list.php">
      <span class="m-left m-now">ReWrite规则</span>
    </a>
    <a href="help.php">
      <span class="m-right">帮助</span>
    </a>
  </div>
  <div id="divMain2">
    <?php if ($zbp->option['ZC_STATIC_MODE'] == 'ACTIVE') {
    ?>
    <p>动态模式下不生成静态规则.</p>
    <?php
} else {
        ?>
    <form id="edit" name="edit" method="post" action="#">
        <?php if (function_exists('CheckIsRefererValid')) {
            echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
        } ?>
        <input id="reset" name="reset" type="hidden" value="" />

      <div class="content-box">
        <!-- Start Content Box -->

        <div class="content-box-header">
          <ul class="content-box-tabs">
            <li>
              <a href="#tab1" <?php echo $default_tab == 1 ? 'class="default-tab"' : ''; ?>>
                <span>Apache + .htaccess</span>
              </a>
            </li>
            <li>
              <a href="#tab2" <?php echo $default_tab == 2 ? 'class="default-tab"' : ''; ?>>
                <span>IIS 7及以上  + URL Rewrite Module</span>
              </a>
            </li>
            <li>
              <a href="#tab3" <?php echo $default_tab == 3 ? 'class="default-tab"' : ''; ?>>
                <span>IIS 6 + ISAPI Rewrite 2.X</span>
              </a>
            </li>
            <li>
              <a href="#tab4" <?php echo $default_tab == 4 ? 'class="default-tab"' : ''; ?>>
                <span>Nginx</span>
              </a>
            </li>
            <li>
              <a href="#tab5" <?php echo $default_tab == 5 ? 'class="default-tab"' : ''; ?>>
                <span>Lighttpd</span>
              </a>
            </li>
            <li>
              <a href="#tab6" <?php echo $default_tab == 6 ? 'class="default-tab"' : ''; ?>>
                <span>无组件</span>
              </a>
            </li>
          </ul>
          <div class="clear"></div>
        </div>
        <!-- End .content-box-header -->

        <div class="content-box-content">

          <div class="tab-content <?php echo $default_tab == 1 ? 'default-tab' : ''; ?>" style='border:none;padding:0px;margin:0;' id="tab1">
            <textarea style="width:99%;height:200px" readonly><?php echo htmlentities(show_htaccess())?></textarea>
            <hr/>
            <p>
              <input type="button" onclick="window.location.href='<?php echo BuildSafeURL('?mak=1'); ?>'" value="创建.htaccess" />
              &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="button" onclick="window.location.href='<?php echo BuildSafeURL('?del=1'); ?>'" value="删除.htaccess" />
              <hr/>
              <span class="star">
                请在网站 <u>"当前目录"</u>
                创建.htaccess文件并把相关内容复制进去,也可以点击按钮生成.
              </span>
              <hr/>
              提示:ISAPI Rewrite 3也适用于此规则.
            </p>
          </div>

          <div class="tab-content <?php echo $default_tab == 2 ? 'default-tab' : ''; ?>" style='border:none;padding:0px;margin:0;' id="tab2">
            <textarea style="width:99%;height:400px" readonly><?php echo htmlentities(show_webconfig())?></textarea>
            <hr/>
            <p>
              <input type="button" onclick="window.location.href='<?php echo BuildSafeURL('?mak=2'); ?>'" value="创建web.config" />
              &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="button" onclick="window.location.href='<?php echo BuildSafeURL('?del=2'); ?>'" value="删除web.config" />
              <hr/>
              <span class="star">
                请在网站 <u>"当前目录"</u>
                创建web.config文件并把相关内容复制进去,也可以点击按钮生成.
              </span>
            </p>
          </div>

          <div class="tab-content <?php echo $default_tab == 3 ? 'default-tab' : ''; ?>" style='border:none;padding:0px;margin:0;' id="tab3">
            <textarea id="ta_httpini" style="width:99%;height:200px" readonly><?php echo htmlentities(show_httpini())?></textarea>
            <hr/>
            <p>
              <input type="button" onclick="window.location.href='<?php echo BuildSafeURL('?mak=3'); ?>'" value="创建httpd.ini" />
              &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="button" onclick="window.location.href='<?php echo BuildSafeURL('?del=3'); ?>'" value="删除httpd.ini" />
              <hr/>
              <span class="star">
                请在网站根目录创建httpd.ini文件并把相关内容复制进去,httpd.ini文件必须为ANSI编码,也可以点击按钮生成.
              </span>
              <hr/>
              提示:本规则用户可以加入自定义规则,将自己的目录或是文件排除过于广泛的重写之外.
              <hr/>
              提示:ISAPI Rewrite 3请按Apache规则生成.
            </p>
          </div>

          <div class="tab-content <?php echo $default_tab == 4 ? 'default-tab' : ''; ?>" style='border:none;padding:0px;margin:0;' id="tab4">
            <textarea style="width:99%;height:200px" readonly><?php echo htmlentities(show_nginx())?></textarea>
            <hr/>
            <p>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <span class="star">
                修改nginx.conf,在  location / { }节点 或者是 location [安装目录名称] / { }（子目录安装）节点间加入上述规则.
              </span>
            </p>
          </div>

          <div class="tab-content <?php echo $default_tab == 5 ? 'default-tab' : ''; ?>" style='border:none;padding:0px;margin:0;' id="tab5">
            <textarea style="width:99%;height:250px" readonly><?php echo htmlentities(show_lighttpd())?></textarea>
            <hr/>
            <p>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <span class="star">
                在主机控制面板的lighttpd静态规则中加入,或是修改/etc/lighttpd/lighttpd.conf加入上述规则.
              </span>
            </p>
          </div>

          <div class="tab-content <?php echo $default_tab == 6 ? 'default-tab' : ''; ?>" style='border:none;padding:0px;margin:0;' id="tab6">
            <p>你可以到主机的控制面板中找到类似“自定义404错误提示页面”等，填入：
            <code><?php echo str_replace("zb_users/plugin/STACentre/list.php", "index.php", $_SERVER['PHP_SELF']); ?></code></p>
            <p>如下图所示，这样你的网站将被Z-BlogPHP全面接管。此方案不需要伪静态组件，且发送的状态码符合规范。</p>
            <p><img src="data:image/png;base64,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"/></p>
            <p>IIS 7 / 8用户也可以创建<code>web.config</code>放在站点目录下，填入代码：</p>
            <textarea style="width:99%;height:250px" readonly>
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;configuration&gt;
    &lt;system.webServer&gt;
        &lt;httpErrors errorMode="Custom"&gt;
            &lt;remove statusCode="404" subStatusCode="-1" /&gt;
            &lt;error statusCode="404" prefixLanguageFilePath="" path="/index.php" responseMode="ExecuteURL" /&gt;
        &lt;/httpErrors&gt;
        &lt;directoryBrowse enabled="true" /&gt;
    &lt;/system.webServer&gt;
&lt;/configuration&gt;
            </textarea>
            <hr/>
            <p>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <span class="star">
                在主机控制面板的lighttpd静态规则中加入,或是修改/etc/lighttpd/lighttpd.conf加入上述规则.
              </span>
            </p>
          </div>

        </div>



      </div>
      <!-- End .content-box-content --> </div>

    <!-- End .content-box -->

    <hr/>
  </form>
    <?php
    }?>
  <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
  <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/STACentre/logo.png'; ?>");</script>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
