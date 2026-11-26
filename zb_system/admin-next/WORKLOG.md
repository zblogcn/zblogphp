# admin-next 工作总结与后续规划

> 范围：`zb_system/admin-next/`、`utils/admin-next-src/`、`zb_users/plugin/BlockEditor/`、
> `tests/phpunit/`、`tests/e2e/` 与 `.github/workflows/admin-next.yml`。
> 本文是当前代码、发布配置与本地验证状态记录，不是 CI 报告。代码已落地、测试已发现、测试已通过是三个不同状态；
> 本地定向 E2E 已执行，完整 CI/全套 E2E 最终报告仍待运行。

## 1. 当前进度

| 阶段 | 状态 | 事实基线 |
|---|---|---|
| 0：前端工程与安全网 | 进行中 | 本地 frozen install、构建、产物/体积/归档校验、Composer validate 与 PHPUnit 已通过；完整 CI 报告待运行 |
| 1：SPA 壳与只读页面 | 已落地 | HashRouter、`__ZB__`、导航、仪表盘、官方列表/设置读取、无障碍与响应式外壳已实现 |
| 2：官方写操作 | 部分 | 文章/页面、分类标签、评论、附件、用户、模块、主题切换、插件启停、43 字段设置已实现；不等同旧页全部维护动作 |
| 3：插件/主题与编辑器接入 | 部分 | v2 清单、按需 ESM、共享 runtime、legacy 回退、编辑器契约、图片选择上传、应用管理入口已实现；组件包未发布 |
| 4：默认策略与发布加固 | 进行中 | 默认暂为 `ZC_ADMIN_UI=legacy`，显式 `next` 才启用 SPA；体积/归档/Composer 门禁已落地，本地关键 E2E 通过，完整 CI 待运行 |
| 5：兼容深化 | 部分 | 旧编辑 Hook 自动回退和核心旧库补列已有；新 Hook 插槽、高级编辑器、组件包等未完成 |

不再使用“默认进入新版”“自动补列由 SPA 执行”“评论审核等于评论编辑”“附件上传等于编辑器图片上传”
“XML capabilities 等于实际能力”“作业存在等于 E2E 通过”等表述。

## 2. 已落地的实现

### 2.1 默认入口、Schema 与兼容回退

- `zb_system/defend/option.php` 将 `ZC_ADMIN_UI` 默认设为 `legacy`；
  `AdminUiIsNext()` 只在显式 `next` 时返回 true，`ui=legacy` 可临时覆盖。
- `admin/index.php?force=1` 可强制停留旧后台；`admin-next/index.php?ui=legacy` 直接跳转到该入口。
- `c_system_updatedb.php` 的 `updatedb_migrate()` 幂等补齐 Post/Category/Tag/Member/Config 的历史缺失列；
  全部 DDL 成功后才推进 `ZC_LAST_VERSION`，没有自动 down migration。
- `bootstrap/schema.php` 只检查当前 `datainfo` 所需列，不执行 DDL：
  - SPA 缺列时返回 409 阻断页；
  - root 看到数据库升级修复链接，非 root 提示联系管理员；
  - 内部 API 写请求缺列返回 409，GET/HEAD/OPTIONS 保持只读。
- `bootstrap/compat.php` 检测当前已注册的 8 个旧编辑 Hook，并发布 `legacyEditRequired`、
  `legacyEditHooks`、`legacyEditPlugins`。
- `PostEditorPage` 在 `legacyEditRequired` 时自动跳转到带 `force=1` 的旧文章/页面编辑页，
  避免自定义字段丢失；这不是旧 Hook 的等价执行。

### 2.2 后端与内部 API

- `zb_system/admin-next/api.php`：内部 API，复用系统 mod/act；不受 `ZC_API_ENABLE` 影响，
  要求登录及 `admin`，按用户限流，不输出 runtime 调试块。
