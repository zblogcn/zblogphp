<!-- article node -->
<div id="node-{$article.ID}" class="node">


  <h2><a href="{$article.Url}" title="{$article.Title}">{$article.Title}</a></h2>

      <span class="submitted">{$article.Time('l Y-m-d H:i')} - {$article.Author.Name}</span>
  
  <div class="content">
    {$article.Intro}
  </div>

  <div class="clear-block clear">
    <div class="meta">
          <div class="terms">
		  <ul class="links inline"><li class="first last taxonomy_term_2">
{foreach $article.Tags as $tag}
<a href="{$tag.Url}">{$tag.Name}</a>&nbsp;
{/foreach}
		  </li>
          </ul>
          </div>
    </div>

    <div class="links">
<ul class="links inline">
<li class="first blog_usernames_blog"><a href="{$article.Category.Url}" class="blog_usernames_blog">{$article.Category.Name}</a></li>
<li class="last comment_add"><a id="p_comments{$article.ID}"  title="添加新的评论。"href="{$article.Url}#comment">{$article.CommNums} 条评论</a><script type="text/javascript">if({$article.CommNums}==0){document.getElementById("p_comments{$article.ID}").innerHTML="发表评论"}</script>
</li>
</ul></div>
      </div>

</div>