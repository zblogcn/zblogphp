# admin-next 实施计划与工程门禁

> 状态：当前已实现基线（2026-11-27 对照 SPA、内部 API、前端产物、PHPUnit 与 CI 配置核对）。
> 根目录 `1792152278422-z-blogphp-shadcn-ui.md` 保留为历史设计稿；当前阶段、功能矩阵、验收门禁与回退策略以本文为准。
> 本文区分“代码已落地”和“验证已通过”。未运行、已跳过或仅有测试定义的项不计为通过，隔离 E2E 结果必须以对应隔离运行及 Playwright 输出为准；本地完整验证不代表 GitHub Actions 已运行。

## 1. 当前基线

| 领域 | 当前实现 | 边界 |
|---|---|---|
| 默认后台 | `ZC_ADMIN_UI` 默认值为 `next`；`zb_system/cmd.php?act=admin` 默认进入 `zb_system/admin-next/index.php` | 新安装和缺失选项时启用 SPA；已有显式 `legacy` 保持不变，未知值仍安全回退 legacy |
| 新版入口 | `zb_system/admin-next/index.php` 提供 React 19 + TypeScript + Vite 7 的 HashRouter SPA | 登录页仍是旧版；直接访问 SPA 入口不等同于更改默认后台 |
| 旧后台回退 | `admin/index.php?force=1` 可强制停留 legacy；`admin-next/index.php?ui=legacy` 直接跳转到该入口 | `force=1` 必须保留，否则旧入口在 `ZC_ADMIN_UI=next` 时会再次跳回 SPA |
| 数据入口 | `zb_system/admin-next/api.php` 复用系统 API 的 mod/act、鉴权、限流与响应 | 独立于公开 `zb_system/api.php`，不受 `ZC_API_ENABLE` 影响；要求登录及 `admin` 权限 |
| 同源写请求 | 内部 API 校验 `Origin`/`Referer` 的 scheme/host/port；非安全方法统一执行强制 CSRF 校验 | `X-ZBP-Admin-Spa` 只是前端标识，不是免校验凭证 |
| 引导数据 | `window.__ZB__` 包含用户、权限、导航、应用/编辑器清单、兼容诊断、语言、CSRF 与少量选项 | 前端权限只控制呈现，服务端仍逐请求鉴权 |
| 旧库补列 | 核心 `updatedb_migrate()` / `updatedb()` 按历史版本幂等补齐缺失列，全部 DDL 成功后才推进 `ZC_LAST_VERSION` | 补列发生在数据库升级流程，不是 SPA 每次加载时自动执行；没有自动 down migration |
| Schema 阻断 | SPA 入口检查 Post/Category/Tag/Member 当前 `datainfo` 所需列；缺列时返回 409 阻断页 | 内部 API 的 GET/HEAD/OPTIONS 保持只读，所有写请求缺列时返回 409；root 可看到数据库升级修复链接 |
| 旧编辑 Hook 兼容 | 当前已注册 `Filter_Plugin_Edit_Begin/End/SubMenu/Response/Response2–5` 任一项时，`legacyEditRequired=true` | 文章/页面编辑自动跳到带 `force=1` 的旧编辑页；SPA 不执行这些旧回调，也没有等价的新插槽 |
| 官方写操作 | 文章/页面、分类/标签、评论、附件、用户、模块、主题切换、插件启停、设置保存均有实现 | 不等同 legacy 全量功能；安装/卸载/上传应用、缓存/统计维护等仍使用旧入口 |
| 评论更新 | 前端通过 `comment.put` 更新评论内容、作者名称和邮箱 | API 可另行接收 `HomePage`；所属文章、父子关系、AuthorID、时间、Meta、审核状态等不会被该接口改写 |
| 编辑器图片上传 | 文章编辑页向编辑器上下文传入 `apiUpload("upload", "post", form)`；BlockEditor 图片按钮可选择文件、上传并插入图片 | 已接通“选择文件上传”；拖拽上传、粘贴上传、媒体库与上传进度仍未实现 |
| 设置 | 六个分组共 43 个服务端白名单字段，SPA 与 `$setting_keys` 一一对应 | `ZC_BLOG_HOST` 只读并提供 forced legacy 链接；其余旧页维护动作不在 SPA |
| 应用中心/管理入口 | 导航和插件/主题页头提供应用管理入口；启用且可管理的 AppCentre 使用其服务端管理地址 | 该入口是 external/legacy；未启用 AppCentre 或没有管理页时回退到 `PluginMng&force=1`。应用中心本身未 SPA 化 |
| 无障碍/响应式 | 跳转主内容链接、可见焦点、ARIA 名称/状态、移动抽屉焦点回转、44px 关闭按钮、减少动效、响应式侧栏与布局已实现 | Playwright 已定义桌面/移动及 320–1920px 溢出检查，但本次核对不宣称隔离 E2E 已通过 |
| 构建产物 | 源码在 `utils/admin-next-src/`，产物提交到 `zb_system/admin-next/assets/` | PHP 壳读取 `manifest.json`；代码与 hash 产物必须同版本提交 |
| PHP 版本 | `composer.json` 与安装器要求 PHP >= 8.0；SPA 入口对低版本显示可读错误页 | 不支持在 PHP < 8.0 上通过 legacy 回退运行当前版本 |
| 插件协议 | `plugin.xml` / `theme.xml` 使用 `<admin version="2">`，支持菜单、SPA、legacy 与 editor 声明 | 只采集已启用插件和当前主题；`@zblogphp/admin-ui` 尚未发布 |

