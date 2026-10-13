# Z-BlogPHP v1.8.0 文件级变更说明

## 比较范围

- 起点：`1.8-base`（`cef68e2d`，2025-11-04，1.7.4.3430 / Shelter）
- 终点：`v1.8.0.4020`（`4d37ec69`，2026-09-20，1.8.0.4020 / Martis）
- 提交数：119
- Git 统计：263 个文件，新增 195、修改 63、删除 4、重命名 1；共新增 109,381 行、删除 8,052 行。
- 统计使用 `git diff --name-status`；新增量主要来自后台界面模板、样式和静态资源，不等同于核心 PHP 代码增长。

## 变更概览

这段差异将系统推进到 1.8.0，主要工作包括：引入全新的 admin2 后台及双主题机制（`backend-toyean`、`backend-legacy`）、重构 Web 安装流程并新增 CLI 安装器、引入 ZBA 应用打包与版本读取工具链、修复内容访问鉴权与上传处理若干问题。期间还跨版本同步了 1.7 → 1.7.5 → 1.8 的多项修复与稳定性更新。

`composer.json` 将最低 PHP 版本设为 `>=7.3`。版本文件中 `ZC_VERSION_*` 更新为 `1.8.0.4020`、代号改为 `Martis`；`ZC_LAST_VERSION` 仍为 `173000`，本次版本号变化没有伴随数据库版本号提升。

## 一、后台入口与主题机制

涉及文件：

- `zb_system/admin/index.php`、`zb_system/admin/admin_header.php`
- `zb_system/admin2/index.php`、`zb_system/admin2/login.php`
- `zb_system/admin2/function/{admin2_function,admin2_misc,admin2_view}.php`
- `zb_system/admin2/backend-toyean/`（less + 模板 + 脚本 + 字体 + 图标）
- `zb_system/admin2/backend-legacy/`（sass + 模板 + 脚本 + 图片）
- `zb_system/admin2/script/c_admin_js_add.js`
- `zb_system/admin2/backend-toyean/plugin/aieditor/`
- `zb_system/defend/backend/*`（19 个后台页面模板与登录、布局）
- `zb_system/defend/actions.php`、`zb_system/defend/thanks.php`
- `zb_system/css/admin2.css`、`zb_system/css/install.css`（由 `admin3.css` 重命名）
- `zb_system/css/jquery-ui.custom.css`
- `zb_system/script/{jquery-3.7.1.min.js,jquery-4.0.0.min.js,jquery-ui.custom.min.js,jquery.tagto.js,...}`

要点：

1. `admin/index.php` 将后台请求统一转至 `admin2/index.php`，并保留原操作参数；新增 `admin2` 子系统，包含两套独立后台主题（`backend-toyean` 走 less、`backend-legacy` 走 sass），各自配套 rollup 构建工程（`dev_rollup/`、`pnpm-workspace.yaml`、`rollup.config.mjs`）与分层组件库。
2. `backend-toyean` 内置 AI 编辑器插件（`plugin/aieditor/`）、DINPro 字体与全套 SVG 图标（菜单/卡片/暗色变体），并提供完整的 `template/` 页面：ArticleEdt/Mng、CategoryEdt/Mng、CommentMng、MemberEdt/Mng、ModuleEdt/Mng、PageMng、PluginMng、SettingMng、SiteInfo、TagEdt/Mng、ThemeMng、UploadMng、index、login、layout_*。
3. `defend/backend/` 下新增对应的 19 个后台页面控制器与登录、布局，作为旧后台兼容层或默认主题。
4. 后台资源本地化：新增 jQuery 3.7.1 与 jQuery 4.0.0；重写滑动开关样式，调试模式下显示后台主题切换开关。
5. `ZBPDK` 的 `BlogConfig.js` 博客配置项与右键菜单相关更新。

## 二、安装器与首次配置

涉及文件：

- `zb_install/index.php`、`zb_install/install.php`、`zb_install/cli.php`
- `zb_install/install/index.html`、`zb_install/install/style/*`
- `zb_system/cmd.php`

要点：

