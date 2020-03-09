<div class="pane{if !in_array($module.FileName,$sideMods)} hidem{/if}" id="{$module.HtmlID}">
  {if (!$module.IsHideTitle)&&($module.Name)}<h4 class="zit">{$module.Name}</h4>{/if}
  <{$module.Type}>{$module.Content}</{$module.Type}>
</div>