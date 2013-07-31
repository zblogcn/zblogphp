<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require_once '../function/c_system_base.php';
require_once '../function/c_system_admin.php';

$zbp->Initialize();

$action='ArticleEdt';
if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}

$blogtitle=$blogname . '-' . $lang['msg']['article_edit'];

require_once $blogpath . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript" src="../script/jquery.tagto.js"></script>
<script type="text/javascript" src="../script/jquery-ui-timepicker-addon.js"></script>
<?php
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<script type="text/javascript">ActiveLeftMenu("aArticleEdt");</script>

<div class="divHeader2"><?php echo $lang['msg']['article_edit']?></div>


<div class="SubMenu"></div>
<div id="divMain2">
<form id="edit" name="edit" method="post" action="#">
  <div id="divEditLeft">
    <div id="divEditTitle">
      <input type="hidden" name="edtID" id="edtID" value="0" />
      <input type="hidden" name="edtFType" id="edtFType" value="0" />
      <!-- title( -->
      <p><span class='editinputname'><?php echo $lang['msg']['title']?>:</span>
        <input type="text" name="edtTitle" id="edtTitle" style="width:60%;max-width:520px" maxlength="100" onBlur="if(this.value=='') this.value='<?php echo $lang['msg']['unnamed']?>'" onFocus="if(this.value=='<?php echo $lang['msg']['unnamed']?>') this.value=''" value="<?php echo $lang['msg']['unnamed']?>" />
      </p>
      <!-- )title --> 
      
      <!-- alias( -->
      <p><span class='editinputname'><?php echo $lang['msg']['alias']?>:</span>
        <input type="text" style="width:60%;max-width:520px" name="edtAlias" id="edtAlias" maxlength="250" value="" />
      </p>
      <!-- )alias --> 
      
      <!-- tags( -->
      
      <p><span class='editinputname'><?php echo $lang['msg']['tags']?>:</span>
        <input type="text" style="width:60%;max-width:520px" name="edtTag" id="edtTag" value="" />
        (<?php echo $lang['msg']['use_commas_to_separate']?>) <a href="#" id="showtags"><?php echo $lang['msg']['show_common_tags']?></a></p>
      <!-- Tags -->
      <div id="ulTag" style="display:none;">
        <div id="ajaxtags">Watting...</div>
      </div>
      
      <!-- )tags --> 
      
    </div>
    
    <!-- 1号输出接口 -->
    
    <div id="divContent" style="clear:both;">
      <p style="text-align:left;"><span class='editinputname'><?php echo $lang['msg']['content']?>:</span>&nbsp;&nbsp;<span id="timemsg"></span><span id="msg2"></span><span id="msg"></span><span class='editinputname'></span><script type="text/javascript" src="c_autosaverjs.asp?act=edit"></script></p>
      <textarea id="editor_content" name="txaContent"></textarea>
      <div id="contentready" style="display:none"><img alt="loading" id="statloading1" src="../image/admin/loading.gif"/>Watting...</div>
      <p><span><?php echo $lang['msg']['help_generate_summary']?><a href="" onClick="try{AutoIntro();return false;}catch(e){}">[<?php echo $lang['msg']['generate_summary']?>]</a></span></p>
    </div>
    <div id="divIntro" style="display:none;">
      <p><span class='editinputname'><?php echo $lang['msg']['intro']?>:</span></p>
      <textarea id="editor_intro" name="txaIntro"></textarea>
      <div id="introready" style="display:none"><img alt="loading" id="statloading2" src="../image/admin/loading.gif"/>Watting...</div>
    </div>
    
    <!-- 2号输出接口 -->
    
  </div>
  <!-- divEditLeft -->
  
  <div id="divEditRight">
    <div id="divEditPost">
      <div id="divBox">
        <div id="divFloat">
          <p>
            <input class="button" style="width:150px;height:30px;" type="submit" value="提交" id="btnPost" onclick='return checkArticleInfo();' />
          </p>
          
          <!-- cate -->
          <p>
            
            <span class='editinputname'><?php echo $lang['msg']['category']?>:</span>
            <select style="width:150px;" class="edit" size="1" id="cmbCate" onChange="edtCateID.value=this.options[this.selectedIndex].value;selectlogtemplate(this.options[this.selectedIndex].value);">
            </select>
            <input type="hidden" name="edtCateID" id="edtCateID" value="0" />
            
          </p>
          <!-- cate --> 
          
          <!-- template( -->
          <p> <span class='editinputname'><?php echo $lang['msg']['template']?>:</span>
            <select style="width:150px;" class="edit" size="1" id="cmbTemplate" onChange="edtTemplate.value=this.options[this.selectedIndex].value">
              <option value="PAGE">PAGE</option><option value="SINGLE" selected="selected">SINGLE(<?php echo $lang['msg']['default_template']?>)</option><option value="TOP">TOP</option>
            </select>
            <input type="hidden" name="edtTemplate" id="edtTemplate" value="" />
          </p>
          <!-- )template --> 
          
          <!-- level -->
          <p> <span class='editinputname'><?php echo $lang['msg']['status']?>:</span>
            <select class="edit" style="width:150px;" size="1" id="cmbArticleLevel" onChange="edtLevel.value=this.options[this.selectedIndex].value">
              <option value="0" ><?php echo $lang['post_status_name']['0']?></option><option value="1" ><?php echo $lang['post_status_name']['1']?></option><option value="2" ><?php echo $lang['post_status_name']['2']?></option>
            </select>
            <input type="hidden" name="edtLevel" id="edtLevel" value="4" />
          </p>
          <!-- )level --> 
          
          <!-- user( -->
          <p> <span class='editinputname'><?php echo $lang['msg']['author']?>:</span>
            <select style="width:150px;" size="1" id="cmbUser" onChange="edtAuthorID.value=this.options[this.selectedIndex].value">
              <option value="1" selected="selected">zblogger</option>
            </select>
            <input type="hidden" name="edtAuthorID" id="edtAuthorID" value="1" />
          </p>
          <!-- )user --> 
          
          <!-- newdatetime( -->
          <p> <span class='editinputname'><?php echo $lang['msg']['date']?>:</span><span>
            <input type="text" name="edtDateTime" id="edtDateTime"  value="" style="width:141px;"/>
            </span> </p>
          <!-- )newdatetime --> 
          
          <!-- Istop( -->
          <p>
            
            <label><span class='editinputname'><?php echo $lang['msg']['top']?>:
              
              <input type="checkbox" name="edtIstop" id="edtIstop" value="True"/>
              
              </span></label>
            
          </p>
          <!-- )Istop --> 
          <p>
            
            <label><span class='editinputname'><?php echo $lang['msg']['disable_comment']?>:
              
              <input type="checkbox" name="edtIslock" id="edtIslock" value="True"/>
              
              </span></label>
            
          </p>
          <!-- IsLock( -->

          <!-- )IsLock --> 

          <!-- Navbar( -->
          
          <!-- )Navbar --> 
          
          <!-- 3号输出接口 -->
          
        </div>
      </div>
    </div>
  </div>
  <!-- divEditRight -->
  
