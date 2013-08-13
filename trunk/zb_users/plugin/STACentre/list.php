<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('STACentre')) {$zbp->ShowError(68);die();}

$blogtitle='静态管理中心';

if(count($_POST)>0){

	Redirect('./list.php');
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
                  <li><a href="#tab2"><span>IIS7、8 + Url Rewrite</span></a></li>
                  <li><a href="#tab3"><span>IIS6+ISAPI Rewrite 2.X</span></a></li>
                </ul>
                <div class="clear"></div>
              </div>
              <!-- End .content-box-header -->
              
              <div class="content-box-content">

			  
                <div class="tab-content default-tab" style='border:none;padding:0px;margin:0;' id="tab1">
<textarea style="width:80%;height:300px" readonly>
<?php echo htmlentities(show_htaccess())?>
</textarea>
                  <hr/>
                  <p>
                    <input type="button" onclick="if(showmsg(2)){window.location.href='?mak=2'}" value="创建.htaccess" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" onclick="if(showmsg(2)){window.location.href='?del=2'}" value="删除.htaccess" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="star">请在网站根目录创建.htaccess文件并把相关内容复制进去,也可以点击按钮生成..</span></p>
                </div>

				
				
                <div class="tab-content" style='border:none;padding:0px;margin:0;' id="tab2">
<textarea style="width:80%;height:300px" readonly>
<?php echo htmlentities(show_webconfig())?>
</textarea>
                  <hr/>
                  <p>
                    <input type="button" onclick="if(showmsg(3)){window.location.href='?mak=3'}" value="创建web.config" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" onclick="if(showmsg(3)){window.location.href='?del=3'}" value="删除web.config" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="star">请在网站<u>"当前目录"</u>创建web.config文件并把相关内容复制进去,也可以点击按钮生成..</span></p>
                </div>
			  

			  
			  
                <div class="tab-content" style='border:none;padding:0px;margin:0;' id="tab3">
<textarea style="width:80%;height:300px" readonly>
<?php echo htmlentities(show_httpini())?>
</textarea>
                  <hr/>
                  <p>
                    <input type="button" onClick="if(showmsg(1)){window.location.href='?mak=1'}" value="创建httpd.ini" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" onClick="if(showmsg(1)){window.location.href='?del=1'}" value="删除httpd.ini" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="star">请在网站根目录创建httpd.ini文件并把相关内容复制进去,httpd.ini文件必须为ANSI编码,也可以点击按钮生成.</span></p>
                </div>

				
              </div>
              <!-- End .content-box-content --> 
              
            </div>
            <!-- End .content-box -->



	  <hr/>
	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
	  </p>

	</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/STACentre/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

function show_htaccess(){
	$ur=new UrlRule("");
	return $ur->Make_htaccess();
}

function show_httpini(){
	$ur=new UrlRule("");
	return $ur->Make_webconfig();
}

function show_webconfig(){
	echo 123;
}

RunTime();
?>