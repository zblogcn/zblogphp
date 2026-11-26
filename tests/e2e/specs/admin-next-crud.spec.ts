import { expectAdminShell, login } from "../support"
import {
  cleanupCommentRecords,
  cleanupNamedRecords,
  cleanupPostRecords,
  cleanupUploadRecords,
  expect,
  fixtureName,
  restoreCommentFixture,
  test,
} from "../crud-support"
import {
  rowAction,
  rowByText,
  rowLink,
  waitForApi,
  waitForList,
} from "../row-helpers"

/**
 * admin-next CRUD smoke tests.
 *
 * 写用例只接受显式隔离标记与 loopback 地址；每个用例创建自己的唯一 fixture，
 * 并由 afterEach/finally 按 ID 或精确名称强制清理。
 */

const writeRequested = process.env.ZBP_E2E_WRITE === "1"
const isolatedLoopback = (() => {
  try {
    const url = new URL(process.env.ZBP_BASE_URL ?? "http://127.0.0.1:8099/")
    return (
      process.env.ZBP_E2E_ISOLATED === "1" &&
      (url.protocol === "http:" || url.protocol === "https:") &&
      ["127.0.0.1", "localhost", "[::1]", "::1"].includes(url.hostname)
    )
  } catch {
    return false
  }
})()

if (writeRequested && !isolatedLoopback) {
  throw new Error(
    "ZBP_E2E_WRITE=1 requires ZBP_E2E_ISOLATED=1 and a loopback ZBP_BASE_URL"
  )
}

const writeEnabled = writeRequested
const seededComment = process.env.ZBP_E2E_COMMENT_MARKER ?? "Isolated E2E comment"
const seededUser = process.env.ZBP_E2E_USER_NAME ?? "e2e_user"

/** 等待一次 API 落定；监听必须在动作前挂好。 */
async function actAndSettle<T = unknown>(
  page: Parameters<typeof waitForList>[0],
  mod: string,
  action: () => Promise<void>,
  act = "list"
): Promise<T> {
  const [data] = await Promise.all([waitForApi<T>(page, mod, act), action()])
  return data
}

test.beforeEach(async ({ page }) => {
  const loggedIn = await login(page)
  test.skip(!loggedIn, "ZBP_USER / ZBP_PASS required")
  await page.goto("zb_system/admin-next/index.php#/")
  await expectAdminShell(page)
})

/** 进入指定 hash 路由；列表页同时严格校验列表 API。 */
async function gotoPage(
  page: Parameters<typeof waitForList>[0],
  hash: string,
  marker: string,
  mod?: string
) {
  const settled = mod ? waitForList(page, mod) : Promise.resolve()
  await page.goto(`zb_system/admin-next/index.php${hash}`)
  await settled
  await expect(page.getByRole("heading", { name: marker })).toBeVisible()
}

test.describe("分类增删改", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/categories", "分类管理", "category")
  })

  test("可以新建、编辑并删除分类", async ({ page, writeFixture }) => {
    const name = fixtureName(writeFixture, "Category")
    const renamed = `${name}-x`
    writeFixture.cleanup((api) =>
      cleanupNamedRecords(api, "category", [name, renamed])
    )

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
    await dialog.locator("#term-name").fill(renamed)

    await actAndSettle(page, "category", async () => {
      await dialog.getByRole("button", { name: "保存" }).click()
    })
    await expect(dialog).toBeHidden()
    await expect(page.locator("table")).toContainText(renamed)

    await rowAction(rowByText(page, renamed), "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    await actAndSettle(page, "category", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })
    await expect(page.locator("table")).not.toContainText(renamed)
  })
})

