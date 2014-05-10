/*
 * jScroller 0.3 - Scroller Script
 *
 * Copyright (c) 2007 Markus Bordihn (http://markusbordihn.de)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * $Date: 2007-11-15 13:00:00 +0100 (Thu, 15 Nov 2007) $
 * $Rev: 0.3 $
 */

$(document).ready(function(){
   this.defaults = {
     scroller: {
       interval:  0,    // Dont touch !
       refresh:   150,  // Refresh Time in ms
       direction: "up", // down,right,left,up
       speed:     1,    // Set the Scroller Speed
       id:        "#scroller",
       cont_id:   "#scroller_container"
     }
   }

   var jscroller_config = $.extend(this.defaults), jscroller_scroller = $(jscroller_config.scroller.id), jscroller_scroller_cont = $(jscroller_config.scroller.cont_id);
   
   if (jscroller_scroller && jscroller_scroller_cont) {
      jscroller_scroller.css({position: 'absolute', left: 0, top: 0});
      jscroller_init();
   }

   function jscroller_startScroll() {
     if(!jscroller_config.scroller.interval) {
      jscroller_config.scroller.interval=window.setInterval(jscroller_doScroll,jscroller_config.scroller.refresh);
     }
   }

   function jscroller_stopScroll() {
     if (jscroller_config.scroller.interval) {
      window.clearInterval(jscroller_config.scroller.interval);
      jscroller_config.scroller.interval=0;
     }
   }

   function jscroller_init() {
    $("#scroller a").click(function(){
      window.open(this.href);
      return false;
    });
    jscroller_scroller_cont.css('overflow','hidden');
    if(!jscroller_config.scroller.interval) { 
      if (window.attachEvent) {
       window.attachEvent("onfocus", jscroller_startScroll);
       window.attachEvent("onblur",  jscroller_stopScroll);
       window.attachEvent("onresize", jscroller_startScroll);
       window.attachEvent("onscroll", jscroller_startScroll);
      }
      else if (window.addEventListener) {
       window.addEventListener("focus", jscroller_startScroll, false);
       window.addEventListener("blur",  jscroller_stopScroll, false);
       window.addEventListener("resize", jscroller_startScroll, false);
       window.addEventListener("scroll", jscroller_startScroll, false);
      }
      jscroller_startScroll();
      if ($.browser.msie) {window.focus()}
     }
   }

   function jscroller_getElem(Elem) {
    return (typeof Elem == "string" && document.getElementById)? document.getElementById(Elem) : Elem;
   }

   function jscroller_doScroll() {
    if (scroller_dom = jscroller_getElem(jscroller_scroller.attr("id"))) {
     var
      p_top= Number((/[0-9-,.]+/.exec(jscroller_scroller.css('top'))||0)),
      p_left=Number((/[0-9-,.]+/.exec(jscroller_scroller.css('left'))||0)),
      min_height=jscroller_scroller_cont.height(),
      min_width=jscroller_scroller_cont.width(),
      speed=jscroller_config.scroller.speed,
      p_height=scroller_dom.offsetHeight,
      p_width=scroller_dom.offsetWidth,
      direction=jscroller_config.scroller.direction,
      jscroller=jscroller_scroller;

     switch(direction) {
       case 'up':
        if (p_top <= -1*p_height) {p_top=min_height;}
        jscroller.css('top',p_top-speed+'px');
       break;
       case 'right':
        if (p_left >= min_width) {p_left=-1*p_width;}
        jscroller.css('left',p_left+speed+'px');
       break;
       case 'left':
        if (p_left <= -1*p_width) {p_left=min_width;}
        jscroller.css('left',p_left-speed+'px');
       break;
       case 'down':
        if (p_top >= min_height) {p_top=-1*p_height;}
        jscroller.css('top',p_top+speed+'px');
       break;
     }
    }
   }
});