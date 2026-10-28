import { expect } from "@playwright/test"

import { expectAdminShell, login } from "../support"
import { test } from "../crud-support"
import { rowAction, rowByText, rowLink, waitForList } from "../row-helpers"

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
    // 列表是异步渲染的，先等表格出现再判断有没有评论
    await expect(page.locator("table tbody tr").first()).toBeVisible({
      timeout: 15_000,
    })
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
    const fileName = `e2e-${stamp}.txt`

    await actAndSettle(page, "upload", async () => {
      await page.locator('input[type="file"]').setInputFiles({
        name: fileName,
        mimeType: "text/plain",
        buffer: Buffer.from("zblogphp admin-next e2e"),
      })
    })

    // 列表按 ID 倒序，刚上传的文件应在第一行；
    // 服务端会重命名文件，因此用 SourceName 里的原始名定位更稳。
    const uploadedRow = rowByText(page, fileName)
    await expect(uploadedRow).toBeVisible({ timeout: 10_000 })

    await rowAction(uploadedRow, "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    await actAndSettle(page, "upload", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })

    await expect(rowByText(page, fileName)).toHaveCount(0)
  })
})

test.describe("文章删除", () => {
  test.beforeEach(async ({ page }) => {
    await page.goto("zb_system/admin-next/index.php#/posts")
  })

  test("文章行提供删除入口且需要二次确认", async ({ page }) => {
    // 列表是异步渲染的，先等表格出现再判断有没有文章
    await expect(page.locator("table tbody tr").first()).toBeVisible({
      timeout: 15_000,
    })
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

test.describe("文章编辑器", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
  })

  test("新建页加载插件编辑器，没有则回退纯文本", async ({ page }) => {
    await page.goto("zb_system/admin-next/index.php#/posts/new")
    await expect(page.getByRole("heading", { name: "新建文章" })).toBeVisible()

    const blockEditor = page.locator(".zbpe-content")
    const fallback = page.locator('textarea[aria-label="正文"]')

    // 编辑器是异步加载的：先等两者之一出现
    await expect(blockEditor.or(fallback).first()).toBeVisible({
      timeout: 15_000,
    })

    test.skip((await blockEditor.count()) === 0, "未启用区块编辑器插件")

    await expect(page.locator(".zbpe-btn")).not.toHaveCount(0)
    // 有插件编辑器时不应再显示回退文本框
    await expect(fallback).toHaveCount(0)
  })

  test("可以新建文章、回填编辑并删除", async ({ page }) => {
    const stamp = Date.now()
    const title = `E2E-Post-${stamp}`
    const editor = page.locator(".zbpe-content")
    const fallback = page.locator('textarea[aria-label="正文"]')

    await page.goto("zb_system/admin-next/index.php#/posts/new")
    await expect(page.getByRole("heading", { name: "新建文章" })).toBeVisible()
    await expect(editor.or(fallback).first()).toBeVisible({ timeout: 15_000 })

    await page.fill('input[aria-label="标题"]', title)

    const useBlockEditor = (await editor.count()) > 0
    if (useBlockEditor) {
      await editor.click()
      await page.keyboard.type(`第一段 ${stamp}`)
      // 分隔符在编辑器内显示为 <hr class="more">
      await page.getByRole("button", { name: "插入更多分隔符" }).click()
      await page.keyboard.type("第二段")
    } else {
      await fallback.fill(`<p>第一段 ${stamp}</p><hr class="more" /><p>第二段</p>`)
    }

    await actAndSettle(page, "post", async () => {
      await page.getByRole("button", { name: /发布/ }).click()
    })

    // 新建成功后原地转到编辑态
    await expect(page).toHaveURL(/posts\/\d+\/edit/)
    const postId = Number(page.url().match(/posts\/(\d+)\/edit/)![1])

    // 列表里能看到新文章
    await page.goto("zb_system/admin-next/index.php#/posts")
    const row = rowByText(page, title)
    await expect(row).toBeVisible({ timeout: 15_000 })

    // 打开编辑页，标题与正文都应回填
    await rowLink(row, "编辑").click()
    await expect(page).toHaveURL(new RegExp(`posts/${postId}/edit`))
    await expect(page.locator('input[aria-label="标题"]')).toHaveValue(title)
    if (useBlockEditor) {
      await expect(editor).toContainText(`第一段 ${stamp}`)
      await expect(editor.locator('hr.more')).toHaveCount(1)
    } else {
      await expect(fallback).toHaveValue(new RegExp(`第一段 ${stamp}`))
    }

    // 改标题后保存
    const renamed = `${title}-改`
    await page.fill('input[aria-label="标题"]', renamed)
    await page.getByRole("button", { name: /发布/ }).click()
    await expect(page.getByText("已保存")).toBeVisible({ timeout: 15_000 })

    // 回列表确认改名生效，然后删除
    await page.goto("zb_system/admin-next/index.php#/posts")
    const renamedRow = rowByText(page, renamed)
    await expect(renamedRow).toBeVisible({ timeout: 15_000 })

    await rowAction(renamedRow, "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()
    await actAndSettle(page, "post", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })

    await expect(rowByText(page, renamed)).toHaveCount(0)
  })

  test("页面同样可以通过编辑器新建", async ({ page }) => {
    const stamp = Date.now()
    const title = `E2E-Page-${stamp}`

    await page.goto("zb_system/admin-next/index.php#/pages/new")
    await expect(page.getByRole("heading", { name: "新建页面" })).toBeVisible()

    await page.fill('input[aria-label="标题"]', title)

    const editor = page.locator(".zbpe-content")
    const fallback = page.locator('textarea[aria-label="正文"]')
    await expect(editor.or(fallback).first()).toBeVisible({ timeout: 15_000 })
    if ((await editor.count()) > 0) {
      await editor.click()
      await page.keyboard.type(`页面正文 ${stamp}`)
    } else {
      await fallback.fill(`<p>页面正文 ${stamp}</p>`)
    }

    await actAndSettle(page, "post", async () => {
      await page.getByRole("button", { name: /发布/ }).click()
    })
    await expect(page).toHaveURL(/pages\/\d+\/edit/)

    await page.goto("zb_system/admin-next/index.php#/pages")
    const row = rowByText(page, title)
    await expect(row).toBeVisible({ timeout: 15_000 })

    await rowAction(row, "删除").click()
    const confirm = page.getByRole("alertdialog")
    await actAndSettle(page, "post", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })
    await expect(rowByText(page, title)).toHaveCount(0)
  })
})

