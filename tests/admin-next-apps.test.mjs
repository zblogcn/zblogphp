import assert from "node:assert/strict"
import { readFile } from "node:fs/promises"
import test from "node:test"

import {
  appManageLink,
  appManagementEntry,
} from "../utils/admin-next-src/src/lib/app-management.js"

const root = new URL("../", import.meta.url)
const appsSource = await readFile(
  new URL("utils/admin-next-src/src/pages/apps.tsx", root),
  "utf8"
)
const i18nSource = await readFile(
  new URL("utils/admin-next-src/src/lib/i18n.ts", root),
  "utf8"
)

const legacyUrl = "https://site.example/blog/zb_system/admin/index.php?act=PluginMng&force=1"

test("enabled AppCentre is the application management entry", () => {
  const url = "https://site.example/blog/zb_users/plugin/AppCentre/main.php"

  assert.deepEqual(
    appManagementEntry({
      appCentre: { enabled: true, url },
      legacyPluginManageUrl: legacyUrl,
    }),
    { kind: "app-centre", url, labelKey: "app_center" }
  )
})

test("disabled AppCentre falls back to forced legacy plugin management", () => {
  assert.deepEqual(
    appManagementEntry({
      appCentre: { enabled: false, url: null },
      legacyPluginManageUrl: legacyUrl,
    }),
    { kind: "legacy-plugin", url: legacyUrl, labelKey: "legacy_plugin_manage" }
  )
})

test("missing AppCentre URL cannot leave the SPA without a legacy fallback", () => {
  const entry = appManagementEntry({
    appCentre: { enabled: true, url: null },
    legacyPluginManageUrl: legacyUrl,
  })

  assert.equal(entry.kind, "legacy-plugin")
  assert.equal(entry.url, legacyUrl)
})

test("manage links consume the exact server-provided URL", () => {
  const manageUrl = "https://site.example/blog/zb_users/plugin/Demo/main.php?tab=advanced&mode=edit"

  assert.deepEqual(
    appManageLink({ can_manage: true, manage_url: manageUrl }),
    { href: manageUrl, labelKey: "configure_manage" }
  )
})

test("apps without a management page do not render a manage link", () => {
  assert.equal(appManageLink({ can_manage: false, manage_url: "/ignored/" }), null)
  assert.equal(appManageLink({ can_manage: true, manage_url: "" }), null)
  assert.equal(appManageLink({ can_manage: true, manage_url: null }), null)
  assert.equal(appManageLink({}), null)
})

test("plugin and theme actions render the API manage_url without rebuilding it", () => {
  assert.match(appsSource, /can_manage\?: boolean/)
  assert.match(appsSource, /manage_url\?: string \| null/)
  assert.match(appsSource, /<AppManageLink app=\{row\.original\} \/>/)
  assert.match(appsSource, /<a href=\{link\.href\}>/)
  assert.doesNotMatch(appsSource, /zb_users\/\$\{/)
})

test("both application pages put the legacy operation entry in the page header", () => {
  assert.match(appsSource, /<AppPageHeader title=\{t\("plugin_manage"\)\} \/>/)
  assert.match(appsSource, /<AppPageHeader title=\{t\("theme_manage"\)\} \/>/)
  assert.match(appsSource, /description=\{t\("legacy_app_operations_desc"\)\}/)
  assert.match(appsSource, /<a href=\{entry\.url\}>/)
  assert.match(i18nSource, /legacy_app_operations_desc: "安装、卸载和上传插件\/主题包/)
  assert.match(i18nSource, /configure_manage: "配置\/管理"/)
})
