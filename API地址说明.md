API地址说明
=================

==== 前台 ====

获取分类（用ID）：GET /category/1 以及 GET /category/?id=1

获取分类（用名字）：GET /category/?name=xx

创建分类：POST /category/

更新分类信息：PUT /category/1

删除分类：DELETE /category/1



获取文章：GET /article/1 以及 GET /article/?id=1

创建文章：POST /article/

更新文章：PUT /article/1

删除文章：DELETE /article/1

得到分类下文章：GET /articles/?page=1&limit=30&category=1



获取评论列表：GET /comments/?page=1&limit=30&article=1

获取单条评论：GET /comment/1 以及 GET /comment/?id=1

创建评论：POST /comment/

更新评论：PUT /comment/1

删除评论：DELETE /comment/1

获取子评论：GET /comments/?parent=1



获取Tag列表：GET /tags/?page=1&limit=30

获取Tag：GET /tag/1 或 GET /tag/?id=1

创建Tag：POST /tag/

更新Tag：PUT /tag/1

删除Tag：DELETE /tag/1



获取用户列表：GET /users/?page=1&limit=30&category=1

获取用户：GET /user/1 或 GET /user/?id=1

创建用户：POST /user/

更新用户：PUT /user/1

删除用户：DELETE /user/1



搜索：GET /search/Key



==== 后台 ====


得到网站信息：GET /

更新网站配置：PUT /setting



启用/停用插件：PUT /plugin/id/

删除插件：DELETE /plugin/id



启用/停用/调整主题：PUT /theme/id

删除主题：DELETE /theme/id



获取模块：GET /module/1 或 GET /module/?id=1

创建模块：POST /module

更新模块：PUT /module/1

删除模块：DELETE /module/1

得到模块列表：GET /modules/?page=1&limit=30



获取附件：GET /attachment/1 或 GET /attachment/?id=1

上传附件：POST /attachment

删除附件：DELETE /attachment/1

得到附件列表：GET /attachments/?page=1&limit=30
