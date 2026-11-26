# Z-BlogPHP 后台管理重写方案（shadcn/ui）

> **状态：历史设计稿（已归档）。**
> 本文保留早期方案讨论与决策背景，不再作为实施清单或现状说明。
> 当前基线、阶段 0–5、功能覆盖矩阵、验收门禁与回滚策略以
> [`zb_system/admin-next/IMPLEMENTATION_PLAN.md`](zb_system/admin-next/IMPLEMENTATION_PLAN.md) 为准。
> 已落地实现是 HashRouter SPA + `zb_system/admin-next/api.php` 内部 API；本文中的
> Islands、`admin-next` XML 节点、公开 API 复用等表述均为历史提案，与当前代码不一致。
> 目标：用 shadcn/ui 重写后台管理，保留全部现有插件/主题兼容。

## Steps

- [ ] 决策最低 PHP 版本，明确降级策略
- [ ] 搭建 `zb_system/admin-next/` 壳 + Vite 多入口构建管线（manifest 注入）
- [ ] 实现导航采集层 `nav.php`（官方结构化 + 老钩子 HTML 解析 + 新钩子）
- [ ] 实现 `window.__ZB__` 引导数据（用户、rights、nav、lang、csrf）
- [ ] 试点迁移「网站设置」单页验证技术栈
- [ ] 迁移列表五件套：文章 / 页面 / 分类 / 标签 / 评论
- [ ] 迁移编辑页 `admin/edit.php`
- [ ] 迁移用户 / 附件 / 主题 / 模块 / 插件 / 应用中心
- [ ] 迁移登录页
- [ ] 实现遗留插件菜单聚合与 Legacy Bridge
- [ ] 发布 `@zblogphp/admin-ui` 组件包 + `plugin.xml` 声明式菜单文档
- [ ] 补齐缺失的 API mod

---

## 1. 背景与核心约束

现状：后台由表格 + 大量 HTML 字符串拼接构成。

- `zb_system/admin/index.php` 用 `switch ($zbp->action)` 分发到 `c_system_admin.php` 中的 12 个 `Admin_*()` 函数。
- 每个函数内部 `echo` 拼表格，权限、分页、排序、语言全部手工处理。
- 单个函数体量巨大（`Admin_SettingMng` 单页超过 400 行拼串）。

耦合分四层，**每层需要不同的策略**：

| 层级 | 现状 | 规模 | 策略 |
|---|---|---|---|
| A. 页面外壳 | `admin_header.php` / `admin_top.php` / `admin_footer.php` 被插件 `require` | 约 28 个插件+主题目录，60–73 个文件 | 保留不动，继续兼容 |
| B. 菜单 | `MakeLeftMenu()` / `MakeTopMenu()` / `MakeSubMenu()` 返回 HTML 字符串，插件 push 进数组 | 8 个钩子，实际 4 个插件使用 | 增加结构化数据接口，旧函数继续吞字符串 |
| C. 页面内嵌资源 | 插件向 `Filter_Plugin_Admin_Header` `echo` `<link>` 与 `<script src=jquery-ui>`，依赖 jQuery 全局变量 | 4 个插件 / 67 处 | 必须兼容，不可一刀切 |
| D. 表格行 | `Filter_Plugin_Admin_ArticleMng_Table` 传 `$tabletds`（`<td>` 字符串数组）供插件改写 | 官方核心 + 第三方 | 最痛点，见第 4 节 |

**结论：A 层不痛。** 插件页面是独立的 `main.php`，只 `require` 那三个壳文件。只要保留这三个文件的 HTML class 结构与 jQuery 全局对象（`zbp`、`ActiveLeftMenu`、`ChangeCheckValue`、`SetCookie` 等），它们能继续运行，无需改动。

## 2. 方案定位：路线 1 + Islands

三条候选路线：

- **路线 1：新老双后台并存（采纳）** — `zb_system/admin/*` 冻结为 legacy，新 UI 挂在 `zb_system/admin-next/`，两者共用 `$zbp` 与 `cmd.php`。零破坏、可逐页迁移、随时回退。代价：短期维护两套 CSS/路由。
- 路线 2：直接替换内置页面函数体 — 改动小、见效快；但老插件点进去仍是旧样式，观感割裂。
- 路线 3：一次全重写 + Legacy Bridge — 最干净；但 Z-BlogPHP 插件作者多数不活跃，生态适配成本过高，不可作为起点。

渲染架构选 **Islands**，而非 Vite SPA：

