
Z-BlogPHP
=============

Z-BlogPHP 是由 Z-Blog 社区提供的博客程序，一直致力于给国内用户提供优秀的博客写作体验。从 2005 年起发布第一版，至今已有 16 年的历史，是目前国内为数不多的持续提供更新的开源 CMS 系统之一。

我们的目标是使用户沉浸于写作、记录生活，不需要关注繁琐的设置等，让用户专注于创作。对于用户而言，它简单易用，体积小，速度快，支持数据量大。对开发者而言，它又有着强大的可定制性、丰富的插件接口和精美的主题模板。

期待 Z-BlogPHP 能成为您写博、建站的第一选择。

## 安全漏洞说明 / Security Vulnerabilities

For security vulnerabilities, please contact us via ``contact#rainbowsoft.org`` and do not post on GitHub Issue.

提交安全漏洞，请直接联系我们：contact#rainbowsoft.org。您也可以通过[先知安全服务平台](https://xianzhi.aliyun.com)、[360补天](https://loudong.360.cn/)、[国家信息安全漏洞共享平台](http://www.cnvd.org.cn)等平台向我们提交。**请不要在 GitHub Issue 等公开领域发布和漏洞有关的信息，更不要百度翻译成英文后向我们提交，我们看得懂汉字。**

致某些安全初学者：在管理员权限下，通过后台主题管理/插件管理上传 PHP 文件**不是漏洞**！自从 2017 年起，CNVD 已经给我们报了至少五次相关“漏洞”（如 CNVD-2019-12772、CNVD-2019-43601)。请不要用这种东西浪费我们和 CNVD 的时间，同时通过刷低水平“漏洞”骗到的 CNVD 编号对你的简历没有任何帮助。

## 社区说明
1. 使用交流及开发建议，请转向[Z-Blog 论坛](http://bbs.zblogcn.com/)；
1. 开发文档，参看[Z-Wiki](http://wiki.zblogcn.com/doku.php?id=zblogphp)；
1. 提交功能 BUG，请在论坛内，或直接在 GitHub Issue 内提交；
1. 欢迎 Pull Request，如果你喜欢，请为我们点一个 Star :)


## 运行环境
- Windows / Linux / macOS and so on...
- IIS / Apache / nginx / Lighttpd / Kangle / Tengine / Caddy and so on...
- PHP 5.2 - 8.0
- MySQL 5+ / MariaDB 10+ / SQLite 3

另：开发版内有 PostgreSQL 支持，需要手动启动，欢迎测试。

## 安装说明
首先请确保网站目录拥有 755 权限。若要使用 GitHub 内的开发版本，请先[下载稳定版](http://www.zblogcn.com/zblogphp/)并安装，然后再用 GitHub 内的文件进行覆盖，方可使用。

1. 上传 Z-BlogPHP 程序到网站目录
2. 打开 http://你的网站/ ，进入安装界面
3. 建立数据库
   - 选择 MySQL 数据库，请输入空间商为您提供的 MySQL 帐号密码等信息
   - 选择 SQLite，请确保服务器支持 SQLite，安装程序将在点击下一步后自动创建 SQLite 数据库文件
   - 选择 Postgresql 数据库，请 Postgresql 相关的主机名数据库名帐号密码等信息
4. 填写你为站点设置的管理员账号密码，务必使用强口令账号
5. 点击下一步，安装成功，进入网站

安装完成后请删除 zb_install 文件夹。对于 GitHub 上的开发版本，请删除 standards、tests、utils 等文件夹。

## 代码标准及说明

[代码标准](standards)

## 开源协议

Z-BlogPHP 项目，基于[The MIT License](http://opensource.org/licenses/mit-license.php)协议开放源代码。
