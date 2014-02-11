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
{template:comments}		
{/if}
     </div>
     <div class="clear"></div>
</div>