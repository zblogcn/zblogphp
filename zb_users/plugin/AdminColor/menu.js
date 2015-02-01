function admincolor_hideMenu(){
 $("div.left,aside.left").css({"background-color":"#ededed"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"10px","padding-left":"100px"}); 
 
 $("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/arror2.png)");
 $("#aAdminColor").attr('href','javascript:admincolor_showMenu()');
 $("#aAdminColor").attr('title',lang_admincolor_expandmenu);
 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();
}

function admincolor_showMenu(){
 $("div.left,aside.left").css({"background-color":"transparent"});
 $("div.left,aside.left").animate({"width":"140px"});
 $("div.main,section.main").animate({"padding-left":"150px"});
 $("#leftmenu span").animate({"margin-left":"25px","padding-left":"22px"});
 
 $("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/arror.png)");
 $("#aAdminColor").attr('href','javascript:admincolor_hideMenu()');
  $("#aAdminColor").attr('title',lang_admincolor_closemenu);
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
