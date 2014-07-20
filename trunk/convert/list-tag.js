exports.func = function(){
	return [
		{
			"name": "global",
			"list": {
				'ZC_BLOG_HOST': '$host',
				'ZC_BLOG_NAME': '$name',
				'ZC_BLOG_TITLE': '$name',
				'ZC_BLOG_SUBNAME': '$subname',
				'ZC_BLOG_SUBTITLE': '$subname',
				'ZC_BLOG_THEME': '$theme',
				'ZC_BLOG_STYLE': '$style',
				'ZC_BLOG_COPYRIGHT': '$copyright',
				'ZC_BLOG_VERSION': '$zblogphp',
				'ZC_BLOG_LANGUAGE': '$language'
			}
		},
		{
			"name": "category",
			"list": {
				'article\/category\/id': '$article.Category.ID',
				'article\/category\/name': '$article.Category.Name',
				'article\/category\/order': '$article.Category.Order',
				'article\/category\/count': '$article.Category.Count',
				'article\/category\/url': '$article.Category.Url',
				'article\/category\/staticname': '$article.Category.StaticName',
				'article\/category\/parent/id': '$categorys[article.Category.ParentID].ID',
				'article\/category\/parent/name': '$categorys[article.Category.ParentID].Name',
				'article\/category\/parent/order': '$categorys[article.Category.ParentID].Order',
				'article\/category\/parent/count': '$categorys[article.Category.ParentID].Count',
				'article\/category\/parent/url': '$categorys[article.Category.ParentID].Url',
				'article\/category\/parent/staticname': '$categorys[article.Category.ParentID].StaticName',

				'articlelist\/category\/id': '$category.ID',
				'articlelist\/category\/name': '$category.Name',
				'articlelist\/category\/order': '$category.Order',
				'articlelist\/category\/count': '$category.Count',
				'articlelist\/category\/url': '$category.Url',
				'articlelist\/category\/staticname': '$category.StaticName',
				'articlelist\/category\/parent/id': '$categorys[category.ParentID].ID',
				'articlelist\/category\/parent/name': '$categorys[category.ParentID].Name',
				'articlelist\/category\/parent/order': '$categorys[category.ParentID].Order',
				'articlelist\/category\/parent/count': '$categorys[category.ParentID].Count',
				'articlelist\/category\/parent/url': '$categorys[category.ParentID].Url',
				'articlelist\/category\/parent/staticname': '$categorys[category.ParentID].StaticName',

			}
		},
		{
			"name": "author",
			"list": {
				'articlelist\/author\/id': '$author.ID',
				'articlelist\/author\/name': '$author.Name',
				'articlelist\/author\/level': '$author.Level',
				'articlelist\/author\/email': '$author.Email',
				'articlelist\/author\/homepage': '$author.HomePage',
				'articlelist\/author\/count': '$author.Count',
				'articlelist\/author\/url': '$author.Url',
				'articlelist\/author\/staticname': '$author.StaticName',
				'articlelist\/author\/levelname': '$zbp.language["user_level_name"][author.Level]',
				'articlelist\/author\/avatar': '$author.Avatar',
				'articlelist\/author\/intro': '$author.Intro',

				'article\/author\/id': '$article.Author.ID',
				'article\/author\/name': '$article.Author.Name',
				'article\/author\/level': '$article.Author.Level',
				'article\/author\/email': '$article.Author.Email',
				'article\/author\/homepage': '$article.Author.HomePage',
				'article\/author\/count': '$article.Author.Count',
				'article\/author\/url': '$article.Author.Url',
				'article\/author\/staticname': '$article.Author.StaticName',
				'article\/author\/levelname': '$zbp.language["user_level_name"][article.Author.Level]',
				'article\/author\/avatar': '$article.Author.Avatar',
				'article\/author\/intro': '$article.Author.Intro'
			}
		},
		{
			"name": "tags",
			"list": {
				"article(list)?\/tags\/id": '$tag.ID',
				"article(list)?\/tags\/name": '$tag.Name',
				"article(list)?\/tags\/intro": '$tag.Intro',
				"article(list)?\/tags\/count": '$tag.Count',
				"article(list)?\/tags\/url": '$tag.Url',
				"article(list)?\/tags\/encodename": 'urlencode(tag.Name)'
			}
		},
		{
			"name": "date",
			"list": {
				"article(list)?\/(date|posttime)\/year": "$article.Time('Y')",
				"article(list)?\/(date|posttime)\/month": "$article.Time('m')",
				"article(list)?\/(date|posttime)\/day": "$article.Time('d')",
				"article(list)?\/(date|posttime)\/hour": "$article.Time('hh')",
				"article(list)?\/(date|posttime)\/minute": "$article.Time('mi')",
				"article(list)?\/(date|posttime)\/second": "$article.Time('ss')",
				"article(list)?\/(date|posttime)\/weekday": "$article.Time('w')",
				"article(list)?\/(date|posttime)\/monthname": "$zbp.language['month'][article.Time('m')]",
				"article(list)?\/(date|posttime)\/weekdayname": "$zbp.language['week'][article.Time('w')]",
				"article(list)?\/(date|posttime)\/shortdate": "$article.Time('Y年m月d日')",
				"article(list)?\/(date|posttime)\/longdate": "$article.Time('Y年m月d日')",
				"article(list)?\/(date|posttime)\/longtime": "$article.Time('hh:mm:ss')",
				"article(list)?\/(date|posttime)\/shorttime": "$article.Time('hh:mm:ss')",
				"article(list)?\/(date|posttime)": "$article.Time('Y年m月d日')",


				"article\/comment\/posttime\/year": "$comment.Time('Y')",
				"article\/comment\/posttime\/month": "$comment.Time('m')",
				"article\/comment\/posttime\/day": "$comment.Time('d')",
				"article\/comment\/posttime\/hour": "$comment.Time('hh')",
				"article\/comment\/posttime\/minute": "$comment.Time('mi')",
				"article\/comment\/posttime\/second": "$comment.Time('ss')",
				"article\/comment\/posttime\/weekday": "$comment.Time('w')",
				"article\/comment\/posttime\/monthname": "$zbp.language['month'][comment.Time('m')]",
				"article\/comment\/posttime\/weekdayname": "$zbp.language['week'][comment.Time('w')]",
				"article\/comment\/posttime\/shortdate": "$comment.Time('Y年m月d日')",
				"article\/comment\/posttime\/longdate": "$comment.Time('Y年m月d日')",
				"article\/comment\/posttime\/longtime": "$comment.Time('hh:mm:ss')",
				"article\/comment\/posttime\/shorttime": "$comment.Time('hh:mm:ss')",
				"article\/comment\/posttime": "$comment.Time('Y年m月d日')",
			}
		},
		{
			"name": "comment",
			"list": {
				"article\/comment\/id": "$comment.ID",
				"article\/comment\/name": "$comment.Name",
				"article\/comment\/url": "$comment.Url",
				"article\/comment\/urlencoder": "$comment.???",	
				"article\/comment\/email": "$comment.Author.Email",
				"article\/comment\/posttime": "$comment.Time('Y年m月d日')",
				"article\/comment\/content": "$comment.Content",
				"article\/comment\/count": "$comment.Count",
				"article\/comment\/authorid": "$comment.Author.ID",
				//"article\/comment\/firstcontact": "",
				"article\/comment\/emailmd5": "$MD5(comment.Author.Email)",
				"article\/comment\/parentid": "$comment.ParentID",
				"article\/comment\/avatar": "$comment.Avatar",
				"article\/comment\/agent": "$comment.Agent"
		
			}
		},
		{
			"name": "module",
			"list": {
				"function\/id": '$module.ID',
				"function\/htmlid": '$module.HtmlID',
				"function\/name": '$module.Name',
				"function\/content": '$module.Content',
				"function\/filename": '$module.FileName'
			}
		},
		{
			"name": "article",
			"list": {
				'article\/id', '$article.ID',
				'article\/title', '$article.Title',
				'article\/alias', '$article.Alias',
				'article\/intro', '$article.Intro',
				'article\/content', '$article.Content',
				'article\/url', '$article.Url',
				'article\/commnums', '$article.CommNums',
				'article\/viewnums', '$article.ViewNums',
				'article\/commentposturl', '$article.CommentPostUrl',
			}
		}

	]
}