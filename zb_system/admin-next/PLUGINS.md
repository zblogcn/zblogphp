# admin-next 插件与主题接入指南

插件/主题通过在 `plugin.xml` / `theme.xml` 中声明 `<admin version="2">`，把自己的前端页面或
编辑器交给新版后台加载。用户安装包时必须包含已构建的 `dist/`，不需要在用户机器上编译。

> 当前只有已启用的插件和当前主题会被采集。协议版本不匹配时整段忽略，旧菜单/旧页面仍可能通过
> legacy 导航出现。`@zblogphp/admin-ui` 组件包尚未发布。
> 后台默认已为 `ZC_ADMIN_UI=next`；已有显式 `legacy` 保持不变，并可用 `ui=legacy` 或 `force=1` 回退。插件验收应分别覆盖 legacy 与 SPA。

## 1. 声明页面

```xml
<admin version="2">
  <menu>
    <item id="panel"   title="我的面板" route="/plugin/MyPlugin"
          icon="puzzle" group="extend" order="150" require="admin"/>
    <item id="settings" title="设置"    route="/plugin/MyPlugin/settings"
          icon="settings" group="extend" order="160" require="admin"/>
  </menu>
  <spa>
    <entry>dist/admin.js</entry>
    <style>dist/admin.css</style>
  </spa>
  <legacy>main.php</legacy>
</admin>
```

| 字段 | 当前契约 |
|---|---|
| `version` | 协议大版本，当前只支持整数 `2`；不匹配时整段忽略 |
| `menu[].id` / `title` | 必填，空值项被跳过 |
| `menu[].route` | 必须以 `/plugin/{插件id}` 开头；省略时默认为 `/plugin/{插件id}`。浏览器路径必须精确命中已声明菜单，未声明子路由默认拒绝加载 |
| `menu[].require` | 对应 `zb_system/defend/actions.php` 的权限键；空值表示不额外限制菜单呈现，服务端仍鉴权 |
| `menu[].icon` / `group` / `order` | 图标名、分组、排序；主题未声明分组时默认 `theme`，插件默认 `extend` |
| `spa.entry` | 相对应用目录的 ESM 入口，必须存在且导出 `mount()`；拒绝绝对路径和含 `..` 的路径 |
| `spa.style` | 可选，注入 `<link data-plugin="应用id">` |
| `legacy` | 可选，提供“旧版页面”入口；bundle 导入失败或未导出 `mount()` 时自动跳转 |

解析层允许只声明 `<menu>`，但要形成可用页面仍需 `spa.entry` 或 `legacy`。只有菜单、两者都没有时，
插件页无法加载出实际内容。`style` 与 `legacy` 也应使用安全相对路径，不要依赖宿主未提供的路径校验。

## 2. 页面入口契约

入口必须是 ES Module，导出 `mount`：

```js
export function mount(el, ctx) {
  // 自己 createRoot 渲染，或直接操作 DOM
  return () => {
    // 可选：同步清理
  }
  // 也可 return Promise<(() => void) | void>
}
```

`ctx` 提供：

| 字段 | 说明 |
|---|---|
| `appId` / `appType` | 应用 id 与类型（plugin / theme） |
| `baseUrl` | 应用目录的绝对 URL，结尾带 `/` |
| `route` | 去掉 `/plugin/{appId}` 后的子路由，例如 `/settings` |
| `api.get(mod, act, query)` | 走后台内部 API，自带登录态与错误处理 |
| `api.post(mod, act, body)` | JSON 写请求；multipart 上传需插件自行用 `FormData` 调用内部 API |
| `user` / `rights` / `lang` | 当前用户、动作权限、语言消息；前端权限仅用于呈现 |
| `toast.success / error / info` | 复用宿主通知 |
| `navigate(path)` | 写 hash 路由；非本应用命名空间的路径会被限制回本应用根路由 |

宿主在路由变化时会重新挂载插件，并调用 mount 返回的清理函数。动态 import 之前必须精确匹配
已声明菜单且通过 `require` 权限；未声明路由不会加载 bundle。bundle 导入失败可回退 `legacy`；
mount 执行阶段抛错则显示插件页错误，不会自动跳转。

