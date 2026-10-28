# admin-next 工作总结与后续规划

> 分支：`admin-next`（自 `master` 切出）　最后整理：2026-10-28
> 范围：`zb_system/admin-next/`（新 SPA 后台）、`utils/admin-next-src/`（前端源码）、
> `zb_users/plugin/BlockEditor/`（编辑器插件）、`tests/`（单元 + e2e）

## 进度

- [x] 阶段 0：前端工程与构建管线、`__ZB__` 引导数据、导航采集、内部 API、壳与路由
- [x] 阶段 1：只读页面（仪表盘 / 文章 / 页面 / 分类 / 标签 / 评论 / 附件 / 用户 / 主题 / 模块 / 插件 / 设置）
- [x] 阶段 2A：写操作（设置保存、批量删除、附件上传、评论审核、插件启停、主题切换）
- [x] 阶段 3：插件/主题 SPA 接入（`plugin.xml` 的 `<admin version="2">`）
- [x] 阶段 4：默认切到新后台 + 三重回退（`ZC_ADMIN_UI` / `?ui=legacy` / 插件级）
- [x] BlockEditor 插件 + 文章/页面编辑页 + 评论编辑
- [x] 内置页面的完整增删改查
- [x] 测试体系：PHPUnit 88 个用例 + Playwright 34 个用例（桌面/移动）
- [x] 修复：选项请求无限循环、SPA 写操作 419、`<!--more-->` 往返
- [ ] 编辑器深度能力（表格、代码块高亮、图片拖拽上传、短代码节点）
- [ ] 旧编辑钩子迁移路径（`Filter_Plugin_Edit_Response*` → 新槽位）
- [ ] `@zblogphp/admin-ui` 组件包与插件接入模板
- [ ] 数据迁移脚本（缺列的旧库补列）
- [ ] 发版：`utils/admin-next-src/` 排除规则、产物一致性校验

---

## 1. 总体思路（已落地）

**路线：新老双后台并存**。`zb_system/admin/` 冻结为 legacy，不再加功能；
新 UI 挂在 `zb_system/admin-next/`，两者共用 `$zbp` 与 `cmd.php`。
选择理由：零破坏、可逐页迁移、随时回退；代价是短期维护两套样式。

关键决策（全部已实施）：

| ID | 决策 | 落地位置 |
|---|---|---|
| D1 | PHP 最低 8.0 | `composer.json`、`admin-next/index.php` 版本闸门 |
| D2 | 路由用 `HashRouter`（`#/posts`） | `utils/admin-next-src/src/router.tsx` |
| D3 | 认证复用登录 cookie，外部用 Bearer token | `admin-next/api.php` |
| D4 | 官方产物提交进仓库，插件自带 `dist/` | `zb_system/admin-next/assets/` |
| D5 | `ZC_ADMIN_UI` 默认 `next`，三重回退 | `AdminHomeUrl()`、`bootstrap/shell.php` |
| D6 | 后台走专用入口 `admin-next/api.php` | `zb_system/admin-next/api.php` |
| D7 | 编辑器独立成插件 | `zb_users/plugin/BlockEditor/` |

---

## 2. 已完成的工作（按提交）

| 提交 | 内容 |
|---|---|
| `04337382` | SPA 壳骨架 + 由 API 驱动的列表页 |
| `f441db4a` | 修复壳缓存过期与 chunk 404 白屏 |
| `aa83ceb6` | 要求 PHP 8.0 |
| `8b36c64f` | 写操作 + 补齐 API 缺口 |
| `e1f21dbc` | 通过 `plugin.xml` 加载第三方插件 SPA |
| `cdedc802` | 默认切到 SPA，保留 legacy 回退 |
| `8e002fe2` | 导航、admin 清单、UI 切换的单元测试 |
| `c4a4f7da` | 让全量测试可运行 |
| `da17fbbf` | 内置页面完整增删改查 |
| `4dbba3c3` | BlockEditor 插件 + 文章/页编辑页 + 评论审核 |
| `ab161956` | 修复下拉选项请求无限循环 |

### 2.1 后端（PHP）

- `zb_system/admin-next/index.php`：SPA 入口。PHP < 8.0 给可读错误页；未登录跳登录；
  非 admin 报错；`?ui=legacy` 回退。
- `zb_system/admin-next/api.php`：内部 API。不受 `ZC_API_ENABLE` 影响，强制登录 +
  admin，按用户限流，不输出 runtime；`X-ZBP-Admin-Spa` 头需通过同源校验。
- `bootstrap/shell.php`：组装 `window.__ZB__`（host/entry/api/user/rights/nav/apps/
  editor/lang/csrf/options），注入编辑器样式。
- `bootstrap/nav.php`：官方菜单结构化直出 + `ResponseAdmin_LeftMenu()` 的 HTML 兜底解析 +
  `Filter_Plugin_Admin_Menu_Items`；`icon-*` → lucide 映射。
