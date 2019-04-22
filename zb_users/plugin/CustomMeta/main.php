<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('CustomMeta')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'CustomMeta自定义作用域';

if (count($_GET) == 0) {
    Redirect('./main.php?type=post');
}

if (count($_POST) > 0) {
    $type = $_GET['type'];

    $array = $_POST['meta'];
    $array2 = array();
    foreach ($array as $key => $value) {
        if (trim($value) != '') {
            if (CheckRegExp(trim($value), '/^[a-zA-Z][a-zA-Z0-9_]{0,30}$/')) {
                $array2[] = trim($value);

                $name_meta_intro = $type . '_' . $value . '_intro';
                $name_meta_type = $type . '_' . $value . '_type';
                $name_meta_option = $type . '_' . $value . '_option';

                if (isset($_POST['meta_intro'][$key])) {
                    $single_meta_intro = $_POST['meta_intro'][$key];
                    $zbp->Config('CustomMeta')->$name_meta_intro = $single_meta_intro;
                }
                if (isset($_POST['meta_type'][$key])) {
                    $single_meta_type = $_POST['meta_type'][$key];
                    $zbp->Config('CustomMeta')->$name_meta_type = $single_meta_type;
                }
                if (isset($_POST['meta_option'][$key])) {
                    $single_meta_option = $_POST['meta_option'][$key];
                    $zbp->Config('CustomMeta')->$name_meta_option = $single_meta_option;
                }
            }
        }
    }
    $array2 = array_unique($array2);

    $zbp->Config('CustomMeta')->$type = $array2;
    $zbp->SaveConfig('CustomMeta');

    $zbp->SetHint('good');
    Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
 <a href="?type=post"><span class="m-left <?php if ($_GET['type'] == 'post') {
    echo 'm-now';
}?>">文章自定义域</span></a>
 <a href="?type=category"><span class="m-left <?php if ($_GET['type'] == 'category') {
    echo 'm-now';
}?>">分类自定义域</span></a>
 <a href="?type=tag"><span class="m-left <?php if ($_GET['type'] == 'tag') {
    echo 'm-now';
}?>">标签自定义域</span></a>
 <a href="?type=member"><span class="m-left <?php if ($_GET['type'] == 'member') {
    echo 'm-now';
}?>">用户自定义域</span></a>
  </div>
  <div id="divMain2">
    <ul style="margin: 10px 15px;">
        <li > 自定义作用域名称只能用字母，数字和下划线，且必须是字母打头。</li>
        <li > 单选框、多选框的选项值用“|”做分隔。</li>
        <li > 注意：与普通标签相同，以下标签调用也只能用在特定页面。模块中页面分离的方法请参考：<a href="http://wiki.zblogcn.com/doku.php?id=zblogphp:themes:tips#页面判断">页面判断 & 分离</a>。</li>
    </ul>
    <form id="edit" name="edit" method="post" action="#">
    <input id="reset" name="reset" type="hidden" value="" />
    <table border="1" class="tableFull tableBorder tableBorder-thcenter">
        <tr>
            <th style='width:45%'>自定义作用域配置</th>
            <th style='width:45%'>标签调用说明</th>
            <th class="td10"></th>  
        </tr>
<?php
        $type = $_GET['type'];
        $array = $zbp->Config('CustomMeta')->$type;