- 内部 API 校验 `Origin`/`Referer` 的 scheme/host/port，非安全方法统一执行强制 CSRF 校验。
- `bootstrap/shell.php`：输出 `window.__ZB__`，包含 host/entry/api/user/rights/nav/apps/editor/
  compat/appCentre/legacyPluginManageUrl/lang/csrf/options。
- `bootstrap/nav.php`：官方结构化菜单、旧左/顶部菜单解析、v2 应用菜单和应用管理入口。
- `bootstrap/apps.php`：采集已启用插件和当前主题的应用/编辑器清单，拒绝非法资源与越界菜单路由。
- `api/comment.php` 的 `api_comment_put()`：校验权限与 CSRF，原地更新 Content、Name、Email
  （API 还可接收 HomePage），保留所属文章、父子关系、AuthorID、时间、Meta、审核状态等。
- `api/system.php`：`$setting_keys` 共 43 项；`get_setting` / `save_setting` 使用同一白名单。
- `api/app.php`：插件/主题列表返回 `can_manage` 与 `manage_url`，支持单应用管理入口。

### 2.3 前端写操作

- 路由：仪表盘、文章、页面、分类、标签、评论、附件、用户、主题、模块、插件、设置、
  文章/页面编辑、插件宿主、404。
- 评论编辑弹窗保存走 `comment.put`，更新内容、作者名、邮箱；审核/删除仍走原接口。
- 设置页六组共 43 个字段，与服务端白名单一一对应；`ZC_BLOG_HOST` 只读并提供 forced legacy 链接。
- 文章编辑页向编辑器传入 `apiUpload("upload", "post", form)`，BlockEditor 图片按钮可选择文件上传并插入图片。
- 插件/主题页头与导航使用 AppCentre 管理地址；未启用 AppCentre 或没有管理页时回退
  `PluginMng&force=1`。应用中心本身不是 SPA 页面。
- 插件宿主：`import()` 按需加载，通过 import map 共享 React；mount 可返回清理函数或异步清理。
- 编辑器宿主：`mount/getHTML/setHTML/focus/destroy` 契约；无编辑器或加载失败时回退 textarea。

### 2.4 无障碍与响应式

- 跳转主内容链接、主内容焦点、语义化 `main`/`nav`、图标按钮可访问名称。
- 桌面侧栏键盘开关、折叠链接保留名称、`aria-expanded` / `aria-controls`。
- 移动抽屉支持键盘开关、Escape 关闭、焦点回转，关闭按钮触控尺寸至少 44px。
- 全局可见焦点、`prefers-reduced-motion`、深浅色/跟随系统主题。
- Playwright 已定义 320/375/768/1024/1440/1920px 横向溢出检查；测试定义不等于已通过。

## 3. BlockEditor 实际能力

当前 `zb_users/plugin/BlockEditor/dist/editor.js` 是 **contenteditable + `document.execCommand`**，
不是 ProseMirror。实际支持：

- H2/H3/正文、加粗、斜体、删除线、无序/有序列表、引用。
- 链接、`pre/code` 代码块、撤销/重做。
- 源码模式；源码/粘贴/回填/读出接入 HTML 白名单净化，限制标签、属性与 URL 协议。
- `<!--more-->` 与 `<hr class="more">` 的双向转换，包含接口返回的转义形式。
- 图片按钮选择文件，经宿主 `upload.post` 上传并插入图片；返回 URL 经协议白名单净化。

当前**不支持**：

- 图片拖拽/粘贴上传、媒体库、上传进度与失败重试。
- 表格、嵌入、代码高亮、块拖拽/嵌套、协同、事务模型。
- 本地草稿：插件安装配置虽有 `EnableLocalDraft`，当前编辑器没有实现对应逻辑。

`plugin.xml` 声明了 `rich-text,blocks,images,media,table,code,embed,source`，但 `capabilities`
只是清单元数据，宿主不据此提供能力，也不应据此宣称编辑器已完成上述功能。

## 4. 工程门禁现状

`.github/workflows/admin-next.yml` 定义三个作业：

