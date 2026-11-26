import { expect, test } from "@playwright/test"

import { expectAdminShell, login } from "../support"

test.beforeEach(async ({ page }) => {
  const loggedIn = await login(page)
  test.skip(!loggedIn, "需要 ZBP_USER / ZBP_PASS 才能运行无障碍测试")
  await page.goto("zb_system/admin-next/index.php#/")
  await expectAdminShell(page)
})

test("skip link 获得可见焦点并将焦点转移到主内容", async ({ page }) => {
  const skipLink = page.getByRole("link", { name: "跳到主内容" })
  const originalHash = new URL(page.url()).hash

  await page.keyboard.press("Tab")
  await expect(skipLink).toBeFocused()
  await expect(skipLink).toBeVisible()
  await expect
    .poll(() =>
      skipLink.evaluate((element) => {
        const rect = element.getBoundingClientRect()
        return (
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.right <= window.innerWidth &&
          rect.bottom <= window.innerHeight
        )
      })
    )
    .toBe(true)

  await page.keyboard.press("Enter")

  const main = page.getByRole("main", { name: "主内容" })
  await expect(main).toBeFocused()
  await expect(main).toHaveAttribute("id", "admin-main-content")
  await expect(main).toHaveAttribute("tabindex", "-1")
  expect(new URL(page.url()).hash).toBe(originalHash)
})

test("桌面侧栏可通过键盘开关且折叠链接保留可访问名称", async ({
  page,
  isMobile,
}) => {
  test.skip(Boolean(isMobile), "桌面侧栏用例")

  const navigation = page.getByRole("navigation", { name: "主导航" })
  const toggle = page.getByRole("button", { name: "折叠/展开侧栏" })
  const articleLink = navigation.getByRole("link", { name: "文章管理" })

  await expect(navigation).toBeVisible()
  await expect(toggle).toHaveAttribute("aria-expanded", "true")
  await toggle.focus()

  await page.keyboard.press("Enter")
  await expect(toggle).toBeFocused()
  await expect(toggle).toHaveAttribute("aria-expanded", "false")
  await expect(articleLink).toBeVisible()
  await expect(articleLink).toHaveCount(1)

  await page.keyboard.press("Space")
  await expect(toggle).toBeFocused()
  await expect(toggle).toHaveAttribute("aria-expanded", "true")
  await expect(navigation.getByText("文章管理", { exact: true })).toBeVisible()
})

test("移动抽屉支持键盘开关、Escape 关闭和焦点回转", async ({
  page,
  isMobile,
}) => {
  test.skip(!isMobile, "移动端抽屉用例")

  const trigger = page.getByRole("button", { name: "打开菜单" })
  const triggerState = page.locator('button[aria-controls="admin-mobile-menu"]')

  await trigger.focus()
  await page.keyboard.press("Enter")

  const drawer = page.getByRole("dialog", { name: "导航菜单" })
  await expect(drawer).toBeVisible()
  await expect(triggerState).toHaveAttribute("aria-expanded", "true")
  await expect(
    drawer.getByRole("navigation", { name: "移动端导航" })
  ).toBeVisible()

  const closeButton = drawer.getByRole("button", { name: "关闭菜单" })
  await expect(closeButton).toBeVisible()
  const closeBox = await closeButton.boundingBox()
  expect(closeBox).not.toBeNull()
  expect(closeBox!.width).toBeGreaterThanOrEqual(44)
  expect(closeBox!.height).toBeGreaterThanOrEqual(44)

  await page.keyboard.press("Escape")
  await expect(drawer).toBeHidden()
  await expect(trigger).toBeFocused()
  await expect(triggerState).toHaveAttribute("aria-expanded", "false")

  await page.keyboard.press("Space")
  await expect(drawer).toBeVisible()
  await closeButton.focus()
  await page.keyboard.press("Enter")
  await expect(drawer).toBeHidden()
  await expect(trigger).toBeFocused()
  await expect(triggerState).toHaveAttribute("aria-expanded", "false")
})

test("指定断点下 document.scrollWidth 不超过 viewport", async ({ page }) => {
  const widths = [320, 375, 768, 1024, 1440, 1920]

  await expect(page.getByRole("heading", { name: "仪表盘" })).toBeVisible()

  for (const width of widths) {
    await page.setViewportSize({ width, height: 900 })
    await expect
      .poll(
        () =>
          page.evaluate(() => {
            const documentWidth = (
              document as Document & { scrollWidth?: number }
            ).scrollWidth
            const scrollWidth =
              typeof documentWidth === "number"
                ? documentWidth
                : document.documentElement.scrollWidth
            return scrollWidth - window.innerWidth
          }),
        { message: `${width}px 断点存在横向溢出` }
      )
      .toBeLessThanOrEqual(0)
  }
})
