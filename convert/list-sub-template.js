exports.func = function(){
	return [
		{
			"name": "module",
			"list": {
				"CACHE_INCLUDE_NAVBAR": "module:navbar"
			}
		},
		{
			"name": "template",
			"close": false,
			"list": {
				"template:article-multi": "{if $article.IsTop}\n{template:post-istop}\n{else}\n{template:post-multi}\n{/if}",
				"template:article-single": "{if $article.Type == ZC_POST_TYPE_ARTICLE}\n{template:post-single}\n{else}\n{template:post-page}\n{/if}"
				"TEMPLATE_FOOTER": "{template:footer}",
				"template:(.+)": "{template:$1}",

			}
		}
	];
}