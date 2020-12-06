# Z-BlogPHP API 标签模块接口文档

## 接口列表

### 新增标签：post

#### 请求

POST `https://example.com/zb_system/api.php?mod=tag&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性       | 类型    | 示例值  | 说明               |
  | ---------- | ------- | ------- | ------------------ |
  | name       | string  | 未命名  | 名称               |
  | alias      | string  | unnamed | 别名               |
  | intro      | string  | 摘要    | 摘要               |
  | template   | string  | index   | 模板               |
  | add_navbar | boolean | false   | 是否加入导航栏菜单 |

  示例：

    ```json
  {
      "name": "未命名",
      "alias": "unnamed",
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




### 获取标签信息：get

#### 请求

GET `https://example.com/zb_system/api.php?mod=tag&act=get&id=1`

POST `https://example.com/zb_system/api.php?mod=tag&act=get`

或者省略 act：

GET `https://example.com/zb_system/api.php?mod=tag&id=1`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型 | 示例值 | 说明   |
  | ---- | ---- | ------ | ------ |
  | id   | int  | 1      | 标签id |

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
          "name": "未命名",
          "alias": "unnamed",
          "intro": "摘要",
          ...
      },
      "error": null
  }
  ```

  data 内容：

  | 属性       | 类型    | 示例值  | 说明               |
  | ---------- | ------- | ------- | ------------------ |
  | id         | int     | 2       | 标签id             |
  | name       | string  | 未命名  | 名称               |
  | alias      | string  | unnamed | 别名               |
  | intro      | string  | 摘要    | 摘要               |
  | template   | string  | index   | 模板               |
  | add_navbar | boolean | false   | 是否加入导航栏菜单 |



### 修改标签：update

#### 请求

POST `https://example.com/zb_system/api.php?mod=tag&act=update`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性       | 类型    | 示例值  | 说明               |
  | ---------- | ------- | ------- | ------------------ |
  | id         | int     | 2       | 标签id             |
  | name       | string  | 未命名  | 名称               |
  | alias      | string  | unnamed | 别名               |
  | intro      | string  | 摘要    | 摘要               |
  | template   | string  | index   | 模板               |
  | add_navbar | boolean | false   | 是否加入导航栏菜单 |

  示例：

    ```json
  {
      "id": 1,
      "name": "未命名",
      "alias": "unnamed",
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



### 删除标签：delete

#### 请求

POST `https://example.com/zb_system/api.php?mod=tag&act=delete`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

- Body

  | 属性 | 类型 | 示例值 | 说明           |
  | ---- | ---- | ------ | -------------- |
  | id   | int  | 1      | 待删除的标签id |

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

  

### 获取标签列表：get_tags

#### 请求

GET/POST `https://example.com/zb_system/api.php?mod=tag&act=get_tags`

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
    	"data": [{
          "id": 1,
          "name": "未命名",
          "alias": "unnamed",
          "intro": "摘要",
          ...
      }],
      "error": null
  }
  ```

  data 为数组，数组元素内容参见获取标签信息接口。

