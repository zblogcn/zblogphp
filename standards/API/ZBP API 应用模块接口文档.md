# Z-BlogPHP API 应用模块接口文档

## 接口列表

### 安装应用：install

#### 请求


### 获取应用信息：get

#### 请求

GET `https://example.com/zb_system/api.php?mod=app&act=get&type=plugin&id=UEditor`

POST `https://example.com/zb_system/api.php?mod=app&act=get`

或者省略 act：

GET `https://example.com/zb_system/api.php?mod=app&id=UEditor&type=plugin&id=UEditor`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型   | 示例值  | 说明   |
  | ---- | ------ | ------- | ------ |
  | type | string | plugin  | 应用类型，theme:主题/plugin:插件 |
  | id   | string | UEditor | 应用id |

  示例：

  ```json
  {
      "type": "plugin",
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
          "is_active": true,
          "id": "UEditor",
          "name": "UEditor",
          "url": "http://www.rainbowsoft.org",
          "note": "UEditor编辑器.",
          ...
      },
      "error": null
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




### 启用插件：enable_plugin

#### 请求

POST `https://example.com/zb_system/api.php?mod=app&act=enable_plugin`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型   | 示例值  | 说明           |
  | ---- | ------ | ------- | -------------- |
  | id   | string | UEditor | 待启用的插件ID |

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
    	"message": "操作成功",
    	"data": {
        "enabled": true,
        "id": "UEditor"
      },
      "error": null
  }
  ```

  data 内容：

  | 键      | 类型    | 示例值                | 说明             |
  | ------- | ------- | --------------------- | ---------------- |
  | enabled | boolean | true                  | 是否启用成功     |
  | id      | string  | UEditor               | 本次启用的插件ID |



### 停用应用：disable_plugin

#### 请求

POST `https://example.com/zb_system/api.php?mod=app&act=disable_plugin`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型   | 示例值  | 说明           |
  | ---- | ------ | ------- | -------------- |
  | id   | string | UEditor | 待停用的插件ID |

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
    	"message": "OK",
    	"data": {
        "disabled": true,
        "id": "UEditor"
      },
      "error": null
  }
  ```

  data 内容：

  | 键       | 类型    | 示例值                 | 说明             |
  | -------- | ------- | ---------------------- | ---------------- |
  | disabled | boolean | true                   | 是否停用成功     |
  | id       | string  | UEditor                | 本次停用的插件ID |



### 更换主题：set_theme

#### 请求

POST `https://example.com/zb_system/api.php?mod=app&act=set_theme`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性 | 类型   | 示例值  | 说明           |
  | ---- | ------ | ------- | -------------- |
  | id   | string | tpure | 待更换的主题ID |
  | style    | string  | style.css  |  样式 |

  示例：

  ```json
  {
      "id": "tpure",
      "style": "style.css"
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
    	"message": "操作成功",
    	"data": {
        "enabled": true,
        "id": "tpure"
      },
      "error": null
  }
  ```

  data 内容：

  | 键      | 类型    | 示例值                | 说明             |
  | ------- | ------- | --------------------- | ---------------- |
  | enabled | boolean | true                  | 是否启用成功     |
  | id      | string  | tpure                | 本次更换的主题ID |



### 列出所有应用：get_apps

包括主题和插件。

#### 请求

POST `https://example.com/zb_system/api.php?mod=app&act=get_apps`

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
          "is_active": true,
          "id": "UEditor",
          "name": "UEditor",
          "url": "http://www.rainbowsoft.org",
          "note": "UEditor编辑器.",
          ...
      }],
      "error": null
  }
  ```

  data 为数组，数组元素内容：

  | 键           | 类型   | 示例值                       | 说明       |
  | ------------ | ------ | ---------------------------- | ---------- |
  | id           | string | UEditor                      | 应用id     |
  | is_active    | boolean | true                      | 是否启用     |
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