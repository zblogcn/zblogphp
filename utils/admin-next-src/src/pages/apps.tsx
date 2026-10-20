import { useQuery } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { useMemo } from "react"

import { apiGet } from "@/lib/api"
import { t } from "@/lib/i18n"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Badge } from "@/components/ui/badge"

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

function useAppList(mod: "app", act: "get_themes" | "get_plugins") {
  return useQuery({
    queryKey: ["app", act],
    queryFn: () => apiGet<{ list: AppRow[] }>(mod, act),
  })
}

function AppColumns(): ColumnDef<AppRow, unknown>[] {
  return [
    {
      id: "name",
      header: "名称",
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
        <span className="text-muted-foreground">{row.original.author_name || "—"}</span>
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
  ]
}

export function ThemesPage() {
  const { data, isLoading } = useAppList("app", "get_themes")

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("theme_manage")} />
      <DataTable
        columns={AppColumns()}
        data={data?.list ?? []}
        isLoading={isLoading}
      />
    </div>
  )
}

export function PluginsPage() {
  const { data, isLoading } = useAppList("app", "get_plugins")

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("plugin_manage")} />
      <DataTable
        columns={AppColumns()}
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
