# admin-next 前端源码

Z-BlogPHP 新版后台（SPA）的前端工程。构建产物输出到
`zb_system/admin-next/assets/`，由 PHP 壳读取 `manifest.json` 注入。

## 环境

- Node 22+（当前开发使用 24）
- pnpm 10+

## 命令

```bash
pnpm install
pnpm dev        # 本地开发（无 PHP 时使用内置假数据）
pnpm build      # 类型检查 + 构建到 zb_system/admin-next/assets/
pnpm typecheck
```

开发模式下如果没有 `window.__ZB__`，会自动使用 `src/lib/zbp.ts` 里的假数据，
只用于调界面；接口相关逻辑请通过 PHP 入口验证：

```
http://<host>/zb_system/admin-next/index.php
```

## 目录

```
src/
├─ components/        布局与通用组件（ui/ 为 shadcn 组件源码）
├─ pages/             官方页面
├─ lib/               api 客户端、i18n、引导数据类型
├─ stores/            Zustand（仅 UI 状态）
└─ router.tsx         HashRouter + 路由级懒加载
```

## 约定

- 每个关注点只用一个库，新增依赖前先看计划文档的技术栈表
- 颜色只用语义 token（`bg-card`、`text-muted-foreground`），不写死色值
- 图标统一 `lucide-react`，尺寸 16 / 20 / 24
- 服务端数据只用 TanStack Query；Zustand 只放 UI 状态
- 筛选、分页放 URL，不放 store
