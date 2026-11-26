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

test("桌面侧栏折叠与展开时宽度和文字同步切换", async ({ page, isMobile }) => {
  test.skip(Boolean(isMobile), "桌面侧栏用例")

  const sidebar = page.locator("#zb-admin-root aside:visible")
  const toggle = page.getByRole("button", { name: "折叠/展开侧栏" })
  const articleLabel = sidebar.getByText("文章管理", { exact: true })

  await expect(sidebar).toHaveCSS("width", "240px")
  await expect
    .poll(() =>
      sidebar.evaluate((element) =>
        Number.parseFloat(getComputedStyle(element).transitionDuration)
      )
    )
    .toBeLessThanOrEqual(0.001)
  await expect(articleLabel).toBeVisible()

  await toggle.click()
  await expect(sidebar).toHaveCSS("width", "56px")
  await expect(articleLabel).toHaveCount(0)

  await toggle.click()
  await expect(sidebar).toHaveCSS("width", "240px")
  await expect(articleLabel).toBeVisible()
})

test("侧栏折叠状态刷新后保持", async ({ page, isMobile }) => {
  test.skip(Boolean(isMobile), "桌面侧栏用例")

  const sidebar = page.locator("#zb-admin-root aside:visible")
  const toggle = page.getByRole("button", { name: "折叠/展开侧栏" })

  await toggle.click()
  await expect(sidebar).toHaveCSS("width", "56px")
  await expect
    .poll(() =>
      page.evaluate(() => {
        const stored = JSON.parse(localStorage.getItem("zbp-admin-ui") || "{}")
        return stored.state?.sidebarCollapsed
      })
    )
    .toBe(true)

  await page.reload()
  await expectAdminShell(page)
  await expect(sidebar).toHaveCSS("width", "56px")
  await expect(sidebar.getByText("文章管理", { exact: true })).toHaveCount(0)

  await toggle.click()
  await expect(sidebar).toHaveCSS("width", "240px")
  await expect(sidebar.getByText("文章管理", { exact: true })).toBeVisible()
})

test("超宽屏正文左边缘紧邻侧栏", async ({ page, isMobile }) => {
  test.skip(Boolean(isMobile), "超宽屏桌面布局用例")

  await page.setViewportSize({ width: 2560, height: 1440 })

  const sidebar = page.locator("#zb-admin-root aside:visible")
  const main = page.locator("#zb-admin-root main")
  const sidebarBox = await sidebar.boundingBox()
  const mainBox = await main.boundingBox()

  expect(sidebarBox).not.toBeNull()
  expect(mainBox).not.toBeNull()
  expect(mainBox!.x).toBeCloseTo(sidebarBox!.x + sidebarBox!.width, 0)
})

test("移动端抽屉忽略桌面折叠状态并显示完整导航文字", async ({
  page,
  isMobile,
}) => {
  test.skip(!isMobile, "移动端抽屉用例")

  await page.evaluate(() => {
    localStorage.setItem(
      "zbp-admin-ui",
      JSON.stringify({
        state: { sidebarCollapsed: true, theme: "system" },
        version: 0,
      })
    )
  })
  await page.reload()
  await expectAdminShell(page)

  await page.getByRole("button", { name: "打开菜单" }).click()
  const drawer = page.getByRole("dialog", { name: "打开菜单" })
  await expect(drawer).toBeVisible()
  await expect(drawer).toHaveCSS("width", "256px")
  await expect(drawer.getByText("文章管理", { exact: true })).toBeVisible()

  await drawer.locator('a[href="#/posts"]').click()
  await expect(drawer).toBeHidden()
  await expect(page.locator("main")).toContainText("文章管理")
})

test("文章和页面旧版入口直达带 force=1 的后台动作", async ({ page }) => {
  const cases: Array<[string, string]> = [
    ["#/posts", "ArticleMng"],
    ["#/pages", "PageMng"],
  ]

  for (const [hash, action] of cases) {
    await page.goto(`zb_system/admin-next/index.php${hash}`)
    const link = page.getByRole("link", { name: "旧版（含插件列）" })
    await expect(link).toBeVisible()

    const href = await link.getAttribute("href")
    expect(href).not.toBeNull()
    const target = new URL(href!, page.url())
    expect(target.pathname).toBe("/zb_system/admin/index.php")
    expect(target.searchParams.get("act")).toBe(action)
    expect(target.searchParams.get("force")).toBe("1")
    expect(target.searchParams.has("ui")).toBe(false)
  }
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

test("ui=legacy 直接回退旧后台且不经 cmd.php", async ({ page }) => {
  const requests: string[] = []
  page.on("request", (request) => requests.push(request.url()))

  await page.goto("zb_system/admin-next/index.php?ui=legacy")

  await expect(page).toHaveURL(/\/zb_system\/admin\/index\.php\?force=1$/)
  await expect(page.locator("body.body-admin")).toBeVisible()
  await expect(page.locator("header.header .logo")).toBeVisible()
  expect(
    requests.some((url) => url.includes("/zb_system/cmd.php?act=admin"))
  ).toBe(false)
})

test("深链可直接打开（hash 路由）", async ({ page }) => {
  await page.goto("zb_system/admin-next/index.php#/plugins")
  await expect(page.locator("main")).toContainText("插件管理")

  await page.reload()
  await expect(page.locator("main")).toContainText("插件管理")
})
