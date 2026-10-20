# admin-next 端到端冒烟测试

基于 Playwright，覆盖「能进入后台 + 导航可用 + 主要页面渲染 + 回退路径」，
**不写入任何数据**，可以放心对测试站点运行。

## 运行

```bash
cd tests/e2e
pnpm install
pnpm exec playwright install chromium

# 指向一个可访问的站点（默认 http://127.0.0.1:8099/）
export ZBP_BASE_URL=http://localhost/
export ZBP_USER=admin
export ZBP_PASS=<管理员密码>

pnpm test
```

未设置 `ZBP_USER` / `ZBP_PASS` 时用例会整体 skip，不会误报失败。

## 覆盖内容

| 用例 | 断言点 |
|---|---|
| 仪表盘 | 标题、环境信息卡片、六项统计 |
| 侧边栏导航 | 7 个列表页的深链与标题 |
| 侧边栏点击（桌面） | 导航项点击后路由与标题 |
| 文章列表 | 表格渲染、搜索无结果时的空态 |
| 设置页 | 标签切换、脏值计数、撤销 |
| 旧后台回退 | `admin/index.php?act=admin&force=1` 仍可渲染 |
| 深链 | 刷新后停留在同一 hash 路由 |

## 项目

`playwright.config.ts` 中定义了两个项目：

- `desktop`：桌面 Chrome
- `mobile`：Pixel 7（校验移动端布局不崩、深链可用）

只跑一个：

```bash
pnpm exec playwright test --project=desktop
```

## 注意

- 测试依赖站点已安装并启用 admin-next（`ZC_ADMIN_UI=next`）
- 旧后台用例使用 `force=1`，不依赖全局设置
- 失败时会保存截图与 trace 到 `test-results/`
