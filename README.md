Z-BlogPHP
=============

Z-BlogPHP是由RainbowSoft Studio团队开发的博客程序，基于高效的PHP环境，体积小，速度快，支持比较大的数据量。

Z-BlogPHP有着强大的可定制性、丰富的插件接口和独立的主题模板，方便开发者和用户定制与优化。

##运行环境
- Web Server: IIS、Apache、Lighttpd、Nginx、Kangle
- PHP 5.2 - 5.6（PHP 7 未做完整测试，基本保证可用。） / HHVM 3 
- MySQL / MariaDB / SQLite / PostgreSQL

##安装说明
> 首先请确保网站目录拥有755权限。

1. 上传Z-BlogPHP程序到网站目录
2. 打开http://你的网站/，进入安装界面
3. 建立数据库
   - 选择MySQL数据库，请输入空间商为您提供的MySQL帐号密码等信息
   - 选择SQLite，请确保服务器支持SQLite，安装程序将在点击下一步后自动创建SQLite数据库文件
4. 填写你为站点设置的管理员账号密码，务必使用强口令账号
5. 点击下一步，安装成功，进入网站

> 安装完成后请删除zb_install文件夹。

> 若要使用开发版本，请先[下载稳定版](http://www.zblogcn.com/zblogphp/)并安装，然后再用GitHub内的文件进行覆盖，方可使用。

##开发文档
请访问我们的wiki站点：http://wiki.zblogcn.com/doku.php?id=zblogphp

##BUG反馈
请直接提交Issue告知我们，谢谢。