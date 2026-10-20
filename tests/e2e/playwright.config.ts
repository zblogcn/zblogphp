import { defineConfig, devices } from "@playwright/test"

/**
 * admin-next 冒烟测试配置.
 *
 * 需要一个可访问的 Z-BlogPHP 站点：
 *   ZBP_BASE_URL=http://localhost/  pnpm test
 * 默认指向本机的 php -S 开发服务器。
 */
const baseURL = process.env.ZBP_BASE_URL ?? "http://127.0.0.1:8099/"

export default defineConfig({
  testDir: "./specs",
  timeout: 30_000,
  expect: { timeout: 8_000 },
  fullyParallel: false,
  forbidOnly: Boolean(process.env.CI),
  retries: process.env.CI ? 1 : 0,
  reporter: process.env.CI ? "line" : "list",
  use: {
    baseURL,
    trace: "on-first-retry",
    screenshot: "only-on-failure",
  },
  projects: [
    { name: "desktop", use: { ...devices["Desktop Chrome"] } },
    { name: "mobile", use: { ...devices["Pixel 7"] } },
  ],
})
