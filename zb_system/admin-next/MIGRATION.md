# admin-next 迁移与回退

## 默认行为

从本版本开始，登录后与后台入口默认落到新版 SPA：

```
zb_system/cmd.php?act=admin   →   zb_system/admin-next/index.php
```

旧后台 `zb_system/admin/index.php` 仍然可用，检测到新版可用时会跳转，
加上 `?force=1` 可强制停留在旧后台。

## 回到旧后台的三种方式

| 方式 | 写法 | 适用范围 |
|---|---|---|
| URL 参数 | `/zb_system/admin/index.php?force=1` | 临时，单次 |
| 全局设置 | 设置 → 全局 → 后台界面 → 旧版后台（表格） | 长期，写入 `ZC_ADMIN_UI` |
| 前台入口 | `cmd.php?act=admin&ui=legacy` | 临时 |

在设置里切到「旧版后台」后，所有登录跳转、`cmd.php` 重定向都会回到旧后台，
不需要改任何文件。

## 新版后台覆盖范围

| 功能 | 新版 | 旧版 |
|---|---|---|
| 仪表盘 / 统计 | ✅ | ✅ |
| 文章、页面、分类、标签、评论、附件、用户 列表 | ✅ | ✅ |
| 批量删除文章、批量评论操作 | ✅ | ✅ |
| 网站设置（含后台界面切换） | ✅ | ✅ |
| 插件启停、主题切换 | ✅ | ✅ |
| 文章编辑 | ❌ 跳旧版 | ✅ |
| 附件上传 | ❌ 跳旧版 | ✅ |
| 插件安装/卸载 | ❌ 跳旧版 | ✅ |
| 插件自带页面（已声明 admin-next） | ✅ SPA | ✅ `main.php` |
| 插件自带页面（未升级） | ✅ 新窗口打开旧页面 | ✅ |

## 已知差异

- **文章编辑页**：新版尚未实现，列表页的「新建」「编辑」按钮直接打开旧版编辑页。
- **插件表格行钩子**：`Filter_Plugin_Admin_ArticleMng_Table` 追加的列在新版列表不显示，
  页面提供「旧版（含插件列）」入口。
- **后台外观**：`AdminColor` 等换肤插件只作用于旧后台。

## 回退判定逻辑

```
1. URL 带 ui=legacy            → 旧后台
2. ZC_ADMIN_UI = legacy        → 旧后台
3. 以上都不满足                → 新版后台
```

对应实现：`zb_system/function/c_system_admin_ui.php` 的 `AdminUiIsNext()`。

## 升级注意

- 本版本要求 PHP 8.0 及以上，安装器与 `composer.json` 已同步更新。
- 升级不会改动已有的 `ZC_ADMIN_UI` 值；全新安装默认为 `next`。
- 若新版出现白屏，先在 URL 加 `?ui=legacy` 进入旧后台，再到设置里切换。

## 测试

| 命令 | 范围 |
|---|---|
| `vendor/bin/phpunit tests/phpunit` | 全量 PHP 单元测试（含 admin-next） |
| `vendor/bin/phpunit -c tests/phpunit-admin-next.xml` | 只跑 admin-next 相关用例 |
| `cd tests/e2e && pnpm test` | 浏览器冒烟测试（见 `tests/e2e/README.md`） |

admin-next 的单元测试位于 `tests/phpunit/AdminNextNavTest.php` 与
`AdminNextHomeUrlTest.php`，覆盖导航采集、旧菜单解析、`<admin>` 声明解析、
清单校验（路径穿越 / 路由越界 / 版本忽略）以及后台入口切换逻辑。

浏览器冒烟测试覆盖「进入后台、导航、列表、设置、旧后台回退、深链刷新」，
不写入数据，可对测试站点直接运行。

