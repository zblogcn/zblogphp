# admin-next 插件接入指南

插件/主题通过在 `plugin.xml` / `theme.xml` 中声明 `<admin version="2">`，
把自己的前端产物交给新后台加载。整个接入不需要改 PHP 逻辑之外的东西，
也不需要在用户机器上编译。

## 1. 声明

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

| 字段 | 说明 |
|---|---|
| `version` | 协议大版本，当前只支持 `2`。不匹配时整段忽略，插件回退旧版页面 |
| `menu[].route` | 必须以 `/plugin/{插件id}` 开头，否则该菜单项被丢弃 |
| `menu[].require` | 对应 `zb_system/defend/actions.php` 中的权限键 |
| `spa.entry` | 相对插件目录的 ESM 入口，必须有 `mount()` 导出 |
| `spa.style` | 可选，会以 `<link data-plugin="插件id">` 注入 |
| `legacy` | 可选，bundle 加载失败时自动跳转的旧版页面 |

只声明 `<menu>` 不声明 `<spa>` 是允许的，用于纯后端插件挂一个菜单。

## 2. 入口契约

入口文件必须是 ES Module，导出 `mount`：

```js
export function mount(el, ctx) {
  // 自己 createRoot 渲染，或直接操作 DOM
  return () => {
    // 可选：卸载清理
  }
}
```

`ctx` 提供：

| 字段 | 说明 |
|---|---|
| `appId` / `appType` | 插件 id 与类型（plugin / theme） |
| `baseUrl` | 插件目录的绝对 URL，结尾带 `/` |
| `route` | 当前子路由，例如 `/settings` |
| `api.get(mod, act, query)` | 走后台内部 API，自带登录态与错误处理 |
| `api.post(mod, act, body)` | 同上 |
| `user` / `rights` / `lang` | 当前用户、权限位、语言包 |
| `toast.success / error / info` | 复用宿主通知 |
| `navigate(path)` | 页面内跳转，只能跳到自己的命名空间 |

## 3. 后端接口

推荐复用宿主能力，在 `include.php` 里注册：

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
    ApiCheckAuth(true, 'admin');   // 登录 + 权限，宿主负责
    return ['data' => ['list' => []]];
}
```

这样自动获得：登录校验、动作权限、JSON body 解析、限流、统一响应格式。
自己写独立 PHP 接口也可以，但要自行实现登录校验与限流。

## 4. React 共享（避免多份实例）

插件必须使用宿主提供的 React，否则 hooks 会报 `invalid hook call`。

宿主在页面里注入了 import map：

```json
{"imports":{
  "react": "…/admin-next/assets/runtime.js",
  "react-dom": "…/admin-next/assets/runtime.js",
  "react-dom/client": "…/admin-next/assets/runtime.js"
}}
```

**用 Vite 构建**（推荐）：

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

`base: './'` 是必需的：产物里的 chunk 依赖 `import.meta.url` 相对解析，
插件才能随目录迁移。

**不构建**（手写 ESM）：使用宿主注入的全局运行时。

```js
const { createElement: h, useState, createRoot } = window.__ZB_PLUGIN_RUNTIME__
```

`zb_users/plugin/DemoPanel/dist/admin.js` 就是这种写法的完整示例。

## 5. 样式隔离

- 根节点带 `class="zbp-plugin-root"` 与 `data-plugin="{插件id}"`
- 插件样式请全部限定在 `.zbp-plugin-root[data-plugin="MyPlugin"]` 下
- 使用宿主的设计令牌（`var(--card)`、`var(--border)` 等），自动跟随深浅色

## 6. 发布检查

- `dist/` 必须随包发布，且不能被 `zbignore.txt` 或打包脚本排除
- 安装时宿主会校验 `entry` 是否存在；缺失则整条声明被忽略并记录日志
- 建议同时保留 `main.php`，供旧版后台与加载失败时回退
