# Z-BlogPHP API 附件模块接口文档

## 接口列表

### 新增附件：post

#### 请求

POST `https://example.com/api.php?mod=atta&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性     | 类型    | 示例值     | 说明         |
  | -------- | ------- | ---------- | ------------ |
  | filename | UEditor | 文件名.png | 原始文件名   |
  | user_id  | int     | 1          | 上传者id     |
  | type     | string  | image/png  | 文件类型     |
  | file     | blob    | xxxx       | 二进制文件流 |

  示例：

  ```json
  {
    	"filename": "文件名.png",
    	"user_id": 1,
    	"type": "image/png",
    	"file": blob
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体




### 获取附件信息：get

#### 请求

GET `https://example.com/api.php?mod=atta&act=get&id=1`

POST `https://example.com/api.php?mod=atta&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=atta&id=1`

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

POST `https://example.com/api.php?mod=atta&act=delete`

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



### 列出所有附件：get_attas

#### 请求

POST `https://example.com/api.php?mod=atta&act=get_attas`

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


