<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('WhitePage')) {
    $zbp->ShowError(48);
    die();
}
$blogtitle = $zbp->lang['WhitePage']['theme_config'];

if (count($_POST) > 0) {
    CheckIsRefererValid();
    if (GetVars('pagetype', 'POST')) {
        $zbp->Config('WhitePage')->custom_pagetype = GetVars('pagetype', 'POST');
    }
    if (GetVars('pagewidth', 'POST')) {
        $zbp->Config('WhitePage')->custom_pagewidth = 1200;
    }
    if (GetVars('headtitle', 'POST')) {
        $zbp->Config('WhitePage')->custom_headtitle = GetVars('headtitle', 'POST');
    }
    if (GetVars('bgcolor', 'POST')) {
        $zbp->Config('WhitePage')->custom_bgcolor = GetVars('bgcolor', 'POST');
    }
    if (GetVars('fontcolor', 'POST')) {
        $zbp->Config('WhitePage')->custom_fontcolor = GetVars('fontcolor', 'POST');
    }
    if (GetVars('pagecolor', 'POST')) {
        $zbp->Config('WhitePage')->custom_pagecolor = GetVars('pagecolor', 'POST');
    }
    if (GetVars('acolor', 'POST')) {
        $zbp->Config('WhitePage')->custom_acolor = GetVars('acolor', 'POST');
    }
    if (GetVars('ahovercolor', 'POST')) {
        $zbp->Config('WhitePage')->custom_ahovercolor = GetVars('ahovercolor', 'POST');
    }
    if (GetVars('blogtitlecolor', 'POST')) {
        $zbp->Config('WhitePage')->custom_blogtitlecolor = GetVars('blogtitlecolor', 'POST');
    }
    if (GetVars('text_indent', 'POST') !== false) {
        $zbp->Config('WhitePage')->text_indent = GetVars('text_indent', 'POST');
    }
    $zbp->Config('WhitePage')->SuperFast = (bool) GetVars('super_fast', 'POST');
    $zbp->Config('WhitePage')->ShowAvatar = (bool) GetVars('show_avatar', 'POST');
    $zbp->SaveConfig('WhitePage');

    $zbp->SetHint('good');
    Redirect($_SERVER["HTTP_REFERER"]);
}

$percolors = array(//          封面     a=bg;   b=page;  c=font;  d=title;  e=a;    f=ahover;
    'White'       => array('1', 'fafafa', 'ffffff', 'ffffff', '333333', '80092e', '5c1515', 'b38a22'),
    'Blue'        => array('2', '6699ff', '6699ff', 'ffffff', '333333', '005f92', '0079ba', 'b22222'),
    'Monokai'     => array('3', '3f4b4e', '3f4b4e', '272822', 'b3b689', '83c763', '8cbbad', 'ff8409'),
    'Abyss'       => array('4', '6688a9', '242e36', '000c18', '6688a9', '22aa32', 'cfbb88', 'ea5740'),
    'Kimbei Dark' => array('5', 'dc3939', '362712', '221a0f', 'd3af67', 'dc3939', '8ab1a1', '888b28'),
    'Dark+'       => array('6', '252526', '252526', '1e1e1e', 'dcdc9d', 'ce9178', '569cd6', '9cdcfe'),
    'Khaki'       => array('7', '5f5f00', 'afaf87', 'd7d7af', '005f5f', '5f5f00', '000087', '87005f'),
);

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<link href="source/colpick.css" rel="stylesheet" /> 
<script src="source/colpick.js" type="text/javascript"></script>
<style>
input.colorpicker { 
border-right-width: 25px; 
width: 84px; 
height: 25px;
line-height:25px;
cursor: pointer; 
font-family: 'Lucida Console', Monaco, monospace;
box-sizing: border-box;
padding:0;
margin:0;
float:left;
}
.color-box {
float:left;
width:30px;
height:30px;
margin:5px;
border: 1px solid white;
cursor: pointer; 
box-sizing: border-box;
}
.color-box-picker{
margin: 8px 10px;
border: 1px solid #aaa; width: 86px;height: 27px;
}
</style>
<!--#include file="..\..\..\..\zb_system\admin\admin_top.asp"-->
<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle; ?></div>
    <div class="SubMenu"></div>
    <div id="divMain2"> 
        <form action="?save" method="post">
            <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCsrfToken(); ?>">
            <table width="100%" border="1" width="100%" class="tableBorder">
                <tr>
                    <th scope="col"  height="32" width="20%"><?php echo $zbp->lang['WhitePage']['global_settings']; ?></th>
                    <th></th>
                </tr>
                <tr>
                    <td scope="col"  height="52"><?php echo $zbp->lang['WhitePage']['page_type']; ?></td>
                    <td >
                        <label><input type="radio" id="pt2" name="pagetype" value="2" <?php echo($zbp->Config('WhitePage')->custom_pagetype == 2) ? 'checked="checked"' : ''; ?>/>&nbsp;CSS3 <?php echo $zbp->lang['WhitePage']['shadow']; ?>(<?php echo $zbp->lang['WhitePage']['right_angle']; ?>)</label>
                        &nbsp;&nbsp;
                        <label><input type="radio" id="pt3" name="pagetype" value="3" <?php echo($zbp->Config('WhitePage')->custom_pagetype == 3) ? 'checked="checked"' : ''; ?>/>&nbsp;CSS3 <?php echo $zbp->lang['WhitePage']['shadow']; ?>(<?php echo $zbp->lang['WhitePage']['rounded_corner']; ?>)</label>
                        &nbsp;&nbsp;
                        <label><input type="radio" id="pt4" name="pagetype" value="4" <?php echo($zbp->Config('WhitePage')->custom_pagetype == 4) ? 'checked="checked"' : ''; ?>/>&nbsp;<?php echo $zbp->lang['WhitePage']['flat']; ?>(<?php echo $zbp->lang['WhitePage']['right_angle']; ?>)</label>
                        &nbsp;&nbsp;
                        <label><input type="radio" id="pt5" name="pagetype" value="5" <?php echo($zbp->Config('WhitePage')->custom_pagetype == 5) ? 'checked="checked"' : ''; ?>/>&nbsp;<?php echo $zbp->lang['WhitePage']['flat']; ?>(CSS3<?php echo $zbp->lang['WhitePage']['rounded_corner']; ?>)</label>
                        &nbsp;&nbsp;
                        <label><input type="radio" id="pt1" name="pagetype" value="1" <?php echo($zbp->Config('WhitePage')->custom_pagetype == 1) ? 'checked="checked"' : ''; ?>/>&nbsp;<?php echo $zbp->lang['WhitePage']['none']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td scope="col"  height="52"><?php echo $zbp->lang['WhitePage']['text_indent']; ?></td>
                    <td >
                        <label><input type="radio" id="text_indent1" name="text_indent" value="0" <?php echo($zbp->Config('WhitePage')->text_indent == 0) ? 'checked="checked"' : ''; ?>/>&nbsp;<?php echo $zbp->lang['WhitePage']['none']; ?></label>
                        &nbsp;&nbsp;
                        <label><input type="radio" id="text_indent2" name="text_indent" value="2" <?php echo($zbp->Config('WhitePage')->text_indent == 2) ? 'checked="checked"' : ''; ?>/>&nbsp;<?php echo $zbp->lang['WhitePage']['standard']; ?></label>
                    </td>
                </tr>
                <tr>
                 <td scope="col"  height="52"><?php echo $zbp->lang['WhitePage']['display_avatar']; ?></td>
                  <td>
