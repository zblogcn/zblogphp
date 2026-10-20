import { test as base, expect } from "@playwright/test"

/**
 * 写操作测试共用的日志采集：把内部 API 的请求与响应打印出来，
 * 便于在 CI 里定位失败原因。
 */
export const test = base.extend({
  page: async ({ page }, use) => {
    const log: string[] = []

    page.on("request", (request) => {
      if (request.url().includes("admin-next/api.php")) {
        log.push(`>> ${request.method()} ${request.url()}`)
      }
    })

    page.on("response", async (response) => {
      if (response.url().includes("admin-next/api.php")) {
        let body = ""
        try {
          body = (await response.text()).slice(0, 200)
        } catch {
          body = "(unreadable)"
        }
        log.push(`<< ${response.status()} ${body}`)
      }
    })

    await use(page)

    if (process.env.ZBP_E2E_DEBUG) {
      console.log(log.join("\n"))
    }
  },
})

export { expect }