插件页面应复用宿主的焦点样式、语义 token、移动抽屉断点和响应式布局约定；不要假设旧后台的
jQuery、全局 `zbp` 对象或旧 CSS 在 SPA 中可用。

## 3. 后端接口

推荐在 `include.php` 注册 mod，复用宿主内部 API：

```php
function ActivePlugin_MyPlugin()
{
    ApiAddMod('myplugin', __DIR__ . '/api/myplugin.php');
}
```

```php
// api/myplugin.php
function api_myplugin_list()
{
    ApiCheckAuth(true, 'admin');   // 动作级权限仍需自行声明
    return ['data' => ['list' => []]];
}
```

内部 API 入口已经要求登录和 `admin`，并提供 JSON body 解析、同源/CSRF 校验、限流与统一响应；
公开 `zb_system/api.php` 的启用状态不影响它。独立 PHP 接口必须自行实现登录、权限、CSRF/同源与限流。
核心旧库缺列由数据库升级流程补齐，SPA 只做 Schema 预检；缺列时内部 API 写请求返回 409。
官方接口已包含 `comment.put` 等写操作；系统设置 API 使用 43 项白名单，插件不应假设未列出字段可写。

## 4. React 共享

插件必须复用宿主 React，避免 hooks 出现 `invalid hook call`。页面注入的 import map 将：

```json
{
  "react": "…/admin-next/assets/runtime.js",
  "react-dom": "…/admin-next/assets/runtime.js",
  "react-dom/client": "…/admin-next/assets/runtime.js"
}
```

Vite 构建示例：

```ts
// vite.config.ts
export default defineConfig({
  build: {
    lib: { entry: 'src/admin.js', formats: ['es'], fileName: () => 'admin.js' },
    base: './',
    rollupOptions: {
      external: ['react', 'react-dom', 'react-dom/client'],
    },
  },
})
```

`base: './'` 用于让 chunk 随插件目录迁移。不要将第二份 React 打进插件 bundle。

不构建的手写 ESM 可使用宿主全局运行时：

```js
const { createElement: h, useState, createRoot } = window.__ZB_PLUGIN_RUNTIME__
```

`zb_users/plugin/DemoPanel/dist/admin.js` 是当前仓库中的手写 ESM 示例。
`@zblogphp/admin-ui` 尚未发布，不能把它写成已可安装的依赖。

## 5. 样式与无障碍

- 宿主容器带 `class="zbp-plugin-root"` 与 `data-plugin="{应用id}"`。
- 插件样式限定在 `.zbp-plugin-root[data-plugin="MyPlugin"]` 下。
- 使用宿主语义 token（`var(--card)`、`var(--border)` 等）以兼容深浅色。
- 为图标按钮提供可访问名称，保留可见焦点；弹层打开、Escape 关闭和焦点回转需自行验证。
- 布局应兼容桌面折叠侧栏与移动抽屉，避免固定宽度和横向溢出。

## 6. 编辑器接入

编辑器是独立声明，不占菜单，也不要求页面 `mount()`：

```xml
<admin version="2">
  <editor>
    <capabilities>rich-text,source,images</capabilities>
    <asset>dist/editor.js</asset>
    <style>dist/editor.css</style>
  </editor>
</admin>
```

宿主要求 ESM 导出：

```js
export function mount(el, ctx) {
  return {
    getHTML() { /* 返回正文 HTML */ },
    setHTML(html) { /* 回填正文 */ },
    focus() {},
    destroy() {},
    // insertMore? 可选
  }
}
```

当前编辑器 `ctx` 传入 `appId` 与 `api`（`get/post/upload`）。不要把页面插件的 `toast`、
`navigate` 或 `save` 当作已提供的编辑器上下文。

### 6.1 图片上传已接通选择文件链路

文章编辑页当前传入：

```ts
upload: async (_mod, _act, form) => apiUpload("upload", "post", form)
```

编辑器可构造 `FormData` 并调用 `ctx.api.upload("upload", "post", form)`。BlockEditor 的图片按钮
已实现选择文件、上传、校验返回 URL 协议并插入 `<img>`。拖拽上传、粘贴上传、媒体库、上传进度和
失败重试仍未实现；`capabilities` 中包含 `images` 不代表这些能力全部存在。

