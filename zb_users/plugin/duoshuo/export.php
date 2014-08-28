<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);exit();}
if (!$zbp->CheckPlugin('duoshuo')) {$zbp->ShowError(48);exit();}
$blogtitle='多说社会化评论';
require $blogpath . 'zb_system/admin/admin_header.php';
?>
<style type="text/css">
tr {
	height: 32px;
}
#divMain2 ul li {
	margin-top: 6px;
	margin-bottom: 6px
}
.bold {
	font-weight: bold;
}
.note {
	margin-left: 10px
}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
$duoshuo->init();
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"><?php echo $duoshuo->export_submenu('export');?></div>
  <div id="divMain2">
    <form action="event.php?act=export" method="post" id="_form">
      <p id="_status">必须导出数据到多说才可以正常使用。如导入有任何问题，请联系多说客服解决。</p>
      <table width="100%">
        <thead>
          <tr>
            <th width="30%">配置项 </th>
            <th>选择 </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><p><span class="bold"> · 立即进行数据同步</span><br/>
                <span class="note">从多说导入评论</span></p></td>
            <td><input name="" type="submit" class="button" onClick="$('#type').val('backup')" value="立即从多说备份数据" /></td>
          </tr>
          <tr>
            <td><p><span class="bold"> · 一键导出</span><br/>
                <span class="note">如您的站点数据过多，请选择下面的分块导出</span></p></td>
            <td><input name="" type="submit" class="button" onClick="return stepbystep()" value="一键导出全部数据" /></td>
          </tr>
          <tr>
            <td><p><span class="bold"> · 用户数据导出</span><br/>
                <span class="note">用于历史评论中用户的展示，站点管理权限的匹配，以及文章被评论时的提醒等功能。<span style="color:red">必须先导入用户以匹配正确的用户信息</span></span></p></td>
            <td><input name="" type="submit" class="button" onClick="$('#type').val('member')" value="导出用户" /></td>
          </tr>
          <tr>
            <td><p><span class="bold"> · 文章数据导出</span><br/>
                <span class="note">文章数据无论是否存在都将同步</span></p></td>
            <td>
            <?php
			$sql = $zbp->db->sql->Select($GLOBALS['table']['Post'], 'max(log_ID)',null,null,null,null);
			$sql = $zbp->db->Query($sql);
			$postid = $sql[0]['max(log_ID)'];
			?>
              <p> 文章ID:
                <input type="number" id="articlemin" name="articlemin" min="1" max="<?php echo $postid ?>" value="1"/>
                -
                <input type="number" id="articlemax" name="articlemax" min="1" max="<?php echo $postid ?>" value="<?php echo $postid ?>"/>
              </p>
              <p>
                <input name="" type="submit" class="button" onClick="$('#type').val('article')" value="导出文章" />
              </p></td>
          </tr>
          <tr>
          <?php
	      $sql = $zbp->db->sql->Select($GLOBALS['table']['Comment'],'max(comm_ID)',array(array('=','comm_IsChecking',0)),null,null,null);
		  $sql = $zbp->db->Query($sql);
		  $commid = (int)$sql[0]['max(comm_ID)'];
		  if ($commid < 1) $commid = 1;
	      $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_duoshuo_comment'],'max(ds_cmtid)',null,null,null,null);
		  $sql = $zbp->db->Query($sql);
          $ds_comid = (int)$sql[0]['max(ds_cmtid)'];
		  $pl = $commid - $ds_comid;
		  
		  ?>
                      <td><p><span class="bold"> · 评论数据导出</span><br/>
                <span class="note">只同步未向多说同步的评论，还有<?php echo $pl ?>条未同步</span></p></td>
            <td><p> 评论ID:
                <input type="number" id="commentmin" name="commentmin" min="1" max="<?php echo $commid ?>" value="1"/>
                -
                <input type="number" id="commentmax" name="commentmax" min="1" max="<?php echo $commid ?>" value="<?php echo $commid ?>"/>
              </p>
              <p>
                <input name="" type="submit" class="button" onClick="$('#type').val('comment')" value="导出评论" />
              </p></td>
          </tr>
        </tbody>
        <tfoot>
        </tfoot>
      </table>
      <p>
        <input type="hidden" name="type" value="all" id="type"/>
      </p>
    </form>
  </div>
