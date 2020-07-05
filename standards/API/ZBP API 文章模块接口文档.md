# Z-BlogPHP API 文章模块接口文档

## 接口列表

### 新增/修改 文章/页面：post

#### 请求

POST `https://example.com/api.php?mod=post&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body
  
  | 属性      | 类型    | 示例值              | 说明                            |
  | --------- | ------- | ------------------- | ------------------------------- |
  | ID     | int  | 0     | 文章id，0为新增操作，大于0时为修改操作             |
  | Type     | int  | 0     | 类型，0为文章，1为页面             |
  | Title     | string  | 标题              | 文章标题                        |
  | Content   | string  | 正文                | 正文内容                        |
  | Intro     | string  | 摘要                | 摘要内容                        |
  | Alias     | string  | article_1           | 别名                            |
  | Tag      | string  | 标签1,标签2             | 标签名                          |
  | CateID  | int     | 1                   | 分类id                          |
  | Status    | int     | 0                   | 状态，0：公开；1：草稿，2：审核 |
  | AuthorID | int     | 1                   | 作者id                          |
  | PostTime      | string  | 2020-06-30 10:19:14 | 发布时间                        |
  | IsTop    | int | 0               | 是否置顶，0:不置顶；1:全局；2:首页；4:分类                      |
  | IsLock   | boolean | false               | 是否禁止评论                    |
  | Metas      | string  |                     | 附加元信息                      |
  | Template      | string  |                     | 模板                      |
  

示例：

  ```json
  {
        "ID": 0,
        "Type": 0,
    	"Title": "标题",
    	"Content": "正文",
    	"Intro": "摘要",
    	...
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体




### 获取文章：get

#### 请求

GET `https://example.com/api.php?mod=post&act=get&id=1`

POST `https://example.com/api.php?mod=post&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=post&id=1`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型 | 示例值 | 说明   |
  | ---- | ---- | ------ | ------ |
  | id   | int  | 1      | 文章id |

  示例：

  ```json
  {
    	"id": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  示例：

  ```json
  {
    	"message": "OK",
    	"data": {
          "id": 1,
          "title": "标题",
          "content": "正文",
        "intro": "摘要",
          ...
      },
      "error": null
  }
  ```
  
  data 内容：
  
  | 属性      | 类型    | 示例值              | 说明                            |
  | --------- | ------- | ------------------- | -------------------------------   |
  | id | int | 1 | 文章id |
  | title     | string  | 标题            | 文章标题 |
  | content   | string  | 正文                | 正文内容                        |
  | intro     | string  | 摘要                | 摘要内容                        |
  | alias     | string  | article_1           | 别名                            |
  | tags      | string  | 1,2,3,4             | 标签id                          |
  | category  | int     | 1                   | 分类id                          |
  | status    | int     | 0                   | 状态，0：公开；1：草稿，2：审核 |
  | author_id | int     | 1                   | 作者id                          |
  | date      | string  | 2020-06-30 10:19:14 | 发布时间                        |
  | is_top    | boolean | false               | 是否置顶                        |
  | is_lock   | boolean | false               | 是否禁止评论                    |
  | meta | string |  | 附加元信息 |


### 删除文章：delete

#### 请求

POST `https://example.com/api.php?mod=post&act=delete`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型 | 示例值 | 说明   |
  | ---- | ---- | ------ | ------ |
  | id   | int  | 1      | 文章id |

  示例：

  ```json
  {
    	"id": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体

  

### 获取文章/页面列表：list

#### 请求

GET/POST `https://example.com/api.php?mod=post&act=list`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body
  | 属性      | 类型    | 示例值              | 说明                            |
    | --------- | ------- | ------------------- | ------------------------------- |
    | mng        | string     | admin    | 管理类型，admin：管理所有文章；author：管理某个用户自己的文章 |
    | cate_id    | int    | 1 | 分类id |
    | tag_id    | int  | 1 | 标签id |
    | auth_ id  | int | 1 | 用户id |
    | date    | string  | 2020-7-4 |  日期 |
    | type   | string  | page、article 或 all | 内容类型 |
    
    支持的过滤器：limit、offset、page、perpage、sortby、order  
    其中，sortby 支持的字段有：  
    | URL sortby 参数 | 对应数据表属性 | 说明 |
    | --- | --- | --- |
    | id  | log_ID | id |
    | create_time | log_CreateTime | 文章创建时间 |
    | post_time | log_PostTime | 文章提交时间 |
    | update_time | log_UpdateTime | 文章更新时间 |
    | comm_num | log_CommNums | 文章评论数量 |
    | view_num | log_ViewNums | 文章浏览数量 |
    

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  示例：

  ```json
  {
    	"message": "OK",
    	"data": [{
          "id": 1,
          "title": "标题",
          "content": "正文",
          "intro": "摘要",
          ...
      }],
      "error": null
  }
  ```

  data 为数组，数组元素内容：

  | 属性      | 类型    | 示例值              | 说明                            |
  | --------- | ------- | ------------------- | ------------------------------- |
  | id        | int     | 1                   | 文章id                          |
  | title     | string  | 标题                | 文章标题                        |
  | content   | string  | 正文                | 正文内容                        |
  | intro     | string  | 摘要                | 摘要内容                        |
  | alias     | string  | article_1           | 别名                            |
  | tags      | string  | 1,2,3,4             | 标签id                          |
  | category  | int     | 1                   | 分类id                          |
  | status    | int     | 0                   | 状态，0：公开；1：草稿，2：审核 |
  | author_id | int     | 1                   | 作者id                          |
  | date      | string  | 2020-06-30 10:19:14 | 发布时间                        |
  | is_top    | boolean | false               | 是否置顶                        |
  | is_lock   | boolean | false               | 是否禁止评论                    |
  | meta      | string  |                     | 附加元信息                      |

