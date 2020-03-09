<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('Sitemap')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'SitemapXML生成器';

if (count($_POST) > 0) {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><urlset />');
    $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $url = $xml->addChild('url');
    $url->addChild('loc', $zbp->host);

    if (GetVars('category')) {
        foreach ($zbp->categorys as $c) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $c->Url);
        }
    }

    if (GetVars('article')) {
        $array = $zbp->GetArticleList(
            null,
            array(array('=', 'log_Status', 0)),
            null,
            null,
            null,
            false
        );

        foreach ($array as $key => $value) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $value->Url);
        }
    }

    if (GetVars('page')) {
        $array = $zbp->GetPageList(
            null,
            array(array('=', 'log_Status', 0)),
            null,
            null,
            null
        );

        foreach ($array as $key => $value) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $value->Url);
        }
    }

    if (GetVars('tag')) {
        $array = $zbp->GetTagList();

        foreach ($array as $key => $value) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $value->Url);
        }
    }

    file_put_contents($zbp->path . 'sitemap.xml', $xml->asXML());

    $zbp->SetHint('good');
    Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">

  </div>
  <div id="divMain2">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
<tr>
	<th class="td20"></th>
	<th>SitemapXML内容组成</th>
</tr>
<tr>
	<td>选项</td>
	<td>
<p>首页<input type="text" class="checkbox" value="1" /></p>
<p>分类<input type="text" name="category" class="checkbox" value="1" /></p>
<p>文章<input type="text" name="article" class="checkbox" value="1" /></p>
<p>页面<input type="text" name="page" class="checkbox" value="1" /></p>
<p>标签<input type="text" name="tag" class="checkbox" value="0" /></p>
	</td>
</tr>

</table>
	  <hr/>
	  <p><input type="submit" class="button" value="生成sitemap.xml文件" /></p>
	  <hr/>
<table border="1" class="tableFull tableBorder">
<tr>
	<th class="td20">sitemap.xml地址：</td>
	<th><p><?php echo $zbp->host; ?>sitemap.xml</p></td>
</tr>
<tr>
	<td class="td20">向Google提交：</td>
	<td><p><a href="http://www.google.com/webmasters">http://www.google.com/webmasters</a></p></td>
</tr>
<tr>
	<td class="td20">向百度站长平台提交：</td>
	<td><p><a href="http://zhanzhang.baidu.com/sitemap">http://zhanzhang.baidu.com/sitemap</a></p></td>
</tr>
</table>


	</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Sitemap/logo.png'; ?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>