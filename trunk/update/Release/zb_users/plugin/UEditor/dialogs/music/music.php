<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>插入音乐</title>
    <script type="text/javascript" src="../internal.js"></script>
    <link rel="stylesheet" type="text/css" href="music.css">
</head>
<body>
<div class="wrapper">
  <div id="musicTab">
    <div id="tabHeads" class="tabhead"> <span tabSrc="remote" class="focus"><var id="lang_tab_remote"></var></span> <span id="local_" tabSrc="local"><var id="lang_tab_local"></var></span> </div>

    <div id="tabBodys" class="tabbody">
      <div id="remote" class="panel">
        <div class="searchBar">
          <input id="J_searchName" type="text"/>
          <input type="button" class="searchBtn" id="J_searchBtn">
        </div>
        <div class="resultBar" id="J_resultBar">
          <div class="loading" style="display:none"></div>
          <div class="empty"><var id="lang_input_tips"></var></div>
        </div>
        <div id="J_preview"></div>
      </div>
      
      <div id="local" class="panel" style="display:none">
      <table>
		  <tbody>
			  <tr><td><label for ="songurl">歌曲地址：</label></td><td><input id="songurl" type="text" class="txt"/></td></tr>
			  <tr><td><label for ="authorname">歌曲名：</label></td><td><input id="authorname" type="text" class="txt"/></td></tr>
			  <tr><td><label for ="songname">歌手名：</label></td><td><input id="songname" type="text" class="txt"/></td></tr>
			  <tr><td><label for ="songalbum">专辑名：</label></td><td><input id="songalbum" type="text" class="txt"/></td></tr>
		  </tbody>
      </table>
      <div id="J_preview2"></div>
      </div>
      
    </div>
  </div>
</div>
<script type="text/javascript" src="../tangram.js"></script> 
<script type="text/javascript" src="music.js"></script> 
<script type="text/javascript">
    var music = new Music;
	music.init();
    dialog.onok = function () {
        music.exec();
    };
    dialog.oncancel = function () {
        $G('J_preview').innerHTML = "";
    };
	$G("songurl").onchange=function(){showpreview()};
	$G("authorname").onchange=function(){showpreview()};
	$G("songname").onchange=function(){showpreview()};
	$G("songalbum").onchange=function(){showpreview()};
	function showpreview(){
		$G("J_preview2").innerHTML=music._buildMusicHtmlPreview(music._getUrl2({url:$G("songurl").value,
				title:$G("songname").value,
				author:$G("authorname").value,
				album_title:$G("songalbum").value}));
	}
</script>
</body>
</html>