if (is_array($array)) {
    foreach ($array as $key => $value) {
        $single_meta_intro = $type . '_' . $value . '_intro';
        $single_meta_intro = $zbp->Config('CustomMeta')->$single_meta_intro;
        $single_meta_type = $type . '_' . $value . '_type';
        if (!$single_meta_type) {
            $single_meta_type = 'text';
        } else {
            $single_meta_type = $zbp->Config('CustomMeta')->$single_meta_type;
        }
        $single_meta_option = $type . '_' . $value . '_option';
        $single_meta_option = $zbp->Config('CustomMeta')->$single_meta_option;
        $distr = 'display:none';
        if ($single_meta_type == 'radio' || $single_meta_type == 'checkbox') {
            $distr = "";
        }
        echo '<tr>';
        echo '<td style=\'width:65%\'><p>名称：<input type="text" style="width:84%" name="meta[]" value="' . $value . '" /></p>';
        echo '<p>说明：<input type="text" style="width:84%" name="meta_intro[]" value="' . $single_meta_intro . '" /></p>';
        echo '<p>类型：<select style="width:85%" name="meta_type[]">';
        echo '<option value="text"     ' . ($single_meta_type == 'text' ? 'selected="selected"' : '') . '>单行文本框（默认）</option>';
        echo '<option value="textarea" ' . ($single_meta_type == 'textarea' ? 'selected="selected"' : '') . '>多行文本框</option>';
        echo '<option value="bool"     ' . ($single_meta_type == 'bool' ? 'selected="selected"' : '') . '>On/Off按钮</option>';
        echo '<option value="radio"    ' . ($single_meta_type == 'radio' ? 'selected="selected"' : '') . '>单选框</option>';
        echo '<option value="checkbox" ' . ($single_meta_type == 'checkbox' ? 'selected="selected"' : '') . '>多选框</option>';
        echo '</select></p>';
        echo '<p style="' . $distr . '">选项：<input style="width:84%" name="meta_option[]"  value="' . htmlspecialchars($single_meta_option) . '" /></p>';
        echo '</td>';
        echo '<td><p>';
        if ($type == 'post') {
            echo '{$article.Metas.' . $value . '}<br/>php代码:<br/>$article->Metas->' . $value . ';';
        }
        if ($type == 'category') {
            echo '文章页模板调用标签：{$article.Category.Metas.' . $value . '}<br/>列表页调用标签：{$category.Metas.' . $value . '}<br/>php调用形式:<br/>$article->Category->Metas->' . $value . ';<br/>$zbp->categorys[<abbr title="请替换成相应的分类ID">?</abbr>]->Metas->' . $value . ';';
        }
        if ($type == 'tag') {
            echo '{$tag.Metas.' . $value . '}<br/>php调用形式:<br/>$tag->Metas->' . $value . ';';
        }
        if ($type == 'member') {
            echo '文章页模板调用标签：{{$article.Author.Metas.' . $value . '}<br/> 作者页调用标签：{$author.Metas.' . $value . '}<br/>php调用形式:<br/>$article->Author->Metas->' . $value . ';<br/>$zbp->members[<abbr title="请替换成相应的用户ID">?</abbr>]->Metas->' . $value . ';<br/>';
        }

        echo '</p></td>';
        echo '<td><p><input type="submit" value="删除" onclick="$(this).parent().parent().parent().remove();" /></p></td>';
        echo '</tr>';
    }
}

?>
        <tr>
        <td colspan="3">&nbsp;添加一个Meta标签</td>
        </tr>
        <tr>
        <td><p>名称：<input type="text" style="width:84%" name="meta[]" value="" id="meta-new"/></p></td>
        <td colspan="2"><p>新增Meta字段名称</p></td>
        </tr>
      </table>
      <hr/>
      <input type="submit" class="button" value="提交" />
    </form>

  </div>
</div>

<script type="text/javascript">
    ActiveLeftMenu("aPluginMng");
    AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/CustomMeta/logo.png'; ?>");

    $("select").change(function () {
      var str = $(this).find("option:selected").val();
      if (str=="radio"||str=="checkbox"){
            $(this).parent().next().show();
      }
      else { $(this).parent().next().hide(); }
    });

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "meta-error" );
        updateTips( o ,n , 1 );
        return false;
      } else {
        o.removeClass( "meta-error" );
        updateTips( o ,n , 0 );
        return true;
      }
    }

    function updateTips( t , c , s) {
      if (s) { 
          if (t.next("p:contains('"+c+"')").length==0 ) {
                t.after("<p class='meta-warning'>"+c+"</p>");
          }
      } else  { 
          t.next("p:contains('"+c+"')").remove();
      }
    }

$(function(){
    var tips = ['名称只能由字母，数字和下划线组成，且必须是字母打头','选项值须用“|”做分隔'];
    var allFields= $( [] ).add( $( "input[name='meta[]']" ).not($( "#meta-new" )));
    var newMeta = $( "#meta-new" );

    allFields.change(function () {
          checkRegexp( $( this ), /^[a-z]([0-9a-z_])*$/i, tips[0] );
    });

    newMeta.change(function () {
        if($( this ).val().length>0){
          checkRegexp( $( this ), /^[a-z]([0-9a-z_])*$/i, tips[0] );
        }
        else {
            $( this ).removeClass( "meta-error" );
            updateTips(  $( this ) , tips[0] , 0 );
        }
    })

    $("form#edit").submit(function() {
        var valid = true;  
        allFields.each(function () {
              valid = valid && checkRegexp( $( this ), /^[a-z]([0-9a-z_])*$/i, tips[0] );
        });
        if(newMeta.val().length>0){
            valid = valid && checkRegexp( newMeta, /^[a-z]([0-9a-z_])*$/i, tips[0] );
        }
        return valid;
    });
});
</script>	
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