<input id="show_avatar" name="show_avatar" class="checkbox" type="text" value="<?php echo (bool) $zbp->Config('WhitePage')->ShowAvatar; ?>"  size="100"/><br/>
                  </td>
                </tr>
                <tr>
                 <td scope="col"  height="52"><?php echo $zbp->lang['WhitePage']['super_fast_mode']; ?></td>
                  <td>
<input id="super_fast" name="super_fast" class="checkbox" type="text" value="<?php echo (bool) $zbp->Config('WhitePage')->SuperFast; ?>"  size="100"/> (<?php echo $zbp->lang['WhitePage']['only_one_cssjs']; ?>)<br/>
                  </td>
                </tr> 
            </table>

            <table width="100%" border="1" width="100%" class="tableBorder">
                <tr>
                    <th scope="col" height="32" width="20%"><?php echo $zbp->lang['WhitePage']['color_config']; ?></th>
                    <th scope="col" colspan="4">
                    <div  style="float:left;margin: 0.25em"></div>
                    <div id="loadconfig">
                    <?php
                    foreach ($percolors as $key => $value) {
                        echo "<span class='color-box' data-order='" . $value[0] . "' style='background-color:#" . $value[1] . "'></span><em style='float:left;padding:0.5em 1em 0 0;'>" . $key . "</em>";
                    }
                    ?>
                    </div>
                    </th>
                </tr>

                <tr>
                    <td></td>
                    <td colspan="2"><?php echo $zbp->lang['WhitePage']['background_color']; ?>
                        <div class="color-box-picker">
                            <input type="text" id="bgpicker" class="colorpicker" name="bgcolor" value="<?php echo $zbp->Config('WhitePage')->custom_bgcolor; ?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_bgcolor; ?>" />
                        </div>
                    </td>
                    <td colspan="2"><?php echo $zbp->lang['WhitePage']['page_color']; ?>
                        <div class="color-box-picker">
                            <input type="text" id="bgpicker2" class="colorpicker" name="pagecolor" value="<?php echo $zbp->Config('WhitePage')->custom_pagecolor; ?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_pagecolor; ?>" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
<td><?php echo $zbp->lang['WhitePage']['font_color']; ?>:
                        <div class="color-box-picker">
                            <input type="text" id="bgpicker3" class="colorpicker" name="fontcolor" value="<?php echo $zbp->Config('WhitePage')->custom_fontcolor; ?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_fontcolor; ?>" />
                        </div>
