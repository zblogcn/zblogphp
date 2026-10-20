import { StrictMode } from "react"
import { createRoot } from "react-dom/client"
import { QueryClient, QueryClientProvider } from "@tanstack/react-query"

import "./index.css"

import { t } from "@/lib/i18n"
import { useUiStore } from "@/stores/ui"
import { AppErrorBoundary } from "@/components/error-boundary"
import { Toaster } from "@/components/ui/sonner"
import { TooltipProvider } from "@/components/ui/tooltip"
import { AppRouter } from "@/router"

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      staleTime: 15_000,
      refetchOnWindowFocus: false,
    },
  },
})

// 首屏前脚本已加过 .dark，这里只同步 store
useUiStore.getState().applyTheme()

// 版本更新后旧的 lazy chunk 会 404：刷新一次即可拿到新产物
window.addEventListener("vite:preloadError", (event) => {
  event.preventDefault()
  const key = "zbp-admin-chunk-reload"
  try {
    if (sessionStorage.getItem(key) === "1") {
      sessionStorage.removeItem(key)
      return
    }
    sessionStorage.setItem(key, "1")
  } catch {
    /* ignore */
  }
  location.reload()
})

function Bootstrap() {
  if (window.__ZB_ASSETS_MISSING__) {
    return (
      <div className="mx-auto max-w-md px-6 py-16 text-sm">
        <h1 className="text-base font-semibold">{t("error_title")}</h1>
        <p className="mt-2 text-muted-foreground">{t("missing_assets")}</p>
      </div>
    )
  }

  if (!window.__ZB__ && import.meta.env.PROD) {
    return (
      <div className="mx-auto max-w-md px-6 py-16 text-sm">
        <h1 className="text-base font-semibold">{t("error_title")}</h1>
        <p className="mt-2 text-muted-foreground">
          缺少引导数据，请通过 zb_system/admin-next/index.php 访问。
        </p>
      </div>
    )
  }

  return <AppRouter />
}

createRoot(document.getElementById("zb-admin-root")!).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <AppErrorBoundary>
          <Bootstrap />
        </AppErrorBoundary>
        <Toaster />
      </TooltipProvider>
    </QueryClientProvider>
  </StrictMode>
)
