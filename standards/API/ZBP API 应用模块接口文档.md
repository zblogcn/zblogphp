# Z-BlogPHP API 应用模块接口文档

## 接口列表

### 安装应用：install

#### 请求

POST `https://example.com/api.php?mod=app&act=install`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性     | 类型   | 示例值  | 说明   |
  | -------- | ------ | ------- | ------ |
  | username | string | UEditor | 用户名 |

  示例：

  ```json
  {
    	"id": "UEditor"
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
    	"code": 200,
    	"message": "OK",
    	"data": {
        "installed": true,
        "message": "Installed successfully!",
        "id": "UEditor"
      }
  }
  ```

  data 内容：

  | 键        | 类型    | 示例值                  | 说明          |
  | --------- | ------- | ----------------------- | ------------- |
  | installed | boolean | true                    | 是否安装成功  |
  | message   | string  | Installed successfully! | 消息          |
  | id        | string  | UEditor                 | 用户昵称/别名 |



### 获取应用信息：get

#### 请求

GET `https://example.com/api.php?mod=app&act=get&id=UEditor`

POST `https://example.com/api.php?mod=app&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=app&id=UEditor`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型   | 示例值  | 说明   |
  | ---- | ------ | ------- | ------ |
  | id   | string | UEditor | 应用id |

  示例：

  ```json
  {
    	"id": "UEditor"
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
    	"code": 200,
    	"message": "OK",
    	"data": {
          "id": "UEditor",
          "name": "UEditor",
          "url": "http://www.rainbowsoft.org",
          "note": "UEditor编辑器.",
          ...
      }
  }
  ```

  data 内容：

  | 键           | 类型   | 示例值                       | 说明       |
  | ------------ | ------ | ---------------------------- | ---------- |
  | id           | string | UEditor                      | 应用id     |
  | name         | string | UEditor                      | 应用名称   |
  | url          | string | `http://www.rainbowsoft.org` | 应用网址   |
  | note         | string | UEditor编辑器.               | 应用简介   |
  | description  | string | ZBP的默认编辑器              | 应用描述   |
  | author_name  | string | zsx                          | 开发者名称 |
  | author_email | string | @                            | 开发者邮箱 |
  | author_url   | string | `http://www.zsxsoft.com`     | 开发者网址 |
  | version      | string | 1.6.5                        | 应用版本   |
  | published    | string | 2013-07-07                   | 发布日期   |
  | updated      | string | 2020-06-12                   | 更新日期   |




### 启用应用：enable

#### 请求

POST `https://example.com/api.php?mod=app&act=enable`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型   | 示例值  | 说明           |
  | ---- | ------ | ------- | -------------- |
  | id   | string | UEditor | 待启用的应用id |

  示例：

  ```json
  {
    	"id": "UEditor"
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
    	"code": 200,
    	"message": "OK",
    	"data": {
        "enabled": true,
        "message": "Enabled successfully!",
        "id": "UEditor"
      }
  }
  ```

  data 内容：

  | 键      | 类型    | 示例值                | 说明             |
  | ------- | ------- | --------------------- | ---------------- |
  | enabled | boolean | true                  | 是否启用成功     |
  | message | string  | Enabled successfully! | 消息             |
  | id      | string  | UEditor               | 本次启用的应用id |



### 停用应用：disable

#### 请求

POST `https://example.com/api.php?mod=app&act=disable`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型   | 示例值  | 说明           |
  | ---- | ------ | ------- | -------------- |
  | id   | string | UEditor | 待停用的应用id |

  示例：

  ```json
  {
    	"id": "UEditor"
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
    	"code": 200,
    	"message": "OK",
    	"data": {
        "disabled": true,
        "message": "Disabled successfully!",
        "id": "UEditor"
      }
  }
  ```

  data 内容：

  | 键       | 类型    | 示例值                 | 说明             |
  | -------- | ------- | ---------------------- | ---------------- |
  | disabled | boolean | true                   | 是否停用成功     |
  | message  | string  | Disabled successfully! | 消息             |
  | id       | string  | UEditor                | 本次停用的应用id |



### 删除应用：delete

#### 请求

POST `https://example.com/api.php?mod=app&act=delete`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型   | 示例值  | 说明           |
  | ---- | ------ | ------- | -------------- |
  | id   | string | UEditor | 待删除的应用id |

  示例：

  ```json
  {
    	"id": "UEditor"
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
    	"code": 200,
    	"message": "OK",
    	"data": {
        "deleted": true,
        "message": "Deleted successfully!",
        "id": "UEditor"
      }
  }
  ```

  data 内容：

  | 键      | 类型    | 示例值                | 说明             |
  | ------- | ------- | --------------------- | ---------------- |
  | deleted | boolean | true                  | 是否删除成功     |
  | message | string  | Deleted successfully! | 消息             |
  | id      | string  | UEditor               | 本次删除的应用id |



### 列出所有应用：get_apps

包括主题和插件。

#### 请求

POST `https://example.com/api.php?mod=app&act=get_apps`

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
    	"code": 200,
    	"message": "OK",
    	"data": [{
          "id": "UEditor",
          "name": "UEditor",
          "url": "http://www.rainbowsoft.org",
          "note": "UEditor编辑器.",
          ...
      }]
  }
  ```

  data 为数组，数组元素内容：

  | 键           | 类型   | 示例值                       | 说明       |
  | ------------ | ------ | ---------------------------- | ---------- |
  | id           | string | UEditor                      | 应用id     |
  | name         | string | UEditor                      | 应用名称   |
  | url          | string | `http://www.rainbowsoft.org` | 应用网址   |
  | note         | string | UEditor编辑器.               | 应用简介   |
  | description  | string | ZBP的默认编辑器              | 应用描述   |
  | author_name  | string | zsx                          | 开发者名称 |
  | author_email | string | @                            | 开发者邮箱 |
  | author_url   | string | `http://www.zsxsoft.com`     | 开发者网址 |
  | version      | string | 1.6.5                        | 应用版本   |
  | published    | string | 2013-07-07                   | 发布日期   |
  | updated      | string | 2020-06-12                   | 更新日期   |



### 列出所有主题：get_plugins

参考列出应用接口，除了 `act` 不同，其他内容一样。



### 列出所有插件：get_themes

参考列出应用接口，除了 `act` 不同，其他内容一样。