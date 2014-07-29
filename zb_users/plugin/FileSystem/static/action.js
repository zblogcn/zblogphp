(function(){
	var actions_bottom = {
		"select_all": function(){$(".checkbox-file").attr("checked", "checked").change();},
		"select_none": function(){$(".checkbox-file").removeAttr("checked").change();},
		"delete_file": function(){},
		"copy_file": function(){},
		"move_file": function(){},
		"pack_file": function(){},
		"chmod_file": function(){},
	}
	
	var actions_top = {
		"create_file": function(form){
			var filename = prompt('输入文件名称：', 'index.php');
			if (filename.length > 0)
			{ 
				form.find("[name='cmd_data']").val(filename);
				form.find("[name='cmd_arg']").val(5);
				form.find("[name='action']").val(1);
				form.submit();
			}
		},
		"create_dir": function(form){
			var filename = prompt('输入文件夹名称：', '');
			if (filename.length > 0)
			{ 
				form.find("[name='cmd_data']").val(filename);
				form.find("[name='cmd_arg']").val(1);
				form.find("[name='action']").val(1);
				form.submit();
			}
		},
		"show_phpinfo": function(form){
			form.find("[name='cmd_arg']").val(888);
			form.find("[name='action']").val(1);
			form.attr("target", "_blank").submit();	
		}
		
	}
	
	var actions_filelist = {
		"down_file": function(filename, form)
		{
			form.find("[name='selected_file_list']").val(filename);
			form.find("[name='cmd_data']").val(filename);
			form.find("[name='cmd_arg']").val(4);
			form.find("[name='action']").val(1);
			form.attr("target", "_blank").submit();	
		},
		"edit_file": function(filename, form)
		{
			form.find("[name='selected_file_list']").val(filename);
			form.find("[name='cmd_data']").val(filename);
			form.find("[name='cmd_arg']").val(6);
			form.find("[name='action']").val(1);
			form.attr("action", "editingcode.php").submit();	
		},
		"rename_file": function(filename, form)
		{
			form.find("[name='selected_file_list']").val(filename);
			form.find("[name='cmd_data']").val(filename = prompt('输入文件(夹)名称：', filename));
			if (filename.length > 0)
			{ 
				form.find("[name='cmd_arg']").val(5);
				form.find("[name='action']").val(1);
				form.submit();
			}
		},
		"delete_file": function(filename, form)
		{
			if (window.confirm("是否删除" + filename))
			{
				form.find("[name='selected_file_list']").val(filename);
				form.find("[name='cmd_data']").val(filename);
				form.find("[name='cmd_arg']").val(3);
				form.find("[name='action']").val(1);
				form.submit();	
			}
		},
	}
	
	$(document).ready(function(){
		$(".button-control-bottom").click(function(){
			var that = $(this),
				func = that.data("function"),
				object_form = $("#form-action");
			actions_bottom[func](object_form);
			return false;
		});
		
		$(".button-control-top").click(function(){
			var that = $(this),
				func = that.data("function"),
				object_form = $("#form-action");
			actions_top[func](object_form);
			return false;
		});
		
		$(".checkbox-file").change(function(){
			var that = $(this),
				filename = that.data("filename");
			//document.form_action.selected_file_list.value = filename;
		});
		
		$(".href-command").click(function(){
			var that = $(this),
				filename = that.data("filename"),
				func = that.data("function"),
				object_form = $("#form-action");
			actions_filelist[func](filename, object_form);
			return false;
		});
		
		
	});

	
})();