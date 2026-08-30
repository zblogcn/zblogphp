{* Template Name:单个模块 *}
<dl class="function" id="{$module.HtmlID}">
{if (!$module.IsHideTitle)&&($module.Name)}<dt class="function_t">{$module.Name}</dt>{else}<dt style="display:none;"></dt>{/if}
<dd class="function_c">

{if $module.Type=='div'}
<div>{$module.Content}</div>
{/if}

{if $module.Type=='ul'}
<ul>
{if $module.FileName!='catalog'}
{foreach $module.Links as $link}
{php}
  echo '<li><'.'a ';
  foreach ($link as $link_key => $link_value) {
    if ($link_key=='content') {

    }elseif($link_key=='target' && empty($link_value)) {

    }else{
      $link_key=str_replace('data_','data-',$link_key);
      echo $link_key.'="'.$link_value.'" ';
    }
  }
  echo '>'.$link->content.'</a></li>';
{/php}
{/foreach}
{else}
{$module.Content}
{/if}
</ul>
{/if}

</dd>
</dl>