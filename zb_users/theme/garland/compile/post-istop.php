<!-- article node -->
<div id="node-<?php  echo $article->ID;  ?>" class="node">


  <h2>[置顶] <a href="<?php  echo $article->Url;  ?>" title="<?php  echo $article->Title;  ?>"><?php  echo $article->Title;  ?></a></h2>

      <span class="submitted"><?php  echo $article->Time('l Y-m-d H:i');  ?> - <?php  echo $article->Author->Name;  ?></span>
  

</div>