import { ExternalLink } from "lucide-react"
import { NavLink } from "react-router-dom"

import { hasRight, zbp, type NavItem } from "@/lib/zbp"
import { t } from "@/lib/i18n"
import { cn } from "@/lib/utils"
import { NavIcon } from "@/components/nav-icon"
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from "@/components/ui/tooltip"

const groupOrder = ["main", "content", "manage", "extend", "system"]
const groupLabelKey: Record<string, string> = {
  content: "group_content",
  manage: "group_manage",
  extend: "group_extend",
  system: "group_system",
}

export function groupNav(items: NavItem[]): Array<[string, NavItem[]]> {
  const visible = items.filter((item) => hasRight(item.require))
  const groups = new Map<string, NavItem[]>()

  for (const item of visible) {
    const key = item.group || "extend"
    const list = groups.get(key) ?? []
    list.push(item)
    groups.set(key, list)
  }

  return [...groups.entries()].sort(
    (a, b) =>
      (groupOrder.indexOf(a[0]) === -1 ? 99 : groupOrder.indexOf(a[0])) -
      (groupOrder.indexOf(b[0]) === -1 ? 99 : groupOrder.indexOf(b[0]))
  )
}

function routeOf(item: NavItem): string {
  const url = item.url || "#/"
  return url.startsWith("#") ? url.slice(1) : url
}

interface SidebarNavProps {
  collapsed?: boolean
  onNavigate?: () => void
}

export function SidebarNav({ collapsed = false, onNavigate }: SidebarNavProps) {
  const groups = groupNav(zbp.nav)

  return (
    <nav className="flex flex-1 flex-col gap-4 overflow-y-auto px-2 py-3">
      {groups.map(([group, items]) => (
        <div key={group} className="flex flex-col gap-1">
          {groupLabelKey[group] && !collapsed ? (
            <div className="px-2 pb-1 text-[11px] font-medium tracking-wide text-muted-foreground uppercase">
              {t(groupLabelKey[group])}
            </div>
          ) : null}

          {items.map((item) => {
            const icon = <NavIcon name={item.icon} className="size-4 shrink-0" />

            const inner = (
              <>
                {icon}
                {!collapsed ? (
                  <>
                    <span className="truncate">{item.title}</span>
                    {item.legacy ? (
                      <span className="ml-auto rounded bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground">
                        {t("legacy_badge")}
                      </span>
                    ) : null}
                  </>
                ) : null}
                {item.external && !collapsed ? (
                  <ExternalLink className="ml-auto size-3 shrink-0 opacity-50" />
                ) : null}
              </>
            )

            const baseClass = cn(
              "flex h-9 items-center gap-2 rounded-md px-2 text-sm text-sidebar-foreground transition-colors",
              "hover:bg-sidebar-accent hover:text-sidebar-accent-foreground",
              "focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sidebar-ring",
              collapsed && "justify-center px-0"
            )

            const node = item.external ? (
              <a
                key={item.id}
                href={item.url}
                target="_blank"
                rel="noopener noreferrer"
                className={baseClass}
                onClick={onNavigate}
              >
                {inner}
              </a>
            ) : (
              <NavLink
                key={item.id}
                to={routeOf(item)}
                end={item.id === "dashboard"}
                className={({ isActive }) =>
                  cn(
                    baseClass,
                    isActive &&
                      "bg-sidebar-accent font-medium text-sidebar-accent-foreground"
                  )
                }
                onClick={onNavigate}
              >
                {inner}
              </NavLink>
            )

            if (!collapsed) {
              return node
            }

            return (
              <Tooltip key={item.id}>
                <TooltipTrigger asChild>{node}</TooltipTrigger>
                <TooltipContent side="right">{item.title}</TooltipContent>
              </Tooltip>
            )
          })}
        </div>
      ))}
    </nav>
  )
}
