
        function go(arg) {
            document.location.href='main2.php?frame=3&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/'+arg+'/';
        }
        function resolveIDs() {
            document.location.href='main2.php?frame=3&set_resolveIDs=1&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/';
        }
        var entry_list = new Array();
        // Custom object constructor
        function entry(name, type, size, selected){
            this.name = name;
            this.type = type;
            this.size = size;
            this.selected = false;
        }
        // Declare entry_list for selection procedures
entry_list['entry0'] = new entry('static', 'dir', 0, false);
entry_list['entry1'] = new entry('plugin.xml', 'file', 565, false);
entry_list['entry2'] = new entry('logo.png', 'file', 4139, false);
entry_list['entry3'] = new entry('function.php', 'file', 3197, false);
entry_list['entry4'] = new entry('include.php', 'file', 528, false);
entry_list['entry5'] = new entry('main.php', 'file', 3971, false);
entry_list['entry6'] = new entry('main2.php', 'file', 147812, false);
        // Select/Unselect Rows OnClick/OnMouseOver
        var lastRows = new Array(null,null);
        function selectEntry(Row, Action){
            if (multipleSelection){
                // Avoid repeated onmouseover events from same Row ( cell transition )
                if (Row != lastRows[0]){
                    if (Action == 'over') {
                        if (entry_list[Row.id].selected){
                            if (unselect(entry_list[Row.id])) {
                                Row.className = 'entryUnselected';
                            }
                            // Change the last Row when you change the movement orientation
                            if (lastRows[0] != null && lastRows[1] != null){
                                var LastRowID = lastRows[0].id;
                                if (Row.id == lastRows[1].id){
                                    if (unselect(entry_list[LastRowID])) {
                                        lastRows[0].className = 'entryUnselected';
                                    }
                                }
                            }
                        } else {
                            if (select(entry_list[Row.id])){
                                Row.className = 'entrySelected';
                            }
                            // Change the last Row when you change the movement orientation
                            if (lastRows[0] != null && lastRows[1] != null){
                                var LastRowID = lastRows[0].id;
                                if (Row.id == lastRows[1].id){
                                    if (select(entry_list[LastRowID])) {
                                        lastRows[0].className = 'entrySelected';
                                    }
                                }
                            }
                        }
                        lastRows[1] = lastRows[0];
                        lastRows[0] = Row;
                    }
                }
            } else {
                if (Action == 'click') {
                    var newClassName = null;
                    if (entry_list[Row.id].selected){
                        if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                    } else {
                        if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                    }
                    if (newClassName) {
                        lastRows[0] = lastRows[1] = Row;
                        Row.className = newClassName;
                    }
                }
            }
            return true;
        }
        // Disable text selection and bind multiple selection flag
        var multipleSelection = false;
        if (is.ie) {
            document.onselectstart=new Function('return false');
            document.onmousedown=switch_flag_on;
            document.onmouseup=switch_flag_off;
            // Event mouseup is not generated over scrollbar.. curiously, mousedown is.. go figure.
            window.onscroll=new Function('multipleSelection=false');
            window.onresize=new Function('multipleSelection=false');
        } else {
            if (document.layers) window.captureEvents(Event.MOUSEDOWN);
            if (document.layers) window.captureEvents(Event.MOUSEUP);
            window.onmousedown=switch_flag_on;
            window.onmouseup=switch_flag_off;
        }
        // Using same function and a ternary operator couses bug on double click
        function switch_flag_on(e) {
            if (is.ie){
                multipleSelection = (event.button == 1);
            } else {
                multipleSelection = (e.which == 1);
            }
			var type = String(e.target.type);
			return (type.indexOf('select') != -1 || type.indexOf('button') != -1 || type.indexOf('input') != -1 || type.indexOf('radio') != -1);
        }
        function switch_flag_off(e) {
            if (is.ie){
                multipleSelection = (event.button != 1);
            } else {
                multipleSelection = (e.which != 1);
            }
            lastRows[0] = lastRows[1] = null;
            update_sel_status();
            return false;
        }
        var total_dirs_selected = 0;
        var total_files_selected = 0;
        function unselect(Entry){
            if (!Entry.selected) return false;
            Entry.selected = false;
            sel_totalsize -= Entry.size;
            if (Entry.type == 'dir') total_dirs_selected--;
            else total_files_selected--;
            return true;
        }
        function select(Entry){
            if(Entry.selected) return false;
            Entry.selected = true;
            sel_totalsize += Entry.size;
            if(Entry.type == 'dir') total_dirs_selected++;
            else total_files_selected++;
            return true;
        }
        function is_anything_selected(){
            var selected_dir_list = new Array();
            var selected_file_list = new Array();
            for(var x=0;x<7;x++){
                if(entry_list['entry'+x].selected){
                    if(entry_list['entry'+x].type == 'dir') selected_dir_list.push(entry_list['entry'+x].name);
                    else selected_file_list.push(entry_list['entry'+x].name);
                }
            }
            document.form_action.selected_dir_list.value = selected_dir_list.join('<|*|>');
            document.form_action.selected_file_list.value = selected_file_list.join('<|*|>');
            return (total_dirs_selected>0 || total_files_selected>0);
        }
        function format_size (arg) {
            var resul = '';
            if (arg>0){
                var j = 0;
                var ext = new Array(' bytes',' Kb',' Mb',' Gb',' Tb');
                while (arg >= Math.pow(1024,j)) ++j;
                resul = (Math.round(arg/Math.pow(1024,j-1)*100)/100) + ext[j-1];
            } else resul = 0;
            return resul;
        }
        var sel_totalsize = 0;
        function update_sel_status(){
            var t = total_dirs_selected+' 目录 和 '+total_files_selected+' 文件 选择 = '+format_size(sel_totalsize);
            //document.getElementById("sel_status").innerHTML = t;
            window.status = t;
        }
        // Select all/none/inverse
        function selectANI(Butt){
            for(var x=0;x<7;x++){
                var Row = document.getElementById('entry'+x);
                var newClassName = null;
                switch (Butt.value){
                    case '全选':
                        if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                    break;
                    case '取消选择':
                        if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                    break;
                    case '反选':
                        if (entry_list[Row.id].selected){
                            if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                        } else {
                            if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                        }
                    break;
                }
                if (newClassName) {
                    Row.className = newClassName;
                }
            }
            if (Butt.value == '全选'){
                Butt.value = '取消选择';
            } else if (Butt.value == '取消选择'){
                Butt.value = '全选';
            }
            update_sel_status();
            return true;
        }
        function download(arg){
            parent.frame1.location.href='main2.php?action=3&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/&filename='+escape(arg);
        }
        function upload(){
            var w = 400;
            var h = 250;
            window.open('main2.php?action=10&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function execute_cmd(){
            var arg = prompt('输入命令.');
            if(arg.length>0){
                if(confirm('确定执行 \' '+arg+' \' ?')) {
                    var w = 800;
                    var h = 600;
                    window.open('main2.php?action=6&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/&cmd='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
                }
            }
        }
        function decompress(arg){
            if(confirm('解压 \' '+arg+' \' ?')) {
                document.form_action.action.value = 72;
                document.form_action.cmd_arg.value = arg;
                document.form_action.submit();
            }
        }
        function execute_file(arg){
            if(arg.length>0){
                if(confirm('确定执行 \' '+arg+' \' ?')) {
                    var w = 800;
                    var h = 600;
                    window.open('main2.php?action=11&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/&filename='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
                }
            }
        }
        function edit_file(arg){
            var w = 1024;
            var h = 768;
            // if(confirm('编辑 \' '+arg+' \' ?')) 
            window.open('main2.php?action=7&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/&filename='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function config(){
            var w = 650;
            var h = 400;
            window.open('main2.php?action=2', 'win_config', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function server_info(arg){
            var w = 800;
            var h = 600;
            window.open('main2.php?action=5', 'win_serverinfo', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function shell(){
            var w = 800;
            var h = 600;
            window.open('main2.php?action=9', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function view(arg){
            var w = 800;
            var h = 600;
            if(confirm('查看 \' '+arg+' \' ?')) window.open('main2.php?action=4&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/&filename='+escape(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=yes,toolbar=no,menubar=no,location=yes');
        }
        function rename(arg){
            var nome = '';
            if (nome = prompt('重命名 \' '+arg+' \' 为 ...')) document.location.href='main2.php?frame=3&action=3&current_dir=E:/wwwroot/www/z-blog/zb_users/plugin/FileSystem/&old_name='+escape(arg)+'&new_name='+escape(nome);
        }
        function set_dir_dest(arg){
            document.form_action.dir_dest.value=arg;
            if (document.form_action.action.value.length>0) test(document.form_action.action.value);
            else alert('JavaScript 错误.');
        }
        function sel_dir(arg){
            document.form_action.action.value = arg;
            document.form_action.dir_dest.value='';
            if (!is_anything_selected()) alert('没有选择任何文件.');
            else {
                if (!getCookie('sel_dir_warn')) {
                    // alert('Select the destination directory on the left tree.');
                    document.cookie='sel_dir_warn'+'='+escape('true')+';';
                }
                parent.frame2.set_flag(true);
            }
        }
        function set_chmod_arg(arg){
            document.form_action.chmod_arg.value=arg;
            if (document.form_action.action.value.length>0) test(document.form_action.action.value);
            else alert('JavaScript 错误');
        }
        function chmod(arg){
            document.form_action.action.value = arg;
            document.form_action.dir_dest.value='';
            document.form_action.chmod_arg.value='';
            if (!is_anything_selected()) alert('没有选择任何文件.');
            else {
                var w = 280;
                var h = 180;
                window.open('main2.php?action=8', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
            }
        }
		
		
        function test_action(){
            if (document.form_action.action.value != 0) return true;
            else return false;
        }
		
		function create_file(){
			document.form_action.cmd_data.value = prompt('输入文件名称：', 'index.php')
			if(document.form_action.cmd_data.value.length>0) {
				document.form_action.cmd_arg.value = 2;
				document.form_action.action.value = 1;
				document.form_action.submit();
			}
		}
		
		function create_dir(){
			document.form_action.cmd_data.value = prompt('输入文件夹名称：')
			if(document.form_action.cmd_data.value.length>0) {
				document.form_action.cmd_arg.value = 1;
				document.form_action.action.value = 1;
				document.form_action.submit();
			}
		}
		function show_phpinfo(){
			document.form_action.cmd_arg.value = 888;
			document.form_action.target = '_blank';
			document.form_action.action.value = 1;
			document.form_action.submit();
		}
		function del_file(filename){
			document.form_action.selected_file_list.value = filename
			document.form_action.cmd_arg.value = 3;
			document.form_action.action.value = 1;
			document.form_action.submit();
		}
		function down_file(filename){
			document.form_action.cmd_data.value = filename
			document.form_action.cmd_arg.value = 4;
			document.form_action.action.value = 1;
			document.form_action.target = '_blank';
			document.form_action.submit();
		}
		function selectfile(filename){
			document.form_action.selected_file_list.value = filename
		}
		function upload_file(filename){
			 var w = 600;
            var h = 250;
            window.open('".addslashes($path_info["basename"])."?action=10&current_dir=".addslashes($current_dir)."', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
			

			//$("#file").click();
			//document.form_action.cmd_arg.value = 4;
			//document.form_action.action.value = 1;
			//document.form_action.target = '_blank';
			//document.form_action.submit();
		}
		
        function test_prompt(arg){
            var erro='';
            var conf='';
            if (arg == 1){
                document.form_action.cmd_arg.value = prompt('输入文件夹名称.');
            } else if (arg == 2){
                document.form_action.cmd_arg.value = prompt('输入文件名.');
            } else if (arg == 71){
                if (!is_anything_selected()) erro = '没有选择任何文件.';
                else document.form_action.cmd_arg.value = prompt('输入文件名称.\n仅支持可用的扩展类型.\n如:\nnome.zip\nnome.tar\nnome.bzip\nnome.gzip');
            }
            if (erro!=''){
                document.form_action.cmd_arg.focus();
                alert(erro);
            } else if(document.form_action.cmd_arg.value.length>0) {
                document.form_action.action.value = arg;
                document.form_action.submit();
            }
        }
        function strstr(haystack,needle){
            var index = haystack.indexOf(needle);
            return (index==-1)?false:index;
        }
        function valid_dest(dest,orig){
            return (strstr(dest,orig)==false)?true:false;
        }
        // ArrayAlert - Selection debug only
        function aa(){
            var str = 'selected_dir_list:\n';
            for (x=0;x<selected_dir_list.length;x++){
                str += selected_dir_list[x]+'\n';
            }
            str += '\nselected_file_list:\n';
            for (x=0;x<selected_file_list.length;x++){
                str += selected_file_list[x]+'\n';
            }
            alert(str);
        }
        function test(arg){
            var erro='';
            var conf='';
            if (arg == 4){
                if (!is_anything_selected()) erro = '没有选择任何文件.\n';
                conf = '删除选定的文件 ?\n';
            } else if (arg == 5){
                if (!is_anything_selected()) erro = '没有选择任何文件.\n';
                else if(document.form_action.dir_dest.value.length == 0) erro = 'There is no selected destination directory.';
                else if(document.form_action.dir_dest.value == document.form_action.current_dir.value) erro = 'Origin and destination directories are equal.';
                else if(!valid_dest(document.form_action.dir_dest.value,document.form_action.current_dir.value)) erro = 'Destination directory is invalid.';
                conf = '复制到 \' '+document.form_action.dir_dest.value+' \' ?\n';
            } else if (arg == 6){
                if (!is_anything_selected()) erro = '没有选择任何文件.';
                else if(document.form_action.dir_dest.value.length == 0) erro = 'There is no selected destination directory.';
                else if(document.form_action.dir_dest.value == document.form_action.current_dir.value) erro = 'Origin and destination directories are equal.';
                else if(!valid_dest(document.form_action.dir_dest.value,document.form_action.current_dir.value)) erro = 'Destination directory is invalid.';
                conf = '移动到 \' '+document.form_action.dir_dest.value+' \' ?\n';
            } else if (arg == 9){
                if (!is_anything_selected()) erro = '没有选择任何文件.';
                else if(document.form_action.chmod_arg.value.length == 0) erro = 'New permission not set.';
                conf = 'CHANGE PERMISSIONS to \' '+document.form_action.chmod_arg.value+' \' ?\n';
            }
            if (erro!=''){
                document.form_action.cmd_arg.focus();
                alert(erro);
            } else if(conf!='') {
                if(confirm(conf)) {
                    document.form_action.action.value = arg;
                    document.form_action.submit();
                }
            } else {
                document.form_action.action.value = arg;
                document.form_action.submit();
            }
        }