| 作业 | 内容 | 写数据 |
|---|---|---|
| `frontend` | pnpm frozen lockfile 安装、typecheck、build、`verify:assets`、从仓库根目录执行产物零差异检查 | 否 |
| `phpunit` | PHP 8.4 下安装 Composer 开发依赖，运行 admin-next 与核心 PHPUnit | 否 |
| `e2e-isolated` | PHP 8.0 + 全新 SQLite 安装，启用 BlockEditor、植入隔离评论/用户，临时服务器上跑桌面/移动 Playwright | 仅此作业设置 `ZBP_E2E_WRITE=1` |

发布配置核对结果：

- `pnpm install --frozen-lockfile`：前端与 E2E 作业均已覆盖。
- `pnpm typecheck` / `pnpm build` / `pnpm verify:assets`：前端作业均已覆盖；
  `utils/admin-next-src/package.json` 另提供聚合 `verify`（build + verify:assets，build 自身包含 typecheck）。
- PHPUnit：Composer scripts `test:admin-next` / `test` 与 CI 两条 PHPUnit 命令均已定义。
- 隔离 E2E：`needs: [frontend, phpunit]`，一次性 runner、loopback 地址、全新 SQLite 安装和写入开关均已定义。
- 修正了产物零差异步骤的 Git 路径问题：该步骤显式从仓库根目录运行，避免 job 默认目录导致检查错误路径。
- 门禁定义存在不等于某次运行通过；本地关键定向 E2E 已执行，完整 CI/全套 E2E 报告仍待运行。

## 5. 已知问题与风险

| 级别 | 问题 | 影响与规避 |
|---|---|---|
| 高 | 完整 CI 与全套隔离 E2E 未最终重跑 | 本地关键定向 E2E 已通过，但发布前仍需全新 runner 的完整报告，不能把定向通过写成全套通过 |
| 中 | PHP 8.0 开发依赖兼容未单独证明 | 运行时最低 8.0；CI PHPUnit 使用 8.4，不能据此宣称 8.0 开发依赖兼容 |
| 中 | 旧表格行 Hook 缺失 | `Filter_Plugin_Admin_ArticleMng_Table` 追加列不在 SPA 列表显示；使用“旧版（含插件列）” |
| 中 | 旧编辑 Hook 仅自动回退 | SPA 不执行旧回调，也没有新结构化插槽；依赖旧 Hook 的编辑必须走 legacy |
| 中 | 编辑器仍非 ProseMirror | 无块事务、复杂结构保证、表格/嵌入/高亮/协同/本地草稿 |
| 中 | 图片上传仅选择文件链路 | 无拖拽/粘贴、媒体库、进度与重试 |
| 中 | 应用中心未 SPA 化 | 使用 AppCentre 外部管理页或 forced legacy 插件管理；安装/卸载/上传仍在旧流程 |
| 中 | 组件包未发布 | `@zblogphp/admin-ui` 不可用；插件需复用 runtime 或手写 ESM |
| 中 | 全套 E2E 最终报告待生成 | `comment.put` 保存、评论审核和 BlockEditor 图片上传已有独立断言并定向通过；仍需完整 CI 报告 |
| 低 | CSP/浏览器基线 | import map 与插件 ESM 需要较新浏览器；旧插件脚本仍依赖 legacy |

## 6. 本轮核对记录

本轮本地验证环境为 Windows + Node 24.22.3 + pnpm 10.30.3 + PHP 8.4.2；CI 前端使用 Node 22，
PHPUnit 使用 PHP 8.4，隔离 E2E 使用 PHP 8.0。本地通过不等于对应 CI 作业已经运行。

