import { ExternalLink, LogOut, Menu, Monitor, Moon, PanelLeft, Sun } from "lucide-react"
import { useRef, useState, Suspense } from "react"
import { Outlet } from "react-router-dom"

import { t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { cn } from "@/lib/utils"
import { useUiStore } from "@/stores/ui"
import { SidebarNav } from "@/components/sidebar-nav"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Sheet, SheetContent, SheetTitle } from "@/components/ui/sheet"
import { Skeleton } from "@/components/ui/skeleton"
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from "@/components/ui/tooltip"

function Brand({
  collapsed = false,
  className,
}: {
  collapsed?: boolean
  className?: string
}) {
  return (
    <div
      className={cn(
        "flex h-14 shrink-0 items-center gap-2 border-b border-sidebar-border px-3",
        collapsed && "justify-center px-0",
        className
      )}
    >
      <div className="flex size-8 shrink-0 items-center justify-center rounded-md bg-primary text-xs font-bold text-primary-foreground">
        ZB
      </div>
      {!collapsed ? (
        <div className="min-w-0">
          <div className="truncate text-sm font-semibold leading-tight">
            {zbp.options.blogName || t("app_name")}
          </div>
          <div className="truncate text-[11px] text-muted-foreground">
            {zbp.options.blogSubname || "管理后台"} · {zbp.version}
          </div>
        </div>
      ) : null}
    </div>
  )
}

function ThemeMenu() {
  const theme = useUiStore((state) => state.theme)
  const setTheme = useUiStore((state) => state.setTheme)

  const icon =
    theme === "dark" ? (
      <Moon className="size-4" />
    ) : theme === "light" ? (
      <Sun className="size-4" />
    ) : (
      <Monitor className="size-4" />
    )

  return (
    <DropdownMenu>
      <Tooltip>
        <TooltipTrigger asChild>
          <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="icon" aria-label={t("theme_toggle")}>
              {icon}
            </Button>
          </DropdownMenuTrigger>
        </TooltipTrigger>
        <TooltipContent>{t("theme_toggle")}</TooltipContent>
      </Tooltip>
      <DropdownMenuContent align="end">
        <DropdownMenuItem onClick={() => setTheme("light")}>
          <Sun className="size-4" /> 浅色
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => setTheme("dark")}>
          <Moon className="size-4" /> 深色
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => setTheme("system")}>
          <Monitor className="size-4" /> 跟随系统
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}

function UserMenu() {
  const user = zbp.user
  const initial = (user.staticName || user.name || "?").slice(0, 1).toUpperCase()
  const logoutUrl = `${zbp.host}zb_system/cmd.php?act=logout&csrfToken=${encodeURIComponent(
    zbp.csrf.admin
  )}`

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" className="h-9 gap-2 px-1.5" aria-label={user.name}>
          <Avatar className="size-7">
            {user.avatar ? <AvatarImage src={user.avatar} alt="" /> : null}
            <AvatarFallback>{initial}</AvatarFallback>
          </Avatar>
          <span className="hidden max-w-28 truncate text-sm sm:inline">
            {user.staticName || user.name}
          </span>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-52">
        <DropdownMenuLabel>
          <div className="text-sm font-medium">{user.staticName || user.name}</div>
          <div className="text-xs font-normal text-muted-foreground">
            {user.levelName}
          </div>
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuItem asChild>
          <a href={zbp.host} target="_blank" rel="noopener noreferrer">
            <ExternalLink className="size-4" /> {t("view_site")}
          </a>
        </DropdownMenuItem>
        <DropdownMenuItem asChild>
          <a href={zbp.legacy + (zbp.legacy.includes("?") ? "&" : "?") + "ui=legacy"}>
            <PanelLeft className="size-4" /> {t("legacy_admin")}
          </a>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem asChild variant="destructive">
          <a href={logoutUrl}>
            <LogOut className="size-4" /> {t("logout")}
          </a>
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}

function RouteFallback() {
  return (
    <div className="flex flex-col gap-3">
      <Skeleton className="h-7 w-40" />
      <Skeleton className="h-4 w-64" />
      <Skeleton className="mt-2 h-64 w-full" />
    </div>
  )
}

export function AppShell() {
  const collapsed = useUiStore((state) => state.sidebarCollapsed)
  const toggleSidebar = useUiStore((state) => state.toggleSidebar)
  const [mobileOpen, setMobileOpen] = useState(false)
  const mainRef = useRef<HTMLElement>(null)
  const mobileMenuButtonRef = useRef<HTMLButtonElement>(null)

  return (
    <div className="flex min-h-dvh w-full bg-background">
      <a
        href="#admin-main-content"
        className="skip-link"
        onClick={(event) => {
          event.preventDefault()
          mainRef.current?.focus()
        }}
      >
        {t("skip_to_content")}
      </a>

      <aside
        id="admin-sidebar"
        className={cn(
          "sticky top-0 hidden h-dvh shrink-0 flex-col border-r border-sidebar-border bg-sidebar md:flex",
          collapsed ? "w-14" : "w-60"
        )}
      >
        <Brand collapsed={collapsed} />
        <SidebarNav collapsed={collapsed} />
      </aside>

      <Sheet open={mobileOpen} onOpenChange={setMobileOpen}>
        <SheetContent
          id="admin-mobile-menu"
          side="left"
          closeLabel={t("close_menu")}
          className="w-64 gap-0 bg-sidebar p-0"
          onCloseAutoFocus={(event) => {
            event.preventDefault()
            mobileMenuButtonRef.current?.focus()
          }}
        >
          <SheetTitle className="sr-only">{t("navigation_menu")}</SheetTitle>
          <Brand className="pr-14" />
          <SidebarNav
            label={t("mobile_nav")}
            onNavigate={() => setMobileOpen(false)}
          />
        </SheetContent>
      </Sheet>

      <div className="flex min-w-0 flex-1 flex-col">
        <header className="sticky top-0 z-30 flex h-14 items-center gap-1 border-b bg-background/85 px-2 backdrop-blur sm:px-4">
          <Button
            ref={mobileMenuButtonRef}
            variant="ghost"
            size="icon"
            className="md:hidden"
            aria-label={t("open_menu")}
            aria-haspopup="dialog"
            aria-controls="admin-mobile-menu"
            aria-expanded={mobileOpen}
            onClick={() => setMobileOpen(true)}
          >
            <Menu className="size-5" />
          </Button>

          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                variant="ghost"
                size="icon"
                className="hidden md:inline-flex"
                aria-label={t("toggle_sidebar")}
                aria-controls="admin-sidebar"
                aria-expanded={!collapsed}
                onClick={toggleSidebar}
              >
                <PanelLeft className="size-4" />
              </Button>
            </TooltipTrigger>
            <TooltipContent>{t("toggle_sidebar")}</TooltipContent>
          </Tooltip>

          <div className="flex-1" />

          <ThemeMenu />
          <UserMenu />
        </header>

        <main
          ref={mainRef}
          id="admin-main-content"
          tabIndex={-1}
          aria-label={t("main_content")}
          className="w-full max-w-none flex-1 scroll-mt-20 px-3 py-4 pb-[max(1rem,env(safe-area-inset-bottom))] sm:px-5 md:py-6"
        >
          <Suspense fallback={<RouteFallback />}>
            <Outlet />
          </Suspense>
        </main>
      </div>
    </div>
  )
}