</form>
</div>


        <script type="text/javascript">

var tag_loaded=false; //是否已经ajax读取过TAGS
var sContent="",sIntro="";//原内容与摘要
var isSubmit=false;//是否提交保存



$(document).click(function (event){$('#ulTag').slideUp("fast");});  

//文章内容或摘要变动提示保存
window.onbeforeunload = function(){
  if (!isSubmit && ($('#editor_content').val())) return "<?php echo $zbp->lang['error'][71];?>";
}


function checkArticleInfo(){
  document.getElementById("edit").action="../cmd.php?act=ArticlePst";

  if(!$('#editor_content').val()){
    alert('<?php echo $zbp->lang['error'][70];?>');
    return false
  }

  isSubmit=1;
}



//日期时间控件
$.datepicker.regional['zh-cn'] = {
  closeText: '完成',
  prevText: '上个月',
  nextText: '下个月',
  currentText: '现在',
  monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
  monthNamesShort: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
  dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
  dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
  dayNamesMin: ['日','一','二','三','四','五','六'],
  weekHeader: '周',
  dateFormat: 'yy-m-d',
  firstDay: 1,
  isRTL: false,
  showMonthAfterYear: true,
  yearSuffix: ' 年  '
};
$.datepicker.setDefaults($.datepicker.regional['zh-cn']);
$.timepicker.regional['zh-cn'] = {
  timeOnlyTitle: '时间',
  timeText: '时间',
  hourText: '小时',
  minuteText: '分钟',
  secondText: '秒钟',
  millisecText: '毫秒',
  currentText: '现在',
  closeText: '完成',
  timeFormat: 'h:m:s',
  ampm: false
};
$.timepicker.setDefaults($.timepicker.regional['zh-cn']);
$('#edtDateTime').datetimepicker({
  showSecond: true
  //changeMonth: true,
  //changeYear: true
});




//显示tags
$('#showtags').click(function (event) {  
  event.stopPropagation();  
  var offset = $(event.target).offset();  
  $('#ulTag').css({ top: offset.top + $(event.target).height()+20+ "px", left: offset.left});  
  $('#ulTag').slideDown("fast");    
  if(tag_loaded==false){$.getScript('../function/c_admin_js.asp?act=tags');tag_loaded=true;}
});  



function AddKey(i) {
  var strKey=$('#edtTag').val();
  var strNow=","+i
  if(strKey==""){
    strNow=i
  }
  if(strKey.indexOf(strNow)==-1){
    strKey=strKey+strNow;
  }
  $('#edtTag').val(strKey);
}
function DelKey(i) {
  var strKey=$('#edtTag').val();
  var strNow="{"+i+"}"
  if(strKey.indexOf(strNow)!=-1){
    strKey=strKey.substring(0,strKey.indexOf(strNow))+strKey.substring(strKey.indexOf(strNow)+strNow.length,strKey.length)
  }
  $('#edtTag').val(strKey);
}

//提取摘要
function AutoIntro() {
  var s=$('#editor_content').val();
  if(s.indexOf("<hr class=\"more\" />")>-1||s.indexOf("<hr class=\"more\"/>")>-1){
    $('#editor_intro').val(s.split("<hr class=\"more\" />")[0]);
    $('#editor_intro').val(s.split("<hr class=\"more\"/>")[0]);
  }else{
    $('#editor_intro').val(s.substring(0,250));
  }

  $("#divIntro").show();
  $('html,body').animate({scrollTop:$('#divIntro').offset().top},'fast');
}


//文章编辑提交区随动JS开始
var oDiv=document.getElementById("divFloat");
var H=0;var Y=oDiv;
while(Y){H+=Y.offsetTop;Y=Y.offsetParent;};
$(window).bind("scroll resize",function(){
  var s=document.body.scrollTop||document.documentElement.scrollTop;
  if(s>H){
    $("#divFloat").addClass("boxfloat");
  }
  else{
    $("#divFloat").removeClass("boxfloat");
  }   
});

//选择模板
function selectlogtemplate(c){

}
function selectlogtemplatesub(a){
  $("#cmbTemplate").find("option[value='"+a+"']").attr("selected","selected");
  $("#edtTemplate").val(a);
}


</script>



</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
