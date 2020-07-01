# Z-BlogPHP API 分类模块接口文档

## 接口列表

### 新增分类：post

#### 请求

POST `https://example.com/api.php?mod=category&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性         | 类型    | 示例值  | 说明                 |
  | ------------ | ------- | ------- | -------------------- |
  | name         | string  | 未命名  | 分类名称             |
  | alias        | string  | unnamed | 正文内容             |
  | intro        | string  | 摘要    | 摘要                 |
  | order        | int     | 0       | 排序                 |
  | parent_id    | int     | 0       | 父分类id             |
  | template     | string  | index   | 模板                 |
  | log_template | string  | single  | 该分类文章的默认模板 |
  | add_navbar   | boolean | false   | 是否加入导航栏菜单   |

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




### 获取分类信息：get

#### 请求

GET `https://example.com/api.php?mod=category&act=get&id=1`

POST `https://example.com/api.php?mod=category&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=category&id=1`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型 | 示例值 | 说明   |
  | ---- | ---- | ------ | ------ |
  | id   | int  | 1      | 分类id |

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

  | 属性         | 类型    | 示例值  | 说明                 |
  | ------------ | ------- | ------- | -------------------- |
  | id           | int     | 2       | 分类id               |
  | name         | string  | 未命名  | 分类名称             |
  | alias        | string  | unnamed | 别名                 |
  | intro        | string  | 摘要    | 摘要                 |
  | order        | int     | 0       | 排序                 |
  | parent_id    | int     | 0       | 父分类id             |
  | template     | string  | index   | 模板                 |
  | log_template | string  | single  | 该分类文章的默认模板 |
  | add_navbar   | boolean | false   | 是否加入导航栏菜单   |



### 修改分类：update

#### 请求

POST `https://example.com/api.php?mod=category&act=update`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性         | 类型    | 示例值  | 说明                 |
  | ------------ | ------- | ------- | -------------------- |
  | id           | int     | 2       | 分类id               |
  | name         | string  | 未命名  | 分类名称             |
  | alias        | string  | unnamed | 别名                 |
  | intro        | string  | 摘要    | 摘要                 |
  | order        | int     | 0       | 排序                 |
  | parent_id    | int     | 0       | 父分类id             |
  | template     | string  | index   | 模板                 |
  | log_template | string  | single  | 该分类文章的默认模板 |
  | add_navbar   | boolean | false   | 是否加入导航栏菜单   |

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



### 删除分类：delete

#### 请求

POST `https://example.com/api.php?mod=catefory&act=delete`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

- Body

  | 属性 | 类型 | 示例值 | 说明           |
  | ---- | ---- | ------ | -------------- |
  | id   | int  | 1      | 待删除的分类id |

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

  

### 获取分类列表：get_categories

#### 请求

GET/POST `https://example.com/api.php?mod=category&act=get_categories`

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

  data 为数组，数组元素内容参见获取分类信息接口。