| 检查 | 状态 | 结果 |
|---|---|---|
| 默认 UI 代码与 PHPUnit 测试定义 | 已核对 | 默认 `legacy`，显式 `next` 才启用 SPA |
| 旧库补列与 Schema 阻断 | 已核对 | 核心 `updatedb()` 补列；SPA 只预检并阻断缺列 |
| 旧编辑 Hook 自动回退 | 已核对 | 8 个旧 Hook 任一项注册即跳转 forced legacy 编辑页 |
| 评论 `comment.put` | 已核对 | 前端保存与 API 原地更新路径存在 |
| 设置字段 | 已核对 | 服务端 43 项，SPA 43 项且唯一 |
| 编辑器图片上传 | 已核对 | 选择文件上传链路存在；拖拽/粘贴仍未实现 |
| 应用中心/管理入口 | 已核对 | AppCentre 管理地址与 forced legacy 回退存在 |
| 无障碍/响应式 | 已核对实现与测试定义 | 代码路径存在；定向浏览器验收通过，完整 CI 报告待运行 |
| Composer lock | 通过 | `composer validate --strict --no-check-publish` 与 `composer install --dry-run` 均通过；56 个依赖版本未变化 |
| 前端 frozen install | 通过 | `pnpm install --frozen-lockfile` 退出 0，lockfile 未变化 |
| 前端 typecheck | 通过 | `pnpm typecheck` 退出 0 |
| 前端 build / verify | 通过 | `pnpm build` 与 `pnpm verify` 均退出 0；`verify:assets` 报告 33 个 manifest 项、34 个文件、33 个可达入口 |
| 体积预算 | 通过 | 首屏静态 gzip 170,392 bytes（166.40KB）/ 180KB；最大单页 chunk 12,091 bytes（11.81KB）/ 80KB |
| 发布包边界 | 通过 | `git archive HEAD` 包含 assets 35 个文件；前端源码与 tests 均为 0 |
| 产物零差异 | 通过 | 重构建后 `git diff --exit-code -- zb_system/admin-next/assets` 与 `git status --porcelain` 均无输出 |
| admin-next PHPUnit | 通过 | PHP 8.4.2：83 tests / 781 assertions；有既有 Scheduler CronExpression nullable 参数弃用警告 |
| 核心 PHPUnit | 通过 | PHP 8.4.2：61 tests / 181 assertions；有同一既有 Scheduler 弃用警告 |
| Playwright frozen install / 用例发现 | 通过（仅发现） | `pnpm install --frozen-lockfile` 退出 0；`playwright test --list` 发现 3 个文件、56 个 desktop/mobile 用例 |
| 工作流 YAML | 通过 | Python PyYAML 解析 `.github/workflows/admin-next.yml` 成功 |
| 隔离定向写 E2E | 通过 | BlockEditor 图片上传 2/2；评论审核与评论管理 6/6；Desktop/Mobile、`--retries=0`、0 skip；数据库 SHA256 与基线一致、0 残留 |
| 全套隔离 E2E | 待最终重跑 | 最终修复前 47 passed / 2 failed / 7 skipped；两项失败已修复并定向通过，但完整最终报告尚未生成 |

## 7. 后续工作

### 阶段 4：发布加固（当前优先）

- [x] 同步 Composer lock，并保持锁定依赖版本不变。
- [ ] 在全新 runner 实际运行全套隔离 E2E，检查 skipped 项并保存报告。
- [x] 为评论 `comment.put` 保存与编辑器图片选择上传补独立 E2E 断言。
- [x] 验证发布包包含 `assets/` 且按约定排除 `utils/admin-next-src/` 与 `tests/`。
- [x] 增加壳首屏与单页 chunk 体积预算。

### 阶段 5：兼容与编辑器深化

- [ ] 定义旧编辑 Hook 的新插槽契约（侧栏字段、提交前改写、序列化）。
- [ ] 为旧表格行 Hook 设计结构化行数据契约，不解析旧 `<td>`。
- [ ] 完善旧库迁移的运维说明、失败恢复与 down migration 策略。
- [ ] 接通编辑器图片拖拽/粘贴上传、进度与失败处理。
- [ ] 块模型、表格、代码高亮、嵌入与短代码节点。
- [ ] 本地草稿新老编辑页互认。
- [ ] 发布 `@zblogphp/admin-ui` 与插件 Vite 模板。
- [ ] `AdminColor` 等外观插件迁移到 CSS 变量 preset。

