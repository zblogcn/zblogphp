export interface AppManagementBoot {
  appCentre: {
    enabled: boolean
    url: string | null
  }
  legacyPluginManageUrl: string
}

export interface AppManagementEntry {
  kind: "app-centre" | "legacy-plugin"
  url: string
  labelKey: "app_center" | "legacy_plugin_manage"
}

export interface ManageableApp {
  can_manage?: boolean
  manage_url?: string | null
}

export interface AppManageLink {
  href: string
  labelKey: "configure_manage"
}

export function appManagementEntry(boot: AppManagementBoot): AppManagementEntry
export function appManageLink(app: ManageableApp): AppManageLink | null
