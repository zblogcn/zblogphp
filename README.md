Z-BlogPHP
=============

Z-BlogPHP是由RainbowSoft Studio团队开发的博客程序,基于高效的PHP环境，体积小，速度快，支持MySQL(MariaDB)和SQLite数据库。
Z-BlogPHP有着强大的可定制性、丰富的插件接口和独立的主题模板，方便开发者和用户的定制与优化。

##运行环境
- IIS、Apache、Lighttpd、Nginx、Kangle等Web服务器
- PHP 5.2及以上版本的PHP环境
- MySQL或SQLite数据库环境

##安装说明
> 首先请确保网站目录拥有755权限。

1. 上传Z-BlogPHP程序到网站目录
1. 打开http://你的网站/，进入安装界面
1. 建立数据库
   - 选择MySQL数据库:输入空间商为您提供的MySQL帐号密码等信息
   - 选择SQLite，请确保服务器支持SQLite，安装程序将在点击下一步后自动创建SQLite数据库文件
1. 填写你为站点设置的管理员账号密码，务必使用强口令账号
1. 点击下一步，安装成功，进入网站

> 安装完成后请删除zb_install文件夹。
> 需要使用开发版本的同学，请先下载正式版并安装，然后再把GitHub内的文件进行覆盖，方可使用。

##BUG反馈
请直接提交Issue告知我们，谢谢。