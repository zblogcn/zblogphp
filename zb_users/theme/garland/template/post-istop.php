<!-- article node -->
<div id="node-{$article->ID}" class="node">


  <h2>[置顶] <a href="{$article->Url}" title="{$article->Title}">{$article->Title}</a></h2>

      <span class="submitted">{$article->Time('l Y-m-d H:i')} - {$article->Author->Name}</span>
  

</div>