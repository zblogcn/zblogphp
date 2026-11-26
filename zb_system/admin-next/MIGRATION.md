# admin-next 迁移与回退

> 本文对应当前 SPA/内部 API 实现。完整阶段计划、功能矩阵和验收门禁见
> [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md)。
> 文中的命令是验证方式，不代表测试已经运行或通过；隔离 E2E 未完成前不得写成“已通过”。

## 1. 默认行为：暂不切换 legacy 站点

当前版本**默认继续使用旧后台**：

```text
ZC_ADMIN_UI = legacy（默认）
zb_system/cmd.php?act=admin
  → zb_system/admin/index.php?act=admin
```

- 只有显式设置 `ZC_ADMIN_UI=next` 才默认进入 `zb_system/admin-next/index.php`。
- 已有 `next` 值不会被升级过程主动改写；未知值按 `legacy` 处理。
- 旧后台 `zb_system/admin/index.php` 保留；检测到 `next` 且没有 `force=1` 时才跳转 SPA。
- 登录页仍是旧版登录页，不属于已迁移的 SPA 页面。
- 直接访问 `admin-next/index.php` 可以进入 SPA，但不会修改 `ZC_ADMIN_UI`。

## 2. 可靠的界面回退

| 方式 | 写法 | 持续时间 | 说明 |
|---|---|---|---|
| 强制打开旧后台 | `/zb_system/admin/index.php?act=admin&force=1` | 当前访问 | 最可靠的临时入口 |
| SPA 入口回退 | `/zb_system/admin-next/index.php?ui=legacy` | 当前访问 | 直接跳转到 `admin/index.php?force=1` |
| 命令入口回退 | `/zb_system/cmd.php?act=admin&ui=legacy` | 当前跳转链 | `ui=legacy` 参与 `AdminHomeUrl()` 判定 |
| 全局设置 | 设置 → 全局 → 后台界面 → 旧版后台（表格） | 长期 | 写入 `ZC_ADMIN_UI=legacy` |
| 旧编辑 Hook | 打开 SPA 文章/页面编辑时自动跳转 | 当前编辑 | 跳到带 `force=1` 的旧编辑页，避免自定义字段丢失 |
| 旧表格行 Hook | 文章/页面列表 →“旧版（含插件列）” | 当前列表 | 使用 `ArticleMng` / `PageMng` 的 forced legacy 页面 |
| 单个插件页面 | 插件页“旧版页面”或声明的 `legacy` | 单应用 | bundle 加载失败时也会跳转；不是全局回退 |

判定顺序：

```text
请求带 ui=legacy  → 旧后台
ZC_ADMIN_UI=legacy → 旧后台
其他情况          → 新版后台
```

对应实现：`zb_system/function/c_system_admin_ui.php` 的 `AdminUiIsNext()`。

## 3. 升级前检查

1. 备份数据库、`zb_users/c_option.php`、`zb_users/upload/` 和插件/主题配置。
2. 确认 PHP >= 8.0，并确认 Web 服务器允许访问 `zb_system/admin-next/api.php`。
3. 在旧库上执行数据库升级流程，让核心迁移补齐历史缺失列；不要只升级 PHP 文件而跳过数据库升级。
4. 列出依赖 `Filter_Plugin_Edit_*`、`Filter_Plugin_Edit_Response*` 和 `Filter_Plugin_Admin_ArticleMng_Table` 的插件。
5. 需要修改域名、安装/卸载应用、使用应用中心或执行缓存/统计维护时，直接使用旧后台或外部应用中心。
6. 升级后先访问 `/zb_system/admin/index.php?act=admin&force=1`，确认 legacy 可读，再显式尝试 SPA。

## 4. 旧库自动补列与 Schema 阻断

### 4.1 自动补列的责任边界

- `zb_system/function/c_system_updatedb.php` 的 `updatedb_migrate()` 会检查并补齐历史缺失列，包括：
  - Post：`log_CreateTime`、`log_UpdateTime`；
  - Category：`cate_Type`、`cate_Group`、`cate_CreateTime`、`cate_UpdateTime`、`cate_PostTime`；
  - Tag：`tag_Type`、`tag_Group`、`tag_CreateTime`、`tag_UpdateTime`、`tag_PostTime`；
  - Member：`mem_CreateTime`、`mem_UpdateTime`；
  - 以及 Config 的历史 `Key` 列等迁移步骤。
- 迁移可重复执行；只有全部 DDL 成功后才推进 `ZC_LAST_VERSION`。
- **SPA 自身不自动执行 DDL**。`bootstrap/schema.php` 只检查当前 `datainfo` 所需的 Post/Category/Tag/Member 列。

### 4.2 缺列时的行为

| 入口/请求 | 当前行为 |
|---|---|
| SPA 页面 | 检测到缺列时返回 HTTP 409 阻断页，不渲染后台 |
| root 用户 | 阻断页提供 `zb_system/admin/updatedb.php?updatedb=1` 修复链接，并提示先备份 |
| 非 root 用户 | 提示联系管理员运行数据库升级 |
| 内部 API 写请求 | 返回 HTTP 409 和缺失字段列表，不派发写操作 |
| 内部 API GET/HEAD/OPTIONS | 保持只读，可用于诊断；但 SPA 入口仍会被缺列阻断 |

升级失败时不要继续写入。迁移可能已经执行部分早期 DDL，可靠恢复方式是恢复升级前数据库备份；当前没有自动 down migration。

## 5. 当前功能差异

