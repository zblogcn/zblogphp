<!-- Layout -->
<div id="header-region" class="clear-block"></div>
    <div id="wrapper">
    <div id="container" class="clear-block">
      <div id="header">
        <div id="logo-floater">
        <h1><a href="<?php  echo $host;  ?>" title="<?php  echo $name;  ?>"><img src="<?php  echo $host;  ?>zb_users/theme/<?php  echo $theme;  ?>/style/<?php  echo $style;  ?>/logo.png" alt="<?php  echo $name;  ?>" id="logo"><span><?php  echo $name;  ?></span></a></h1>
		 </div>
        <ul id="divNavBar" class="links primary-links"><?php  if(isset($modules['navbar'])){echo $modules['navbar']->Content;}  ?></ul> 
      </div> <!-- /header -->
	  <div id="center"><div id="squeeze"><div class="right-corner"><div class="left-corner">