- PHP 继续负责路由、权限、外壳、语言包注入。
- 仅在需要交互处挂 React 岛：列表页、编辑器、设置表单、媒体库。
- 表单仍是 PHP `<form>`，`BuildSafeCmdURL()` 与 `CheckIsRefererValid()` 链路不动，CSRF 天然继承。
- 必须保留 `zb_system/script/c_admin_js_add.php`：它是带 Etag/304 与 `Filter_Plugin_Admin_Js_Add` 钩子的 PHP 生成 JS，是老插件的 JS 入口。

**后端数据源不新造 REST**，直接复用 `zb_system/api.php`：`c_system_api.php` 已提供 `ApiDispatch` / `ApiGetObjectArray` / `ApiCheckAuth` / `ApiGetPagebarInfo` / `ApiGetRequestFilter`，`mod=post&act=list` 已支持 `manage=1`、分页、`without_content`。缺口通过小插件 `zb_users/plugin/ZbpAdminApi/` 用 `ApiAddMod()` 注册，同时经 `Filter_Plugin_API_*` 暴露给第三方。

## 3. 导航机制（核心决策）

**导航必须「服务端解析 + 前端纯渲染」。** 理由：Z-BlogPHP 是传统多页 PHP 应用，若由 React 客户端 fetch 菜单，会导致首屏空白与权限判断暴露前端。

分工：

- **PHP 侧**：跑一遍已有插件钩子得到完整菜单树，连同样式入口序列化进页面。
- **前端侧**：拿到 JSON 后纯渲染，不发请求、不判权限。

关键点：**不需要新机制就能采集现有插件菜单**。`MakeLeftMenu()` 已返回字符串，`Filter_Plugin_Admin_LeftMenu` 收到的就是该数组，采集层直接复用老全局即可。

```php
// zb_system/admin-next/nav.php（示意）
function ZbpAdminNext_CollectNav() {
    global $zbp, $leftmenus;
    $leftmenus = array();                      // 复用老全局，老插件直接 push
    ob_start(); ResponseAdmin_LeftMenu(); $legacy = ob_get_clean();
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_LeftMenu'] as $fp => $_) { $fp($leftmenus); }

    $items = ZbpAdminNext_OfficialNavItems();  // 官方菜单结构化直出
    foreach ($leftmenus as $html) { $items[] = ZbpAdminNext_ParseMenuHtml($html); }
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Menu_Items'] as $fp => $_) { $fp($items); }
    return $items;
}
```

`ZbpAdminNext_ParseMenuHtml()` 用 `DOMDocument` 解析 `MakeLeftMenu()` 产出的固定结构（`<li id><a id href title><span><i class="icon-x"></i>名字</span></a></li>`），取出 `id / href / icon class / title`；`href` 非 `/zb_system/...` 的标记 `external: true`。`icon-*` 字体名可映射到 lucide，视觉不跳。

新增结构化钩子供新插件使用：

```php
Add_Filter_Plugin('Filter_Plugin_Admin_Menu_Items', 'MyPlugin_Menu');
function MyPlugin_Menu(&$items) {
    $items[] = array(
        'id'      => 'myprefix_dashboard',
        'title'   => '我的面板',
        'url'     => $zbp->host . 'zb_users/plugin/MyPlugin/main.php',
        'icon'    => 'lucide:layout-dashboard',
        'group'   => 'plugin',
        'order'   => 100,
        'require' => 'root',      // 对应 zb_system/defend/actions.php 中的键
        'badge'   => null,
    );
}
```

## 4. 前端页面程序形态

**首屏引导数据**（`c_admin_js_add.php` 中 `var zbp = new ZBP({...})` 的升级版）：

```php
<script>
window.__ZB__ = {
  host: "<?= $zbp->host ?>",
  action: "ArticleMng",
  user: { id, name, staticName, level, levelName, avatar },
  rights: { admin: true, ArticleMng: true, root: false /* 遍历 actions.php 计算 */ },
  nav: <?= json_encode(ZbpAdminNext_CollectNav()) ?>,
  lang: <?= json_encode(ZbpAdminNext_LangSubset('admin_next')) ?>,
  csrf: { token: "...", expiration: 24 },
  version: "<?= ZC_VERSION_FULL ?>",
  apiEnabled: <?= $zbp->option['ZC_API_ENABLE'] ? 'true' : 'false' ?>,
  debug: <?= $zbp->isdebug ? 'true' : 'false' ?>
};
</script>
```

有了 `rights` 与 `nav`，菜单渲染即纯函数：

