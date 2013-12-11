<div class="pathinfo">
	<div class="p">
		<div class="icon folder_icon"></div>
		<div class="content" style="line-height:40px;margin-left:40px;">共<?php echo $pathinfo['file_num'];?> 个文件，<?php echo $pathinfo['folder_num'];?> 个文件夹</div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">类型：</div>
		<div class="content">多种类型</div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">位置：</div>
		<div class="content"><?php echo $pathinfo['father_name'];?></div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">大小：</div>
		<div class="content"><?php echo $pathinfo['size_friendly'];?>  (<?php echo $pathinfo['size'];?> 字节)</div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">父目录权限：</div>
		<div class="content"><?php echo $pathinfo['mode'];?></div>
		<div style="clear:both"></div>
	</div>	
</div>