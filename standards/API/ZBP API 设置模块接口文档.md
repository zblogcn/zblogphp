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

  data 内容：（具体参见后台“网站设置”）

  | 属性                       | 类型   | 示例值          | 说明                                                         |
  | -------------------------- | ------ | --------------- | ------------------------------------------------------------ |
  | ZC_BLOG_HOST               | string | https://example.com/           | 固定域名                                                 |
  | ZC_PERMANENT_DOMAIN_ENABLE | string | false           | 是否固定域名                                                |
  | ZC_BLOG_NAME               | string | 网站 | 网站名称                                                   |
  | ZC_BLOG_SUBNAME            | string | Good luck to you!            | 副标题 |
  | ZC_BLOG_COPYRIGHT          | string | All Rights Reserved | 版权说明                      |
  | ZC_TIME_ZONE_NAME          | string | Asia/Shanghai +08:00 | 时区                                              |

  示例：

  ```json	
  {
      "code": 200,
    	"message": "OK",
    	"data": {
          "ZC_BLOG_HOST": "https://example.com/",
          "ZC_PERMANENT_DOMAIN_ENABLE": false,
          "ZC_BLOG_NAME": "网站名称",
          "ZC_BLOG_SUBNAME": "网站副标题",
          "ZC_BLOG_COPYRIGHT": "版权说明",
          "ZC_TIME_ZONE_NAME": "Asia/Shanghai +08:00",
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

POST `https://example.com/api.php?mod=setting&act=update`

- Headers

  见通用请求头

- Cookies

  见通用请求Cookies

- Body

  传什么就更新什么，具体参见后台“网站设置”。

  | 属性                       | 类型   | 示例值          | 说明                                                         |
  | -------------------------- | ------ | --------------- | ------------------------------------------------------------ |
  | ZC_BLOG_HOST               | string | https://example.com/           | 固定域名                                                 |
  | ZC_PERMANENT_DOMAIN_ENABLE | string | false           | 是否固定域名                                                |
  | ZC_BLOG_NAME               | string | 网站 | 网站名称                                                   |
  | ZC_BLOG_SUBNAME            | string | Good luck to you!            | 副标题 |
  | ZC_BLOG_COPYRIGHT          | string | All Rights Reserved | 版权说明                      |
  | ZC_TIME_ZONE_NAME          | string | Asia/Shanghai +08:00 | 时区                                              |

  示例：

  ```json
  {
      "ZC_BLOG_HOST": "https://example.com/",
      "ZC_PERMANENT_DOMAIN_ENABLE": false,
      "ZC_BLOG_NAME": "网站名称",
      "ZC_BLOG_SUBNAME": "Good luck to you!",
      "ZC_BLOG_COPYRIGHT": "版权说明",
      "ZC_TIME_ZONE_NAME": "Asia/Shanghai +08:00",
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

  