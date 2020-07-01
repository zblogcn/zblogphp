# Z-BlogPHP API 系统模块接口文档

## 接口列表

### 获取系统信息：get_info

#### 请求

GET `https://example.com/api.php?mod=system&act=get_info`

POST `https://example.com/api.php?mod=system&act=get_info`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

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
    	"data": {
          "environment": 1,
          "major_version": 7,
          "minor_version": 0,
          "build_version": 2330,
          ...
      },
      "error": null
  }
  ```

  data 内容：

  | 键               | 类型   | 示例值                      | 说明            |
  | ---------------- | ------ | --------------------------- | --------------- |
  | environment      | int    | Linux3.10.0; nginx1.18.0... | 系统环境        |
  | major_version    | int    | 1                           | 主版本号        |
  | minor_version    | int    | 7                           | 用户昵称/别名   |
  | build_version    | int    | 0                           | 用户级别        |
  | commit_version   | int    | 2330                        | 提交版本号      |
  | version_codename | string | Alpha                       | 版本代号        |
  | articles         | int    | 2333                        | 文章总数        |
  | categories       | int    | 233                         | 分类总数        |
  | pages            | int    | 2333                        | 页面总数        |
  | tags             | int    | 23                          | 标签总数        |
  | comments         | int    | 23333                       | 评论总数        |
  | page_views       | int    | 2333333                     | 浏览总数        |
  | users            | int    | 23                          | 用户总数        |
  | theme            | sring  | default                     | 当前主题        |
  | xml_rpc          | string | http://...                  | XML-RPC协议地址 |



### 检查系统更新：update

#### 请求

POST `https://example.com/api.php?mod=system&act=update`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

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
    	"data": {
          "has_new_version": false,
          "current_version": "1.6.4.2130 (Valyria)",
          "new_version": ""
      },
      "error": null
  }
  ```

  data 内容：

  | 键              | 类型    | 示例值                | 说明                 |
  | --------------- | ------- | --------------------- | -------------------- |
  | has_new_version | boolean | false                 | 是否有新版本需要更新 |
  | current_version | string  | 1.6.4.2130 (Valyria)  | 当前版本             |
  | new_version     | string  | 1.7.0.2333 (Valkyrie) | 新版本               |



### 检查系统更新：upgrade

默认更新到最新版，不支持选择版本。

#### 请求

POST `https://example.com/api.php?mod=system&act=upgrade`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

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
    	"data": {
          "upgraded": true,
        	"message": "Upgraded successfully!"
      },
      "error": null
  }
  ```

  data 内容：

  | 键       | 类型    | 示例值                 | 说明         |
  | -------- | ------- | ---------------------- | ------------ |
  | upgraded | boolean | true                   | 是否更新成功 |
  | message  | string  | Upgraded successfully! | 消息         |

