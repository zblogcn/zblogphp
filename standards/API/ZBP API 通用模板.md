# Z-BlogPHP API 通用模板

## 请求

POST `https://example.com/api.php`

### Headers

以下简称“通用请求头”：

| 参数            | 示例值                          | 说明                 |
| --------------- | ------------------------------- | -------------------- |
| Content-Type    | application/json; charset=utf-8 | 客户端发送的内容类型 |
| Accept-Encoding | gzip, deflate                   | 客户端接受的压缩算法 |
| User-Agent      | Mozilla/5.0                     | -                    |
| Accept-Language | zh-cn                           | 客户端接受的语言代码 |

### Cookies

以下简称“通用请求 Cookies”：

| 键        | 示例值     | 域          | 路径 | 过期 |
| --------- | ---------- | ----------- | ---- | ---- |
| username  | Chris      | example.com | /    | -    |
| token     | xxxxxxxxxx | example.com | /    | -    |
| addinfo   | xxxxxxxxxx | example.com | /    | -    |
| http304ok | 1          | example.com | /    | -    |
| timezone  | 8          | example.com | /    | -    |

### Body

无



## 响应

### Headers

以下简称“通用响应头”：

| 参数             | 示例值                          | 说明               |
| ---------------- | ------------------------------- | ------------------ |
| Content-Type     | application/json; charset=utf-8 | 响应内容的类型     |
| Content-Encoding | gzip                            | 响应使用的压缩算法 |
| Date             | Sun, 23 Feb 2020 07:03:41 GMT   | 响应的时间         |

### Cookies

无

### Body

以下简称“通用响应体”。

根据客户端的身份验证以及服务端的状态返回内容，code 和 message 目前是与 HTTP 状态码对应的。

| code | message               | 说明                                                     |
| ---- | --------------------- | -------------------------------------------------------- |
| 200  | OK                    | 服务器成功返回用户请求的数据或者操作                     |
| 400  | INVALID REQUEST       | 用户发出的请求有错误，服务器没有进行新建或修改数据的操作 |
| 401  | UNAUTHORIZED          | 表示用户没有权限（令牌、用户名、密码错误）               |
| 403  | FORBIDDEN             | 表示用户得到授权（与401错误相对），但是访问是被禁止的    |
| 404  | NOT FOUND             | 用户发出的请求针对的是不存在的记录，服务器没有进行操作   |
| 500  | INTERNAL SERVER ERROR | 服务器发生错误，用户将无法判断发出的请求是否成功         |
| 503  | SERVICE UNAVAILABLE   | 由于临时的服务器维护或者过载，服务器当前无法处理请求     |

例如：

```json
{
  	"code": 200,
  	"message": "OK",
  	"data": {}
}
```

data 内容由具体的接口决定，一些常见的范例如下：

如，某个用于添加内容的接口。

用 `posted` 布尔值表示是否添加成功。

`message` 传递相应消息，内容无规定。

其他操作以此类推。

```json
{
  	"code": 200,
  	"message": "OK",
  	"data": {
      	"posted": true,
 				"message": "添加成功！"     	
    }
}
```

```json
{
  	"code": 200,
  	"message": "OK",
  	"data": {
      	"deleted": false,
 				"message": "删除失败，该资源不存在！"     	
    }
}
```



| 操作结果属性 | 类型    | 示例值 | 说明          |
| ------------ | ------- | ------ | ------------- |
| posted       | boolean | true   | 添加操作      |
| updated      | boolean | true   | 更新/修改操作 |
| deleted      | boolean | true   | 删除操作      |