- `bootstrap/apps.php`：扫描已启用插件与当前主题的 `<admin version="2">`，
  生成应用清单与编辑器清单（`ZbpAdminNext_BuildEditor` / `ZbpAdminNext_EditorManifest`）。
- `bootstrap/rights.php` / `assets.php`：权限矩阵、manifest 资源注入。
- `zb_system/function/lib/app.php`：`ParseAdminNode()` 新增 `<editor>` 子节点。
- `zb_system/function/c_system_api.php`：`ApiVerifyCSRF()` 对同源 SPA 会话放行
  （修复 `ApiVerifyCSRF(true)` 接口全站 419）。

### 2.2 前端（`utils/admin-next-src/`）

Vite 7 + React 19 + TS + Tailwind v4 + shadcn/ui，每个关注点只用一个库。

- 页面：仪表盘、文章、页面、分类、标签、评论、附件、用户、主题、模块、插件、设置、
  插件宿主、文章/页面编辑页、404。
- 组件：`app-shell`、`sidebar-nav`、`data-table`（TanStack Table）、`confirm-dialog`、
  `page-header`、`form-row`、`error-boundary` + `ui/*`（shadcn）。
- 插件宿主：`plugin-host/{loader,runtime,plugin-host,editor}`。`import()` 加载插件 bundle，
  React 通过 import map 共享同一实例；编辑器走 `usePluginEditor`。
- 状态：URL 存筛选/分页，TanStack Query 管服务端数据，Zustand 只管 UI（3 个 store 上限）。

### 2.3 编辑器插件 `BlockEditor`

- `plugin.xml` 用 `<admin version="2"><editor>` 声明资源与能力。
- `dist/editor.js`：contenteditable 实现，16 个工具栏按钮，源码模式，
  `<!--more-->` ↔ `<hr class="more" />` 双向转换（含接口返回的转义形式）。
- 没装插件时编辑页回退为纯文本 textarea 并提示。

### 2.4 测试

| 套件 | 文件 | 结果 |
|---|---|---|
| PHPUnit 核心 | `tests/phpunit.xml` | 57 tests / 146 assertions |
| PHPUnit admin-next | `tests/phpunit-admin-next.xml` | 31 tests / 288 assertions |
| Playwright | `tests/e2e/specs/*` | 32 passed / 2 skipped（桌面 + 移动） |

e2e 覆盖：登录、导航、深链、列表搜索、设置编辑、旧后台回退、分类/标签/模块/附件 CRUD、
文章与页面通过编辑器新建→回填→改名→删除、评论审核往返、编辑器加载与回退。

---

## 3. 期间修掉的问题（值得记住的坑）

| 问题 | 根因 | 修法 |
|---|---|---|
| 编辑页/分类页每秒 3.8 次 `category.list` | `useOptions` 把内联 `key`/`label` 函数放进 effect 依赖，每次渲染都重跑 | 回调存 ref，依赖只留 `mod/act/perpage`；加模块级缓存 |
| 批量删除、评论审核、分类/标签/模块/附件删除全部 419 | `ApiVerifyCSRF()` 的 SPA 放行写在 `!$force_check` 分支内 | 同源 SPA 会话直接放行，并补 `Origin/Referer` 同源校验 |
| `<!--more-->` 重新打开变成注释文本 | 接口返回的是转义形式 `&lt;!--more--&gt;` | `decodeMore()` 两种形式都还原 |
| 附件上传后列表不刷新 | React 合成事件里 `event.target.files` 被回收 | `onChange` 里先 `Array.from(...)` 快照，再 `refetchQueries` |
| 删除接口收不到 id | 删除接口只读 URL 的 `?id=` | `ZbpAdminNext_NormalizeIdParam()` 在 CSRF 校验**之后**回填 |
| e2e 用例莫名跳过 | 列表异步渲染，数按钮时为 0 被当成「站点没数据」 | 先等首行可见再判断 |
| 中文源码被破坏 | PowerShell `Set-Content` / `-replace` 按 GBK 改写 | 只用编辑器工具或 Node 脚本改文件 |

---

## 4. 已知问题 / 风险

| 级别 | 问题 | 说明 |
|---|---|---|
| 高 | **旧库缺列** | 部分旧库 `zbp_category` 缺 `cate_PostTime`/`cate_UpdateTime`，`zbp_tag` 缺 `tag_CreateTime`/`tag_PostTime`/`tag_UpdateTime`；产品里**没有迁移**，老站点写分类/标签会 `Unknown column` |
| 高 | 旧编辑钩子失配 | `Filter_Plugin_Edit_Response*` 在新编辑页不触发，装了 `CustomMeta` 这类插件会「丢字段」 |
| 中 | 表格行钩子 | `Filter_Plugin_Admin_ArticleMng_Table` 追加列在新列表不显示，只能走「旧版（含插件列）」 |
| 中 | CSP / 浏览器基线 | 插件 SPA 同源加载脚本；import map 需要较新浏览器，低于基线整体回退 legacy |
| 中 | 打包 | `utils/admin-next-src/` 必须从发行包排除，`assets/` 必须在包内 |
| 低 | 文章编辑器能力偏弱 | 当前是内容级 HTML 编辑，没有 ProseMirror 的事务/协同/复杂块模型 |

