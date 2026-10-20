import {
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { useMemo, useState } from "react"
import { toast } from "sonner"

import { apiGet, apiPost } from "@/lib/api"
import { t } from "@/lib/i18n"
import { hasRight } from "@/lib/zbp"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Switch } from "@/components/ui/switch"

interface AppRow {
  id: string
  name: string
  version: string
  author_name: string
  note?: string
  is_actived?: boolean
}

interface ModuleRow {
  ID: number
  Name: string
  FileName: string
  Type: string
  SideID: number
}

function useApps(act: "get_plugins" | "get_themes") {
  return useQuery({
    queryKey: ["app", act],
    queryFn: () => apiGet<{ list: AppRow[] }>("app", act),
  })
}

export function PluginsPage() {
  const queryClient = useQueryClient()
  const { data, isLoading } = useApps("get_plugins")
  const [pendingDisable, setPendingDisable] = useState<AppRow | null>(null)

  const toggle = useMutation({
    mutationFn: ({ id, enable }: { id: string; enable: boolean }) =>
      apiPost<Record<string, never>>(
        "app",
        enable ? "enable_plugin" : "disable_plugin",
        { id }
      ),
    onSuccess: (_result, variables) => {
      toast.success(variables.enable ? "插件已启用" : "插件已停用")
      setPendingDisable(null)
      queryClient.invalidateQueries({ queryKey: ["app"] })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setPendingDisable(null)
    },
  })

  const columns = useMemo<ColumnDef<AppRow, unknown>[]>(
    () => [
      {
        id: "name",
        header: "插件",
        cell: ({ row }) => (
          <div className="min-w-0">
            <div className="truncate font-medium">{row.original.name}</div>
            <code className="text-xs text-muted-foreground">{row.original.id}</code>
          </div>
        ),
      },
      {
        id: "version",
        header: "版本",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {row.original.version || "—"}
          </span>
        ),
      },
      {
        id: "author",
        header: "作者",
        cell: ({ row }) => (
          <span className="text-muted-foreground">
            {row.original.author_name || "—"}
          </span>
        ),
      },
      {
        id: "status",
        header: t("publish_state"),
        cell: ({ row }) =>
          row.original.is_actived ? (
            <Badge variant="success">已启用</Badge>
          ) : (
            <Badge variant="muted">未启用</Badge>
          ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end">
            <Switch
              aria-label={row.original.is_actived ? "停用" : "启用"}
              checked={Boolean(row.original.is_actived)}
              disabled={!hasRight("PluginEnb") || toggle.isPending}
              onCheckedChange={(checked) => {
                if (checked) {
                  toggle.mutate({ id: row.original.id, enable: true })
                } else {
                  setPendingDisable(row.original)
                }
              }}
            />
          </div>
        ),
      },
    ],
    [toggle]
  )

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("plugin_manage")} />
      <DataTable
        columns={columns}
        data={data?.list ?? []}
        isLoading={isLoading}
      />

      <ConfirmDialog
        open={pendingDisable !== null}
        onOpenChange={(open) => {
          if (!open) {
            setPendingDisable(null)
          }
        }}
        title={`停用 ${pendingDisable?.name ?? ""}？`}
        description="停用后该插件提供的功能会立即失效。"
        confirmText="停用"
        destructive
        onConfirm={() => {
          if (pendingDisable) {
            toggle.mutate({ id: pendingDisable.id, enable: false })
          }
        }}
      />
    </div>
  )
}

export function ThemesPage() {
  const queryClient = useQueryClient()
  const { data, isLoading } = useApps("get_themes")
  const info = useQuery({
    queryKey: ["system", "get_info"],
    queryFn: () => apiGet<{ info: { theme: string } }>("system", "get_info"),
  })

  const activeId = info.data?.info?.theme

  const activate = useMutation({
    mutationFn: (id: string) =>
      apiPost<Record<string, never>>("app", "set_theme", { id }),
    onSuccess: () => {
      toast.success("主题已切换")
      queryClient.invalidateQueries({ queryKey: ["app"] })
      queryClient.invalidateQueries({ queryKey: ["system", "get_info"] })
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const columns = useMemo<ColumnDef<AppRow, unknown>[]>(
    () => [
      {
        id: "name",
        header: "主题",
        cell: ({ row }) => (
          <div className="min-w-0">
            <div className="truncate font-medium">{row.original.name}</div>
            <code className="text-xs text-muted-foreground">{row.original.id}</code>
          </div>
        ),
      },
      {
        id: "version",
        header: "版本",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {row.original.version || "—"}
          </span>
        ),
      },
      {
        id: "author",
        header: "作者",
        cell: ({ row }) => (
          <span className="text-muted-foreground">
            {row.original.author_name || "—"}
          </span>
        ),
      },
      {
        id: "status",
        header: t("publish_state"),
        cell: ({ row }) =>
          row.original.id === activeId ? (
            <Badge variant="success">使用中</Badge>
          ) : (
            <Badge variant="muted">未启用</Badge>
          ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) =>
          row.original.id === activeId ? null : (
            <div className="flex justify-end">
              <Button
                size="sm"
                variant="outline"
                disabled={!hasRight("ThemeSet") || activate.isPending}
                onClick={() => activate.mutate(row.original.id)}
              >
                启用
              </Button>
            </div>
          ),
      },
    ],
    [activeId, activate]
  )

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("theme_manage")} />
      <DataTable
        columns={columns}
        data={data?.list ?? []}
        isLoading={isLoading}
      />
    </div>
  )
}

export function ModulesPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["module", "list"],
    queryFn: () =>
      apiGet<{ list: ModuleRow[] }>("module", "list", { perpage: 200 }),
  })

  const columns = useMemo<ColumnDef<ModuleRow, unknown>[]>(
    () => [
      {
        id: "Name",
        header: "名称",
        cell: ({ row }) => (
          <span className="font-medium">{row.original.Name}</span>
        ),
      },
      {
        id: "FileName",
        header: "文件名",
        cell: ({ row }) => (
          <code className="text-xs text-muted-foreground">
            {row.original.FileName}
          </code>
        ),
      },
      {
        id: "Type",
        header: "类型",
        cell: ({ row }) => (
          <span className="text-muted-foreground">{row.original.Type}</span>
        ),
      },
      {
        id: "SideID",
        header: "侧栏",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {row.original.SideID || "—"}
          </span>
        ),
      },
    ],
    []
  )

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("module_manage")} />
      <DataTable
        columns={columns}
        data={data?.list ?? []}
        isLoading={isLoading}
      />
    </div>
  )
}
