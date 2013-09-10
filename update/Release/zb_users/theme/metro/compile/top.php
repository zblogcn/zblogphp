<div id="headerbg">
  <div class="header">
    <div class="logo">
      <h1><a href="<?php  echo $host;  ?>"><?php  echo $name;  ?></a></h1>
      <h3><?php  echo $subname;  ?></h3>
    </div>
    <div class="search">
      <form method="post" action="<?php  echo $host;  ?>zb_system/cmd.php?act=search"><input type="text" name="q" id="edtSearch" size="12" /><input type="submit" value="搜索" name="btnPost" id="btnPost" /></form>
    </div>
    <div class="clear"></div>
  </div>
</div>