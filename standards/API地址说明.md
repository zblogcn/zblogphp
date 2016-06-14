API地址说明
=================

### 前台 
- [x] 获取分类（用ID）：GET /category/?id=1
- [x] 获取分类（用名字）：GET /category/?name=xx
- [x] 创建分类：POST /category/create/
- [x] 更新分类信息：POST /category/update/?id=1
- [x] 删除分类：POST /category/delete/?id=1
- [x] 获取文章：GET /article/?id=1
- [x] 创建文章：POST /article/create/
- [x] 更新文章：POST /article/update/?id=1
- [x] 删除文章：POST /article/delete/?id=1
- [ ] 得到分类下文章：GET /articles/?page=1&limit=30&category=1
- [ ] 获取评论列表：GET /comments/?page=1&limit=30&article=1
- [x] 获取单条评论：GET /comment/?id=1
- [ ] 创建评论：POST /comment/create/
- [ ] 更新评论：POST /comment/update/?id=1
- [ ] 删除评论：POST /comment/delete/?id=1
- [ ] 获取子评论：GET /comments/?parent=1
- [ ] 获取Tag列表：GET /tags/?page=1&limit=30
- [x] 获取Tag：GET /tag/?id=1
- [x] 创建Tag：POST /tag/create/
- [x] 更新Tag：POST /tag/update/?id=1
- [x] 删除Tag：POST /tag/delete/?id=1
- [ ] 获取用户列表：GET /members/?page=1&limit=30&category=1
- [x] 获取用户：GET /member/?id=1
- [x] 创建用户：POST /member/create/
- [x] 更新用户：POST /member/update/?id=1
- [x] 删除用户：POST /member/delete/?id=1
- [ ] 搜索：GET /search/Key

### 后台 
- [x] 得到网站信息：GET /
- [ ] 更新网站配置：POST /setting/update/
- [x] 启用应用：POST /app/enable/id/
- [x] 停用应用：POST /app/disable/id/
- [ ] 调整应用：POST /app/modify/id/
- [ ] 删除应用：POST /app/delete/id
- [x] 获取模块：GET /module/?id=1
- [x] 创建模块：POST /module/create
- [x] 更新模块：POST /module/update/?id=1
- [x] 删除模块：POST /module/delete/?id=1
- [ ] 得到模块列表：GET /modules/?page=1&limit=30
- [x] 获取附件：GET /upload/?id=1
- [ ] 上传附件：POST /upload/create
- [ ] 删除附件：POST /upload/delete/?id=1
- [ ] 得到附件列表：GET /attachments/?page=1&limit=30
