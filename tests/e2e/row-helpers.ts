import type { Page, Locator } from "@playwright/test"

/**
 * 行内操作按钮有 aria-label 与图标，这里统一按行定位，
 * 避免 getByRole("button", { name }) 匹配到页面上多个同名按钮。
 */
export function rowByText(page: Page, text: string): Locator {
  return page.locator("table tbody tr").filter({ hasText: text }).first()
}

/**
 * 行内动作：既可能是按钮（弹窗编辑），也可能是链接（跳转编辑页）。
 */
export function rowAction(row: Locator, label: "编辑" | "删除"): Locator {
  return row.locator(`button[aria-label="${label}"]`).first()
}

export function rowLink(row: Locator, title: "编辑"): Locator {
  return row.locator(`a[title="${title}"]`).first()
}

/**
 * 等列表数据落定。
 *
 * 不用 waitForLoadState("networkidle")：后台会周期性轮询，永远不会 idle。
 * 这里等当前页对目标接口的响应返回即可。
 */
export async function waitForList(page: Page, mod: string): Promise<void> {
  await page
    .waitForResponse(
      (response) =>
        response.url().includes(`mod=${mod}`) &&
        response.url().includes("act=list") &&
        response.ok(),
      { timeout: 15_000 }
    )
    .catch(() => {
      // 某些操作不触发列表刷新，交给后续断言报错
    })
}
