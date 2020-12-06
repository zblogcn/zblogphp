# Z-BlogPHP API 用户模块接口文档

## 接口列表

### 用户登录：login

#### 请求

POST `https://example.com/zb_system/api.php?mod=member&act=login`

- Headers

  见通用请求头

- Cookies

  无

- Body

  | 属性     | 类型    | 示例值 | 说明             |
  | -------- | ------- | ------ | ---------------- |
  | username | string  | Chris  | 用户名           |
  | password | string  | 12345  | 密码             |
  | remember | boolean | true   | 是否记住登录状态 |

  示例：

  ```json
  {
    	"username": "Chris",
    	"password": "123456",
    	"remember": true
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  | 键       | 值         | 域          | 路径 | 过期 |
  | -------- | ---------- | ----------- | ---- | ---- |
  | username | Chris      | example.com | /    | -    |
  | token    | xxxxxxxxxx | example.com | /    | -    |

- Body

  示例：

  ```json
  {
      "code": 200,
    	"message": "OK",
    	"data": {
        "userid": 1,
        "username": "Chris",
        "nickname": "Chris",
        "level": 6
      },
      "error": null
  }
  ```

  data 内容：

  | 键       | 类型   | 示例值                        | 说明               |
  | -------- | ------ | ----------------------------- | ------------------ |
  | userid  | int | 1                             | 用户id             |
  | username | string | Chris                         | 响应使用的压缩算法 |
  | nickname | string | Sun, 23 Feb 2020 07:03:41 GMT | 用户昵称/别名      |
  | level    | int | 1                             | 用户级别           |



### 退出登录：logout

#### 请求

POST `https://example.com/zb_system/api.php?mod=member&act=logout`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  | 属性    | 类型   | 示例值 | 说明   |
  | ------- | ------ | ------ | ------ |
  | userid | int | 1      | 用户id |

  示例：

  ```json
  {
    	"userid": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体



### 新增用户：post

#### 请求

POST `https://example.com/zb_system/api.php?mod=member&act=post`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  | 属性     | 类型   | 示例值          | 说明                                                         |
  | -------- | ------ | --------------- | ------------------------------------------------------------ |
  | username | string | Chris           | 用户名                                                       |
  | password | string | 123456          | 密码                                                         |
  | email    | string | 123@example.com | 邮箱                                                         |
  | level    | int | 1               | 用户级别，1：管理员，2：网站编辑，3：作者，4：协作者，5：评论者，6：游客 |
  | status   | int | 0               | 用户状态，0：正常，1：审核，2：禁止                          |
  | alias    | string | Chris           | 别名/昵称                                                    |
  | homepage | string | 123@example.com | 主页                                                         |
  | intro    | string | ...             | 个人简介                                                     |
  | template | string | index           | 模板                                                         |

  示例：

  ```json
  {
    	"username": "Chris",
    	"password": "123456",
    	"emial": "123@example.com",
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



### 获取用户信息：get

#### 请求

GET `https://example.com/zb_system/api.php?mod=member&act=get&id=123`

POST `https://example.com/zb_system/api.php?mod=member&act=get`

或者省略 act：

GET `https://example.com/zb_system/api.php?mod=member&id=123`

