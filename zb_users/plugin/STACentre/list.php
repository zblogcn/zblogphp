<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('STACentre')) {$zbp->ShowError(68);die();}

$blogtitle='静态管理中心';

if(count($_GET)>0){

	if(GetVars('mak','GET')=='1'){
		@file_put_contents($zbp->path . '.htaccess',show_htaccess());
	}elseif(GetVars('mak','GET')=='2'){
		@file_put_contents($zbp->path . 'web.config',show_webconfig());
	}elseif(GetVars('mak','GET')=='3'){
		@file_put_contents($zbp->path . 'http.ini',iconv('UTF-8', 'ASCII//IGNORE', show_httpini()));
	}

	if(GetVars('del','GET')=='1'){
		@unlink($zbp->path . '.htaccess');
	}elseif(GetVars('del','GET')=='2'){
		@unlink($zbp->path . 'web.config');
	}elseif(GetVars('del','GET')=='3'){
		@unlink($zbp->path . 'http.ini');
	}

	$zbp->SetHint('good');

	Redirect('./list.php');
}


function show_htaccess(){
	$ur=new UrlRule("");
	return $ur->Make_htaccess();
}

function show_httpini(){
	$ur=new UrlRule("");
	return $ur->Make_httpini();
}

function show_webconfig(){
	$ur=new UrlRule("");
	return $ur->Make_webconfig();
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"> <a href="main.php"><span class="m-left">配置页面</span></a><a href="list.php"><span class="m-left m-now">ReWrite规则</span></a><a href="help.php"><span class="m-right">帮助</span></a> </div>
  <div id="divMain2" class="edit category_edit">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />


            <div class="content-box"><!-- Start Content Box -->
              
              <div class="content-box-header">
                <ul class="content-box-tabs">
                  <li><a href="#tab1" class="default-tab" ><span>Apache + .htaccess</span></a></li>
                  <li><a href="#tab2"><span>IIS7,8 + URL Rewrite Module</span></a></li>
                  <li><a href="#tab3"><span>IIS6 + ISAPI Rewrite 2.X</span></a></li>
                </ul>
                <div class="clear"></div>
              </div>
              <!-- End .content-box-header -->
              
              <div class="content-box-content">

			  
                <div class="tab-content default-tab" style='border:none;padding:0px;margin:0;' id="tab1">
<textarea style="width:99%;height:400px" readonly>
<?php echo htmlentities(show_htaccess())?>
</textarea>
                  <hr/>
                  <p>
                    <input type="button" onclick="window.location.href='?mak=1'" value="创建.htaccess" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" onclick="window.location.href='?del=1'" value="删除.htaccess" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="star">请在网站<u>"当前目录"</u>创建.htaccess文件并把相关内容复制进去,也可以点击按钮生成..</span></p>
                </div>

				
				
                <div class="tab-content" style='border:none;padding:0px;margin:0;' id="tab2">
<textarea style="width:99%;height:400px" readonly>
<?php echo htmlentities(show_webconfig())?>
</textarea>
                  <hr/>
                  <p>
                    <input type="button" onclick="window.location.href='?mak=2'" value="创建web.config" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" onclick="window.location.href='?del=2'" value="删除web.config" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="star">请在网站<u>"当前目录"</u>创建web.config文件并把相关内容复制进去,也可以点击按钮生成..</span></p>
                </div>
			  

			  
			  
                <div class="tab-content" style='border:none;padding:0px;margin:0;' id="tab3">
<textarea style="width:99%;height:400px" readonly>
<?php echo htmlentities(show_httpini())?>
</textarea>
                  <hr/>
                  <p>
                    <input type="button" onclick="window.location.href='?mak=3'" value="创建httpd.ini" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" onclick="window.location.href='?del=3'" value="删除httpd.ini" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="star">请在网站根目录创建httpd.ini文件并把相关内容复制进去,httpd.ini文件必须为ANSI编码,也可以点击按钮生成.</span></p>
                </div>

				
              </div>
              <!-- End .content-box-content --> 
              
            </div>
            <!-- End .content-box -->


	  <hr/>
	</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/STACentre/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>