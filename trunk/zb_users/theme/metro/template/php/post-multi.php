<div class="post cate<?php  echo $article->Category->ID;  ?>  auth<?php  echo $article->Author->ID;  ?>">
      <div class="post_time">
        <h5><?php  echo $article->Time('d');  ?></h5><h6><?php  echo $article->Time('Y');  ?><br /><?php  echo $article->Time('m');  ?></h6>
      </div>
      <div class="post_r">
        <div class="post_body">
          <h2><a href="<?php  echo $article->Url;  ?>" title="<?php  echo $article->Title;  ?>"><?php  echo $article->Title;  ?></a></h2>
          <div class="post_content">
				<?php  echo $article->Intro;  ?>
          </div>
          <div class="post_info">
				作者:<?php  echo $article->Author->Name;  ?> | 分类:<?php  echo $article->Category->Name;  ?> | 浏览:<?php  echo $article->ViewNums;  ?> | 评论:<?php  echo $article->CommNums;  ?>
          </div>
        </div>
      </div>
     <div class="clear"></div>
</div>