export function appManagementEntry(boot) {
  if (boot.appCentre.enabled && boot.appCentre.url) {
    return {
      kind: "app-centre",
      url: boot.appCentre.url,
      labelKey: "app_center",
    }
  }

  return {
    kind: "legacy-plugin",
    url: boot.legacyPluginManageUrl,
    labelKey: "legacy_plugin_manage",
  }
}

export function appManageLink(app) {
  if (!app.can_manage || typeof app.manage_url !== "string" || app.manage_url === "") {
    return null
  }

  return {
    href: app.manage_url,
    labelKey: "configure_manage",
  }
}