POST `https://example.com/zb_system/api.php?mod=member`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  若为 POST 方法：

  | 属性    | 类型   | 示例值 | 说明   |
  | ------- | ------ | ------ | ------ |
  | userid | int | 1      | 用户id |

  示例：

  ```json
  {
    	"userid": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  data 内容：

  | 属性     | 类型   | 示例值          | 说明                                                         |
  | -------- | ------ | --------------- | ------------------------------------------------------------ |
  | username | string | Chris           | 用户名                                                       |
  | password | string | 123456          | 密码                                                         |
  | email    | string | 123@example.com | 邮箱                                                         |
  | level    | int | 1               | 用户级别，1：管理员，2：网站编辑，3：作者，4：协作者，5：评论者，6：游客 |
  | status   | int | 0               | 用户状态，0：正常，1：审核，2：禁止                          |
  | alias    | string | Chris           | 别名/昵称                                                    |
  | homepage | string | 123@example.com | 主页                                                         |
  | intro    | string | ...             | 个人简介                                                     |
  | template | string | index           | 模板                                                         |

  示例：

  ```json	
  {
      "code": 200,
    	"message": "OK",
    	"data": {
        	"username": "Chris",
          "password": "123456",
          "emial": "123@example.com",
          ...
      },
      "error": null
  }
  ```

  

### 修改用户信息：update

#### 请求

POST `https://example.com/zb_system/api.php?mod=member&act=update`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  | 属性     | 类型   | 示例值          | 说明                                                         |
  | -------- | ------ | --------------- | ------------------------------------------------------------ |
  | userid  | int | 123             | 用户id                                                       |
  | username | string | Chris           | 新用户名                                                     |
  | password | string | 123456          | 新密码                                                       |
  | email    | string | 123@example.com | 新邮箱                                                       |
  | level    | int | 1               | 新用户级别，1：管理员，2：网站编辑，3：作者，4：协作者，5：评论者，6：游客 |
  | status   | int | 0               | 新用户状态，0：正常，1：审核，2：禁止                        |
  | alias    | string | Chris           | 新别名/昵称                                                  |
  | homepage | string | 123@example.com | 新主页                                                       |
  | intro    | string | ...             | 新个人简介                                                   |
  | template | string | index           | 新模板                                                       |

  示例：

  ```json
  {
    	"userid": 123,
    	"username": "Chris",
    	"password": "123456",
    	"emial": "123@example.com",
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



### 删除用户：delete

#### 请求

POST `https://example.com/zb_system/api.php?mod=member&act=delete`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  | 属性    | 类型   | 示例值 | 说明   |
  | ------- | ------ | ------ | ------ |
  | userid | int | 1      | 用户id |

  示例：

  ```json
  {
    	"userid": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  见通用响应体

### 获取用户列表：list

#### 请求

GET/POST `https://example.com/zb_system/api.php?mod=member&act=list`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  | 属性    | 类型   | 示例值 | 说明   |
  | ------- | ------ | ------ | ------ |
  | level | int | 1      | 用户等级 |
  | status | int | 1      | 用户状态 |
  
  支持的过滤器：limit、offset、page、perpage、sortby、order  
  其中，sortby 支持的字段有：  
  | URL sortby 参数 | 对应数据表属性 | 说明 |
  | --- | --- | --- |
  | id  | mem_ID | id |
  | create_time | mem_CreateTime | 用户创建时间 |
  | post_time | mem_PostTime | 用户提交时间 |
  | update_time | mem_UpdateTime | 用户更新时间 |
  | articles | mem_Articles | 用户文章数量 |
  | pages | mem_Pages | 用户页面数量 |
  | comments | mem_Comments | 用户评论数量 |
  | uploads | mem_Uploads | 用户附件数量 |

  示例：

  ```json
  {
    	"level": 1,
        "status": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  返回的是数组，每个元素为一个用户，

  示例：

  ```json
  {
      "code": 200,
    	"message": "OK",
    	"data": [{
        	"userid": 123,
        	"username": "Chris",
        	"alias": "Chris",
        	"level": 1,
        	"articles": 2333,
        	"pages": 233,
        	"comments": 23,
        	"attas": 2
      }],
      "error": null
  }
  ```

  data 数组元素：

  | 属性     | 类型   | 示例值 | 说明                                                         |
  | -------- | ------ | ------ | ------------------------------------------------------------ |
  | userid  | int | 123    | 用户id                                                       |
  | username | string | Chris  | 用户名                                                       |
  | level    | int | 1      | 用户级别，1：管理员，2：网站编辑，3：作者，<br />4：协作者，5：评论者，6：游客 |
  | alias    | string | Chris  | 别名/昵称                                                    |
  | articles | int    | 2333   | 新个人简介                                                   |
  | pages    | int    | 233    | 页面总数                                                     |
  | comments | int    | 23     | 评论总数                                                     |
  | attas    | int    | 2      | 附件总数                                                     |



### 获取用户权限：get_auth

#### 请求

GET `https://example.com/zb_system/api.php?mod=member&act=get_auth&id=123`

POST `https://example.com/zb_system/api.php?mod=member&act=get_auth`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  若为 POST 方法：

  | 属性    | 类型   | 示例值 | 说明   |
  | ------- | ------ | ------ | ------ |
  | userid | int | 1      | 用户id |

  示例：

  ```json
  {
    	"userid": 1
  }
  ```

#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  | 属性     | 类型    | 示例值 | 说明                                                         |
  | -------- | ------- | ------ | ------------------------------------------------------------ |
  | username | string  | Chris  | 用户名                                                       |
  | level    | int  | 1      | 用户级别，1：管理员，2：网站编辑，3：作者，4：协作者，5：评论者，6：游客 |
  | login    | boolean | true   | 别名/昵称                                                    |
  | articles | boolean | true   | 登出                                                         |
  | pages    | boolean | true   | 验证                                                         |
  | admin    | boolean | true   | 管理（后台权限）                                             |

  示例：

  ```json
  {
      "code": 200,
    	"message": "OK",
    	"data": {
        	"username": "Chris",
        	"level": 1,
        	"login": true,
        	"logout": true,
        	...
      },
      "error": null
  }
  ```

  属性太多不便列出。

  更多请参考 `zb_system/function/c_system_misc.php:155` ， `zb_system/function/c_system_base.php:158`。

  