## 8. 已修复的问题（根因备忘）

| 问题 | 根因 | 当前处理 |
|---|---|---|
| 编辑页/分类页反复请求选项 | effect 依赖包含内联函数，缓存/依赖设计不稳定 | 回调存 ref，依赖收敛，增加模块级缓存 |
| SPA 写操作 419 | CSRF 放行分支与强制校验路径不一致 | 内部 API 统一校验同源，非安全方法强制校验令牌 |
| `<!--more-->` 回填变成注释文本 | 接口返回转义形式 | `decodeMore()` 同时处理原始与转义形式 |
| 附件上传后列表不刷新 | React 合成事件中的文件集合被回收 | 先 `Array.from()` 快照，再主动 refetch |
| 删除接口收不到 body id | 删除接口只读 URL 参数 | CSRF 校验后统一回填 id |
| 列表异步渲染导致误判无数据 | 过早统计按钮/行数 | 先等待首行或目标控件，再判断数据状态 |
| CI 产物零差异检查漏检 | job 默认目录下使用了仓库根路径 | 零差异步骤显式 `working-directory: .` |
| 中文被破坏 | 非 UTF-8 的 PowerShell 写文件方式 | 使用 UTF-8 编辑器/工具，避免默认编码改写 |

## 9. 关键文件索引

| 位置 | 说明 |
|---|---|
| `zb_system/admin-next/IMPLEMENTATION_PLAN.md` | 当前基线、阶段、矩阵、门禁与回退 |
| `zb_system/admin-next/index.php` / `api.php` | SPA/API 入口与 Schema 阻断 |
| `zb_system/admin-next/bootstrap/*.php` | shell/nav/rights/apps/assets/api-compat/compat/schema |
| `zb_system/function/c_system_admin_ui.php` | 默认 UI 与 legacy 判定 |
| `zb_system/function/c_system_updatedb.php` | 旧库补列与版本推进 |
| `zb_system/api/comment.php` / `system.php` / `app.php` | 评论更新、43 字段设置、应用管理 |
| `utils/admin-next-src/src/router.tsx` | 全部 Hash 路由 |
| `utils/admin-next-src/src/lib/api.ts` | API 客户端与 multipart 上传 |
| `utils/admin-next-src/src/pages/post-editor.tsx` | Hook 回退与编辑器上传上下文 |
| `utils/admin-next-src/src/plugin-host/editor.ts` | 编辑器加载契约 |
| `utils/admin-next-src/scripts/verify-assets.mjs` | 产物/manifest 一致性门禁 |
| `zb_users/plugin/BlockEditor/dist/editor.js` | 编辑器实际实现 |
| `.github/workflows/admin-next.yml` | 前端、PHPUnit、隔离 E2E |
| `tests/phpunit/AdminNextSchemaTest.php` / `UpdateDbMigrationTest.php` | Schema 与旧库迁移回归 |
| `tests/phpunit/AdminNextCompatTest.php` / `CommentApiTest.php` / `AdminNextSettingsTest.php` | Hook、评论、设置回归 |
| `tests/e2e/specs/admin-next*.spec.ts` | 冒烟、CRUD、无障碍/响应式测试定义 |

## 10. 开发备忘

- 前端构建：`cd utils/admin-next-src && pnpm build`，产物落到 `zb_system/admin-next/assets/`。
- 聚合校验：`pnpm verify`（先 build，再 `verify:assets`）；提交前从仓库根目录确认产物无 diff。
- 本地站点：在仓库根目录执行 `php -S 127.0.0.1:8099 -t .`，确保站点配置地址与访问地址一致。
- 本地写 E2E：只能指向独立安装、独立数据库的隔离站点，并显式设置 `ZBP_E2E_WRITE=1`。
- 缺少 `ZBP_USER` / `ZBP_PASS` 或用例主动跳过时，跳过不是通过。
- 修改中文文件使用 UTF-8 工具，避免按系统默认编码重写。