### 1.1 当前明确限制（不阻断本次代码发布）

- 登录、密码找回、应用中心的 SPA 化未完成；插件/主题包安装、卸载、上传也未迁移。
- 旧表格行 Hook（如 `Filter_Plugin_Admin_ArticleMng_Table`）追加的列不在 SPA 列表渲染；文章/页面列表提供“旧版（含插件列）”入口。
- 旧编辑 Hook 采用检测后自动回退，不是兼容执行；新的结构化编辑 Hook 插槽尚未实现。
- BlockEditor 实际是 `contenteditable + document.execCommand`，不是 ProseMirror；没有块事务、块拖拽/嵌套、表格编辑、嵌入、代码高亮、协同或本地草稿。
- XML 中的 `capabilities` 只是元数据，不能作为功能存在或测试通过的证据。
- Composer lock、发布包边界和首屏/chunk 体积预算已有自动门禁；PHP 8.0.30 全新隔离站点完整 E2E 已通过（53 passed / 0 failed / 7 expected skips，0 残留）。本次为本地完整验证，GitHub Actions 远程报告仍需在推送后查看。
- PHPUnit CI 使用 PHP 8.4；PHP 8.0 的完整运行时已由隔离 E2E 覆盖，但 PHP 8.0 开发依赖兼容未单独证明。

## 2. 阶段 0–5

状态含义：**已完成（本地）**表示本轮本地门禁已通过；**已落地**表示代码路径存在；**部分**表示只覆盖矩阵内的子集；**进行中**表示门禁或发布工作尚未完成；**计划**表示不能作为发布承诺。

| 阶段 | 目标 | 当前状态 | 退出条件 |
|---|---|---|---|
| 0：基线与安全网 | 固定 PHP/Node/pnpm 基线，保留 legacy 回退，建立可重复构建与测试门禁 | **已完成（本地）**：Node 22、frozen install、typecheck、build、产物/体积/归档校验、Composer validate、admin-next/core PHPUnit 与完整隔离 E2E 均通过 | frozen lockfile 安装、typecheck、build、`verify`、admin-next/core PHPUnit 全部通过；隔离 E2E 有完整报告且意外跳过已处理 |
| 1：SPA 壳与只读页面 | Hash 路由、`__ZB__`、导航、官方列表/设置读取、响应式与无障碍外壳 | **已落地** | 官方菜单深链可渲染；权限由服务端复核；manifest 注入正确；无障碍/响应式检查在隔离站点实际通过 |
| 2：官方写操作与编辑 | 文章/页面、分类标签、评论、附件、用户、模块、主题、插件、设置 | **部分**：矩阵内官方写路径已实现，包含 `comment.put`、43 字段设置和编辑器图片选择上传；不等同旧后台全量功能 | 已覆盖路径有自动化门禁；缺列、旧编辑 Hook 和设置边界均有可验证回退 |
| 3：插件与编辑器接入 | v2 清单、按需 bundle、legacy 回退、编辑器契约、应用管理入口 | **部分**：协议、宿主、legacy 回退、编辑器上传上下文与应用管理入口已落地；BlockEditor 能力有限，组件包未发布 | 插件入口/样式/管理/回退可验证；编辑器能力以实现为准；发布可复用的组件包与模板 |
| 4：发布加固 | 可复现产物、隔离 E2E、发布包边界、性能与浏览器基线 | **已完成（本地）**：manifest/产物、体积、归档边界、Composer lock 与三作业 CI 已落地；PHP 8.0.30 完整隔离 E2E 为 53 passed / 0 failed / 7 expected skips，0 残留 | 本地门禁全绿且无意外跳过；重构建产物零差异；发布包包含 `assets/` 并按约定排除前端源码；推送后远程 CI 结果另行确认 |
| 5：兼容深化 | 旧 Hook 新插槽、旧库迁移深化、块编辑、插件组件生态 | **部分**：旧编辑 Hook 自动回退和核心旧库补列已有；新插槽、高级编辑器、组件包等未完成 | 先完成新插槽与数据回退验证，再扩大编辑器能力；不得绕过阶段 4 门禁 |

