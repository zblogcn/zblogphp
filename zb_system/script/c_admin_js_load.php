<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
require '../function/c_system_base.php';
$zbp->Load();

header('Content-Type: application/javascript; charset=utf-8');

$action=GetVars('act','GET');
if(!$zbp->CheckRights($action)){$zbp->ShowError(6,__FILE__,__LINE__);die();}

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
		if(mfilename == undefined){ t = '<?php echo $zbp->lang['msg']['new_module']?>'; } 
		else { t = '<?php echo $zbp->lang['msg']['edit']?>'; }
		$('#loading').show();
		$('#dialog-form').load("module_edit.php",
			{"id":mid,"filename":mfilename,"source":msource},
			function(data, status, xhr) {
				if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#error").html(msg + xhr.status + " " + xhr.statusText);
				}
				else {
					$('#loading').hide();
					$(this).dialog({
						title:t,
						width: 600,
						modal: true,
						show:'fadeToggle',
						buttons: {
							"确认": function() {
								if ( checkInfo() ){
									$('#loading').show();
									$.post('../cmd.php?act=ModulePst&token=' + token,
										$('#moduleedit').serialize(),
										function(data, status, xhr) {
											if (status == "error") {
												var msg = "Sorry but there was an error: ";
												$("#error").html(msg + xhr.status + " " + xhr.statusText);
											}
											else {
												$('#loading').hide();
												Ajaxhint("good");
											}
										}
									);
								$( this ).dialog( "close" );
								}
							},
							"取消": function() {
								$( this ).dialog( "close" );
							}
						}
				});
				CheckBoxBind();
				}
		});
	}
	//del
	function moddel(mid,mfilename,msource,item){
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			modal: true,
			height:172,
			buttons: {
				"确认": function() {
					$('#loading').show();
					$.post('../cmd.php?act=ModuleDel&id=' + mid +'&filename=' +mfilename +'&source=' +msource +'&token=' + token,
						function(data, status, xhr) {
							if (status == "error") {
								var msg = "Sorry but there was an error: ";
								$("#error").html(msg + xhr.status + " " + xhr.statusText);
							}
							else {
								$('#loading').hide();
							}
						}
					);
					$(item).closest(".widget").remove();
					$(this).dialog( "close" );
				},
				"取消": function() {
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
