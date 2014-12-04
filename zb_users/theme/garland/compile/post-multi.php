<!-- article node -->
<div id="node-<?php  echo $article->ID;  ?>" class="node">


  <h2><a href="<?php  echo $article->Url;  ?>" title="<?php  echo $article->Title;  ?>"><?php  echo $article->Title;  ?></a></h2>

      <span class="submitted"><?php  echo $article->Time('l Y-m-d H:i');  ?> - <?php  echo $article->Author->StaticName;  ?></span>
  
  <div class="content">
    <?php  echo $article->Intro;  ?>
  </div>

  <div class="clear-block clear">
    <div class="meta">
          <div class="terms">
		  <ul class="links inline"><li class="first last taxonomy_term_2">
<?php  foreach ( $article->Tags as $tag) { ?> 
<a href="<?php  echo $tag->Url;  ?>"><?php  echo $tag->Name;  ?></a>&nbsp;
<?php  }   ?>
		  </li>
          </ul>
          </div>
    </div>

    <div class="links">
<ul class="links inline">
<li class="first blog_usernames_blog"><a href="<?php  echo $article->Category->Url;  ?>" class="blog_usernames_blog"><?php  echo $article->Category->Name;  ?></a></li>
<li class="last comment_add"><a id="p_comments<?php  echo $article->ID;  ?>"  title="添加新的评论。"href="<?php  echo $article->Url;  ?>#comment"><?php  echo $article->CommNums;  ?> 条评论</a><script type="text/javascript">if(<?php  echo $article->CommNums;  ?>==0){document.getElementById("p_comments<?php  echo $article->ID;  ?>").innerHTML="发表评论"}</script>
</li>
</ul></div>
      </div>

</div>