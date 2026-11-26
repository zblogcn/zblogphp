import type { AppManifest, PluginMenuItem } from "./loader"

export interface AppRouteMatch {
  app: AppManifest
  route: string
  menu: PluginMenuItem | null
}

export function findAppRoute(
  apps: AppManifest[],
  path: string
): AppRouteMatch | null

export function canLoadPluginRoute(
  matched: AppRouteMatch | null,
  checkRight: (action: string | null) => boolean
): boolean