test.describe("标签增删", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/tags", "标签管理", "tag")
  })

  test("可以新建并删除标签", async ({ page, writeFixture }) => {
    const name = fixtureName(writeFixture, "Tag")
    writeFixture.cleanup((api) => cleanupNamedRecords(api, "tag", [name]))

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
    await gotoPage(page, "#/modules", "模块管理", "module")
  })

  test("可以新建并删除自定义模块", async ({ page, writeFixture }) => {
    const name = fixtureName(writeFixture, "Module")
    writeFixture.cleanup((api) => cleanupNamedRecords(api, "module", [name]))

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

test.describe("用户删除确认", () => {
  test("按删除数据选项说明文章评论处理方式，并明确附件会删除", async ({ page }) => {
    const memberListResponse = waitForList<{
      delete_member_with_all_data: boolean
    }>(page, "member")
    await gotoPage(page, "#/members", "用户管理")
    const memberList = await memberListResponse
    const deleteWithAllData = memberList.delete_member_with_all_data === true

    const userRow = rowByText(page, seededUser)
    await expect(userRow).toBeVisible({ timeout: 15_000 })
    const deleteButton = rowAction(userRow, "删除")
    test.skip((await deleteButton.count()) === 0, "没有可删除的其他用户")

    await deleteButton.click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()
    await expect(confirm).toContainText(
      deleteWithAllData ? "文章、评论和附件将被永久删除" : "文章和评论将保留"
    )
    await expect(confirm).toContainText("附件")
    await confirm.getByRole("button", { name: "取消" }).click()
    await expect(confirm).toBeHidden()
  })
})

test.describe("评论管理", () => {
  test.beforeEach(async ({ page }) => {
    await gotoPage(page, "#/comments", "评论管理", "comment")
  })

  test("评论列表可以打开编辑弹窗并取消", async ({ page }) => {
    const commentRow = rowByText(page, seededComment)
    await expect(commentRow).toBeVisible({ timeout: 15_000 })
    const editButton = rowAction(commentRow, "编辑")
    test.skip((await editButton.count()) === 0, "站点没有隔离评论 fixture")

    await editButton.click()
    const dialog = page.getByRole("dialog")
    await expect(dialog).toBeVisible()
    await expect(dialog.locator("#comment-content")).toBeVisible()
    await dialog.getByRole("button", { name: "取消" }).click()
    await expect(dialog).toBeHidden()
  })

  test("编辑自建评论时数量不变、内容更新且作者不变，随后还原", async ({
    page,
    writeFixture,
  }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    const originalContent = fixtureName(writeFixture, "Comment")
    const updatedContent = fixtureName(writeFixture, "CommentEdited")
    let commentId = 0
    writeFixture.cleanup((api) =>
      cleanupCommentRecords(api, commentId, [originalContent, updatedContent])
    )

    const created = await writeFixture.api<{ comment: { ID: number } }>(
      "comment",
      "post",
      {
        method: "POST",
        body: {
          postid: 1,
          content: originalContent,
          email: "e2e-comment@example.invalid",
        },
      }
    )
    commentId = Number(created.comment.ID)
    if (!Number.isSafeInteger(commentId) || commentId <= 0) {
      throw new Error("评论 fixture 创建失败")
    }

    interface CommentFixtureRow {
      ID: number | string
      Content: string
      AuthorID: number
      Name: string
      Email: string
      Author?: { Name?: string }
    }
    interface CommentFixtureList {
      list: CommentFixtureRow[]
      pagebar: { AllCount: number }
    }
    const listComments = () =>
      writeFixture.api<CommentFixtureList>("comment", "list", {
        query: { manage: 1, perpage: 100 },
      })

    let original: CommentFixtureRow | undefined
    try {
      // beforeEach 已经在 #/comments；创建评论后重新加载并等待新的列表响应，
      // 不能对同一 hash 再 page.goto，否则不会产生列表请求。
      const refreshed = waitForList(page, "comment")
      await page.reload()
      await refreshed
      await expect(page.getByRole("heading", { name: "评论管理" })).toBeVisible()
      const beforeList = await listComments()
      original = beforeList.list.find((row) => Number(row.ID) === commentId)
      if (!original) {
        throw new Error(`评论 fixture ID ${commentId} 未出现在管理列表`)
      }
      const countBefore = beforeList.pagebar.AllCount
      const authorName = original.Author?.Name ?? original.Name
      const commentRow = rowByText(page, originalContent)
      await expect(commentRow).toBeVisible()
      await expect(commentRow).toContainText(authorName)

      await rowAction(commentRow, "编辑").click()
      const dialog = page.getByRole("dialog")
      await expect(dialog).toBeVisible()
      await expect(dialog.locator("#comment-content")).toHaveValue(
        originalContent
      )
      await dialog.locator("#comment-content").fill(updatedContent)
      await actAndSettle(
        page,
        "comment",
        async () => {
          await dialog.getByRole("button", { name: "保存" }).click()
        },
        "put"
      )
      await expect(dialog).toBeHidden()

      const updatedRow = rowByText(page, updatedContent)
      await expect(updatedRow).toBeVisible()
      await expect(updatedRow).toContainText(authorName)
      const afterList = await listComments()
      expect(afterList.pagebar.AllCount).toBe(countBefore)
      const updated = afterList.list.find((row) => Number(row.ID) === commentId)
      expect(updated).toBeDefined()
      expect(updated!.Content).toBe(updatedContent)
      expect(updated!.AuthorID).toBe(original.AuthorID)
      expect(updated!.Name).toBe(original.Name)

      await rowAction(updatedRow, "编辑").click()
      await expect(dialog).toBeVisible()
      await expect(dialog.locator("#comment-content")).toHaveValue(
        updatedContent
      )
      await dialog.locator("#comment-content").fill(originalContent)
      await actAndSettle(
        page,
        "comment",
        async () => {
          await dialog.getByRole("button", { name: "保存" }).click()
        },
        "put"
      )
      await expect(dialog).toBeHidden()
      await expect(rowByText(page, originalContent)).toBeVisible()

      const restoredList = await listComments()
      const restored = restoredList.list.find((row) => Number(row.ID) === commentId)
      expect(restored?.Content).toBe(originalContent)
      expect(restored?.AuthorID).toBe(original.AuthorID)
      expect(restored?.Name).toBe(original.Name)
      expect(restoredList.pagebar.AllCount).toBe(countBefore)
    } finally {
      if (original) {
        await writeFixture.api("comment", "put", {
          method: "POST",
          body: {
            ID: commentId,
            Content: originalContent,
            Name: original.Name,
            Email: original.Email,
          },
        })
      }
    }
  })
})

test.describe("附件上传", () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    await gotoPage(page, "#/uploads", "附件管理", "upload")
  })

  test("可以上传附件并删除", async ({ page, writeFixture }) => {
    const fileName = `${fixtureName(writeFixture, "Upload")}.txt`
    writeFixture.cleanup((api) =>
      cleanupUploadRecords(api, fileName, writeFixture.token)
    )

    await actAndSettle(page, "upload", async () => {
      await page.locator('input[type="file"]').setInputFiles({
        name: fileName,
        mimeType: "text/plain",
        buffer: Buffer.from("zblogphp admin-next e2e"),
      })
    })

    // 服务端可能重命名文件，用 SourceName 里的原始名定位。
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
    await gotoPage(page, "#/posts", "文章管理", "post")
  })

  test("文章行提供删除入口且需要二次确认", async ({ page }) => {
    const deleteButton = page.locator('button[aria-label="删除"]').first()
    test.skip((await deleteButton.count()) === 0, "站点没有文章")

    await deleteButton.click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()

    // 只验证弹窗，不真的删除内容。
    await confirm.getByRole("button", { name: "取消" }).click()
    await expect(confirm).toBeHidden()
  })
})

