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
          <#template:article_navbar_l#><#template:article_navbar_r#>
        </div>
		{if !$article.IsLock}
		{template:comments}
		{/if}
     </div>
     <div class="clear"></div>
</div>