</td>
<td><?php echo $zbp->lang['WhitePage']['title_color']; ?>:
                        <div class="color-box-picker">
                            <input type="text" id="bgpicker4" class="colorpicker" name="blogtitlecolor" value="<?php echo $zbp->Config('WhitePage')->custom_blogtitlecolor; ?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_blogtitlecolor; ?>" />
                        </div>
</td>
<td><?php echo $zbp->lang['WhitePage']['a_color']; ?>:
                        <div class="color-box-picker">
                            <input type="text" id="bgpicker5" class="colorpicker" name="acolor" value="<?php echo $zbp->Config('WhitePage')->custom_acolor; ?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_acolor; ?>" />
                        </div>
</td>
<td><?php echo $zbp->lang['WhitePage']['active_a_color']; ?>:
                        <div class="color-box-picker">
                            <input type="text" id="bgpicker6" class="colorpicker" name="ahovercolor" value="<?php echo $zbp->Config('WhitePage')->custom_ahovercolor; ?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_ahovercolor; ?>" />
                        </div>
</td>

                </tr>
            </table>
            <input name="ok" type="submit" class="button" value="<?php echo $zbp->lang['WhitePage']['save']; ?>"/>
        </form>
    </div>
</div>
<!--#include file="..\..\..\..\zb_system\admin\admin_footer.asp"-->
<script type="text/javascript">
ActiveTopMenu("topmenu_WhitePage");
if(typeof AddHeaderFontIcon === 'function')
    AddHeaderFontIcon("icon-nut-fill");
else
    AddHeaderIcon("<?php echo $zbp->host; ?>zb_system/image/common/themes_32.png"); 
$('#bgpicker').colpick({
    layout:'hex',
    submit:0,
    onChange:function(hsb,hex,rgb,el,bySetColor) {
        $(el).css('border-color','#'+hex);
        if(!bySetColor) $(el).val(hex);
    }
}).keyup(function(){
    $(this).colpickSetColor(this.value);
});
$('#bgpicker2').colpick({
    layout:'hex',
    submit:0,
    onChange:function(hsb,hex,rgb,el,bySetColor) {
        $(el).css('border-color','#'+hex);
        if(!bySetColor) $(el).val(hex);
    }
}).keyup(function(){
    $(this).colpickSetColor(this.value);
});
$('#bgpicker3').colpick({
    layout:'hex',
    submit:0,
    onChange:function(hsb,hex,rgb,el,bySetColor) {
        $(el).css('border-color','#'+hex);
        if(!bySetColor) $(el).val(hex);
    }
}).keyup(function(){
    $(this).colpickSetColor(this.value);
});
$('#bgpicker4').colpick({
    layout:'hex',
    submit:0,
    onChange:function(hsb,hex,rgb,el,bySetColor) {
        $(el).css('border-color','#'+hex);
        if(!bySetColor) $(el).val(hex);
    }
}).keyup(function(){
    $(this).colpickSetColor(this.value);
});
$('#bgpicker5').colpick({
    layout:'hex',
    submit:0,
    onChange:function(hsb,hex,rgb,el,bySetColor) {
        $(el).css('border-color','#'+hex);
        if(!bySetColor) $(el).val(hex);
    }
}).keyup(function(){
    $(this).colpickSetColor(this.value);
});
$('#bgpicker6').colpick({
    layout:'hex',
    submit:0,
    onChange:function(hsb,hex,rgb,el,bySetColor) {
        $(el).css('border-color','#'+hex);
        if(!bySetColor) $(el).val(hex);
    }
}).keyup(function(){
    $(this).colpickSetColor(this.value);
});

$('.color-box').click(function() {
        var x = $(this).data('order');
        var z = Array();
<?php
foreach ($percolors as $key => $value) {
    echo 'z[' . $value[0] . ']' . '=new Array("' . $value[2] . '","' . $value[3] . '","' . $value[4] . '","' . $value[5] . '","' . $value[6] . '","' . $value[7] . '");' . PHP_EOL;
}
?>
a=z[x][0];
b=z[x][1];
c=z[x][2];
d=z[x][3];
e=z[x][4];
f=z[x][5];

$('#bgpicker' ).colpickSetColor(a);$('#bgpicker' ).val(a);$('#bgpicker' ).css('border-color', '#'+a); 
$('#bgpicker2').colpickSetColor(b);$('#bgpicker2').val(b);$('#bgpicker2').css('border-color', '#'+b); 
$('#bgpicker3').colpickSetColor(c);$('#bgpicker3').val(c);$('#bgpicker3').css('border-color', '#'+c); 
$('#bgpicker4').colpickSetColor(d);$('#bgpicker4').val(d);$('#bgpicker4').css('border-color', '#'+d); 
$('#bgpicker5').colpickSetColor(e);$('#bgpicker5').val(e);$('#bgpicker5').css('border-color', '#'+e); 
$('#bgpicker6').colpickSetColor(f);$('#bgpicker6').val(f);$('#bgpicker6').css('border-color', '#'+f); 
});
</script> 

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
