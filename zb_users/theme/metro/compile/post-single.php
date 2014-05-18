<div class="post cate<?php  echo $article->Category->ID;  ?>  auth<?php  echo $article->Author->ID;  ?>">
      <div class="post_time">
        <h5><?php  echo $article->Time('d');  ?></h5><h6><?php  echo $article->Time('Y');  ?><br /><?php  echo $article->Time('m');  ?></h6>
      </div>
      <div class="post_r">
        <div class="post_body">
          <h2><?php  echo $article->Title;  ?></h2>
          <div class="post_content">
            <?php  echo $article->Content;  ?>
          </div>
			<div class="post_tags"></div>
          <div class="post_info">
            作者:<?php  echo $article->Author->StaticName;  ?> | 分类:<?php  echo $article->Category->Name;  ?> | 浏览:<?php  echo $article->ViewNums;  ?> | 评论:<?php  echo $article->CommNums;  ?>
          </div>
        </div>       
        <div class="post_nav">
<?php if ($article->Prev) { ?>
<a class="l" href="<?php  echo $article->Prev->Url;  ?>" title="<?php  echo $article->Prev->Title;  ?>">« 上一篇</a>
<?php } ?>
<?php if ($article->Next) { ?>
<a class="r" href="<?php  echo $article->Next->Url;  ?>" title="<?php  echo $article->Next->Title;  ?>"> 下一篇 »</a>
<?php } ?>
        </div>
<?php if (!$article->IsLock) { ?>
<?php  include $this->GetTemplate('comments');  ?>		
<?php } ?>
     </div>
     <div class="clear"></div>
</div>