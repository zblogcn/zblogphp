# Z-BlogPHP API 设置模块接口文档

## 接口列表

### 获取所有网站设置：get

#### 请求

GET `https://example.com/api.php?mod=setting&act=get`

POST `https://example.com/api.php?mod=setting&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=setting`

POST `https://example.com/api.php?mod=setting`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  无


#### 响应

- Headers

  见通用响应头

- Cookies

  无

- Body

  data 内容：

  | 属性                       | 类型   | 示例值          | 说明                                                         |
  | -------------------------- | ------ | --------------- | ------------------------------------------------------------ |
  | zc_blog_host               | string | https://example.com/           | 固定域名                                                 |
  | zc_permanent_domain_enable | string | false           | 是否固定域名                                                |
  | zc_blog_name               | string | 网站 | 网站名称                                                   |
  | zc_blog_subname            | string | 副标题            | 用户级别，1：管理员，2：网站编辑，3：作者，4：协作者，5：评论者，6：游客 |
  | zc_blog_copyright          | string | All Rights Reserved | 版权说明                      |
  | zc_time_zone_name          | string | Asia/Shanghai +08:00 | 时区                                              |

  示例：

  ```json	
  {
    	"message": "OK",
    	"data": {
          "zc_blog_host": "网址",
          "zc_permanent_domain_enable": false,
          "zc_blog_name": "网站名称",
          "zc_blog_subname": "网站副标题",
          "zc_blog_copyright": "版权说明",
          "zc_time_zone_name": "Asia/Shanghai +08:00",
        	...
      },
      "error": null
  }
  ```

  配置项太多不便列出。

  具体设置项参考 ZBP 后台的”网站设置“页面。

  代码参考  `zb_system/function/c_system_admin.php:2067` 。



### 更新网站设置：update

#### 请求

GET `https://example.com/api.php?mod=setting&act=get`

POST `https://example.com/api.php?mod=setting&act=get`

或者省略 act：

GET `https://example.com/api.php?mod=setting`

POST `https://example.com/api.php?mod=setting`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  传什么就更新什么。

  | 属性                       | 类型   | 示例值               | 说明                                                         |
  | -------------------------- | ------ | -------------------- | ------------------------------------------------------------ |
  | zc_blog_host               | string | https://example.com/ | 固定域名                                                     |
  | zc_permanent_domain_enable | string | false                | 是否固定域名                                                 |
  | zc_blog_name               | string | 网站                 | 网站名称                                                     |
  | zc_blog_subname            | string | 副标题               | 用户级别，1：管理员，2：网站编辑，3：作者，4：协作者，5：评论者，6：游客 |
  | zc_blog_copyright          | string | All Rights Reserved  | 版权说明                                                     |
  | zc_time_zone_name          | string | Asia/Shanghai +08:00 | 时区                                                         |

  示例：

  ```json
  {
      "zc_blog_host": "网址",
      "zc_permanent_domain_enable": false,
      "zc_blog_name": "网站名称",
      "zc_blog_subname": "网站副标题",
      "zc_blog_copyright": "版权说明",
      "zc_time_zone_name": "Asia/Shanghai +08:00",
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

  