import { expect, type Page, type Locator } from "@playwright/test"

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

interface ApiEnvelope<T> {
  code: number
  message?: string
  data: T
  error: unknown
}

/**
 * 等指定内部 API 响应，并同时校验 HTTP 状态与 JSON envelope。
 *
 * 不用 waitForLoadState("networkidle")：后台会周期性轮询，永远不会 idle。
 * 超时、非 2xx、非 JSON 或 code !== 200 都必须让用例失败，不能降级为等待 UI 断言。
 */
export async function waitForApi<T = unknown>(
  page: Page,
  mod: string,
  act: string
): Promise<T> {
  const response = await page.waitForResponse(
    (candidate) => {
      const url = new URL(candidate.url())
      return (
        url.pathname.endsWith("/zb_system/admin-next/api.php") &&
        url.searchParams.get("mod") === mod &&
        url.searchParams.get("act") === act
      )
    },
    { timeout: 15_000 }
  )

  const text = await response.text()
  expect(
    response.ok(),
    `${mod}.${act} HTTP ${response.status()}: ${text.slice(0, 500)}`
  ).toBe(true)

  let payload: ApiEnvelope<T>
  try {
    payload = JSON.parse(text) as ApiEnvelope<T>
  } catch {
    throw new Error(`${mod}.${act} returned a non-JSON response: ${text.slice(0, 500)}`)
  }
  expect(payload, `${mod}.${act} returned an invalid API envelope`).not.toBeNull()
  expect(payload.code, `${mod}.${act} API error: ${payload.message ?? ""}`).toBe(
    200
  )
  expect(payload.error, `${mod}.${act} API error: ${payload.message ?? ""}`).toBeNull()

  return payload.data
}

/** 等列表数据落定，并校验列表接口的 HTTP 与 envelope。 */
export async function waitForList<T = unknown>(page: Page, mod: string): Promise<T> {
  return waitForApi<T>(page, mod, "list")
}
