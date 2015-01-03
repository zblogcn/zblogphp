<?php  include $this->GetTemplate('header');  ?>
</head>
<body class="multi <?php  echo $type;  ?>">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="<?php  echo $host;  ?>"><?php  echo $name;  ?></a></h1>
			<h3 id="BlogSubTitle"><?php  echo $subname;  ?></h3>
		</div>
		<div id="divNavBar">
<ul>
<?php  if(isset($modules['navbar'])){echo $modules['navbar']->Content;}  ?>
</ul>
		</div>
		<div id="divMain">
<?php  foreach ( $articles as $article) { ?> 

<?php if ($article->IsTop) { ?>
<?php  include $this->GetTemplate('post-istop');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-multi');  ?>
<?php } ?>

<?php  }   ?>
<div class="pagebar"><?php  include $this->GetTemplate('pagebar');  ?></div>
		</div>
		<div id="divSidebar">
<?php  include $this->GetTemplate('sidebar');  ?>
		</div>
<?php  include $this->GetTemplate('footer');  ?>