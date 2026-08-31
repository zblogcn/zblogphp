<?php exit(); ?>

{php}<?php
$csrfToken = $zbp->GetCSRFToken();
?>{/php}
<form id="frmTheme" method="post" action="{BuildSafeCmdURL('act=ThemeSet')}">
  <input type="hidden" name="theme" id="theme" value="">
  <input type="hidden" name="style" id="style" value="">

  {foreach $allthemes as $curTheme}
  {php}<?php
  $cls = $curTheme->IsUsed() ? 'theme-now' : 'theme-other';
  $themeIdEscaped = htmlspecialchars($curTheme->id);
  $themeNameEscaped = htmlspecialchars($curTheme->name);
  $themeUrlEscaped = htmlspecialchars($curTheme->url);
  $themeAuthorUrlEscaped = htmlspecialchars($curTheme->author_url);
  $themeAuthorNameEscaped = htmlspecialchars($curTheme->author_name);
  ?>{/php}
  <div class="theme {$cls}" data-themeid="{$themeIdEscaped}" data-themename="{$themeNameEscaped}">
    <div class="theme-name">
      {php}<?php
      if (isset($zbp->lang[$curTheme->id]['theme_name'])) {
          $curTheme->name = $zbp->lang[$curTheme->id]['theme_name'];
      }
      ?>{/php}

      {if $curTheme.IsUsed() && $curTheme.path && !in_array('AppCentre', $zbp.GetPreActivePlugin())}
      <a href="{$curTheme.GetManageUrl()}" title="{$zbp.lang['msg']['manage']}"><i class="icon-tools"></i></a>&nbsp;&nbsp;
      {else}
      <i class="icon-layout-text-sidebar-reverse"></i>&nbsp;&nbsp;
      {/if}
      <a target="_blank" href="{$themeUrlEscaped}" title="">
        <strong style="display:none;">{$themeIdEscaped}</strong>
        <b>{$themeNameEscaped}</b>
      </a>
    </div>
    <div class="theme-img">
      <span><img src="{$curTheme.GetScreenshot()}" title="{$themeNameEscaped}" alt="{$themeNameEscaped}"></span>
    </div>
    <div class="theme-author">
      {$zbp.lang['msg']['author']}: <a target="_blank" href="{$themeAuthorUrlEscaped}">{$themeAuthorNameEscaped}</a>
    </div>
    <div class="theme-style">
      <select class="edit" size="1" title="{$zbp.lang['msg']['style']}">
        {foreach $curTheme.GetCssFiles() as $curKey => $curValue}
        {php}$keyEscaped = htmlspecialchars($curKey);{/php}
        <option value="{$keyEscaped}" {if $curTheme->IsUsed() && $curKey === $zbp->style}selected="selected"{/if}>{basename($curValue)}</option>
        {/foreach}
      </select>
      <input type="button" onclick="$('#style').val($(this).prev().val());$('#theme').val('{$curTheme.id}');$('#frmTheme').submit();" class="theme-activate button" value="{$zbp.lang['msg']['enable']}">
    </div>
  </div>
  {/foreach}

</form>
