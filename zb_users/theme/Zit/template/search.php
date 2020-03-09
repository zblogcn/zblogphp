{template:header}
<section id="wrap">
  <div class="inner">
    {template:kit-topic}
    <main id="main">
    {foreach $articles as $article}
      {template:post-multi}
    {/foreach}
    {$article=null}
    {template:pagebar}
    </main>
    <aside id="side">
      {template:sidebar}
      {template:kit-mside}
    </aside>
  </div>
</section>
{template:footer}