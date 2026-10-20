import { expect } from "@playwright/test"

import { expectAdminShell, login } from "../support"
import { test } from "../crud-support"
import { rowAction, rowByText, waitForList } from "../row-helpers"

/**
 * admin-next CRUD smoke tests.
 *
 * These create and delete their own records, so they are safe on a development
 * site but should not be pointed at production data.
 * Enable with ZBP_E2E_WRITE=1.
 */

const writeEnabled = process.env.ZBP_E2E_WRITE === "1"

/** 等待一次列表刷新后再断言（发送动作前挂监听，避免错过响应）。 */
async function actAndSettle(
  page: Parameters<typeof waitForList>[0],
  mod: string,
  action: () => Promise<void>
) {
  const settled = waitForList(page, mod)
  await action()
  await settled
}

test.beforeEach(async ({ page }) => {
  const loggedIn = await login(page)
  test.skip(!loggedIn, "ZBP_USER / ZBP_PASS required")
  await page.goto("zb_system/admin-next/index.php#/")
  await expectAdminShell(page)
})

/** 进入指定 hash 路由，并等待目标页面渲染完成。 */
async function gotoPage(page: Parameters<typeof waitForList>[0], hash: string, marker: string) {
  await page.goto(`zb_system/admin-next/index.php${hash}`)
  await expect(page.getByRole("heading", { name: marker })).toBeVisible()
}

test.describe("分类增删改", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/categories", "分类管理")
  })

  test("可以新建、编辑并删除分类", async ({ page }) => {
    const name = `E2E-Category-${Date.now()}`

    await page.getByRole("button", { name: "新建" }).click()
    const dialog = page.getByRole("dialog")
    await expect(dialog).toBeVisible()
    await dialog.locator("#term-name").fill(name)

    await actAndSettle(page, "category", async () => {
      await dialog.getByRole("button", { name: "保存" }).click()
    })
    await expect(dialog).toBeHidden()
    await expect(page.locator("table")).toContainText(name)

    await rowAction(rowByText(page, name), "编辑").click()
    await expect(dialog).toBeVisible()
    await dialog.locator("#term-name").fill(`${name}-x`)

    await actAndSettle(page, "category", async () => {
      await dialog.getByRole("button", { name: "保存" }).click()
    })
    await expect(dialog).toBeHidden()
    await expect(page.locator("table")).toContainText(`${name}-x`)

    await rowAction(rowByText(page, `${name}-x`), "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    await actAndSettle(page, "category", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })
    await expect(page.locator("table")).not.toContainText(`${name}-x`)
  })
})

test.describe("标签增删", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/tags", "标签管理")
  })

  test("可以新建并删除标签", async ({ page }) => {
    const name = `E2E-Tag-${Date.now()}`

    await page.getByRole("button", { name: "新建" }).click()
    const dialog = page.getByRole("dialog")
    await expect(dialog).toBeVisible()
    await dialog.locator("#term-name").fill(name)

    await actAndSettle(page, "tag", async () => {
      await dialog.getByRole("button", { name: "保存" }).click()
    })
    await expect(dialog).toBeHidden()
    await expect(page.locator("table")).toContainText(name)

    await rowAction(rowByText(page, name), "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    await actAndSettle(page, "tag", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })
    await expect(page.locator("table")).not.toContainText(name)
  })
})

test.describe("模块增删", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/modules", "模块管理")
  })

  test("可以新建并删除自定义模块", async ({ page }) => {
    const name = `E2E-Module-${Date.now()}`

    await page.getByRole("button", { name: "新建" }).click()
    const dialog = page.getByRole("dialog")
    await expect(dialog).toBeVisible()
    await dialog.locator("#module-name").fill(name)
    await dialog.locator("#module-content").fill("<p>e2e</p>")

    await actAndSettle(page, "module", async () => {
      await dialog.getByRole("button", { name: "保存" }).click()
    })
    await expect(dialog).toBeHidden()
    await expect(page.locator("table")).toContainText(name, {
      timeout: 10_000,
    })

    await rowAction(rowByText(page, name), "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    await actAndSettle(page, "module", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })
    await expect(page.locator("table")).not.toContainText(name)
  })
})

test.describe("评论管理", () => {
  test.beforeEach(async ({ page }) => {
    await page.goto("zb_system/admin-next/index.php#/comments")
  })

  test("评论列表可以打开编辑弹窗并取消", async ({ page }) => {
    const editButton = page.locator('button[aria-label="编辑"]').first()
    test.skip((await editButton.count()) === 0, "站点没有评论")

    await editButton.click()
    const dialog = page.getByRole("dialog")
    await expect(dialog).toBeVisible()
    await expect(dialog.locator("#comment-content")).toBeVisible()
    await dialog.getByRole("button", { name: "取消" }).click()
    await expect(dialog).toBeHidden()
  })
})

test.describe("附件上传", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/uploads", "附件管理")
  })

  test("可以上传附件并删除", async ({ page }) => {
    const stamp = Date.now()

    await actAndSettle(page, "upload", async () => {
      await page.locator('input[type="file"]').setInputFiles({
        name: `e2e-${stamp}.txt`,
        mimeType: "text/plain",
        buffer: Buffer.from("zblogphp admin-next e2e"),
      })
    })

    // 列表按 ID 倒序，刚上传的文件应在第一行；
    // 服务端会重命名文件，因此用 SourceName 里的原始名定位更稳。
    const firstRow = page.locator("table tbody tr").first()
    await expect(firstRow).toContainText("e2e-", { timeout: 10_000 })

    await rowAction(firstRow, "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    await actAndSettle(page, "upload", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })

    await expect(page.locator("table tbody tr").first()).not.toContainText(
      "e2e-"
    )
  })
})

test.describe("文章删除", () => {
  test.beforeEach(async ({ page }) => {
    await page.goto("zb_system/admin-next/index.php#/posts")
  })

  test("文章行提供删除入口且需要二次确认", async ({ page }) => {
    const deleteButton = page.locator('button[aria-label="删除"]').first()
    test.skip((await deleteButton.count()) === 0, "站点没有文章")

    await deleteButton.click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    // 只验证弹窗，不真的删除内容
    await confirm.getByRole("button", { name: "取消" }).click()
    await expect(confirm).toBeHidden()
  })
})