test.describe("文章编辑器", () => {
  test.beforeEach(async () => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
  })

  test("新建页加载插件编辑器，没有则回退纯文本", async ({ page }) => {
    await page.goto("zb_system/admin-next/index.php#/posts/new")
    await expect(page.getByRole("heading", { name: "新建文章" })).toBeVisible()

    const blockEditor = page.locator(".zbpe-content")
    const fallback = page.locator('textarea[aria-label="正文"]')

    await expect(blockEditor.or(fallback).first()).toBeVisible({
      timeout: 15_000,
    })

    test.skip((await blockEditor.count()) === 0, "未启用区块编辑器插件")

    await expect(page.locator(".zbpe-btn")).not.toHaveCount(0)
    await expect(fallback).toHaveCount(0)
  })

  test("BlockEditor 可上传并插入 1x1 PNG，且上传记录可清理", async ({
    page,
    writeFixture,
  }) => {
    const editor = page.locator(".zbpe-content")
    await page.goto("zb_system/admin-next/index.php#/posts/new")
    await expect(page.getByRole("heading", { name: "新建文章" })).toBeVisible()
    await expect(editor).toBeVisible({ timeout: 15_000 })
    test.skip((await editor.count()) === 0, "未启用区块编辑器插件")

    const fileName = `${fixtureName(writeFixture, "Image")}.png`
    const onePixelPng = Buffer.from(
      "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=",
      "base64"
    )
    writeFixture.cleanup((api) =>
      cleanupUploadRecords(api, fileName, writeFixture.token)
    )

    await page.fill('input[aria-label="标题"]', fixtureName(writeFixture, "ImagePost"))
    await editor.click()
    await page.keyboard.type(`图片测试 ${writeFixture.token}`)

    const fileChooserPromise = page.waitForEvent("filechooser")
    await page.getByRole("button", { name: "插入图片" }).click()
    const fileChooser = await fileChooserPromise
    const uploadedPromise = waitForApi<{
      upload: { ID: number | string; Name: string; Url: string }
    }>(page, "upload", "post")
    await fileChooser.setFiles({
      name: fileName,
      mimeType: "image/png",
      buffer: onePixelPng,
    })
    const { upload } = await uploadedPromise

    const image = editor.locator("img")
    await expect(image).toHaveCount(1)
    await expect(image).toBeVisible()
    await expect(image).toHaveAttribute(
      "src",
      new URL(upload.Url, page.url()).href
    )
    await expect
      .poll(() =>
        image.evaluate((element) => (element as HTMLImageElement).naturalWidth)
      )
      .toBe(1)

    const uploadList = await writeFixture.api<
      Array<{
        list: Array<{ ID: number | string; Name: string; SourceName?: string }>
      }>
    >("upload", "list", {
      query: { page: 1, perpage: 100, sortby: "ID", order: "DESC" },
    })
    expect(
      uploadList[0]?.list.some(
        (row) =>
          Number(row.ID) === Number(upload.ID) && row.SourceName === fileName
      )
    ).toBe(true)

    await cleanupUploadRecords(writeFixture.api, fileName, writeFixture.token)
  })

  test("可以新建文章、回填编辑并删除", async ({ page, writeFixture }) => {
    const title = fixtureName(writeFixture, "Post")
    const renamed = `${title}-改`
    const postIds = new Set<number>()
    writeFixture.cleanup((api) =>
      cleanupPostRecords(api, 0, [title, renamed], postIds)
    )
    const editor = page.locator(".zbpe-content")
    const fallback = page.locator('textarea[aria-label="正文"]')

    await page.goto("zb_system/admin-next/index.php#/posts/new")
    await expect(page.getByRole("heading", { name: "新建文章" })).toBeVisible()
    await expect(editor.or(fallback).first()).toBeVisible({ timeout: 15_000 })

    await page.fill('input[aria-label="标题"]', title)

    const useBlockEditor = (await editor.count()) > 0
    if (useBlockEditor) {
      await editor.click()
      await page.keyboard.type(`第一段 ${writeFixture.token}`)
      await page.getByRole("button", { name: "插入更多分隔符" }).click()
      await page.keyboard.type("第二段")
    } else {
      await fallback.fill(
        `<p>第一段 ${writeFixture.token}</p><hr class="more" /><p>第二段</p>`
      )
    }

    await actAndSettle(
      page,
      "post",
      async () => {
        await page.getByRole("button", { name: /发布/ }).click()
      },
      "post"
    )

    await expect(page).toHaveURL(/posts\/\d+\/edit/)
    const postId = Number(page.url().match(/posts\/(\d+)\/edit/)![1])
    postIds.add(postId)

    await gotoPage(page, "#/posts", "文章管理", "post")
    const row = rowByText(page, title)
    await expect(row).toBeVisible({ timeout: 15_000 })

    await rowLink(row, "编辑").click()
    await expect(page).toHaveURL(new RegExp(`posts/${postId}/edit`))
    await expect(page.locator('input[aria-label="标题"]')).toHaveValue(title)
    if (useBlockEditor) {
      await expect(editor).toContainText(`第一段 ${writeFixture.token}`)
      await expect(editor.locator("hr.more")).toHaveCount(1)
    } else {
      await expect(fallback).toHaveValue(new RegExp(`第一段 ${writeFixture.token}`))
    }

    await page.fill('input[aria-label="标题"]', renamed)
    await actAndSettle(
      page,
      "post",
      async () => {
        await page.getByRole("button", { name: /发布/ }).click()
      },
      "post"
    )
    await expect(page.getByText("已保存")).toBeVisible({ timeout: 15_000 })

    await gotoPage(page, "#/posts", "文章管理", "post")
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

  test("页面同样可以通过编辑器新建", async ({ page, writeFixture }) => {
    const title = fixtureName(writeFixture, "Page")
    const pageIds = new Set<number>()
    writeFixture.cleanup((api) =>
      cleanupPostRecords(api, 1, [title], pageIds)
    )

    await page.goto("zb_system/admin-next/index.php#/pages/new")
    await expect(page.getByRole("heading", { name: "新建页面" })).toBeVisible()

    await page.fill('input[aria-label="标题"]', title)

    const editor = page.locator(".zbpe-content")
    const fallback = page.locator('textarea[aria-label="正文"]')
    await expect(editor.or(fallback).first()).toBeVisible({ timeout: 15_000 })
    if ((await editor.count()) > 0) {
      await editor.click()
      await page.keyboard.type(`页面正文 ${writeFixture.token}`)
    } else {
      await fallback.fill(`<p>页面正文 ${writeFixture.token}</p>`)
    }

    await actAndSettle(
      page,
      "post",
      async () => {
        await page.getByRole("button", { name: /发布/ }).click()
      },
      "post"
    )
    await expect(page).toHaveURL(/pages\/\d+\/edit/)
    pageIds.add(Number(page.url().match(/pages\/(\d+)\/edit/)![1]))

    await gotoPage(page, "#/pages", "页面管理", "post")
    const row = rowByText(page, title)
    await expect(row).toBeVisible({ timeout: 15_000 })

    await rowAction(row, "删除").click()
    const confirm = page.getByRole("alertdialog")
    await expect(confirm).toBeVisible()
    await actAndSettle(page, "post", async () => {
      await confirm.getByRole("button", { name: "删除" }).click()
    })
    await expect(rowByText(page, title)).toHaveCount(0)
  })
})

test.describe("评论审核", () => {
  test("使用隔离评论切换审核状态并按固定 ID 还原", async ({
    page,
    writeFixture,
  }) => {
    test.skip(!writeEnabled, "set ZBP_E2E_WRITE=1 to run write tests")
    const commentContent = fixtureName(writeFixture, "AuditComment")
    const created = await writeFixture.api<{
      comment: { ID: number | string }
    }>("comment", "post", {
      method: "POST",
      body: {
        postid: 1,
        content: commentContent,
        email: "e2e-audit@example.invalid",
      },
    })
    const commentId = Number(created.comment.ID)
    if (!Number.isSafeInteger(commentId) || commentId <= 0) {
      throw new Error("评论审核 fixture 创建失败")
    }
    writeFixture.cleanup((api) =>
      cleanupCommentRecords(api, commentId, [commentContent])
    )

    let fixture:
      | { ID: number | string; Content: string; IsChecking: number }
      | undefined

    for (const ischecking of [0, 1]) {
      const data = await writeFixture.api<{
        list: Array<{
          ID: number | string
          Content: string
          IsChecking: number
        }>
      }>("comment", "list", {
        query: { manage: 1, ischecking, perpage: 100 },
      })
      fixture = data.list.find(
        (row) =>
          row.Content === commentContent && Number(row.ID) === commentId
      )
      if (fixture) {
        break
      }
    }

    if (!fixture) {
      throw new Error("未找到唯一隔离评论 fixture")
    }
    const originalChecking = Number(fixture.IsChecking)
    if (originalChecking !== 0 && originalChecking !== 1) {
      throw new Error(`评论 fixture ID ${commentId} 审核状态无效`)
    }
    expect(originalChecking, "评论管理列表需要已通过的隔离评论").toBe(0)
    writeFixture.cleanup((api) =>
      restoreCommentFixture(api, commentId, originalChecking)
    )

    await gotoPage(page, "#/comments", "评论管理", "comment")
    const commentRow = rowByText(page, commentContent)
    await expect(commentRow).toBeVisible()
    const toggle = commentRow.getByRole("button", { name: "转为待审" })
    await expect(toggle).toBeVisible()

    try {
      const checked = waitForApi(page, "comment", "check")
      await toggle.click()
      await checked

      // 管理列表默认只显示已通过评论；转为待审后该 fixture 行应消失。
      // 不能拿全局第一个按钮的状态代替，也不能要求已隐藏行继续显示按钮。
      await expect(commentRow).toHaveCount(0)
      const pending = await writeFixture.api<{
        list: Array<{ ID: number | string }>
      }>(
        "comment",
        "list",
        { query: { manage: 1, ischecking: 1, perpage: 100 } }
      )
      expect(pending.list.some((row) => Number(row.ID) === commentId)).toBe(true)
    } finally {
      // 还原只认固定 ID，绝不点击页面上的全局第一个按钮。
      await restoreCommentFixture(
        writeFixture.api,
        commentId,
        originalChecking
      )
      const settled = waitForList(page, "comment")
      await page.reload()
      await settled
      await expect(rowByText(page, commentContent)).toBeVisible()
      await expect(
        rowByText(page, commentContent).getByRole("button", { name: "转为待审" })
      ).toBeVisible()
    }
  })
})