## 3. 功能覆盖矩阵

“已覆盖”只表示当前 SPA/API 有对应实现，不表示与 legacy 完全等价，也不表示某次测试已经通过。

| 功能 | SPA/API 基线 | 当前限制或回退 |
|---|---|---|
| 默认入口 | 默认 `ZC_ADMIN_UI=next`；显式 `legacy` 或 `ui=legacy` 回退旧后台 | 已有 `legacy` 设置在升级后保持不变；直接访问 SPA 入口不会改写默认设置 |
| 仪表盘 | 已覆盖：统计、环境信息 | 旧版统计维护/缓存操作未迁移 |
| 文章列表 | 已覆盖：列表、搜索、分页、单选/批量删除、新建/编辑入口 | 旧表格行 Hook 追加列不显示；提供 `ArticleMng&force=1` 的“旧版（含插件列）”入口 |
| 页面列表 | 与文章同一列表组件，`Type=1` | 同上，旧入口为 `PageMng&force=1` |
| 文章/页面编辑 | 已覆盖：新建、回填、保存、草稿/发布、删除及标题、正文、分类、标签、状态、时间、别名、模板、置顶、禁评、摘要 | 当前注册的旧编辑 Hook 会触发自动 legacy 编辑页；未检测到 Hook 时使用 SPA，但仍无旧插件字段的新插槽 |
| 分类 | 已覆盖：列表、新建、编辑、删除、筛选、父分类 | 旧库缺列由数据库升级流程补齐；升级未完成时 SPA 阻断，写 API 返回 409 |
| 标签 | 已覆盖：列表、新建、编辑、删除、筛选 | 同上 |
| Schema 预检 | 覆盖 Post/Category/Tag/Member 当前 `datainfo` 列 | 只检查不执行 DDL；root 可跳转 `updatedb.php?updatedb=1`，非 root 提示联系管理员 |
| 评论 | 已覆盖：列表、单条/批量审核、单条/批量删除、编辑弹窗；保存走 `comment.put` | UI 更新内容、作者名、邮箱；保留关联与不可变字段；旧评论页的全部字段/插件 Hook 仍不等价 |
| 附件 | 已覆盖：列表、多文件上传、自动改名、删除、图片预览 | 附件管理不等同媒体库 |
| 编辑器图片 | BlockEditor 图片按钮通过宿主 `upload.post` 上传并插入图片，URL 经协议白名单净化 | 无拖拽/粘贴上传、媒体库、进度与失败重试；非 ProseMirror |
| 用户 | 已覆盖：列表、新建、编辑、删除、等级/状态/密码字段 | 删除当前用户受前端保护；服务端权限仍是最终判定 |
| 主题 | 已覆盖：列表、切换、管理页入口 | 安装、卸载、上传主题包未迁移 |
| 模块 | 已覆盖：自定义模块新建/编辑/删除，系统模块可编辑 | 系统模块删除被禁用；不等同旧模块页全部操作 |
| 插件 | 已覆盖：列表、启用、停用、管理页入口 | 安装、卸载、上传插件包未迁移；停用有确认 |
| 应用中心/管理入口 | 已覆盖：AppCentre 管理地址与 forced legacy 插件管理回退；插件/主题 API 暴露 `can_manage` / `manage_url` | 不是 SPA 内应用中心；AppCentre 未启用、未安装或无管理页时使用旧插件管理 |
| 网站设置 | 六个分组、43 个白名单字段 | `ZC_BLOG_HOST` 只读；只保存服务端白名单字段；缓存/统计等维护动作仍在 legacy |
| 插件/主题 SPA | 已覆盖：v2 声明、菜单、按需 ESM、共享 React、样式、legacy 入口/加载失败回退 | 动态 import 前要求精确命中声明菜单且权限通过；`@zblogphp/admin-ui` 尚未发布 |
| BlockEditor | 基础富文本、标题/段落、粗斜体/删除线、列表/引用、链接、`pre/code`、源码、撤销/重做、`<!--more-->` 转换、HTML 白名单净化、选择图片上传 | 非 ProseMirror；无块事务、拖拽/嵌套、表格、嵌入、高亮、协同、本地草稿 |
| 无障碍 | 跳转链接、主内容焦点、命名/状态、抽屉焦点管理、键盘操作、可见焦点、减少动效 | PHP 8.0.30 完整隔离 E2E 已覆盖桌面/移动并通过 |
| 响应式 | 桌面折叠侧栏、移动抽屉、安全区、响应式表格/表单/编辑布局 | 320/375/768/1024/1440/1920px 溢出检查已在完整隔离 E2E 通过 |
| 登录/找回/应用中心 | 未 SPA 化 | 使用 legacy 或外部应用中心入口 |

