<?php
/**
 * Z-Blog with PHP
 * @author yzsm
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2014-09-28
 */
require '../function/c_system_base.php';
$zbp->Load();

header('Content-Type: application/javascript; charset=utf-8');

$action=GetVars('act','GET');
if(!$zbp->CheckRights($action)){$zbp->ShowError(6,__FILE__,__LINE__);die();}
?>
// Ajax操作提醒
function Ajaxhint(signal,content){
	if(content==undefined){
		if(signal=='good')content="<?php echo $lang['msg']['operation_succeed'] ?>";
		if(signal=='bad')content="<?php echo $lang['msg']['operation_failed'] ?>";
	}
	s= '<div class=\"hint\"><p class=\"hint hint_' + signal +'\">'+content+'</p></div>';
	$("#divMain").prepend(s);
	$("p.hint:visible").delay(1500).hide(1500);
}

$(document).ajaxError(function() {
	$('#loading').hide();
	Ajaxhint('bad');
});
<?php
switch ($action) {
	case 'login':
		break;
	case 'logout':
		break;
	case 'admin':
		break;
	case 'verify':
		break;
	case 'search':
		break;
	case 'misc':
		break;
	case 'cmt':
		break;
	case 'getcmt':
		break;
	case 'ArticleEdt':
		break;
	case 'ArticleDel':
		break;
	case 'ArticleMng':
		break;
	case 'ArticlePst':
		break;
	case 'PageEdt':
		break;
	case 'PageDel':
		break;
	case 'PageMng':
		break;
	case 'PagePst':
		break;
	case 'CategoryMng':
		break;
	case 'CategoryEdt':
		break;
	case 'CategoryPst':
		break;
	case 'CategoryDel':
		break;
	case 'CommentDel':
		break;
	case 'CommentChk':
		break;
	case 'CommentBat':
		break;
	case 'CommentMng':
		break;
	case 'MemberMng':
		?>
	var token="<?php echo $zbp->GetToken();?>";
	function checkInfo(){
	  if(!$("#edtEmail").val()){
		alert("<?php echo $lang['error']['29']?>");
		return false;
	  }
	  if(!$("#edtName").val()){
		alert("<?php echo $lang['error']['72']?>");
		return false;
	  }
	  if($("#edtPassword").val()!==$("#edtPasswordRe").val()){
		alert("<?php echo $lang['error']['73']?>");
		return false;
	  }
		return true;
	}

	//edit
	function member_edit(mid,item){
		var mact;
		var t;
		if(mid ==undefined) {
			mact = 'MemberNew'; 
			t = '<?php echo $lang['msg']['new_member']?>'; 
		}
		else {
			mact = 'MemberEdt';  
			t = '<?php echo $lang['msg']['edit']?>'; 
		}
		$('#loading').show();
		$('#dialog-form').load("member_edit.php",
			{"id":mid,"act":mact},
			function(data) {
				$('#loading').hide();
				$(this).dialog({
					title:t,
					width: 600,
					modal: true,
					show:'fadeToggle',
					buttons: {
						"<?php echo $lang['msg']['ok'] ?>": function() {
							if ( checkInfo() ){
								$('#loading').show();
								$.post('../cmd.php?act=MemberPst&token=' + token,
									$('#memberedit').serialize(),
									function(data) {
											$('#loading').hide();
											Ajaxhint("good");
									}
								);
							$( this ).dialog( "close" );
							}
						},
						"<?php echo $lang['msg']['cancel'] ?>": function() {
							$( this ).dialog( "close" );
						}
					}
			});
			CheckBoxBind();
		});
	}

	//del
	function member_del(mid,item){
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			modal: true,
			height:172,
			buttons: {
				"<?php echo $lang['msg']['ok'] ?>": function() {
					$('#loading').show();
					$.post('../cmd.php?act=MemberDel&id=' + mid +'&token=' + token,
						function(data) {
							$('#loading').hide();
							$(item).closest("tr").remove();
							Ajaxhint("good");
						});
					$(this).dialog( "close" );
				},
				"<?php echo $lang['msg']['cancel'] ?>": function() {
					$(this).dialog( "close" );
				}
			}
		});
	}

	//show vrs
	function member_vrs(){
		$('#loading').show();
		$('#dialog-form').load("../cmd.php?act=misc&type=vrs&ajax",
			function(data, status, xhr) {
				$('#loading').hide();
				$(this).dialog({
					title:'<?php echo $lang['msg']['view_rights']?>',
					width: 600,
					modal: true,
					show:'fadeToggle',
					buttons:{
						"<?php echo $lang['msg']['ok'] ?>": function() {
							$(this).dialog( "close" );
						}
					}
			});
			CheckBoxBind();
		});
	}
	<?php
		break;
	case 'MemberEdt':
		break;
	case 'MemberNew':
		break;
	case 'MemberPst':
		break;
	case 'MemberDel':
		break;
	case 'UploadMng':
		break;
	case 'UploadPst':
		break;
	case 'UploadDel':
		break;
	case 'TagMng':
		break;
	case 'TagEdt':
		break;
	case 'TagPst':
		break;
	case 'TagDel':
		break;
	case 'PluginMng':
		break;
	case 'PluginDis':
		break;
	case 'PluginEnb':
		break;
	case 'ThemeMng':
		break;
	case 'ThemeSet':
		break;
	case 'SidebarSet':
		break;
	case 'ModuleEdt':
		break;
	case 'ModulePst':
		break;
	case 'ModuleDel':
		break;
	case 'ModuleMng':
	//模块管理页面脚本
	?>
	var token="<?php echo $zbp->GetToken();?>";
	function checkInfo(){
	  if(!$("#edtName").val()){
		alert("<?php echo $lang['error']['72']?>");
		return false
	  }
	  if(!$("#edtFileName").val()){
		alert("<?php echo $lang['error']['75']?>");
		return false
	  }
	  if(!$("#edtHtmlID").val()){
		alert("<?php echo $lang['error']['76']?>");
		return false
	  }
	  return true;
	}
	//edit
	function modedit(mid,mfilename,msource,item){
		var t;
		if(mfilename == undefined){ t = '<?php echo $lang['msg']['new_module']?>'; } 
		else { t = '<?php echo $lang['msg']['edit']?>'; }
		$('#loading').show();
		$('#dialog-form').load("module_edit.php",
			{"id":mid,"filename":mfilename,"source":msource},
			function(data) {
				$('#loading').hide();
				$(this).dialog({
					title:t,
					width: 600,
					modal: true,
					show:'fadeToggle',
					buttons: {
						"<?php echo $lang['msg']['ok'] ?>": function() {
							if ( checkInfo() ){
								$('#loading').show();
								$.post('../cmd.php?act=ModulePst&token=' + token,
									$('#moduleedit').serialize(),
									function(data) {
										$('#loading').hide();
										Ajaxhint("good");
									});
							$( this ).dialog( "close" );
							}
						},
						"<?php echo $lang['msg']['cancel'] ?>": function() {
							$( this ).dialog( "close" );
						}
					}
			});
			CheckBoxBind();
		});
	}
	//del
	function moddel(mid,mfilename,msource,item){
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			modal: true,
			height:172,
			buttons: {
				"<?php echo $lang['msg']['ok'] ?>": function() {
					$('#loading').show();
					$.post('../cmd.php?act=ModuleDel&id=' + mid +'&filename=' +mfilename +'&source=' +msource +'&token=' + token,
						function(data) { 
							$('#loading').hide();					
							$(item).closest(".widget").remove();
							Ajaxhint("good");
						}
					);
					$(this).dialog( "close" );
				},
				"<?php echo $lang['msg']['cancel'] ?>": function() {
					$(this).dialog( "close" );
				}
			}
		});
	}
	//sort draggable
	$(function() {
		function sortFunction(){
			var s1=$("#siderbar").find(".siderbar-sort-list").sortable("toArray", { attribute : "modfilename" }).join('|');
			var s2=$("#siderbar2").find(".siderbar-sort-list").sortable("toArray", { attribute : "modfilename" }).join('|');
			var s3=$("#siderbar3").find(".siderbar-sort-list").sortable("toArray", { attribute : "modfilename" }).join('|');
			var s4=$("#siderbar4").find(".siderbar-sort-list").sortable("toArray", { attribute : "modfilename" }).join('|');
			var s5=$("#siderbar5").find(".siderbar-sort-list").sortable("toArray", { attribute : "modfilename" }).join('|');

			$.post("../cmd.php?act=SidebarSet",
				{"sidebar": s1,"sidebar2": s2,"sidebar3": s3,"sidebar4": s4,"sidebar5": s5},
			   function(data){
				 //alert("Data Loaded: " + data);
			   });
		}
		var t;
		function hideWidget(item){
				item.find(".ui-icon").removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-w");
				t=item.next();
				t.find(".widget").hide("fast").end().show();
				t.find(".siderbar-note>span").text(t.find(".widget").length);
		}
		function showWidget(item){
				item.find(".ui-icon").removeClass("ui-icon-triangle-1-w").addClass("ui-icon-triangle-1-s");
				t=item.next();
				t.find(".widget").show("fast");
				t.find(".siderbar-note>span").text(t.find(".widget").length);
		}

		$(".siderbar-header").toggle( function () {
				hideWidget($(this));
			  },
			  function () {
				showWidget($(this));
			  });

		$( ".siderbar-sort-list" ).sortable({
			items:'.widget',
			start:function(event, ui){
				showWidget(ui.item.parent().prev());
				 var c=ui.item.find(".funid").html();
				 if(ui.item.parent().find(".widget:contains("+c+")").length>1){
					ui.item.remove();
				 }
			} ,
			stop:function(event, ui){$(this).parent().find(".roll").show("slow");sortFunction();$(this).parent().find(".roll").hide("slow");
				showWidget($(this).parent().prev());
			}
		}).disableSelection();

		$( ".widget-list>.widget" ).draggable({
			connectToSortable: ".siderbar-sort-list",
			revert: "invalid",
			containment: "document",
			helper: "clone",
			cursor: "move"
		}).disableSelection();

		$( ".widget-list" ).droppable({
			accept:".siderbar-sort-list>.widget",
			drop: function( event, ui ) {
				ui.draggable.remove();
			}
		});
	});
	<?php
		break;
	case 'SettingMng':
		break;
	case 'SettingSav':
		break;
	default:
		# code...
		break;
}
?>