```tsx
<SidebarMenu>
  {nav.filter(i => !i.require || rights[i.require]).map(item =>
    item.children?.length
      ? <Collapsible key={item.id}>{/* 展开分组 */}</Collapsible>
      : <SidebarMenuItem key={item.id}>
          <SidebarMenuButton
            isActive={item.id === activeId}
            onClick={() => item.external ? window.open(item.url) : router.push(item.url)}>
            <Icon name={item.icon} /><span>{item.title}</span>
            {item.badge && <Badge variant="secondary">{item.badge}</Badge>}
          </SidebarMenuButton>
        </SidebarMenuItem>
  )}
</SidebarMenu>
```

布局采用 shadcn `sidebar-07`（可折叠侧栏 + 顶栏 + `SidebarProvider`），侧栏开合状态存 cookie —— 正好取代 `AdminColor` 现在用 `admincolor_hm` cookie 手改 `.left{width:36px}` 的做法。

**列表页**用 `data-grid` / `DataTable`（TanStack Table）：列定义由 PHP 下发，排序/分页走 `zb_system/api.php`，批量操作沿用 `PostBat` 语义。**表格行钩子的兼容**见第 4 节。

## 5. 最硬的一块：表格行钩子

`Filter_Plugin_Admin_ArticleMng_Table`（`c_system_admin.php:257`）把 `$tabletds`（`<td>` 字符串数组）交给插件，**这是唯一无法自动翻译的一层**。处理优先级：

1. **保留 legacy 表格页**；新 UI 文章列表只渲染官方列，插件追加的列收进「更多列」面板，点击以 legacy 视图打开。（先做这个）
2. 在 `c_system_admin.php` 中并行传结构化行数据：新增 `Filter_Plugin_Admin_ArticleMng_Row($article, &$row_data, &$columns)`，让插件有机会选用新接口。老插件继续走旧钩子。**不要对 `$tabletds` 做 DOM 解析提取**，太脆。
3. 长期：官方发布 shadcn 组件包（`@zblogphp/admin-ui`）供愿意迁移的作者使用。

## 6. 第三方接入分三档

**档 1：零改动**
插件 `main.php` 只 `require` 三个壳文件。新 UI 给其菜单项打 `legacy` 标，点击后：

- 方式 A：`window.open()` 新标签跑老壳（最稳）
- 方式 B：新 UI 内嵌 `<iframe src="...main.php?embed=1">`，`embed=1` 时壳只输出 `<div id="divMain">` + 约 80 行兼容 shim（提供 `$`、`zbp`、`ActiveLeftMenu` 空实现、`.hint` 样式）

建议先上 A，B 作为可选开关。B 的风险：老插件固定宽度布局、`position:fixed` 的 jQuery UI 弹层会在 iframe 内被裁切。

**档 2：只改 `plugin.xml`，不动 PHP（性价比最高，决定生态门槛）**

```xml
<admin-next version="1">
  <menu>
    <item id="myprefix_dashboard" title="我的面板" path="main.php"
          icon="layout-dashboard" group="plugin" order="100" require="root"/>
    <item id="myprefix_settings" title="设置" path="settings.php" require="root"/>
  </menu>
  <assets>
    <entry>dist/admin.js</entry>
    <style>dist/admin.css</style>
  </assets>
</admin-next>
```

在 `App::LoadInfoByXml()`（`zb_system/function/lib/app.php:376`）中加约 8 行读取该节点（`$this->admin_next_menu` / `$this->admin_next_assets`），采集层遍历已启用应用时自动合并。插件作者只写几行声明，无需理解 React。

**档 3：用官方组件包写真正的 React 页面**
发布 npm 包 `@zblogphp/admin-ui`，导出：已配好 Tailwind preset 的 shadcn 组件（Table / Form / Dialog / Toast / DataTable）、`useZbp()`（读 `window.__ZB__`）、`useApi(mod, act)`（封装 `zb_system/api.php`，自动带 `csrf_token` / token）、`<PluginPage>` 布局壳。
插件用 Vite 构建，产物中 `react` / `react-dom` 必须 `external`（页面已提供，避免多份 React），输出到插件自己的 `dist/`，PHP 侧注入 `<script type="module">`。

**构建退路（生态能否活起来的关键）**，从轻到重：

1. 纯 CSS/HTML 遗留页 → 零改动
2. 单文件 React → 提供 `admin-next/standalone.php?app=MyPlugin&entry=admin.js`，页面已注入 React / Tailwind / `useZbp`，插件只写一个 `admin.js`（可用 `htm` 或全局 `React.createElement`，无需构建）
3. 完整工程 → npm 包 + Vite

多数 Z-BlogPHP 插件作者不会为后台面板搭 Vite 工程；「打开编辑器就能写」的入口比完美组件库更重要。

