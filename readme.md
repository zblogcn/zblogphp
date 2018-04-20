Z-BlogPHP
=============
[![StyleCI](https://styleci.io/repos/24038803/shield?branch=master)](https://styleci.io/repos/24038803)
[![License](https://poser.pugx.org/zblogcn/zblogphp/license)](https://packagist.org/packages/zblogcn/zblogphp)

Z-BlogPHP是由Z-Blog社区提供的博客程序，一直致力于给国内用户提供优秀的博客写作体验。从2006年起发布第一版，至今已有13年的历史，是目前国内为数不多的持续提供更新的开源CMS系统之一。我们的目标是使用户沉浸于写作、记录生活，不需要关注繁琐的设置等，让用户专注于创作。对于用户而言，它简单易用，体积小，速度快，支持数据量大。对开发者而言，它又有着强大的可定制性、丰富的插件接口和独立的主题模板。期待Z-BlogPHP能成为您写博客的第一选择。

## 社区说明
1. 使用交流及开发建议，请转向[Z-Blog论坛](http://bbs.zblogcn.com/)；
1. 开发文档，参看[Z-Wiki](http://wiki.zblogcn.com/doku.php?id=zblogphp)；
1. 提交功能BUG，请在论坛内，或直接在GitHub Issue内提交；
1. 提交安全漏洞，请直接联系我们：contact#rainbowsoft.org，或是通过[先知安全服务平台](https://xianzhi.aliyun.com)、[360补天](https://loudong.360.cn/)等平台向我们提交。
1. 欢迎Pull Request，如果你喜欢，请为我们点一个Star :)


## 运行环境
- Windows / Linux / macOS and so on...
- IIS / Apache / nginx / Lighttpd / Kangle / Tengine / Caddy and so on...
- PHP 5.2 - 7.2 / HHVM 3 
- MySQL 5+ / MariaDB 10+ / SQLite 3

## 安装说明
> 首先请确保网站目录拥有755权限。

> 若要使用开发版本，请先[下载稳定版](http://www.zblogcn.com/zblogphp/)并安装，然后再用GitHub内的文件进行覆盖，方可使用。

1. 上传Z-BlogPHP程序到网站目录
2. 打开http://你的网站/，进入安装界面
3. 建立数据库
   - 选择MySQL数据库，请输入空间商为您提供的MySQL帐号密码等信息
   - 选择SQLite，请确保服务器支持SQLite，安装程序将在点击下一步后自动创建SQLite数据库文件
4. 填写你为站点设置的管理员账号密码，务必使用强口令账号
5. 点击下一步，安装成功，进入网站

> 安装完成后请删除zb_install文件夹。

## 代码标准及说明

[代码标准](standards)


## 开源协议

Z-BlogPHP项目，基于[The MIT License](http://opensource.org/licenses/mit-license.php)协议开放源代码。
