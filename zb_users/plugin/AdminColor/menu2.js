function admincolor_hideMenu(){
 $("div.left,aside.left").css({"background-color":"#22282e"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"10px","padding-left":"100px"}); 
 $("#leftmenu #nav_admincolor2 span").animate({"margin-left":"10px","padding-left":"20px","background-positionX":"0px"}); 
 $("body").animate({"background-positionX":"-125px"}); 
 $("#aAdminColor2>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/arroraly2.png)");
 $("#aAdminColor2").attr('href','javascript:admincolor_showMenu()');
 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();
}

function admincolor_showMenu(){
 $("div.left,aside.left").css({"background-color":"#22282e"});
 $("div.left,aside.left").animate({"width":"160px"});
 $("div.main,section.main").animate({"padding-left":"170px"});
 $("#leftmenu span").animate({"margin-left":"25px","padding-left":"29px"});
 $("#leftmenu #nav_admincolor2 span").animate({"padding-left":"60px","background-positionX":"40px"}); 
 $("body").animate({"background-positionX":"+0px"}); 
 $("#aAdminColor2>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/arroraly.png)");
 $("#aAdminColor2").attr('href','javascript:admincolor_hideMenu()');
 SetCookie('admincolor_hm','',-1); 
 $("#leftmenu a").tooltip({disabled: true});
 //$("#leftmenu a").tooltip( "destroy" );
}

function admincolor_tooptip(){
	$("#leftmenu a").tooltip({
	  disabled:false,
      position: {
		my: "left+50 top-33",
       //my: "left+50 top-33",
       at: "left bottom",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow_leftmenu" )
            .appendTo( this );
        }
      }
    });
}

$(document).ready(function(){
  if(GetCookie('admincolor_hm')=='1') {admincolor_tooptip();}
});