---

## 5. 后续规划

### 5.1 编辑器深化（用户可感知，优先级最高）

- [ ] 内容块模型（标题/列表/引用/代码/表格/图片/短代码），支持嵌套与拖拽排序
- [ ] 图片拖拽/粘贴上传，走 `upload.post`，带进度与失败重试
- [ ] 代码块语法高亮（按需加载）
- [ ] 表格编辑（行/列增删、表头切换）
- [ ] 本地草稿沿用旧 key `zblogphp_article_{id}`，新老编辑页互认
- [ ] 摘要三态（手填 / 自动 / 含 `<!--autointro-->`）与服务端推导一致性测试
- [ ] `<!--more-->` 往返自动化测试（100 次不变）

### 5.2 编辑钩子迁移

- [ ] 定义新插槽契约（侧栏字段 / 提交前改写 / 序列化钩子）
- [ ] 检测 `Filter_Plugin_Edit_Response*`，启用这类插件时在设置页提示
- [ ] 官方参考实现：把 `CustomMeta` 迁到新插槽，作为迁移样例

### 5.3 插件生态

- [ ] 发布 `@zblogphp/admin-ui`（shadcn 组件 + `useZbp` + `useApi` + Tailwind preset）
- [ ] 提供插件工程模板（Vite lib 模式，`external: react/react-dom`）
- [ ] `PLUGINS.md` 补：编辑器接入、错误处理、样式隔离、调试技巧
- [ ] 选一个官方插件（建议 `AuditRecords`）做 `<admin version="2">` 参考实现

### 5.4 兼容与迁移

- [ ] 升级脚本：检测并补齐缺失的时间列（分类/标签/文章/评论）
- [ ] 旧插件页面新窗口打开时带上「返回新版后台」入口
- [ ] `AdminColor` 等换肤插件转为 CSS 变量 preset 契约
- [ ] `MIGRATION.md` 补齐「新版已支持编辑页」后的差异说明

### 5.5 工程与发布

- [ ] CI：`tsc --noEmit`、ESLint、PHPUnit、Playwright、产物一致性校验
- [ ] 体积预算：壳首屏 ≤ 180KB gzip，单页 chunk ≤ 80KB gzip
- [ ] 打包脚本排除 `utils/admin-next-src/`
- [ ] 性能收口：首屏请求数（`__ZB__` 已含菜单与清单，不再单独请求）

---

## 6. 关键文件索引

| 位置 | 说明 |
|---|---|
| `zb_system/admin-next/index.php` | SPA 入口 |
| `zb_system/admin-next/api.php` | 内部 API 入口（登录 + admin + 限流 + 同源校验） |
| `zb_system/admin-next/bootstrap/*.php` | shell / nav / rights / apps / assets / api-compat |
| `zb_system/admin-next/MIGRATION.md` | 新旧后台差异与回退方式 |
| `zb_system/admin-next/PLUGINS.md` | 插件接入指南 |
| `zb_system/function/c_system_api.php` | `ApiVerifyCSRF()`（SPA 放行） |
| `zb_system/function/lib/app.php` | `ParseAdminNode()`（`<admin>` / `<editor>`） |
| `utils/admin-next-src/src/router.tsx` | 全部路由 |
| `utils/admin-next-src/src/lib/api.ts` | `apiGet` / `apiPost` / `apiForm` / `apiUpload` |
| `utils/admin-next-src/src/hooks/use-options.ts` | 下拉选项（含缓存，注意依赖） |
| `utils/admin-next-src/src/plugin-host/editor.ts` | 插件编辑器加载契约 |
| `zb_users/plugin/BlockEditor/` | 编辑器插件本体 |
| `tests/e2e/specs/admin-next-crud.spec.ts` | CRUD + 编辑器 e2e |

---

## 7. 开发环境备忘

- 测试站点：在仓库根目录执行 `php -S 127.0.0.1:8099 -t .`
- 站点配置的后台地址是 `http://localhost/`，而开发服务器在 `127.0.0.1:8099`，
  登录/分享时注意 Cookie 域与访问地址一致
- 前端构建：`cd utils/admin-next-src && pnpm build`，产物落到 `zb_system/admin-next/assets/`
- e2e：`cd tests/e2e`，需要 `ZBP_USER` / `ZBP_PASS`，写操作用例还要 `ZBP_E2E_WRITE=1`
- 修改含中文的源码请使用支持 UTF-8 的编辑器或脚本，避免用 PowerShell `Set-Content` / `-replace`
