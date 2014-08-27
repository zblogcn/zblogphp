
<div id="div-module">
	<div id="div-progress">
		<p>操作进度：<span id="span-progress">0</span>%；运行时间：<span id="span-time">0</span>s。</p>
        <progress id="progress" style="width:100%" min="0" max="100"></progress>
	</div>
    <p><br/></p>
    <div id="div-message">
    	<table class="table_border table_border_cells table_stripes" style="width:95%">
		<tbody id="tbody-message">
        <tr><td>正在初始化</td></tr>
        </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	var time = 0,
		runTask = function(pos) {
			var index = pos,
				taskLength = static._tasks.length,
				per = ((index / taskLength) * 100).toFixed(2);
			
			$("#span-progress").html(per);
			$("#progress").val(per);
			
			bmx2table();
		};
		
	static.setModuleId("<?php echo $module['id']?>").init().addTimer(function(){
		console.log(time);
		$("#span-time").html(time++);
	}).ajax({"function": "get_queue"}, function(data) {
		var data = JSON.parse(data),
			addTask = function(argu) {
				var value = argu[1];
				static.ajax(value, function(data, textStatus) {
					var data = JSON.parse(data);
					$.each(data, function(index, value) {
						if (value['type'] == 'queue') {
							static.addTask(addTask, value);
						}
						else {
							static.msg(value.msg);
						}
					});
					static.runTask(runTask);
				}, function(xmlHttp, textStatus, errorThrown) {
					static.msg('<span style="color:red">× 网络访问错误：' + textStatus + '</span>');
				})
			};
		$.each(data, function(index, value) {
			static.addTask(addTask, value);
		});
		static.runTask(runTask);
	});
});


</script>