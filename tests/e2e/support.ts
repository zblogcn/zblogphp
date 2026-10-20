import { expect, test as base } from "@playwright/test"

/**
 * 通过站点自带的登录接口完成登录，再复用 storageState。
 *
 * 需要设置环境变量：
 *   ZBP_USER / ZBP_PASS   管理员账号密码
 *   ZBP_BASE_URL          站点根地址（默认 http://127.0.0.1:8099/）
 *
 * 未提供账号时，依赖预先存在的 .state/auth.json（可用 npx playwright codegen 生成）。
 */
export const test = base

export async function login(page: import("@playwright/test").Page) {
  const user = process.env.ZBP_USER
  const password = process.env.ZBP_PASS

  if (!user || !password) {
    return false
  }

  await page.goto("zb_system/login.php")
  await page.fill("#edtUserName", user)
  await page.fill("#edtPassWord", password)

  // 旧登录页会先做一次 md5 再提交隐藏字段
  await Promise.all([
    page.waitForURL(/admin-next\/index\.php|admin\/index\.php/, {
      timeout: 15_000,
    }),
    page.click("#btnPost"),
  ])

  return true
}

export async function expectAdminShell(page: import("@playwright/test").Page) {
  await expect(page.locator("#zb-admin-root")).toBeVisible()

  // 移动端侧栏收在抽屉里，只校验顶部栏；桌面端校验侧边导航
  const aside = page.locator("#zb-admin-root aside")
  if (await aside.isVisible()) {
    await expect(aside.locator("nav")).toContainText("文章管理")
  } else {
    await expect(
      page.getByRole("button", { name: "打开菜单" })
    ).toBeVisible()
  }
}
