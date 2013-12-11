<div class="pathinfo">
	<div class="p">
		<div class="icon folder_icon"></div>
		<input type="text" name="filename" value="<?php echo $path_this_name;?>"/>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">类型：</div>
		<div class="content">文件夹</div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">位置：</div>
		<div class="content"><?php echo $path_father_name;?></div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">大小：</div>
		<div class="content"><?php echo $path_info['size_friendly'];?>  (<?php echo $path_info['size'];?> 字节)</div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">包含：</div> 
		<div class="content"><?php echo $path_info['file_num'];?> 个文件，<?php echo $path_info['folder_num'];?> 个文件夹</div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">创建时间</div>
		<div class="content"><?php echo $path_info['atime'];?></div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">修改时间</div>
		<div class="content"><?php echo $path_info['ctime'];?></div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">权限：</div>
		<div class="content"><?php echo $path_info['mode'];?></div>
		<div style="clear:both"></div>
	</div>	
</div>