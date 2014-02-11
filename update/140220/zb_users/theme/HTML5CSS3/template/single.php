{template:header}
<body class="single">
<header>
<h2><a href="{$host}">{$name}</a> <sup>{$subname}</sup></h2>
<nav>
<ul>{$modules['navbar'].Content}</ul>
</nav>
</header>
<section>
  <section id="main">
    <section>
{if $article.Type==ZC_POST_TYPE_ARTICLE}
{template:post-single}
{else}
{template:post-page}
{/if}
    </section>
    <aside>
      {template:sidebar}
    </aside>
  </section>
  <aside id="extra">
    {template:sidebar2}
  </aside>
</section>
{template:footer}