1. 抽出 `ZbpInstaller` 安装服务供 Web 安装器和 CLI 共用，负责参数归一化、数据库配置、建表、初始化管理员和保存配置。
2. CLI 安装器（`cli.php`）支持 SQLite、MySQL、PostgreSQL 等类型，可从参数或环境变量读取密码；SQLite 数据库名支持自动生成。
3. Web 安装流程同步重构，并修复禁用 JavaScript 时主题选择未提交的问题；选择 os2020 主题时默认启用 API 接口。
4. 安装器增加 `QUERY_STRING` 兜底处理，避免未定义索引警告。
5. 简化模块创建逻辑，移除 `Setup4` 函数中的冗余代码。
6. 安装样式文件更名为 `install.css`。

## 三、CLI、应用打包与发布工作流

涉及文件：

- `utils/get_version.php`、`utils/pack_zba.php`、`utils/zba_toolkit.php`、`utils/readme.md`
- `.github/bundle-apps.json`
- `.github/workflows/upload-pack.yml`、`.github/workflows/php-cs-fixer.yml`、`.github/workflows/phpunit.yml`

要点：

1. 新增 CLI 版本读取工具（`get_version.php`）。
2. 新增面向 CLI 与 GitHub Actions 的 ZBA 应用打包工具（`pack_zba.php`），支持 gzip、指定输出路径、详细输出和 GitHub Actions 输出变量。
3. 新增 `zba_toolkit.php` 用于 CLI 解压应用，并优化解压路径。
4. 预装应用清单移到 `.github/bundle-apps.json`，CI 改用新脚本下载预装插件。
5. 发布工作流更新：`upload-pack.yml` 加 `target_commitish`、限定在 `master` 与 `dev/zbp18` 分支触发。
6. 引入 `php-cs-fixer.yml` 实现对提交代码的自动格式化。

## 四、内容访问鉴权、API 与上传

涉及文件：

- `zb_system/api/post.php`
- `zb_system/function/c_system_route.php`、`zb_system/function/c_system_api.php`
- `zb_system/function/c_system_event.php`
- `zb_system/function/lib/base/upload.php`

要点：

1. 文章 API 和普通文章路由收紧非公开内容的作者判断：必须是已登录且 ID 匹配的作者，或具备相应权限的用户（修复 #397）。可避免作者删除后 `AuthorID = 0` 与匿名用户 ID 同为 0，导致草稿/待审核文章/页面绕过鉴权。
2. `DelUpload()` 不再仅从 GET 读取附件 ID，兼容 POST 提交。
3. `SaveFile()` 与 `SaveBase64File()` 在扩展名校验失败时明确返回 `false`，避免错误呈现成功结果。
4. `ApiResponseRaw()` 改用 `ob_clean()` 清理输出缓冲，避免没有活动缓冲区时调用 `ob_end_clean()` 报错。

## 五、访客 IP、登录与系统加载

涉及文件：

- `zb_system/function/c_system_common.php`
- `zb_system/function/c_system_admin_function.php`
- `zb_system/login.php`、`zb_system/admin/admin_header.php`
- `zb_system/function/c_system_base.php`

要点：

1. `GetGuestIP()` 处理代理头中的逗号分隔地址、去除空白并校验 IP 格式；配置页面同步改进 CDN/IP 来源选项的显示（修复 #396）。
2. 主题和插件的 `include.php` 改为 `include_once` 加载，避免重复加载。
3. 后台安全响应头中的 `X-Frame-Options` 从 `DENY` 改为 `SAMEORIGIN`，允许同源页面嵌入。
4. 修复旧 `login.php` 中 image 路径问题；侧边作者模块的用户名改为用户静态名。
5. 切换语言后统计模块不实时更新的问题修复。

## 六、模板系统与其他核心

涉及文件：

- `zb_system/function/c_system_admin.php`、`zb_system/function/c_system_admin_function.php`
- `zb_system/function/c_system_function.php`、`zb_system/function/c_system_plugin.php`
- `zb_system/function/c_system_debug.php`、`zb_system/function/c_system_misc.php`
- `zb_system/function/lib/{template,app,modulebuilder,zblogphp}.php`
- `zb_system/function/lib/{base/module,base/post}.php`

要点：

1. 调整 `CheckTemplate/BuildTemplate`，删除冗余的 `Filter_Plugin_Zbp_BuildTemplate_End`，合并繁冗的 `BuildTemplate` 相关函数。
2. 修复搜索结果 URL 中出现 `<strong>` 标签的问题。
3. 修复 2020/12/24 在 `tag_edit.php` 触发的 BUG。
4. 新增 `$zbp_option` 全局变量，取消对 `$option` 的直接引用。
5. `function/lib/zbpai.php` 新增 AI 相关能力。

