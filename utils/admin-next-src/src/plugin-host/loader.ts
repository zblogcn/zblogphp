import { useCallback, useMemo } from "react"

import { apiGet, apiPost } from "@/lib/api"
import { zbp } from "@/lib/zbp"

export interface PluginMenuItem {
  id: string
  title: string
  route: string
  icon: string
  group: string
  order: number
  require: string | null
}

export interface AppManifest {
  appId: string
  type: "plugin" | "theme"
  name: string
  version: string
  baseUrl: string
  entry: string
  style: string
  legacy: string
  menu: PluginMenuItem[]
}

/** 插件 bundle 必须导出的挂载函数。 */
export type PluginMountFn = (
  el: HTMLElement,
  ctx: AdminCtx
) => void | (() => void) | Promise<void | (() => void)>

interface LoadedPlugin {
  mount: PluginMountFn
  unmount?: () => void
}

export interface AdminCtx {
  appId: string
  appType: "plugin" | "theme"
  baseUrl: string
  route: string
  api: {
    get: <T>(mod: string, act: string, query?: Record<string, unknown>) => Promise<T>
    post: <T>(mod: string, act: string, body?: Record<string, unknown>) => Promise<T>
  }
  user: unknown
  rights: Record<string, boolean>
  lang: Record<string, string>
  navigate: (to: string) => void
  toast: {
    success: (message: string) => void
    error: (message: string) => void
    info: (message: string) => void
  }
}

const cache = new Map<string, Promise<LoadedPlugin>>()

/**
 * 按需加载插件 bundle。
 *
 * 失败的 promise 不留在缓存里，否则该插件在本次会话内永远无法恢复。
 */
export function loadPluginBundle(app: AppManifest): Promise<LoadedPlugin> {
  const url = `${app.baseUrl}${app.entry}`
  const cached = cache.get(url)
  if (cached) {
    return cached
  }

  const promise = (async (): Promise<LoadedPlugin> => {
    const mod = (await import(/* @vite-ignore */ url)) as {
      mount?: PluginMountFn
      unmount?: () => void
    }

    if (typeof mod.mount !== "function") {
      throw new Error(`${app.appId}: 入口未导出 mount()`)
    }

    return { mount: mod.mount, unmount: mod.unmount }
  })()

  promise.catch(() => cache.delete(url))
  cache.set(url, promise)

  return promise
}

export function findAppRoute(
  apps: AppManifest[],
  path: string
): { app: AppManifest; route: string } | null {
  for (const app of apps) {
    const prefix = `/plugin/${app.appId}`
    if (path === prefix || path.startsWith(`${prefix}/`)) {
      return { app, route: path.slice(prefix.length) || "/" }
    }
  }
  return null
}

/** 供 PluginPage 使用的 ctx 构造器。 */
export function usePluginContext(app: AppManifest, route: string) {
  const navigate = useCallback((to: string) => {
    window.location.hash = `#${to}`
  }, [])

  return useMemo(
    () => ({
      appId: app.appId,
      appType: app.type,
      baseUrl: app.baseUrl,
      route: route || "/",
      api: {
        get: apiGet,
        post: apiPost,
      },
      user: zbp.user,
      rights: zbp.rights,
      lang: zbp.lang.msg,
      navigate,
      toast: {
        success: () => undefined,
        error: () => undefined,
        info: () => undefined,
      },
    }),
    [app, route, navigate]
  )
}