## 4. 验收门禁

### 4.1 前端与产物

在 `utils/admin-next-src/` 执行，Node 22+、pnpm 10.30.3：

```bash
pnpm install --frozen-lockfile
pnpm typecheck
pnpm build
pnpm verify:assets
# 或一次执行构建与产物校验（build 已包含 typecheck）
pnpm verify
```

从仓库根目录确认重构建没有改动已提交产物：

```bash
git diff --exit-code -- zb_system/admin-next/assets
git status --porcelain -- zb_system/admin-next/assets
```

`verify:assets` 至少拒绝：

1. 缺少 `index.html` 或 `src/plugin-host/runtime.ts` 清单入口。
2. 清单路径为空、绝对路径、反斜杠、`..` 穿越或解析到产物目录之外。
3. `file` / `css` 指向的文件不存在、不是普通文件或为空。
4. `imports` / `dynamicImports` 引用不存在的清单键。
5. 产物目录存在未登记文件，或存在无法从入口/runtime 到达的 JS 清单项。

CI 的零差异步骤必须从仓库根目录执行；在 `utils/admin-next-src/` 下直接使用 `zb_system/admin-next/assets` 路径会因 Git 路径前缀而检查不到真实产物目录。

### 4.2 PHP 与 PHPUnit

```bash
composer install --prefer-dist --no-progress --no-interaction
vendor/bin/phpunit -c tests/phpunit-admin-next.xml
vendor/bin/phpunit -c tests/phpunit.xml
```

也可使用 Composer scripts：

```bash
composer test:admin-next
composer test
```

- 运行时最低版本为 PHP 8.0。
- `composer.lock` 已同步，`composer validate --strict --no-check-publish` 与 `composer install --dry-run` 均通过；56 个锁定依赖版本未变化。
- CI PHPUnit 作业使用 PHP 8.4，不能据此宣称 PHP 8.0 开发依赖兼容。
- PHP 8.0.30 运行时路径已由全新 SQLite 隔离安装的完整 E2E 覆盖；53 passed / 0 failed / 7 expected skips，0 残留。

### 4.3 Playwright 与写操作隔离

CI 工作流：`.github/workflows/admin-next.yml`。

