<?php  include $this->GetTemplate('header');  ?>
	<link rel="alternate" type="application/rss+xml" href="<?php  echo $feedurl;  ?>" title="<?php  echo $name;  ?>" />
</head>
<body class="single <?php  echo $type;  ?>">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="<?php  echo $host;  ?>"><?php  echo $name;  ?></a></h1>
			<h3 id="BlogSubTitle"><?php  echo $subname;  ?></h3>
		</div>
		<div id="divNavBar">
<ul>
<?php  echo $modules['navbar']->Content;  ?>
</ul>
		</div>
		<div id="divMain">
<?php if ($article->Type==ZC_POST_TYPE_ARTICLE) { ?>
<?php  include $this->GetTemplate('post-single');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-page');  ?>
<?php } ?>
		</div>
		<div id="divSidebar">
<?php  include $this->GetTemplate('sidebar');  ?>
		</div>
<?php  include $this->GetTemplate('footer');  ?>