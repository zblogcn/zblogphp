{template:header}
<body class="multi">
<header>
<h2><a href="{$host}">{$name}</a> <sup>{$subname}</sup></h2>
<nav>
<ul>{module:navbar}</ul>
</nav>
</header>
<section>
  <section id="main">
    <section>
{foreach $articles as $article}

{if $article.IsTop}
{template:post-istop}
{else}
{template:post-multi}
{/if}

{/foreach}
      <nav>{template:pagebar}</nav>
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