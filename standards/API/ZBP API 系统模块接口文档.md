# Z-BlogPHP API 系统模块接口文档

## 接口列表

### 获取系统信息：get_info

#### 请求

GET `https://example.com/zb_system/api.php?mod=system&act=get_info`

POST `https://example.com/zb_system/api.php?mod=system&act=get_info`

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
    	"data": {
          "environment": 1,
          "major_version": 7,
          "minor_version": 0,
          "build_version": 2330,
          ...
      },
      "error": null
  }
  ```

  data 内容：

  | 键               | 类型   | 示例值                      | 说明            |
  | ---------------- | ------ | --------------------------- | --------------- |
  | environment      | int    | Linux3.10.0; nginx1.18.0... | 系统环境        |
  | full_version     | string  | 1.7.0.2330 (Alpha); AppCentre2.53   | Z-BlogPHP 版本        |
  | articles         | int    | 2333                        | 文章总数        |
  | categories       | int    | 233                         | 分类总数        |
  | pages            | int    | 2333                        | 页面总数        |
  | tags             | int    | 23                          | 标签总数        |
  | comments         | int    | 23333                       | 评论总数        |
  | views            | int    | 2333333                     | 浏览总数        |
  | members          | int    | 23                          | 用户总数        |
  | theme            | sring  | default                     | 当前主题        |
  | xml_rpc          | string | http://...                  | XML-RPC协议地址 |


### 清空缓存并重新编译模板：statistic

#### 请求

GET `https://example.com/zb_system/api.php?mod=system&act=statistic`

POST `https://example.com/zb_system/api.php?mod=system&act=statistic`

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
    	"message": "操作成功",
    	"data": null,
      "error": null
  }
  ```