| 作业 | 内容 | 写数据 |
|---|---|---|
| `frontend` | pnpm frozen lockfile 安装、typecheck、build、`verify:assets`、体积/归档边界校验、产物零差异 | 否 |
| `phpunit` | PHP 8.4 下安装 Composer 开发依赖，运行 admin-next 与核心 PHPUnit | 否 |
| `e2e-isolated` | 前两项成功后，在全新 runner 上用 PHP 8.0 + SQLite 安装站点，启用 BlockEditor，植入隔离评论/用户，启动 `127.0.0.1:8099` 临时服务器，再运行桌面/移动 Playwright | 仅此作业设置 `ZBP_E2E_WRITE=1` |

- `ZBP_E2E_WRITE=1` 只设置在 `e2e-isolated` 作业；runner 是一次性环境，不使用生产或长期站点地址/密码。
- 本地写 E2E 必须使用独立安装、独立数据库和独立上传目录；严禁对生产或现有站点设置该变量。
- 测试定义、`playwright test --list` 或作业存在都不等于执行通过。跳过不是通过，必须查看报告中的 skipped 项。
- CI 初始化在 `SaveOption()` 前设置 `ZC_LOGIN_VERIFY_ENABLE=false`、`ZC_COMMENT_TURNOFF=false`，避免全新 CLI 安装的登录验证码或评论开关阻断 E2E。
- 最终完整隔离 E2E 使用 Node 22.20.0、PHP 8.0.30、全新 SQLite 站点、Desktop Chrome + Pixel 7，共 60 个用例：53 passed / 0 failed / 7 expected skips；使用 `--retries=0`。
- 7 个跳过均为项目专属用例（桌面侧栏/超宽屏与移动抽屉的跨项目跳过）；评论审核改为每个项目独立创建 fixture，避免并行操作共享种子评论。
- 测试后扫描数据库全部文本列无 `E2E-*` fixture，上传目录无额外文件，种子评论保持已审核，记录为 0 残留。

### 4.4 发布门禁

发布前逐项确认：

- [x] Node 22 本地前端构建、产物、体积、归档边界、Composer validate 与 PHPUnit 门禁通过。
- [x] PHP 8.0.30 全新隔离站点完整 E2E：53 passed / 0 failed / 7 expected skips，0 残留。
- [x] `assets/manifest.json` 与同目录 JS/CSS 同提交，无旧 hash 残留，重构建零差异。
- [x] 发布包包含 `zb_system/admin-next/assets/`，并排除 `utils/admin-next-src/` 与 `tests/`。
- [x] 已用 `MIGRATION.md` 中的可靠回退路径验证 legacy 可访问，包括 `force=1` 与 `ui=legacy`。
- [x] 已评估旧库补列结果、旧编辑 Hook、旧表格行 Hook 和 43 字段设置边界，并记录为非阻断限制。
- [ ] 推送后查看 GitHub Actions 三个作业的远程报告；本轮本地完整验证不冒充远程 CI 已运行。
- [ ] 实际部署前由运维备份数据库、`zb_users/c_option.php`、上传目录与当前插件/主题配置。

## 5. 回退策略

### 5.1 界面级回退（无需改代码）

| 场景 | 可靠操作 | 说明 |
|---|---|---|
| 默认行为 | `ZC_ADMIN_UI=next` | 新安装和缺失选项时进入 SPA；已有显式 `legacy` 继续使用旧后台 |
| 临时进入旧后台 | `/zb_system/admin/index.php?act=admin&force=1` | `force=1` 阻止旧入口自动跳回 SPA |
| 从 SPA 入口回退 | `/zb_system/admin-next/index.php?ui=legacy` | 直接跳转到 `admin/index.php?force=1` |
| 从命令入口回退 | `/zb_system/cmd.php?act=admin&ui=legacy` | `ui=legacy` 参与 `AdminHomeUrl()` 判定 |
| 长期回退 | 设置 → 全局 → 后台界面 → 旧版后台（表格） | 写入 `ZC_ADMIN_UI=legacy`；也可在旧后台直接修改 |
| 旧编辑 Hook | 打开 SPA 文章/页面编辑时自动跳转 | 当前注册任一项旧编辑 Hook 即回退到带 `force=1` 的旧编辑页，避免自定义字段丢失 |
| 旧表格行 Hook | 文章/页面列表 →“旧版（含插件列）” | 仅解决官方列无法渲染插件列的问题，不是全局开关 |
| 单个插件页面 | 页面中的“旧版页面”，或 bundle 加载失败时跳转 `legacy` | 只影响声明了 `legacy` 的应用 |

