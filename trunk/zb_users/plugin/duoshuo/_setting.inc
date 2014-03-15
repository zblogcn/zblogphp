<?php
function getChecked($name,$value)
{
	global $duoshuo;
	if($duoshuo->cfg->$name == $value) return ' checked="checked" ';
	return '';
}
?>
<form action="event.php?act=save" method="post">
  <table width="100%">
    <thead>
      <tr>
        <th width="30%">配置项 </th>
        <th>选择 </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><p><span class="bold"> · 多说API服务器</span><br/>
            <span class="note">选择一个速度更快的服务器</span></p></td>
        <td><ul>
            <li>
              <label>
                <input type="radio" name="duoshuo_api_hostname" value="api.duoshuo.com"<?php echo getChecked("api_hostname","api.duoshuo.com")?>>
                api.duoshuo.com(国内主机使用)</label>
            </li>
            <li>
              <label>
                <input type="radio" name="duoshuo_api_hostname" value="api.duoshuo.org"<?php echo getChecked("api_hostname","api.duoshuo.org")?>>
                api.duoshuo.org(国外主机使用)</label>
            </li>
            <li>
              <label>
                <input type="radio" name="duoshuo_api_hostname" value="118.144.80.201"<?php echo getChecked("api_hostname","118.144.80.201")?>>
                118.144.80.201(DNS故障主机使用)</label>
            </li>
          </ul></td>
      </tr>
      <tr>
        <td><p><span class="bold"> · 本地数据备份</span><br/>
            <span class="note">评论同时写入本地数据库</span></p></td>
        <td><ul>
            <li>
              <label>
                <input type="radio" name="duoshuo_cron_sync_enabled" value="async"<?php echo getChecked("cron_sync_enabled","async")?>>
                定时写入</label>
            </li>
            <!--<li>
                          <label>
                            <input type="radio" name="duoshuo_cron_sync_enabled" value="sync"<?php echo getChecked("cron_sync_enabled","sync")?>>
                            实时写入</label>
                        </li>-->
            <li>
              <label>
                <input type="radio" name="duoshuo_cron_sync_enabled" value="off"<?php echo getChecked("cron_sync_enabled","off")?>>
                不写入</label>
            </li>
          </ul>
      </tr>
      <tr>
        <td><p><span class="bold"> · 评论数修正</span><br/>
            <span class="note">AJAX加载文章的评论数</span></p></td>
        <td><input type="text" class="checkbox" name="duoshuo_cc_fix" value="<?php echo $duoshuo->cfg->cc_fix?>" checked="checked"></td>
      </tr>
      <tr>
        <td><p><span class="bold"> · 评论框前缀</span><br/>
            <span class="note">仅在主题和评论框的div嵌套不正确的情况下使用 </span></p></td>
        <td><input type="text" name="duoshuo_comments_wrapper_intro" value="<?php echo $duoshuo->cfg->comments_wrapper_intro?>" style="width:50%"/></td>
      </tr>
      <tr>
        <td><p><span class="bold"> · 评论框后缀</span><br/>
            <span class="note">仅在主题和评论框的div嵌套不正确的情况下使用 </span></p></td>
        <td><input type="text" name="duoshuo_comments_wrapper_outro" value="<?php echo $duoshuo->cfg->comments_wrapper_outro?>" style="width:50%"/></td>
      </tr>
      <tr>
        <td><p><span class="bold"> · SEO优化</span><br/>
            <span class="note">搜索引擎爬虫访问网页时，显示静态HTML评论</span></p></td>
        <td><input type="text" class="checkbox" name="duoshuo_seo_enabled" value="<?php echo $duoshuo->cfg->seo_enabled?>"/></td>
      </tr>
      <!--<tr>
                    <td><p><span class="bold"> · 启用多说登录</span><br/><span class="note">如果想停用，请打开侧栏管理，编辑控制面板，删除&ltdiv class="ds-login"&gt;&lt;/div&gt;即可<br/></p></td>
                    <td><p> </p>
                      <p>
                        <input name="" type="button" class="button" onClick="location.href='noresponse.asp?act=specfg&t=login'" value="立即往侧栏写入多说登录" />
                      </p></td>
                  </tr>--> 
      <!--未开发完成-->
      <tr>
        <td><p><span class="bold"> · 其它</span></p></td>
        <td><p> </p>
          <p>
            <input name="" type="button" class="button" onClick="if(confirm('你确定要继续吗？')){location.href='event.php?act=fac'}" value="清空插件配置" />
          </p></td>
      </tr>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  <p>
    <input type="submit" class="button" value="提交" />
  </p>
</form>