| 功能 | 新版 SPA | 主要边界 |
|---|---|---|
| 仪表盘 / 统计 | ✅ | 旧版统计维护、缓存操作未迁移 |
| 文章、页面列表 | ✅ | 搜索、分页、批量删除；旧表格行 Hook 追加列不显示，提供 forced legacy 入口 |
| 文章、页面编辑 | ✅ | 新建/回填/保存/删除；当前注册旧编辑 Hook 时自动回退旧编辑页 |
| 分类、标签 | ✅ | 增删改查；依赖数据库升级补齐列，缺列时阻断 |
| 评论 | ✅ | 列表、审核、批量操作、删除、编辑；保存走 `comment.put` |
| 评论 `comment.put` | ✅ | UI 更新内容、作者名、邮箱；保留所属文章、父子关系、AuthorID、时间、Meta、审核状态等 |
| 附件 | ✅ | 列表、上传、删除、预览 |
| 编辑器图片 | ✅ | BlockEditor 可选择图片并通过 `upload.post` 上传插入；无拖拽/粘贴上传、媒体库、进度 |
| 用户 | ✅ | 列表、新建、编辑、删除 |
| 主题 | 部分 | 支持切换与管理页入口，不支持安装/卸载/上传 |
| 模块 | 部分 | 自定义模块增删改；系统模块不可删除 |
| 插件 | 部分 | 支持启停与管理页入口，不支持安装/卸载/上传 |
| 应用中心/管理入口 | 部分 | 使用已启用 AppCentre 的管理地址；否则回退 `PluginMng&force=1`。不是 SPA 内应用中心 |
| 网站设置 | 部分 | 六个分组、43 个白名单字段；`ZC_BLOG_HOST` 只读并提供 legacy 链接 |
| 插件/主题 SPA | ✅ | `<admin version="2">`；未升级插件打开旧页面；`@zblogphp/admin-ui` 尚未发布 |
| 无障碍/响应式 | 已实现 | 跳转链接、焦点管理、ARIA、移动抽屉、减少动效、响应式布局；实际通过与否看隔离 E2E 报告 |
| 登录、找回密码、应用中心 | ❌ | 继续使用旧入口或外部应用中心 |

“✅”表示存在实现，不表示与 legacy 功能完全等价，也不表示测试已通过。

## 6. 插件兼容限制

- **旧编辑 Hook**：当前已注册 `Filter_Plugin_Edit_Begin`、`Filter_Plugin_Edit_End`、
  `Filter_Plugin_Edit_SubMenu`、`Filter_Plugin_Edit_Response`、`Response2`、`Response3`、
  `Response4`、`Response5` 任一项时，SPA 编辑页自动跳转 legacy。SPA 不执行这些旧回调，
  也没有新的结构化插槽。
- **旧表格行 Hook**：`Filter_Plugin_Admin_ArticleMng_Table` 追加的列不在 SPA 渲染。
  使用“旧版（含插件列）”；不要解析旧 `<td>` 字符串来模拟新表格。
- **非 ProseMirror 编辑器**：BlockEditor 是 contenteditable 实现，不是 ProseMirror。
  支持基础富文本、链接、`pre/code`、源码、`<!--more-->`、HTML 白名单净化和选择图片上传；
  没有块事务、表格编辑、嵌入、代码高亮、拖拽、协同或本地草稿。
- **组件包未发布**：`@zblogphp/admin-ui` 尚不可用；插件可复用宿主 React runtime 或手写 ESM。
- **外观插件**：`AdminColor` 等旧换肤插件不作用于 SPA；SPA 使用自己的深浅色/跟随系统主题。

## 7. PHP 8 与开发依赖

- 当前 `composer.json` 和安装器要求 **PHP >= 8.0**。
- `admin-next/index.php` 对 PHP < 8.0 显示可读错误页；这不是低版本 PHP 支持声明。
- 不要在 PHP < 8.0 环境通过 `force=1` 尝试规避版本闸门。应升级 PHP，或部署仍支持该环境的旧版本。
- 当前 Composer lock 与 `composer.json` 不同步，`composer validate` 会报告 lock 过期；
  CI PHPUnit 因此使用 PHP 8.4。开发依赖安装结果不能当作 PHP 8.0 运行时证明。
- 隔离 E2E 使用 PHP 8.0 全新安装，用于覆盖运行时路径；该作业存在或测试被发现不等于 E2E 已通过。

## 8. 版本级回滚

1. 先切到 `ZC_ADMIN_UI=legacy`，停止新增 SPA 写入。
2. 代码和 `zb_system/admin-next/assets/` 必须作为同一版本整体回滚，不要混用不同版本的 PHP 壳、manifest 和 hash 产物。
3. 若升级改过数据库结构，恢复升级前数据库备份；当前没有自动 down migration。
4. 数据库与上传文件存在引用关系，恢复数据库时同步恢复相应上传目录备份。
5. 回滚后验证登录、legacy 页面、文章/评论读取、编辑器图片引用和插件启用状态。

## 9. 验证命令

```bash
# admin-next PHPUnit
vendor/bin/phpunit -c tests/phpunit-admin-next.xml

# 核心 PHPUnit（含旧库迁移回归）
vendor/bin/phpunit -c tests/phpunit.xml

# 前端（utils/admin-next-src/）
pnpm install --frozen-lockfile
pnpm typecheck
pnpm build
pnpm verify:assets

# 从仓库根目录检查产物
git diff --exit-code -- zb_system/admin-next/assets
```

Playwright 的写操作用例会创建、修改并删除数据。CI 只在全新 runner 的一次性 SQLite 站点中设置
`ZBP_E2E_WRITE=1`。本地运行写用例前必须准备独立站点、数据库和上传目录，不能指向生产或现有站点。
隔离要求见 [`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md) 和
[`tests/e2e/README.md`](../../tests/e2e/README.md)。跳过不是通过。
