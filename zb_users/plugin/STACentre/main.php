<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('STACentre')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = '静态管理中心';

if (count($_POST) > 0) {
    if (function_exists('CheckIsRefererValid')) {
        CheckIsRefererValid();
    }
    $zbp->option['ZC_STATIC_MODE'] = trim(GetVars('ZC_STATIC_MODE', 'POST'));
    $zbp->option['ZC_ARTICLE_REGEX'] = trim(GetVars('ZC_ARTICLE_REGEX', 'POST'));
    $zbp->option['ZC_PAGE_REGEX'] = trim(GetVars('ZC_PAGE_REGEX', 'POST'));
    $zbp->option['ZC_INDEX_REGEX'] = trim(GetVars('ZC_INDEX_REGEX', 'POST'));
    $zbp->option['ZC_CATEGORY_REGEX'] = trim(GetVars('ZC_CATEGORY_REGEX', 'POST'));
    $zbp->option['ZC_TAGS_REGEX'] = trim(GetVars('ZC_TAGS_REGEX', 'POST'));
    $zbp->option['ZC_DATE_REGEX'] = trim(GetVars('ZC_DATE_REGEX', 'POST'));
    $zbp->option['ZC_AUTHOR_REGEX'] = trim(GetVars('ZC_AUTHOR_REGEX', 'POST'));
    $zbp->SaveOption();

    $zbp->AddBuildModule('archives');
    $zbp->AddBuildModule('tags');
    $zbp->AddBuildModule('authors');
    $zbp->AddBuildModule('previous');
    $zbp->AddBuildModule('catalog');
    $zbp->AddBuildModule('navbar');

    $zbp->BuildModule();
    $zbp->SetHint('good');
    if ($zbp->option['ZC_STATIC_MODE'] == 'REWRITE' && strpos($zbp->option['ZC_ARTICLE_REGEX'], '{%host%}index.php') === false) {
        Redirect('./list.php');
    }

    Redirect('./main.php');
}

$ua = array(

    'ZC_ARTICLE_REGEX' => array(
        '{%host%}?id={%id%}',
        '{%host%}index.php/post/{%id%}.html',
        '{%host%}post/{%id%}.html',
        '{%host%}post/{%alias%}.html',
        '{%host%}{%year%}/{%month%}/{%id%}/',
        '{%host%}{%category%}/{%alias%}/',
    ),

    'ZC_PAGE_REGEX' => array(
        '{%host%}?id={%id%}',
        '{%host%}index.php/{%id%}.html',
        '{%host%}{%id%}.html',
        '{%host%}{%alias%}.html',
        '{%host%}{%alias%}/',
        //'{%host%}{%alias%}',
    ),

    'ZC_INDEX_REGEX' => array(
        '{%host%}?page={%page%}',
        '{%host%}index.php/page_{%page%}.html',
        '{%host%}page_{%page%}.html',
        '{%host%}page_{%page%}/',
        //'{%host%}page_{%page%}',
        '{%host%}page/{%page%}/',
    ),

    'ZC_CATEGORY_REGEX' => array(
        '{%host%}?cate={%id%}&page={%page%}',
        '{%host%}index.php/category-{%id%}_{%page%}.html',
        '{%host%}category-{%id%}_{%page%}.html',
        '{%host%}category-{%alias%}_{%page%}.html',
        '{%host%}category/{%alias%}/{%page%}/',
        '{%host%}category/{%id%}/{%page%}/',
    ),

    'ZC_TAGS_REGEX' => array(
        '{%host%}?tags={%id%}&page={%page%}',
        '{%host%}index.php/tags-{%id%}_{%page%}.html',
        '{%host%}tags-{%id%}_{%page%}.html',
        '{%host%}tags-{%alias%}_{%page%}.html',
        '{%host%}tags/{%alias%}/{%page%}/',
    ),

    'ZC_DATE_REGEX' => array(
        '{%host%}?date={%date%}&page={%page%}',
        '{%host%}index.php/date-{%date%}_{%page%}.html',
        '{%host%}date-{%date%}_{%page%}.html',
        '{%host%}post/{%date%}_{%page%}.html',
        '{%host%}date/{%date%}/{%page%}/',
    ),

    'ZC_AUTHOR_REGEX' => array(
        '{%host%}?auth={%id%}&page={%page%}',
        '{%host%}index.php/author-{%id%}_{%page%}.html',
        '{%host%}author-{%id%}_{%page%}.html',
        '{%host%}author/{%id%}/{%page%}/',
        '{%host%}author/{%alias%}/{%page%}/',
    ),

);

