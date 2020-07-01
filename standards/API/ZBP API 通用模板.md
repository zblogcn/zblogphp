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
| Authorization   | emhvdXppc2h1LTJjZjMwOWM3ODA...  | 客户端接受的语言代码 |

### Cookies

以下简称“通用请求 Cookies”：

| 键        | 示例值     | 域          | 路径 | 过期 |
| --------- | ---------- | ----------- | ---- | ---- |
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

根据客户端的身份验证以及服务端的状态返回内容。

例如：

```json
{
  	"message": "OK",
	"data": {},
	"error": null
}
```

data 内容由具体的接口决定，一些常见的范例如下：

如，某个用于添加内容的接口。

用 `posted` 布尔值表示是否添加成功。

`message` 传递相应消息，内容无规定。

其他操作以此类推。

```json
{
  	"message": "OK",
  	"data": {
      	"posted": true,
 		"message": "添加成功！"     	
    },
	"error": null
}
```

```json
{
  	"message": "OK",
  	"data": {
      	"deleted": false,
 		"message": "删除失败，该资源不存在！"     	
    },
	"error": null
}
```



| 操作结果属性 | 类型    | 示例值 | 说明          |
| ------------ | ------- | ------ | ------------- |
| posted       | boolean | true   | 添加操作      |
| updated      | boolean | true   | 更新/修改操作 |
| deleted      | boolean | true   | 删除操作      |
