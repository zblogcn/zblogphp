# Z-BlogPHP API 评论模块接口文档

## 接口列表

### 新增评论：post

#### 请求

POST `https://example.com/api.php?mod=comment&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性         | 类型   | 示例值              | 说明              |
  | ------------ | ------ | ------------------- | ----------------- |
  | post_id      | int    | 1                   | 文章id            |
  | content      | string | 内容                | 评论内容          |
  | root_id      | int    | 0                   | 顶级评论id        |
  | parent_id    | int    | 21                  | 父级评论id        |
  | author_id    | int    | 0                   | 评论人id          |
  | author_name  | string | Chris               | 评论人名称        |
  | author_email | string | 123@example.com     | 评论人邮箱        |
  | author_ip    | string | 127.0.0.1           | 评论人ip          |
  | author_ua    | string | Mozilla             | 评论人 User-Agent |
  | post_time    | string | 2019-01-01 12:00:00 | 评论提交时间      |
  | meta         | string |                     | 附加内容          |

  示例：

  ```json
  {
    	"post_id": 1,
    	"content": "内容",
    	"root_id": 0,
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



### 获取评论：get

#### 请求

GET `https://example.com/api.php?mod=comment&act=get&id=1`

POST `https://example.com/api.php?mod=comment&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=comment&id=1`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型 | 示例值 | 说明   |
  | ---- | ---- | ------ | ------ |
  | id   | int  | 1      | 评论id |

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
          "post_id": 1,
          "content": "内容",
          "root_id": 0,
          ...
      },
      "error": null
  }
  ```

  data 内容：

  | 属性         | 类型   | 示例值              | 说明              |
  | ------------ | ------ | ------------------- | ----------------- |
  | post_id      | int    | 1                   | 文章id            |
  | content      | string | 内容                | 评论内容          |
  | root_id      | int    | 0                   | 顶级评论id        |
  | parent_id    | int    | 21                  | 父级评论id        |
  | author_id    | int    | 0                   | 评论人id          |
  | author_name  | string | Chris               | 评论人名称        |
  | author_email | string | 123@example.com     | 评论人邮箱        |
  | author_ip    | string | 127.0.0.1           | 评论人ip          |
  | author_ua    | string | Mozilla             | 评论人 User-Agent |
  | post_time    | string | 2019-01-01 12:00:00 | 评论提交时间      |
  | meta         | string |                     | 附加内容          |



### 删除评论：delete

#### 请求

POST `https://example.com/api.php?mod=comment&act=delete`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型 | 示例值 | 说明           |
  | ---- | ---- | ------ | -------------- |
  | id   | int  | 1      | 待删除的评论id |

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



### 获取评论列表：list

#### 请求

GET `https://example.com/api.php?mod=comment&act=list`

POST `https://example.com/api.php?mod=comment&act=list`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  支持的过滤器：limit、offset、page、perpage、sortby、order  
  其中，sortby 支持的字段有：  
  | URL sortby 参数 | 对应数据表属性 | 说明 |
  | --- | --- | --- |
  | id  | comm_ID | id |
  | post_time | comm_PostTime | 评论提交时间 |

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
          "post_id": 1,
          "content": "内容",
          "root_id": 0,
          ...
      }],
      "error": null
  }
  ```

  data 为数组，数组元素内容参考获取评论接口。



### 评论审核：check

#### 请求

POST `https://example.com/api.php?mod=comment&act=check`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性     | 类型    | 示例值 | 说明           |
  | -------- | ------- | ------ | -------------- |
  | id       | int     | 1      | 待审核的评论id |
  | ischecking | boolean | true   | 是否通过审核   |

  示例：

  ```json
  {
    	"id": 1,
    	"ischecking": true
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体


### 评论批量操作：batch

#### 请求

POST `https://example.com/api.php?mod=comment&act=batch`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性     | 类型    | 示例值  | 说明           |
  | -------- | ------- | ------- | -------------- |
  | id      | array  | [1,2,3,4] | 待审核的评论id |
  | all_del |   |     | 全部删除   |
  | all_pass |   |     | 全部通过审核   |
  | all_audit |   |     | 全部列为待审核   |

  示例：

  ```json
  {
    	"id": [1,2,3,4],
    	"all_del": true
  }
  ```


#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体
