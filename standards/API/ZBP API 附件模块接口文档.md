# Z-BlogPHP API 附件模块接口文档

## 接口列表

### 新增附件：post

#### 请求

POST `https://example.com/zb_system/api.php?mod=upload&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  文件流

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体




### 获取附件信息：get

#### 请求

GET `https://example.com/zb_system/api.php?mod=upload&act=get&id=1`

POST `https://example.com/zb_system/api.php?mod=upload&act=get`

或者省略 act：

GET `https://example.com/zb_system/api.php?mod=upload&id=1`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型 | 示例值 | 说明   |
  | ---- | ---- | ------ | ------ |
  | id   | int  | 1      | 附件id |

  示例：

  ```json
  {
    	"id": 21
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
          "filename": "文件名.png",
          "url": "https://.../xxx.jpg",
          "size": 1234,
          "user_id": 1,
          "type": "image/png",
          "date": "2020-06-28 16:48:40"
      },
      "error": null
  }
  ```

  data 内容：

  | 键       | 类型   | 示例值                    | 说明                 |
  | -------- | ------ | ------------------------- | -------------------- |
  | id       | int    | 1                         | 附件id               |
  | filename | string | 202004211628209436871.png | 文件名               |
  | url      | string | `http://.../xxx.png`      | 文件网址             |
  | size     | int    | 12345                     | 文件大小，单位：Byte |
  | user_id  | string | 1                         | 应用描述             |
  | type     | string | image/png                 | 附件类型             |
  | date     | string | 2020-06-28 16:48:40       | 开发者邮箱           |



### 删除附件：delete

#### 请求

POST `https://example.com/zb_system/api.php?mod=upload&act=delete`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型 | 示例值 | 说明           |
  | ---- | ---- | ------ | -------------- |
  | id   | int  | 1      | 待删除的附件id |

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
        "deleted": true,
        "message": "Deleted successfully!",
        "id": 1
      },
      "error": null
  }
  ```

  data 内容：

  | 键      | 类型    | 示例值                | 说明             |
  | ------- | ------- | --------------------- | ---------------- |
  | deleted | boolean | true                  | 是否删除成功     |
  | message | string  | Deleted successfully! | 消息             |
  | id      | string  | 1                     | 本次删除的附件id |



### 列出所有附件：list

#### 请求

POST `https://example.com/zb_system/api.php?mod=upload&act=list`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性    | 类型   | 示例值 | 说明   |
    | ------- | ------ | ------ | ------ |
    | author_id | int | 1      | 用户id |
    | post_id | int | 1      | 文章id |
    
    支持的过滤器：limit、offset、page、perpage、sortby、order  
    其中，sortby 支持的字段有：  
    | URL sortby 参数 | 对应数据表属性 | 说明 |
    | --- | --- | --- |
    | id  | ul_ID | id |
    | post_time | ul_PostTime | 附件提交时间 |
    | name | ul_Name | 附件文件名 |
    | source_name | ul_SourceName | 附件原始文件名 |
    | downloads | ul_DownNums | 附件下载数量 |

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
          "filename": "文件名.png",
          "url": "https://.../xxx.jpg",
          "size": 1234,
          "user_id": 1,
          "type": "image/png",
          "date": "2020-06-28 16:48:40"
      }],
      "error": null
  }
  ```

  data 为数组，数组元素内容参考获取附件信息接口。


