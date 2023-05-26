/*
 * @Name     : Zit Script
 * @Author   : 吉光片羽
 * @Support  : jgpy.cn
 * @Create   : 2019-12-25 20:10:23
 * @Update   : 2020-03-12 22:26:12
 */

var lang={
  "zh-hans":{
    "submitting":"提交中",
    "submit":"提交",
    "cancel":"取消回复",
    "reply":"回复"
  },
  "zh-hant":{
    "submitting":"正在送出",
    "submit":"送出",
    "cancel":"取消回應",
    "reply":"回應"
  },
  "en":{
    "submitting":"submitting",
    "submit":"submit",
    "cancel":"cancel",
    "reply":"reply to "
  }
},
msg=lang[$("html").attr("lang").toLowerCase()];

$(function(){
  var $face=$("#face").addClass("swell"),
  $menu=$("#menu"),
  $mobi=$("<i id='navim' class='kico'>&equiv;</i>"),
  $search=$("#seek"),
  $side=$("#side"),
  $cmt=$("form.cmt"),
  $revoke=$("<button type='button' class='revoke'>"+msg.cancel+"</button>").click(function(){
    $("#inpRevID").val(0);
    $("#txaArticle").attr("placeholder","").blur();
    zbp.plugin.emit('comment.reply.cancel');
    $(this).fadeOut();
  }),
  $len=$("<span style='position:fixed;top:-999em;transition:none;'></span>"),
  pos=$("#banner").height()/2.5;

  $("img.cover").each(function(){
    if(!this.frame){
      this.style.backgroundImage="url("+this.src+")";
      var img=new Image(),me=this;
      img.src=this.src;
      img.onerror=function(){
        $(me).parents(".poster").removeClass("poster").children("figure").remove();
      }
      this.src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
      this.style.backgroundSize="cover";
      this.frame=1;
    }
  });

  $menu.before($mobi.click(function(){
    this.on=!this.on;
    $(this).text(this.on?"×":"≡");
    $("body")[this.on?"addClass":"removeClass"]("friz");
  }));

  $search.find("button").click(function(){
    var invis=$search.hasClass("invis");
    $search.removeClass("invis").find("input").focus();
    return !invis;
  });
  $("#shuts").click(function(){
    $search.addClass("invis");
  });

  $("img.hue").each(function(){
    this.style.backgroundSize="auto";
    this.style.backgroundPosition=Math.ceil(Math.random()*100)+"%";
  });

  $("#cont").find("h1,h2,h3,h4,h5,h6").addClass("zit");

  $(".album").find(".cover").each(function(){
    $(this).css("opacity",(Math.floor(Math.random()*(95-60))+50)/100);
  });

  $side.find("#tbCalendar").parent("div").css("margin","-1em -1em -.5em");

  $side.find(".cp-hello").parent().addClass("cpanel").find("a").addClass("more");
  $side.find(".cp-login a").addClass("kico-user");
  $side.find(".cp-vrs a").addClass("kico-memo");

  $("textarea").on("keyup blur",function(){
    this.style.height = "auto";
    this.style.height = (this.scrollHeight+4) + "px";
    this.style.overflow = "hidden";
  }).keyup();

  $cmt[0]&&$len.appendTo("body");
  $cmt.find("input:text").on("focus keyup keydown init",function(){
    this.value=$.trim(this.value);
    $len.css("font-size",$(this).css("font-size")).text(this.value||this.placeholder);
    $(this).width($len.width()+3).css("max-width","100%");
  }).each(function(){
    $(this).trigger("init");
  });

  $cmt.submit(function(){
    posting(true);
    return zbp.comment.post();
  });

  $cmt.find(":submit").after($revoke.hide());

  $("#tbCalendar caption a:eq(1)").addClass("zit");

  $(window).scroll(function(){
    $face[$(this).scrollTop()>pos?"removeClass":"addClass"]("swell");
  });

});

function fitfix(){
  $("body,html").delay(100).animate({scrollTop:"-=100px"});
}

function posting(state){
  $("#postcmt").find(":submit").text(state?msg.submitting:msg.submit).prop("disabled",state)[state?"addClass":"removeClass"]("kico-dai kico-gap");
}

zbp.plugin.on("comment.post.success", "zit", function (formData, data) {
  $("#inpRevID").val(0);
  $("#txaArticle").attr("placeholder","");
  $("#cmt"+data.data.ID).addClass("hilite");
  fitfix();
});

zbp.plugin.on("comment.post.done", "zit", function () {
  posting(false);
  $(".revoke").fadeOut();
});

zbp.plugin.on("comment.reply.start", "zit", function (id) {
  $("#txaArticle").attr("placeholder",msg.reply+$("#cmt"+id).children("cite").children("b").text());
  $(".revoke").fadeIn();
  fitfix();
});