test.describe("评论审核", () => {
  test("可以在待审与已审之间切换", async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await page.goto("zb_system/admin-next/index.php#/comments")
    await expect(page.getByRole("heading", { name: "评论管理" })).toBeVisible()

    // 优先挑一条待审核的评论，避免默认首条已被审核。
    // 列表异步渲染，先等表格出现再定位。
    await expect(page.locator("table tbody tr").first()).toBeVisible({
      timeout: 15_000,
    })

    const pendingRow = page
      .locator("table tbody tr")
      .filter({ has: page.getByText("待审核", { exact: true }) })
      .first()
    const toggle = (await pendingRow.count())
      ? pendingRow.locator('button[aria-label="通过审核"]')
      : page.locator('button[aria-label="转为待审"]').first()

    // 用断言代替 test.skip：站点已有评论时不应被跳过
    await expect(toggle).toBeVisible({ timeout: 15_000 })

    // 这一步点击后，该行按钮应从「通过审核」变成「转为待审」
    await actAndSettle(page, "comment", async () => {
      await toggle.click()
    })
    await expect(
      page.locator('button[aria-label="转为待审"]').first()
    ).toBeVisible({ timeout: 15_000 })

    // 还原：再点一次同一位置（此时它显示为「转为待审」）
    await actAndSettle(page, "comment", async () => {
      await page.locator('button[aria-label="转为待审"]').first().click()
    })
  })
})
