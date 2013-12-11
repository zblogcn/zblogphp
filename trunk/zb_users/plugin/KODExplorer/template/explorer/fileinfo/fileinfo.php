<div class="pathinfo">
	<div class="p">
		<div class="icon file_icon"></div>
		<input type="text" name="filename" value="<?php echo $path_this_name;?>"/>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">文件类型：</div>
		<div class="content"><?php echo $file_info['ext'];?> 文件</div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">打开方式：</div>
		<div class="content">系统默认</div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">位置：</div>
		<div class="content" id="id_fileinfo_path"><?php echo $path_father_name;?></div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">大小：</div>
		<div class="content"><?php echo $file_info['size_friendly'];?>  (<?php echo $file_info['size'];?> 字节)</div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">创建时间</div>
		<div class="content"><?php echo $file_info['atime'];?></div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">修改时间</div>
		<div class="content"><?php echo $file_info['ctime'];?></div>
		<div style="clear:both"></div>
	</div>
	<div class="p">
		<div class="title">访问时间</div>
		<div class="content"><?php echo $file_info['mtime'];?></div>
		<div style="clear:both"></div>
	</div>
	<div class="line"></div>
	<div class="p">
		<div class="title">权限：</div>
		<div class="content"><?php echo $file_info['mode'];?></div>
		<div style="clear:both"></div>
	</div>	
</div>