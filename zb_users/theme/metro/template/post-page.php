<div class="post cate{$article.Category.ID}  auth{$article.Author.ID}">
       <div class="post_fu"></div>
      <div class="post_r">
        <div class="post_body">
          <h2>{$article.Title}</h2>
          <div class="post_content">
            {$article.Content}
          </div>
        </div>       
		{if !$article.IsLock}

{if $socialcomment}
{$socialcomment}
{else}

<div class="commentlist" style="overflow:hidden;">
{if $article.CommNums>0}
<h4>评论列表:</h4>
{/if}
{template:comments}		
</div>


<!--评论框-->
{template:commentpost}

{/if}
		
		{/if}
     </div>
     <div class="clear"></div>
</div>