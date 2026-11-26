# admin-next 端到端测试

基于 Playwright，覆盖后台进入、导航、深链、列表、设置、legacy 回退，以及分类/标签/模块/
附件/文章/页面/评论的写操作。

**写操作用例会创建、修改并删除数据。** 只有显式设置 `ZBP_E2E_WRITE=1` 时才启用；
该变量只能用于一次性隔离站点，严禁指向生产或现有站点。

## CI 中的隔离门禁

`.github/workflows/admin-next.yml` 的 `e2e-isolated` 作业会：

1. 使用全新的 GitHub runner，确认仓库中没有 `zb_users/c_option.php`。
2. 用 PHP 8.0 + SQLite 通过 CLI 安装一个临时站点。
3. 在 `SaveOption()` 前关闭登录验证码和评论开关，设置默认 `next`，只启用 BlockEditor，并植入隔离评论与测试用户。
4. 在 `127.0.0.1:8099` 启动临时 PHP 服务器。
5. 只在该作业设置 `ZBP_E2E_WRITE=1`，运行桌面 Chrome 与 Pixel 7 两个项目。
6. 上传 Playwright 报告并停止服务器；runner 随后销毁，不复用任何站点数据。

该作业不使用外部站点地址或真实管理员密码。缺少账号/数据/插件导致的跳过不是通过，
必须查看报告中的 skipped 项，不能把绿色作业直接描述为“所有 E2E 都执行并通过”。

## 本地只读运行

```bash
cd tests/e2e
pnpm install --frozen-lockfile
pnpm exec playwright install chromium

export ZBP_BASE_URL=http://127.0.0.1:8099/
export ZBP_USER=admin
export ZBP_PASS=<测试站点密码>

# 不要设置 ZBP_E2E_WRITE
pnpm test
```

未设置 `ZBP_USER` / `ZBP_PASS` 时依赖登录的用例会整体跳过。未设置 `ZBP_E2E_WRITE` 时，
写操作用例也会跳过。跳过不是通过。

## 本地写操作运行

先准备独立的目录、独立的 SQLite/数据库和独立上传目录，确认它不包含需要保留的数据，再运行：

```bash
export ZBP_BASE_URL=http://127.0.0.1:8099/
export ZBP_USER=admin
export ZBP_PASS=<隔离站点密码>
export ZBP_E2E_WRITE=1

pnpm test
```

不要通过复制现有站点配置后直接运行写用例；应重新安装或明确使用一次性数据库。

## 覆盖内容

| 用例组 | 断言点 |
|---|---|
| 仪表盘 | 标题、环境信息卡片、统计 |
| 侧边栏与深链 | 主要列表页直达、桌面导航、hash 刷新 |
| 文章列表 | 表格渲染、搜索空态、删除确认 |
| 设置页 | 标签切换、脏值计数、撤销；不提交设置 |
| 旧后台回退 | `admin/index.php?act=admin&force=1` 可渲染 |
| 分类/标签/模块 | 隔离写模式下新建、编辑、删除 |
| 评论 | 编辑弹窗、隔离写模式下审核状态往返 |
| 附件 | 隔离写模式下上传并删除 |
| 文章/页面 | 隔离写模式下新建、回填、改名、删除；有插件时使用 BlockEditor，否则 textarea |

## 项目与报告

`playwright.config.ts` 定义 `desktop`（Desktop Chrome）与 `mobile`（Pixel 7）两个项目。
桌面导航与侧栏折叠用例在移动端会按设计跳过；其他意外跳过需要检查环境。

只跑一个项目：

```bash
pnpm exec playwright test --project=desktop
```

失败时截图与 trace 保存在 `test-results/`。CI 同时上传该目录（及 `playwright-report/`，若生成）。

## 本轮最终隔离验证

PHP 8.0.30 + Node 22.20.0，全新 SQLite 站点，Desktop Chrome + Pixel 7，`--retries=0`：
53 passed / 0 failed / 7 expected skips。7 个跳过均为桌面侧栏/超宽屏与移动抽屉的项目专属用例。
测试后数据库文本列无 `E2E-*` fixture，上传目录无额外文件，记录为 0 残留。