function OutputOptionItemsOfUrl($type)
{
    global $ua, $zbp;
    $s = '';
    $d = 'style="display:none;"';
    if ($zbp->option['ZC_STATIC_MODE'] == 'ACTIVE' || strpos($zbp->option['ZC_ARTICLE_REGEX'], '{%host%}index.php') !== false) {
        $r = 'disabled="disabled"';
    } else {
        $r = '';
    }

    $i = 0;
    foreach ($ua[$type] as $key => $value) {
        $s .= '<p ' . $d . '><label><input ' . $r . ' type="radio" name="radio' . $type . '" value="' . $value . '" onclick="$(\'#' . $type . '\').val($(this).val())" />&nbsp;' . $value . '</label></p>';
        $i++;
        if ($i > 1) {
            $d = '';
        }
    }

    echo $s;
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle; ?></div>
<div class="SubMenu"> <a href="main.php"><span class="m-left m-now">配置页面</span></a><a href="list.php"><span class="m-left">ReWrite规则</span></a><a href="help.php"><span class="m-right">帮助</span></a> </div>
  <div id="divMain2">
    <form id="edit" name="edit" method="post" action="#">
        <?php if (function_exists('CheckIsRefererValid')) {
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
}?>
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder">
<tr>
    <th class="td20"><p align='left'><b>·静态化选项</b><br><span class='note'>&nbsp;&nbsp;使用伪静态前必须确认主机是否支持</span></p></th>
    <th>
<p><label><input type="radio" <?php echo $zbp->option['ZC_STATIC_MODE'] == 'ACTIVE' ? 'checked="checked"' : ''?> value="ACTIVE" name="ZC_STATIC_MODE" onchange="changeOptions(0);" /> &nbsp;&nbsp;动态</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" <?php echo !($zbp->option['ZC_STATIC_MODE'] == 'REWRITE' && strpos($zbp->option['ZC_ARTICLE_REGEX'], '{%host%}index.php') === false) ? '' : 'checked="checked"'?>  value="REWRITE"  name="ZC_STATIC_MODE" onchange="changeOptions(2);" />&nbsp;&nbsp;伪静态</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" <?php echo !($zbp->option['ZC_STATIC_MODE'] == 'REWRITE' && strpos($zbp->option['ZC_ARTICLE_REGEX'], '{%host%}index.php') !== false) ? '' : 'checked="checked"'?>  value="REWRITE"  name="ZC_STATIC_MODE" onchange="changeOptions(1);" />&nbsp;&nbsp;index.php式仿伪静态</label>

</p>
    </th>
    </tr>
<tr>
    <td><p align='left'><b>·文章的URL配置</b></p></td>
    <td><input id='ZC_ARTICLE_REGEX' name='ZC_ARTICLE_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_ARTICLE_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_ARTICLE_REGEX')?></td>
</tr>
<tr>
    <td><p align='left'><b>·页面的URL配置</b></p></td>
    <td><input id='ZC_PAGE_REGEX' name='ZC_PAGE_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_PAGE_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_PAGE_REGEX')?></td>
</tr>
<tr>
    <td><p align='left'><b>·首页的URL配置</b></p></td>
    <td><input id='ZC_INDEX_REGEX' name='ZC_INDEX_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_INDEX_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_INDEX_REGEX')?></td>
</tr>
<tr>
    <td><p align='left'><b>·分类页的URL配置</b></p></td>
    <td><input id='ZC_CATEGORY_REGEX' name='ZC_CATEGORY_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_CATEGORY_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_CATEGORY_REGEX')?></td>
</tr>
<tr>
    <td><p align='left'><b>·标签页的URL配置</b></p></td>
    <td><input id='ZC_TAGS_REGEX' name='ZC_TAGS_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_TAGS_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_TAGS_REGEX')?></td>
</tr>
<tr>
    <td><p align='left'><b>·日期页的URL配置</b></p></td>
    <td><input id='ZC_DATE_REGEX' name='ZC_DATE_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_DATE_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_DATE_REGEX')?></td>
</tr>
<tr>
    <td><p align='left'><b>·作者页的URL配置</b></p></td>
    <td><input id='ZC_AUTHOR_REGEX' name='ZC_AUTHOR_REGEX' style='width:500px;' type='text' value='<?php echo $zbp->option['ZC_AUTHOR_REGEX']?>'></td>
</tr>
<tr>
    <td></td>
    <td><?php OutputOptionItemsOfUrl('ZC_AUTHOR_REGEX')?></td>
</tr>
<?php

?>
</table>
      <hr/>
      <p>
        1· 规则可以自定义，请注意如果规则解析过于广泛会覆盖之后的规则，浏览页面时就会出现故障.
        <br/>2· index.php式仿伪静态在Apache,IIS下可以不用生成伪静态规则.
      </p>
      <p>
        <input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
      </p>
      <p>
        &nbsp;
      </p>
    </form>
    <script type="text/javascript">
function changeOptions(i){
    $('input[name^=ZC_]').each(function(){
        var s='radio' + $(this).prop('name');
        $(this).val( $("input[type='radio'][name='"+s+"']").eq(i).val() );
    });
    if(i=='0'){
        $("input[name^='radio']").prop('disabled',true);
        $("input[name='ZC_STATIC_MODE']").val('ACTIVE');
    }else if(i=='1'){
        $("input[name^='radio']").prop('disabled',true);
        $("input[name='ZC_STATIC_MODE']").val('REWRITE');
    }else{
        $("input[name^='radio']").prop('disabled',false);
        $("input[name='ZC_STATIC_MODE']").val('REWRITE');
    }

}
    </script>
    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/STACentre/logo.png'; ?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
