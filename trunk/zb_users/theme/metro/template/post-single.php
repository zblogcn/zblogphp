<div class="post cate{$article.Category.ID}  auth{$article.Author.ID}">
      <div class="post_time">
        <h5>{$article.Time('d')}</h5><h6>{$article.Time('Y')}<br />{$article.Time('m')}</h6>
      </div>
      <div class="post_r">
        <div class="post_body">
          <h2>{$article.Title}</h2>
          <div class="post_content">
            {$article.Content}
          </div>
			<div class="post_tags"></div>
          <div class="post_info">
            作者:{$article.Author.Name} | 分类:{$article.Category.Name} | 浏览:{$article.ViewNums} | 评论:{$article.CommNums}
          </div>
        </div>       
        <div class="post_nav">
{if $article.Prev}
<a class="l" href="{$article.Prev.Url}" title="{$article.Prev.Title}">« 上一篇</a>
{/if}
{if $article.Next}
<a class="r" href="{$article.Next.Url}" title="{$article.Next.Title}"> 下一篇 »</a>
{/if}
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