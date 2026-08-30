# backend-legacy 开发构建使用文档（pnpm）

本目录提供 `zb_system/admin2/backend-legacy` 的前端构建与开发工具链，基于 Rollup、Sass、PostCSS 与 BrowserSync，包管理器为 pnpm。

- 入口源码：`src/main.js`（会引入 `sass/backend-legacy.scss`）
- 构建产物：`dist/backend-legacy.js` 与 `dist/backend-legacy.css`
- 产物复制：构建完成后会自动复制到上级应用目录的 `script/` 与 `style/` 目录，用于后台页面实际引用

> 备注：构建产物文件名以上级应用目录名为准（`appName`），本项目为 `backend-legacy`，因此产物名为 `backend-legacy.{js/css}`。

---

## 环境要求

- Node.js（使用最新 LTS 版本）
- pnpm（全局安装）

安装 pnpm：

```bash
# 安装 pnpm （使用国内镜像源以加速下载）
npm install -g pnpm --registry=https://registry.npmmirror.com


```

---

## 安装依赖

在 `dev_rollup/` 目录下执行：

```bash
pnpm install # 简写 pnpm i

```

安装后会自动将 `.env.sample` 复制为 `.env`（若 `.env` 不存在）。

---

## 环境变量配置（.env）

示例见 `.env.sample`：

```dotenv
PROXY=http://127.0.0.1:8081/
ZB_SYSTEM=/root/www/zbp/zb_system/

```

- `PROXY`：BrowserSync 代理目标地址（本地站点地址）。未设置时默认使用 `http://localhost`。
- `ZB_SYSTEM`：用于开发模式下文件变更监听的 Z-Blog 系统目录路径（监听其中的 `**/*.php`）。

请根据本机环境调整为可访问的地址与正确的系统路径。

---

## 常用命令

```bash
# 开发模式（监听构建 + 浏览器自动刷新）
pnpm run dev

# 生产构建（一次性构建并复制产物）
pnpm run build

# 代码检查（如本地配置了 ESLint）
pnpm run lint

```

- `dev`：
  - 开启 Rollup 监听与 BrowserSync 代理。
  - 监听文件：`ZB_SYSTEM/**/*.php` 与上级应用目录的 `style/**/*.css`。
  - 修改源码（JS/Sass）后会自动重新构建并刷新浏览器。
- `build`：
  - 产出 `dist/backend-legacy.js` 与 `dist/backend-legacy.css`。
  - 自动复制到上级应用目录的 `script/` 与 `style/`，便于后台使用。

---

## 构建管线与目录说明

- Rollup 配置：`rollup.config.mjs`
  - 入口：`src/main.js`
  - 输出：`dist/${appName}.js`（ESM），同时提取 CSS 为 `dist/${appName}.css`
  - 插件：
    - `rollup-plugin-postcss`（集成 Sass，`extract` 输出 CSS）
    - `rollup-plugin-copy`（在 `writeBundle` 时复制到上级 `script/` 与 `style/`）
    - `rollup-plugin-browsersync`（仅在 `NODE_ENV=dev` 时启用，用于代理与刷新）
- 源码结构：
  - JS：`src/main.js`（包含 jQuery 相关交互逻辑）
  - Sass：`sass/backend-legacy.scss` 为主入口，使用 `@use` 组织各模块：
    - `base/`：`_reset.scss`、`_other.scss`
    - `layout/`：`_all.scss`、`_header.scss`、`_sidebar.scss`、`_main.scss`
    - `components/`：`_tables.scss`、`_tabs.scss`、`_forms.scss`、`_pagebar.scss`
    - `pages/`：`_ThemeMng.scss`、`_ModuleMng.scss`、`_ArticleEdt.scss`

---

## 开发指引

- JS 开发：修改 `src/main.js`；本项目交互依赖 jQuery（确保后台页面已加载 jQuery）。
- 样式开发：在 `sass/` 下新增或调整模块，并在 `sass/backend-legacy.scss` 中通过 `@use` 引入；所有样式最终会提取到 `dist/backend-legacy.css`。
- 产物复制：构建完成后自动复制到上级应用目录的 `script/` 与 `style/`；无需手动移动文件。

---

## 进阶：PurgeCSS（可选）

`rollup.config.mjs` 中预置了 PurgeCSS（默认注释）。若需在生产构建中剔除未使用的 CSS，可按需开启并设置扫描范围，例如：

```js
// 在 rollup.config.mjs 的 postcss 配置内启用插件
plugins: [
  purgeCSSPlugin({
    content: [path.join(appPath, 'template', '*.php')],
  }),
]

```

启用前请确认模板路径与实际页面结构一致，以免误删必要样式。