## 7. 主题接入

主题带后台面板是现实：`umYouth`、`zbtraining` 各有 24 处引用老壳，`start` 主题有 5 个子面板。主题无 `RegisterPlugin`，靠 `include.php` 中 `Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', ...)` 挂菜单。

- 主题 `theme.xml` 允许同样一段 `<admin-next>`，但 `group` 默认 `theme`，渲染在「主题」分组下。
- `start/plugin/ad.php` 这类页面走与插件相同的 legacy bridge。

这同时给 `AdminColor` 一个归宿：升级为**后台外观机制**——新 UI 定义 CSS 变量契约（`--admin-accent`、`--admin-sidebar-width`、`--admin-radius` …），插件或主题提供 preset CSS 即可换肤，不再 hack `.left{width:140px}`。其现用的 `$zbp->IsExclusive('backend-ui')` 独占标记可直接复用为「同时只允许一个后台外观包生效」。

## 8. 资源注入与构建

- 官方页面：Vite 多入口（每页一个 entry，共享 `vendor` chunk），`build.manifest: true`；PHP 读 `manifest.json` 注入 `<link>` / `<script type="module">`，用 `filemtime` 或 hash 做版本戳（与现有 `admin_js_hash` 思路一致）。
- 插件产物：不强制 Vite，见第 6 节的三条退路。

## 9. 待决策事项

1. **最低 PHP 版本**：`composer.json` 仍写 `php >=5.2`，`c_system_base.php:58` 有 `PHP_VERSION_ID >= 50300` 分支。新 UI 需 PHP ≥ 7.4。采集层 `nav.php` 必须能在低版本下运行或直接降级回 legacy，建议以 PHP 5.2 兼容语法编写（`json_encode` 选项、`DOMDocument` 需注意），前端产物用 `type="module"` 让老浏览器自然降级。**这是整个项目最大的产品决策。**
2. **遗留插件页面的打开方式**：新标签（稳、观感割裂）还是 iframe 包进新壳（统一、有布局与弹层风险）。
3. 档 2 声明式菜单是否采纳（直接决定第三方接入门槛）。

## 10. 迁移顺序（每步可独立上线）

1. 抽出 `window.__ZB__` 引导数据 + Vite/manifest 构建管线，先做「网站设置」页验证（结构最清晰，无表格钩子）
2. 列表五件套：文章 / 页面 / 分类 / 标签 / 评论（TanStack Table）
3. 编辑页 `admin/edit.php`（30KB 巨型拼接，先原样嵌 legacy，最后处理）
4. 用户 / 附件 / 主题 / 模块 / 插件 / 应用中心
5. 登录页 + 密码找回
6. 老插件 Legacy Bridge 菜单聚合
7. 发布 `@zblogphp/admin-ui` 组件包 + 新钩子文档

## 附：关键代码位置索引

| 位置 | 说明 |
|---|---|
| `zb_system/admin/index.php` | legacy 后台入口，`switch ($zbp->action)` 分发 |
| `zb_system/admin/{admin_header,admin_top,admin_footer,admin_left}.php` | 页面外壳，被 60+ 文件 `require`，必须保留 |
| `zb_system/function/c_system_admin.php` | 12 个 `Admin_*()` 页面函数（约 1600 行拼串） |
| `zb_system/function/c_system_admin_function.php` | `MakeLeftMenu` / `MakeTopMenu` / `MakeSubMenu` / `ResponseAdmin_*` |
| `zb_system/function/c_system_api.php` | API 框架：`ApiDispatch` / `ApiGetObjectArray` / `ApiCheckAuth` / `ApiAddMod` |
| `zb_system/api/*.php` | 现有 API mod：post / page / comment / category / tag / member / module / system / upload / app |
| `zb_system/function/c_system_plugin.php` | 钩子系统：`DefinePluginFilter` / `Add_Filter_Plugin` / `HookFilterPlugin` |
| `zb_system/function/lib/zblogphp.php:1028` | `LoadManage()`，注册后台内置子菜单钩子 |
| `zb_system/function/lib/zblogphp.php:1524` | `CheckRights()`，权限判定 |
| `zb_system/defend/actions.php` | 全部权限键与等级定义（`require` 字段取值来源） |
| `zb_system/function/lib/app.php:376` | `LoadInfoByXml()`，档 2 需在此加 `<admin-next>` 解析 |
| `zb_system/script/c_admin_js_add.php` | 老插件 JS 入口（Etag/304 + `Filter_Plugin_Admin_Js_Add`），必须保留 |