## 七、语言与文案

涉及文件：

- `zb_users/language/{en,zh-cn,zh-tw}.php`
- `zb_system/defend/en.php`、`zb_system/defend/thanks.php`、`zb_system/defend/error.php`
- `zb_system/defend/default/{module,module-authors,module-navbar}.php`

要点：

统一中文/英文文案，将「官方论坛」更名为「开发者社区」；同步更新默认侧边模块、感谢页与错误页文案。

## 八、工程化与规范

涉及文件：

- `.editorconfig`、`.php-cs-fixer.dist.php`、`.php-cs-fixer.cache`
- `composer.json`、`composer.lock`
- `.gitattributes`、`.gitignore`
- `.vscode/{settings,extensions}.json`
- `.github/workflows/*`
- `standards/*`、`readme.md`、`readme.txt`、`SECURITY.md`

要点：

1. 新增 `.editorconfig`：PHP 文件缩进统一为 4 空格。
2. 接入 `.php-cs-fixer.dist.php`，采用 PSR-12 规范；移除 `phpcs.xml` 与 `.github/workflows/phpcs.yml`。
3. `.gitattributes` 调整打包导出忽略项，`.gitignore` 忽略 ZIP 文件。
4. VSCode 工作区配置（推荐扩展与设置）。
5. PHPUnit 工作流、Release Action 同步升级。
6. `standards/编码规范.md` 更新示例采用 PSR-12；`standards/API接口说明.md` 改名为 `standards/API_接口说明.md`（带中文括号），并在 README 中以链接形式指向外部主题模板与 API 文档。

## 删除文件

| 文件                                  | 替换方案                                  |
| ------------------------------------- | ----------------------------------------- |
| `.github/workflows/phpcs.yml`         | 改用 `.github/workflows/php-cs-fixer.yml` |
| `.github/workflows/update-jquery.yml` | 改为静态提交 jQuery 资源                  |
| `phpcs.xml`                           | 改用 `.php-cs-fixer.dist.php`             |
| `standards/API接口说明.md`            | `standards/API_接口说明.md`               |
| `zb_system/css/admin3.css`            | 重命名为 `zb_system/css/install.css`      |

## 新增文件摘录

- `.editorconfig`、`.php-cs-fixer.dist.php`、`.github/bundle-apps.json`
- `.vscode/{settings,extensions}.json`
- `standards/API_接口说明.md`
- `utils/{get_version,pack_zba,zba_toolkit}.php`
- `zb_install/{cli.php,install.php}`
- `zb_system/admin2/`（index、login、function、backend-toyean、backend-legacy）
- `zb_system/admin2/backend-toyean/{template/*,script/*,style/*,plugin/aieditor/*,dev_rollup/*}`
- `zb_system/admin2/backend-legacy/{template/*,script/*,style/*,images/*,dev_rollup/*}`
- `zb_system/defend/backend/*`（19 个控制器 + 4 个 layout + login/index/thanks）
- `zb_system/function/lib/zbpai.php`
- `zb_system/script/jquery-3.7.1.min.js`、`zb_system/script/jquery-4.0.0.min.js`

## 升级与运维关注点

1. 运行环境需满足 PHP 7.3 或更高版本。
2. 后台入口及主题机制发生变化（`admin/index.php` 转至 `admin2/index.php`）；依赖后台模板、样式或入口路径的定制内容应重点回归。
3. CLI 安装器应使用安全的密码传递方式（环境变量或交互输入），并确认目标数据库和 SQLite 文件路径。
4. 数据库版本常量仍为 `173000`；部署升级前仍应按实际站点插件、主题和数据库状况备份并验证。
5. 后台资源本地化的 jQuery 已升至 3.7.1 / 4.0.0，依赖旧版 jQuery 特性的扩展需验证兼容性。
6. 内容鉴权收紧后，曾依赖作者删除绕过鉴权读取草稿的脚本/客户端应停止工作，这是预期行为。
7. 后台 `X-Frame-Options` 由 `DENY` 放宽至 `SAMEORIGIN`，如站点曾依赖此头做额外防护需评估影响。
