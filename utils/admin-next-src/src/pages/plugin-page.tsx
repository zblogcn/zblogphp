import { useCallback, useEffect, useRef, useState } from "react"
import { useLocation, useNavigate } from "react-router-dom"
import { Loader2, PackageX } from "lucide-react"
import { toast } from "sonner"

import { apiGet, apiPost } from "@/lib/api"
import { hasRight, zbp } from "@/lib/zbp"
import { Button } from "@/components/ui/button"
import { Empty } from "@/components/ui/empty"
import { PageHeader } from "@/components/page-header"
import { PluginHost } from "@/plugin-host/plugin-host"
import {
  findAppRoute,
  loadPluginBundle,
  type AdminCtx,
  type AppManifest,
} from "@/plugin-host/loader"

function buildCtx(app: AppManifest, route: string): AdminCtx {
  return {
    appId: app.appId,
    appType: app.type,
    baseUrl: app.baseUrl,
    route,
    api: {
      get: (mod, act, query) =>
        apiGet(mod, act, (query ?? {}) as Record<string, string | number>),
      post: (mod, act, body) => apiPost(mod, act, body ?? {}),
    },
    user: zbp.user,
    rights: zbp.rights,
    lang: zbp.lang.msg,
    navigate: (to) => {
      // 插件只能跳到自己的命名空间
      const prefix = `/plugin/${app.appId}`
      window.location.hash = `#${to.startsWith(prefix) ? to : prefix}`
    },
    toast: {
      success: (message) => toast.success(message),
      error: (message) => toast.error(message),
      info: (message) => toast.info(message),
    },
  }
}

type Status =
  | { kind: "loading" }
  | { kind: "ready"; mount: (el: HTMLElement) => void | (() => void) }
  | { kind: "error"; message: string }

export function PluginPage() {
  const location = useLocation()
  const navigate = useNavigate()
  const [status, setStatus] = useState<Status>({ kind: "loading" })

  const matched = findAppRoute(zbp.apps ?? [], location.pathname)
  const app = matched?.app
  const route = matched?.route ?? "/"

  const routeRef = useRef(route)
  routeRef.current = route

  const handleError = useCallback((error: Error) => {
    setStatus({ kind: "error", message: error.message })
  }, [])

  // 同一条路由切换时也要重新挂载，让插件感知新的 ctx.route
  const routeKey = location.pathname

  useEffect(() => {
    if (!app) {
      return
    }

    let cancelled = false
    setStatus({ kind: "loading" })

    loadPluginBundle(app)
      .then((plugin) => {
        if (cancelled) {
          return
        }
        setStatus({
          kind: "ready",
          mount: (el) => {
            const result = plugin.mount(el, buildCtx(app, routeRef.current))

            if (result && typeof result === "object" && "then" in result) {
              return () => {
                void (result as Promise<void | (() => void)>).then(
                  (cleanup) => cleanup?.()
                )
              }
            }

            return result as void | (() => void)
          },
        })
      })
      .catch((error: Error) => {
        if (cancelled) {
          return
        }

        // bundle 不可用时回退到旧版页面，避免插件整体不可用
        if (app.legacy) {
          toast.error(`${app.name} 前端加载失败，已回退旧版页面`)
          window.location.href = `${app.baseUrl}${app.legacy}`
          return
        }

        setStatus({ kind: "error", message: error.message })
      })

    return () => {
      cancelled = true
    }
  }, [app, routeKey])

  if (!app) {
    return (
      <Empty
        icon={PackageX}
        title="插件不存在"
        description="该插件未启用，或未声明 admin-next 前端。"
        action={
          <Button variant="outline" size="sm" onClick={() => navigate("/")}>
            返回仪表盘
          </Button>
        }
      />
    )
  }

  const activeMenu = app.menu.find((item) => item.route === location.pathname)

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={activeMenu?.title ?? app.name}
        description={`${app.name} ${app.version ? `v${app.version}` : ""} · ${
          app.type === "theme" ? "主题" : "插件"
        }`}
        actions={
          app.legacy ? (
            <Button asChild variant="outline" size="sm">
              <a href={`${app.baseUrl}${app.legacy}`}>旧版页面</a>
            </Button>
          ) : null
        }
      />

      {!hasRight(activeMenu?.require ?? null) ? (
        <Empty icon={PackageX} title="没有访问权限" />
      ) : status.kind === "error" ? (
        <div className="rounded-lg border border-destructive/40 bg-destructive/5 p-4 text-sm">
          <div className="font-medium text-destructive">插件页面加载失败</div>
          <div className="mt-1 text-muted-foreground">{status.message}</div>
        </div>
      ) : status.kind === "loading" ? (
        <div className="flex items-center gap-2 rounded-lg border bg-card p-6 text-sm text-muted-foreground">
          <Loader2 className="size-4 animate-spin" />
          正在加载 {app.name}…
        </div>
      ) : (
        <PluginHost
          key={routeKey}
          mount={status.mount}
          onError={handleError}
          dataPlugin={app.appId}
        />
      )}
    </div>
  )
}
