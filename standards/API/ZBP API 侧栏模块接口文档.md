# Z-BlogPHP API 侧栏模块接口文档

## 接口列表

### 新增模块：post_module

#### 请求

POST `https://example.com/api.php?mod=sidebar&act=post`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性          | 类型    | 示例值    | 说明                                      |
  | ------------- | ------- | --------- | ----------------------------------------- |
  | name          | string  | 未命名    | 名称                                      |
  | filename      | string  | unnamed   | 模块文件名                                |
  | content       | string  | 内容      | 内容                                      |
  | html_id       | string  | module_1  | 包含该模块 HTML 内容的最外围元素的 DOM ID |
  | type          | string  | div 或 ul | 包含该模块 HTML 内容的最外围元素的标签    |
  | max_li        | int     | 10        | UL内LI的最大行数，在 Type 为 ul 是有效    |
  | is_hide_title | boolean | true      | 前台是否隐藏模块名称                      |
  | meta          | string  |           | 附加内容                                  |

  示例：

    ```json
  {
      "name": "未命名",
      "filename": "unnamed",
      "content": "内容",
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




### 获取模块信息：get_module

#### 请求

GET `https://example.com/api.php?mod=sidebar&act=get_module&id=name`

POST `https://example.com/api.php?mod=sidebar&act=get_module`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  若为 POST 请求：

  | 属性 | 类型   | 示例值 | 说明   |
  | ---- | ------ | ------ | ------ |
  | id   | string | name   | 模块id |

  示例：

  ```json
  {
    	"id": "name"
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
          "filename": "unnamed",
          "content": "内容",
          ...
      },
      "error": null
  }
  ```

  data 内容：

  | 属性          | 类型    | 示例值    | 说明                                      |
  | ------------- | ------- | --------- | ----------------------------------------- |
  | id            | int     | 1         | 模块id                                    |
  | name          | string  | 未命名    | 名称                                      |
  | filename      | string  | unnamed   | 模块文件名                                |
  | content       | string  | 内容      | 内容                                      |
  | html_id       | string  | module_1  | 包含该模块 HTML 内容的最外围元素的 DOM ID |
  | type          | string  | div 或 ul | 包含该模块 HTML 内容的最外围元素的标签    |
  | max_li        | int     | 10        | UL内LI的最大行数，在 Type 为 ul 是有效    |
  | is_hide_title | boolean | true      | 前台是否隐藏模块名称                      |
  | meta          | string  |           | 附加内容                                  |



### 修改模块：update_module

#### 请求

POST `https://example.com/api.php?mod=sidebar&act=update_module`

- Headers

  见通用请求头

- Cookies

  通用请求 Cookies

- Body

  | 属性          | 类型    | 示例值    | 说明                                      |
  | ------------- | ------- | --------- | ----------------------------------------- |
  | id            | int     | 1         | 模块id                                    |
  | name          | string  | 未命名    | 名称                                      |
  | filename      | string  | unnamed   | 模块文件名                                |
  | content       | string  | 内容      | 内容                                      |
  | html_id       | string  | module_1  | 包含该模块 HTML 内容的最外围元素的 DOM ID |
  | type          | string  | div 或 ul | 包含该模块 HTML 内容的最外围元素的标签    |
| max_li        | int     | 10        | UL内LI的最大行数，在 Type 为 ul 是有效    |
  | is_hide_title | boolean | true      | 前台是否隐藏模块名称                      |
| meta          | string  |           | 附加内容                                  |
  
  示例：
  
    ```json
  {
      "id": 1,
      "name": "未命名",
      "filename": "unnamed",
      "content": "内容",
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



### 删除模块：delete_module

#### 请求

POST `https://example.com/api.php?mod=sidebar&act=delete_module`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

- Body

  | 属性 | 类型 | 示例值 | 说明           |
  | ---- | ---- | ------ | -------------- |
  | id   | int  | 1      | 待删除的模块id |

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

  

### 获取模块列表：get_modules

可以通过指定模块代码来获取某种类型的模块。

模块代码对应表：

| 模块                                | 代码   |
| ----------------------------------- | ------ |
| 所有模块                            | all    |
| 系统模块                            | system |
| 用户自定义模块                      | user   |
| 主题和插件创建的模块                | app    |
| 主题 include 文件夹存储的文件型模块 | file   |

#### 请求

GET `https://example.com/api.php?mod=sidebar&act=get_modules&type=<模块代码>`

POST `https://example.com/api.php?mod=sidebar&act=get_modules`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

- Body

  若为 POST：
  
  | 属性 | 类型   | 示例值 | 说明     |
  | ---- | ------ | ------ | -------- |
  | type | string | all    | 模块代码 |
  
  示例：
  
  ```json
  {
    	"type": "all"
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
    	"data": [{
          "id": 1,
          "name": "未命名",
          "filename": "unnamed",
          "content": "内容",
          ...
      }],
      "error": null
  }
  ```

  data 为数组，数组元素内容参见获取模块信息接口。



### 设置侧栏模块：update

#### 请求

POST `https://example.com/api.php?mod=sidebar&act=update`

- Headers

  见通用请求头

- Cookies

  见通用请求 Cookies

- Body

  | 属性    | 类型   | 示例值             | 说明         |
  | ------- | ------ | ------------------ | ------------ |
  | id      | int    | 1~9                | 侧栏id       |
  | modules | string | archives\|favorite | 侧栏中的模块 |

  示例：

  ```json
  {
    	"id": 1,
    	"modules": "archives|favorite|link|misc"
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体

