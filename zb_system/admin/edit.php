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
if (!CheckRights($action)) {throw new Exception("没有权限！！！");}

$blogtitle='文章编辑';

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<script type="text/javascript">ActiveLeftMenu("aArticleEdt");</script>

<div class="divHeader2">文章编辑</div>


<div class="SubMenu"></div>
<div id="divMain2">
<form id="edit" name="edit" method="post" action="null">
  <div id="divEditLeft">
    <div id="divEditTitle">
      <input type="hidden" name="edtID" id="edtID" value="0" />
      <input type="hidden" name="edtFType" id="edtFType" value="0" />
      <!-- title( -->
      <p><span class='editinputname'>标题:</span>
        <input type="text" name="edtTitle" id="edtTitle" style="width:60%;max-width:520px" maxlength="100" onBlur="if(this.value=='') this.value='未命名文章'" onFocus="if(this.value=='未命名文章') this.value=''" value="未命名文章" />
      </p>
      <!-- )title --> 
      
      <!-- alias( -->
      <p><span class='editinputname'>别名:</span>
        <input type="text" style="width:60%;max-width:520px" name="edtAlias" id="edtAlias" maxlength="250" value="" />
        .html </p>
      <!-- )alias --> 
      
      <!-- tags( -->
      
      <p><span class='editinputname' style='padding:0 0 0 0;'>Tags:</span>
        <input type="text" style="width:60%;max-width:520px" name="edtTag" id="edtTag" value="" />
        (逗号分割) <a href="#" id="showtags">显示常用Tags</a></p>
      <!-- Tags -->
      <div id="ulTag" style="display:none;">
        <div id="ajaxtags">正在获取中，请稍候。</div>
      </div>
      
      <!-- )tags --> 
      
    </div>
    
    <!-- 1号输出接口 -->
    
    <div id="divContent" style="clear:both;">
      <p style="text-align:left;"><span class='editinputname'>正文:</span>&nbsp;&nbsp;<span id="timemsg"></span><span id="msg2"></span><span id="msg"></span><span class='editinputname'></span><script type="text/javascript" src="c_autosaverjs.asp?act=edit"></script></p>
      <textarea id="editor_txt" name="txaContent"></textarea>
      <div id="contentready" style="display:none"><img alt="loading" id="statloading1" src="../image/admin/loading.gif"/>正在为您加载编辑器</div>
      <p><span>在正文插入分隔符&quot;&lt;hr class=&quot;more&quot; /&gt;&quot;可以让系统识别摘要内容。如需另外指定摘要内容，请点击 <a href="" onClick="try{AutoIntro();return false;}catch(e){}">[手动生成摘要]</a></span></p>
    </div>
    <div id="divIntro" style="display:none;">
      <p><span class='editinputname'>摘要:</span></p>
      <textarea id="editor_txt2" name="txaIntro"></textarea>
      <div id="introready" style="display:none"><img alt="loading" id="statloading2" src="../image/admin/loading.gif"/>正在为您加载编辑器</div>
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
            
            <span class='editinputname'>分类:</span>
            <select style="width:150px;" class="edit" size="1" id="cmbCate" onChange="edtCateID.value=this.options[this.selectedIndex].value;selectlogtemplate(this.options[this.selectedIndex].value);">
            </select>
            <input type="hidden" name="edtCateID" id="edtCateID" value="0" />
            
          </p>
          <!-- cate --> 
          
          <!-- template( -->
          <p> <span class='editinputname'>模板:</span>
            <select style="width:150px;" class="edit" size="1" id="cmbTemplate" onChange="edtTemplate.value=this.options[this.selectedIndex].value">
              <option value="PAGE">PAGE</option><option value="SINGLE" selected="selected">SINGLE(默认模板)</option><option value="TOP">TOP</option>
            </select>
            <input type="hidden" name="edtTemplate" id="edtTemplate" value="" />
          </p>
          <!-- )template --> 
          
          <!-- level -->
          <p> <span class='editinputname'>类型:</span>
            <select class="edit" style="width:150px;" size="1" id="cmbArticleLevel" onChange="edtLevel.value=this.options[this.selectedIndex].value">
              <option value="1" >草稿</option><option value="2" >私人浏览</option><option value="3" >禁止评论</option><option value="4" selected="selected">普通</option>
            </select>
            <input type="hidden" name="edtLevel" id="edtLevel" value="4" />
          </p>
          <!-- )level --> 
          
          <!-- user( -->
          <p> <span class='editinputname'>用户名:</span>
            <select style="width:150px;" size="1" id="cmbUser" onChange="edtAuthorID.value=this.options[this.selectedIndex].value">
              <option value="1" selected="selected">zblogger</option>
            </select>
            <input type="hidden" name="edtAuthorID" id="edtAuthorID" value="1" />
          </p>
          <!-- )user --> 
          
          <!-- newdatetime( -->
          <p> <span class='editinputname'>日期:</span><span>
            <input type="text" name="edtDateTime" id="edtDateTime"  value="2013-7-26 17:49:22" style="width:141px;"/>
            </span> </p>
          <!-- )newdatetime --> 
          
          <!-- Istop( -->
          <p>
            
            <label><span class='editinputname'>置顶:
              
              <input type="checkbox" name="edtIstop" id="edtIstop" value="True"/>
              
              </span></label>
            
          </p>
          <!-- )Istop --> 
          
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




</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