### 5.2 数据与 Schema 回退

1. 升级前备份数据库；`updatedb()` 会执行真实 DDL，虽然没有自动 down migration，仍必须按不可逆结构变更处理。
2. 旧库补列由数据库升级流程完成；SPA 自身不执行 DDL。缺列时页面和写请求会被阻断，root 可先备份再运行升级修复。
3. `updatedb()` 只在全部迁移语句成功后推进 `ZC_LAST_VERSION`；中途失败可能已执行部分早期 DDL，此时应恢复升级前数据库备份，而不是继续写入。
4. 回滚代码前先切 `ZC_ADMIN_UI=legacy`，停止新增 SPA 写入。
5. 代码与 `zb_system/admin-next/assets/` 必须来自同一个已知良好版本，不要混用旧 PHP 壳与另一版本 manifest/hash 产物。
6. 数据库与上传文件存在引用关系，恢复数据库时需同步恢复相应上传目录备份。
7. 回滚后验证登录、legacy 页面、文章/评论读取、编辑器图片引用和插件启用状态。

### 5.3 不支持的回退

- PHP < 8.0：当前版本的安装器与 `composer.json` 均不满足要求；错误页中的旧后台链接不是版本支持承诺。
- 旧编辑 Hook：自动回退只能避免进入不兼容的 SPA 编辑页，不能恢复已经由其他路径保存的数据；发现字段丢失应立即停止写入并检查备份。
- 旧库缺列：legacy 不是数据修复方案，必须备份后运行数据库升级或恢复升级前数据库。
- 非 ProseMirror 编辑器：不能把 XML `capabilities` 或按钮存在当作块模型、协同、表格等能力证明。

## 6. 关键文件与验收来源

| 位置 | 用途 |
|---|---|
| `zb_system/admin-next/IMPLEMENTATION_PLAN.md` | 当前基线、阶段、矩阵、门禁与回退 |
| `zb_system/admin-next/index.php` / `api.php` | SPA 与内部 API 入口、Schema 阻断 |
| `zb_system/admin-next/bootstrap/schema.php` | 必需列检查、409 阻断页与写请求冲突 |
| `zb_system/admin-next/bootstrap/compat.php` | 旧编辑 Hook 诊断与自动回退数据 |
| `zb_system/admin-next/bootstrap/nav.php` / `shell.php` | 导航、应用管理入口、兼容诊断与引导数据 |
| `zb_system/function/c_system_admin_ui.php` / `defend/option.php` | 默认 next、显式 legacy 与 `ui=legacy` 回退判定 |
| `zb_system/function/c_system_updatedb.php` | 旧库幂等补列与版本推进 |
| `zb_system/api/comment.php` / `api/system.php` / `api/app.php` | `comment.put`、43 字段设置、应用管理元数据 |
| `utils/admin-next-src/src/pages/post-editor.tsx` | 旧编辑 Hook 回退与编辑器上传上下文 |
| `utils/admin-next-src/src/pages/comments.tsx` / `settings.tsx` / `apps.tsx` | 评论编辑、设置与应用管理页面 |
| `utils/admin-next-src/src/components/app-shell.tsx` / `index.css` | 无障碍与响应式外壳 |
| `utils/admin-next-src/scripts/verify-assets.mjs` | 产物/manifest 一致性门禁 |
| `zb_users/plugin/BlockEditor/dist/editor.js` | 编辑器实际能力与图片上传 |
| `tests/phpunit/AdminNextSchemaTest.php` / `UpdateDbMigrationTest.php` | Schema 阻断与旧库补列回归 |
| `tests/phpunit/AdminNextCompatTest.php` / `CommentApiTest.php` / `AdminNextSettingsTest.php` | Hook 回退、评论更新与 43 字段一致性 |
| `tests/e2e/specs/admin-next-accessibility.spec.ts` | 无障碍/响应式测试定义（不是通过报告） |
| `.github/workflows/admin-next.yml` | 前端、PHPUnit 与隔离 E2E 门禁 |
| `MIGRATION.md` / `PLUGINS.md` / `WORKLOG.md` | 迁移、插件接入与当前进度/验证记录 |
