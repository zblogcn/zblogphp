<?php exit(); ?>
{php}<?php
      $csrfToken = $zbp->GetCSRFToken();
      ?>{/php}

<form id="frmTheme" method="post" action="{BuildSafeCmdURL('act=ThemeSet')}">
  <input type="hidden" name="theme" id="theme" value="">
  <input type="hidden" name="style" id="style" value="">
  <div class="postlist">

    <div class="tr thead">
      <div class="td-10  theme-img">预览图</div>
      <!-- <div class="td-10 td-title">名称</div> -->
      <div class="td-full td-author">介绍</div>
      <!-- <div class="td-10 ">作者 </div> -->
      <div class="td-20 td-action">操作</div>
    </div>
    {foreach $allthemes as $curTheme}
    {php}<?php
          $cls = $curTheme->IsUsed() ? 'theme-now' : 'theme-other';
          $themeIdEscaped = htmlspecialchars($curTheme->id);
          $themeNameEscaped = htmlspecialchars($curTheme->name);
          $themeUrlEscaped = htmlspecialchars($curTheme->url);
          $themeAuthorUrlEscaped = htmlspecialchars($curTheme->author_url);
          $themeAuthorEmailEscaped = htmlspecialchars($curTheme->author_email);
          $themeAuthorNameEscaped = htmlspecialchars($curTheme->author_name);
          ?>{/php}

    <div class="tr ">
      <div class="td-10 theme-img">
        <img src="{$curTheme.GetScreenshot()}" title="{$themeNameEscaped}" alt="{$themeNameEscaped}">
      </div>
      <div class="td-full td-title theme-info ">
        <h4>{$themeNameEscaped} <small> #{$themeIdEscaped}</small><br></h4> 
        <a target="_blank" href="{$themeAuthorUrlEscaped}">{$themeAuthorNameEscaped}</a>  
        <p class="theme-desc">{$curTheme->description}</p>
      </div>


      <div class="td-20 td-action theme {$cls} ">
        <div class="theme-style">


          {php}
          $cssfiles=$curTheme->GetCssFiles();
          {/php}
          {if count($cssfiles) > 1}
          <select size="1">
            {foreach $cssfiles as $curKey => $curValue}
            {php}$keyEscaped = htmlspecialchars($curKey);{/php}
            <option value="{$keyEscaped}" {if $curTheme->IsUsed() && $curKey === $zbp->style}selected="selected"{/if}>{$curKey}</option>
            {/foreach}
          </select>
          {/if}
          {if !$curTheme->IsUsed()}
          <button type="submit" onclick="$('#style').val($(this).prevAll('select').val()??'');$('#theme').val('{$curTheme.id}');$('#frmTheme').submit();" class="theme-activate button">
            {$zbp.lang['msg']['enable']}
          </button>
          {else}
          {if count($cssfiles) > 1}
          <button type="submit" onclick="$('#style').val($(this).prevAll('select').val()??'');$('#theme').val('{$curTheme.id}');$('#frmTheme').submit();" class="theme-activate button">
            切换样式
          </button>
          {else}
          <button disabled class="disabled button">
            已启用
          </button>
          {/if}
          {/if}
        </div>
        <!-- {if $curTheme.IsUsed() && $curTheme.path }
        <a href="{$curTheme.GetManageUrl()}" title="{$zbp.lang['msg']['manage']}"><i class="ico ico ico-setting"></i></a>
        {/if} -->

      </div>
    </div>
    {/foreach}

</form>