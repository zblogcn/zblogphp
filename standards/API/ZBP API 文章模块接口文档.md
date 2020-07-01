# Z-BlogPHP API 文章模块接口文档

## 接口列表

### 新增文章：post

#### 请求

POST `https://example.com/api.php?mod=post&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性      | 类型    | 示例值              | 说明                            |
  | --------- | ------- | ------------------- | ------------------------------- |
  | title     | string  | 用户名              | 文章标题                        |
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
  

示例：

  ```json
  {
    	"title": "标题",
    	"content": "正文",
    	"intro": "摘要",
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



### 修改文章：update

#### 请求

POST `https://example.com/api.php?mod=post&act=update`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

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
  

示例：

  ```json
  {
    	"id": 1,
    	"title": "标题",
    	"content": "正文",
    	"intro": "摘要",
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

  

### 获取文章列表：get_articles

#### 请求

GET/POST `https://example.com/api.php?mod=post&act=get_articles`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  无

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



### 获取页面列表：get_pages

参考获取文章列表，除了 `act` 不同，其他内容一样。