采集规则：

- 仅检查已启用插件，按采集顺序使用第一个有效编辑器，其余忽略并记录日志。
- `asset` 必须存在；`asset` 与 `style` 拒绝绝对路径和含 `..` 的路径。
- 无有效编辑器声明或加载失败时，文章/页面编辑回退到 HTML textarea。
- `capabilities` 是元数据，宿主不据此补能力，也不据此证明功能存在。

## 7. 旧编辑 Hook 自动回退

当前已注册以下任一项时，SPA 会发布 `legacyEditRequired=true`，并把文章/页面编辑页自动跳转到
带 `force=1` 的旧编辑页：

- `Filter_Plugin_Edit_Begin`
- `Filter_Plugin_Edit_End`
- `Filter_Plugin_Edit_SubMenu`
- `Filter_Plugin_Edit_Response`
- `Filter_Plugin_Edit_Response2`
- `Filter_Plugin_Edit_Response3`
- `Filter_Plugin_Edit_Response4`
- `Filter_Plugin_Edit_Response5`

这是保护性回退，不是 Hook 等价执行。SPA 不运行这些旧回调，也没有新的结构化编辑插槽。
只注册 `Filter_Plugin_Admin_Header` 等外观/外壳 Hook 不会单独触发编辑回退。

## 8. BlockEditor 能力边界

仓库内 `BlockEditor` 的实现是 contenteditable，而非 ProseMirror：

| 能力 | 当前情况 |
|---|---|
| 标题、段落、粗斜体、删除线、列表、引用 | 有 |
| 链接、`pre/code`、源码、撤销/重做 | 有 |
| `<!--more-->` 双向转换 | 有 |
| HTML 白名单净化 | 源码/粘贴/回填/读出已接入；限制标签、属性与 URL 协议 |
| 图片选择上传 | 已通过宿主 `upload.post` 接通 |
| 图片拖拽/粘贴上传、媒体库、上传进度 | 无 |
| 表格编辑命令、嵌入、代码高亮 | 无 |
| 块拖拽/嵌套、事务模型、协同 | 无 |
| 本地草稿 | 无（配置项存在不等于功能实现） |

其 XML 中的 `blocks,images,media,table,embed` 等标签不能作为验收依据。

## 9. 应用中心与管理入口

- 官方导航和插件/主题页头提供应用管理入口。
- 已安装、已启用且声明管理页的 AppCentre 使用其服务端 `GetManageUrl()`；该入口标记为 external/legacy。
- AppCentre 未启用、未安装或没有管理页时，回退到 `zb_system/admin/index.php?act=PluginMng&force=1`。
- `app.get_plugins` / `app.get_themes` 返回 `can_manage` 和 `manage_url`，供列表显示单应用管理入口。
- 应用中心本身、插件/主题包安装、卸载和上传均未 SPA 化。

## 10. 仍受限的旧表格行 Hook

`Filter_Plugin_Admin_ArticleMng_Table` 追加的列不在 SPA 文章/页面列表渲染。当前保留
“旧版（含插件列）”入口，分别跳转到 `ArticleMng&force=1` / `PageMng&force=1`。

不要解析旧 `<td>` 字符串来模拟新表格。后续如需新插槽，应设计结构化行数据契约，而不是依赖旧 HTML。

## 11. 发布检查

- `dist/` 必须随应用包发布，且不能被 `zbignore.txt` 或打包脚本排除。
- 安装时宿主检查 `entry` 是否存在；缺失则整条应用声明被忽略并记录日志。
- 建议始终保留 `main.php` 作为 `legacy`，供旧后台与加载失败回退。
- 在 SPA 与 legacy 两种后台分别验证菜单权限、页面加载、保存、应用管理入口和回退。
- 对编辑器验证新建、回填、保存、`<!--more-->`、选择图片上传和无插件时的 textarea 回退。
- 对插件页面验证键盘焦点、弹层焦点回转、移动抽屉和窄屏无横向溢出。
- 不要把未运行的浏览器测试写成已通过；跳过必须单独说明。本轮 PHP 8.0.30 完整隔离 E2E 为 53 passed / 0 failed / 7 expected skips，0 残留。
