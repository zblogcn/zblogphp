# Z-BlogPHP API 客户端调用

网页的 JS 脚本、移动 APP 、小程序及 PC 软件等客户端向服务器发送带有参数的 HTTP 请求，服务端根据请求类型和参数完成数据操作，并根据结果返回 HTTP 响应内容，至此完成一次 API 的调用。

## 数据格式

直接上例子，用户登录。

#### 客户端请求

GET `https://example.com/zb_system/api/index.php?mod=user&act=login`

- Headers

  | 参数            | 值                              |
  | --------------- | ------------------------------- |
  | Content-Type    | application/json; charset=utf-8 |
  | Accept-Encoding | gzip, deflate                   |
  | User-Agent      | Mozilla/5.0                     |
  | Accept-Language | zh-cn                           |

- Cookies

- Body

  ```json
  {
    	"username": "Chris",
    	"password": "123456",
    	"recaptcha": "2430"
  }
  ```

#### 服务端响应

- HTTP 状态码

  200 OK

- Headers

  | 参数             | 值                              |
  | ---------------- | ------------------------------- |
  | Content-Type     | application/json; charset=utf-8 |
  | Content-Encoding | gzip                            |
  | Date             | Sun, 23 Feb 2020 07:03:41 GMT   |

- Cookies

  设置 Cookies：

  | 键       | 值         | 域          | 路径 | 过期 |
  | -------- | ---------- | ----------- | ---- | ---- |
  | username | Chris      | example.com | /    | -    |
  | token    | xxxxxxxxxx | example.com | /    | -    |

- Body

  ```json
  {
    	"message": "成功",
    	"data": {
        "user": {
          "id": 1,
          "username": "Chris",
          "alias": "Chris",
          "level": 6
        },
        "token": "emhvdXppc2h1LTJjZjMwOWM3ODA2NTY1ODQxOTQ1NjhlY2QyNTBmNmI1ZDk4M2FkNjNjMDEwMDIyYTk2YmUzYmI4NjBiOGNkNWIxNTkzNjg3MDk1"
      },
      "error": null
  }
  ```



## 代码示例

酝酿中。。。