<div class='h1'><i class="font-icon icon-edit"></i>编辑器主题设置</div>
<div class="section">
	<div class='dialog box'>
	<?php 
		$tpl="<div class='{this} list'><div class='ico'><img src='./static/images/thumb/code/{0}.png'/></div><div class='info'>{0}</div></div>";
		echo getTplList('/','=',$this->config['codethemeall'],$tpl,$this->config['codetheme']);
	?>
	<div style="clear:both;"></div>
	</div>
</div>
<div class="savebox">
<a onclick="Setting.tools();" href="javascript:void(0);" class="save button">保存</a>
</div>