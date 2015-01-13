function admincolor_hideMenu(){
 $("div.left,aside.left").css("width","36px");
 $("div.left,aside.left").css("background-color","#ededed");

 $("#leftmenu span").css("margin-left","10px");
 $("#leftmenu span").css("padding-left","100px");
 
 $("div.main,section.main").css("padding-left","46px");
 
 $("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/arror2.png)");
 
 $("#aAdminColor").attr('href','javascript:admincolor_showMenu()');
 
 SetCookie('admincolor_hm','1',365);
 $("#leftmenu a").tooltip({disabled:false});
}

function admincolor_showMenu(){
 $("div.left,aside.left").css("width","140px");
 $("div.left,aside.left").css("background-color","transparent");

 $("#leftmenu span").css("margin-left","25px");
 $("#leftmenu span").css("padding-left","22px");
 $("div.main,section.main").css("padding-left","150px");
 
 $("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/arror.png)");
 
 $("#aAdminColor").attr('href','javascript:admincolor_hideMenu()');
 
 SetCookie('admincolor_hm','',-1);
 
 $("#leftmenu a").tooltip({disabled: true});
}
$(document).ready(function(){
  if(GetCookie('admincolor_hm')=='1')$("#leftmenu a").tooltip({position: { my: "left+36 center"}});
});