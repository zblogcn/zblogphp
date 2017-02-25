<div id="div-module">
	<div id="div-progress">
		<p>操作进度：<span id="span-progress">0</span>%；运行时间：<span id="span-time">0</span>s。</p>
        <progress id="progress" style="width:100%" min="0" max="100"></progress>
	</div>
    <p><br/></p>
    <div id="div-message">
    	<table class="table_border table_border_cells table_striped table_hover" style="width:95%">
		<tbody id="tbody-message">
        <tr><td>正在初始化</td></tr>
        </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
var $tableSelector = $("#tbody-message");
var $timeSelector = $("#span-time");
var $progressSelector = $("#progress");
var $progressSpanSelector = $("#span-progress");
$.xhrPool = [];
$.xhrPool.abortAll = function() {
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool = [];
};

$.ajaxSetup({
    beforeSend: function(jqXHR) {
        $.xhrPool.push(jqXHR);
    },
    complete: function(jqXHR) {
        var index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) {
            $.xhrPool.splice(index, 1);
        }
    }
});

var clinicConfig = {
	moduleId: "<?php echo $module['id']?>",
    maxTasks: 0,
    currentTask: 0,
    currentTime: 0,
    timeInterval: 0
};
var ajaxPost = function(param) {
    var url = "ajax.php?module=" + clinicConfig.moduleId;
    var ajax = $.ajax(url, {
            data: param,
            type: "POST",
            dataType: "json"});
    return new Promise(function(resolve, reject) {
        ajax.done(resolve).fail(reject);
    })
};
var sendMessage = function(textString) {
    $("<tr><td>" + textString + "</td></tr>").prependTo($tableSelector);
};
$(function() {
    var addTask = function(data) {
        console.log(data);
        clinicConfig.maxTasks += data.length;
        return Promise.map(data, function(item, index) {
            return ajaxPost(item).then(function(data) {
                clinicConfig.currentTask++;
                return Promise.map(data, function(item) {
                    if (item.type == "msg") {
                        sendMessage(item.msg);
                    } else {
                        task.then(function() {
                            return addTask([item]);
                        });
                    }
                }).then(function() {
                    var per = ((clinicConfig.currentTask / clinicConfig.maxTasks) * 100).toFixed(2);
                    $progressSpanSelector.text(per);
                    $progressSelector.val(per);
                });
            });
        });
    };

    clinicConfig.timeInterval = setInterval(function() {
        $timeSelector.html(clinicConfig.currentTime++);
        if (parseInt('<?php echo $GLOBALS['blogversion'];?>') < 150601) bmx2table(); // Fuck
    }, 1000);

    var task = ajaxPost({function: "get_queue"}).cancellable().then(addTask).error(function(e) {
        var errorMessage = e.responseText.replace(/\</g, '&lt;').replace(/\>/g, '&gt;').split("\n").reverse();
        errorMessage.forEach(function(item) {
            sendMessage(item);
        });
        sendMessage('错误状态码：' + e.status);
        sendMessage('<span style="color: red">× 执行过程中出现错误！</span>');
        $.xhrPool.abortAll();
    }).finally(function() {
        clearInterval(clinicConfig.timeInterval);
    });


});
</script>
