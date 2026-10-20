import { expect, test } from "@playwright/test"

import { expectAdminShell, login } from "../support"

/**
 * admin-next 冒烟测试：只验证「能进入 + 导航可用 + 主要页面渲染」，
 * 不覆盖数据写入，避免污染站点内容。
 */

test.beforeEach(async ({ page }) => {
  const loggedIn = await login(page)
  test.skip(!loggedIn, "需要 ZBP_USER / ZBP_PASS 才能运行冒烟测试")
  await page.goto("zb_system/admin-next/index.php#/")
  await expectAdminShell(page)
})

test("仪表盘渲染统计与环境信息", async ({ page }) => {
  await expect(page.getByRole("heading", { name: "仪表盘" })).toBeVisible()
  await expect(page.getByText("环境信息")).toBeVisible()
  await expect(page.getByText("运行环境与版本")).toBeVisible()

  for (const label of ["文章", "页面", "评论", "分类", "用户", "浏览"]) {
    await expect(page.getByText(label, { exact: true }).first()).toBeVisible()
  }
})

test("侧边栏导航可跳转到各个列表页", async ({ page }) => {
  const cases: Array<[string, string, string]> = [
    ["文章管理", "#/posts", "文章管理"],
    ["页面管理", "#/pages", "页面管理"],
    ["分类管理", "#/categories", "分类管理"],
    ["标签管理", "#/tags", "标签管理"],
    ["评论管理", "#/comments", "评论管理"],
    ["用户管理", "#/members", "用户管理"],
    ["网站设置", "#/settings", "网站设置"],
  ]

  for (const [label, hash, heading] of cases) {
    // 深链直接进入目标页，避免移动端抽屉遮挡带来的不稳定
    await page.goto(`zb_system/admin-next/index.php${hash}`)
    await expect(page.locator("main")).toContainText(heading)
    expect(page.url()).toContain(hash)
  }
})

test("侧边栏点击可以导航（桌面端）", async ({ page, isMobile }) => {
  test.skip(Boolean(isMobile), "移动端侧栏在抽屉内，已在深链用例中覆盖")

  await page
    .locator("#zb-admin-root aside nav")
    .getByText("文章管理", { exact: true })
    .click()

  await expect(page.locator("main")).toContainText("文章管理")
  expect(page.url()).toContain("#/posts")
})

test("文章列表展示真实数据并可搜索", async ({ page }) => {
  await page.goto("zb_system/admin-next/index.php#/posts")

  await expect(page.locator("table")).toBeVisible()
  await expect(page.getByRole("columnheader", { name: "标题" })).toBeVisible()

  const rows = page.locator("table tbody tr")
  expect(await rows.count()).toBeGreaterThan(0)

  await page.getByPlaceholder(/搜索/).fill("不存在的内容-zbpn")
  await expect(page.getByText("暂无数据")).toBeVisible({ timeout: 10_000 })
})

test("设置页可以切换标签且表单可编辑", async ({ page }) => {
  await page.goto("zb_system/admin-next/index.php#/settings")

  await page.getByRole("tab", { name: "全局" }).click()
  await expect(page.getByText("后台界面")).toBeVisible()

  await page.getByRole("tab", { name: "基础" }).click()
  const subname = page.locator("#ZC_BLOG_SUBNAME")
  const original = await subname.inputValue()

  await subname.fill(`${original}-tz`)
  await expect(page.getByRole("button", { name: /保存 \(1\)/ })).toBeVisible()

  await page.getByRole("button", { name: "撤销" }).click()
  await expect(
    page.getByRole("button", { name: "保存", exact: true })
  ).toBeDisabled()
})

test("旧后台仍可访问（回退路径）", async ({ page }) => {
  await page.goto("zb_system/admin/index.php?act=admin&force=1")
  await expect(page.locator("body.body-admin")).toBeVisible()
  await expect(page.locator("header.header .logo")).toBeVisible()
})

test("深链可直接打开（hash 路由）", async ({ page }) => {
  await page.goto("zb_system/admin-next/index.php#/plugins")
  await expect(page.locator("main")).toContainText("插件管理")

  await page.reload()
  await expect(page.locator("main")).toContainText("插件管理")
})