</div>
<script type="text/javascript">
		$(document).ready(function() {
			ActiveLeftMenu("aCommentMng");
			$("#_form").submit(function() {
				$("#_status").html("正在执行操作，请稍等..");
				$.ajax({
					type: "POST",
					url: "event.php?act=export",
					data: {
						type: $("#type").val(),
						commentmin: $("#commentmin").val(),
						commentmax: $("#commentmax").val(),
						articlemax: $("#articlemax").val(),
						articlemin: $("#articlemin").val()
					},
					success: function(data) {
						try {
							console.log(data)
							 var o = eval('(' + data + ')');
							$("#_status").html(o.success);
						}
						 catch(e) {
							$("#_status").html("操作出错..服务器返回" + data);
						}
					},
					error: function(xmlObj, txterr) {
						if (xmlObj.readyState == 4) {
							$("#_status").html("操作出错..HTTP状态码" + xmlObj.status + ",错误信息" + xmlObj.responseText);
						}
						 else {
							$("#_status").html("操作出错.." + txterr);
						}
					},
				});
				return false;
			})
		})
		 function stepbystep() {
			$('#divMain2').hide().after("<div id='divMain3' style='margin:0.5px;padding:0px'><p style='line-height:20px'>当前状态：<span id='s'>0</span>%</p><div id='bar'></div><ul id='_hint'><li>正在导入，请稍等</li></ul></div>");;
			$("#bar").progressbar({
				min: 0,
				max: 100,
				value: 0
			});
			setTimeout(stepbystep_main, 5);
			return false
		}
		function stepbystep_main() {
			stepbystep_member()
			stepbystep_article(parseInt($("#articlemin").val()), parseInt($("#articlemax").val()))
			stepbystep_comment(parseInt($("#commentmin").val()), parseInt($("#commentmax").val()))
		}
		function stepbystep_log(data) {
			$("#_hint").append("<li>" + data + "</li>");
		}
		function stepbystep_article(articlemin, articlemax) {
			$.ajax({
				type: "POST",
				url: "event.php?act=export",
				data: {
					type: "article",
					articlemin: articlemin,
					articlemax: articlemin + 20
				},
				success: function(data) {
					try {
						var o = eval('(' + data + ')');
						stepbystep_log(o.success)
					}
					 catch(e) {
						stepbystep_log("<span style='color:red'>操作出错..服务器返回" + data + "</span>");
					}
					$("#bar").progressbar({
						value: 1 / 3 * 100 + (articlemin / articlemax) / 3 * 100
					});
					$("#s").text(Math.floor(1 / 3 * 100 + (articlemin / articlemax) / 3 * 100));
					if (articlemin + 20 >= articlemax) {
						$("#bar").progressbar({
							value: 2 / 3 * 100
						});
						$("#s").text(Math.floor(2 / 3 * 100));
						return true
					}
					 else {
						return stepbystep_article(articlemin + 20, articlemax);
					}
				},
				error: function(xmlObj, txterr) {
					if (xmlObj.readyState == 4) {
						$("#_status").html("<span style='color:red'>操作出错..HTTP状态码" + xmlObj.status + ",错误信息" + xmlObj.responseText + "</span>");
					}
					 else {
						$("#_status").html("<span style='color:red'>操作出错.." + txterr + "</span>");
					}
				},
			});
		}
		function stepbystep_member() {
			$.ajax({
				type: "POST",
				url: "event.php?act=export",
				data: {
					type: "member"
				},
				success: function(data) {
					try {
						console.log(data)
						 var o = eval('(' + data + ')');
						stepbystep_log(o.success);
						$("#bar").progressbar({
							value: 1 / 3 * 100
						});
						$("#s").text(Math.floor(1 / 3 * 100));
					}
					 catch(e) {
						stepbystep_log("<span style='color:red'>操作出错..服务器返回" + data + "</span>");
					}
					return true
				},
				error: function(xmlObj, txterr) {
					if (xmlObj.readyState == 4) {
						stepbystep_log("<span style='color:red'>操作出错..HTTP状态码" + xmlObj.status + ",错误信息" + xmlObj.responseText + "</span>");
					}
					 else {
						stepbystep_log("<span style='color:red'>操作出错.." + txterr + "</span>");
					}
				},
			});
		}
		function stepbystep_comment(commentmin, commentmax) {
			$.ajax({
				type: "POST",
				url: "event.php?act=export",
				data: {
					type: "comment",
					commentmin: commentmin,
					commentmax: commentmin + 20
				},
				success: function(data) {
					try {
						var o = eval('(' + data + ')');
						stepbystep_log(o.success)
					}
					 catch(e) {
						stepbystep_log("<span style='color:red'>操作出错..服务器返回" + data + "</span>");
					}
					$("#bar").progressbar({
						value: 2 / 3 * 100 + (commentmin / commentmax) / 3 * 100
					});
					$("#s").text(Math.floor(2 / 3 * 100 + (commentmin / commentmax) / 3 * 100));
					if (commentmin + 20 >= commentmax) {
						$("#bar").progressbar({
							value: 3 / 3 * 100
						});
						$("#s").text(Math.floor(3 / 3 * 100));
						return true
					}
					 else {
						return stepbystep_comment(commentmin + 20, commentmax);
					}
				},
				error: function(xmlObj, txterr) {
					if (xmlObj.readyState == 4) {
						$("#_status").html("<span style='color:red'>操作出错..HTTP状态码" + xmlObj.status + ",错误信息" + xmlObj.responseText + "</span>");
					}
					 else {
						$("#_status").html("<span style='color:red'>操作出错.." + txterr + "</span>");
					}
				},
			});
		}
        </script> 
<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script> 
<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/duoshuo/logo.png';?>");</script>
</div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
