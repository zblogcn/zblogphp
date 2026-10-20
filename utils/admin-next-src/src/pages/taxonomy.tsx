import { keepPreviousData, useQuery } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { useMemo, useState } from "react"

import { apiGet, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"

interface TermRow {
  ID: number
  Name: string
  Alias: string
  Count?: number
  AllCount?: number
  Level?: number
  ParentID?: number
}

interface TermListData {
  list: TermRow[]
  pagebar: Pagebar
}

function TermListPage({
  mod,
  title,
  countKey,
}: {
  mod: "category" | "tag"
  title: string
  countKey: "AllCount" | "Count"
}) {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: [mod, "list", page],
    queryFn: () =>
      apiGet<TermListData>(mod, "list", {
        page,
        perpage: 100,
      }),
    placeholderData: keepPreviousData,
  })

  const columns = useMemo<ColumnDef<TermRow, unknown>[]>(
    () => [
      {
        id: "ID",
        header: "ID",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">{row.original.ID}</span>
        ),
      },
      {
        id: "Name",
        header: "名称",
        cell: ({ row }) => (
          <span className="font-medium">{row.original.Name}</span>
        ),
      },
      {
        id: "Alias",
        header: "别名",
        cell: ({ row }) => (
          <code className="rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground">
            {row.original.Alias || "—"}
          </code>
        ),
      },
      {
        id: "Count",
        header: "数量",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {row.original[countKey] ?? row.original.Count ?? 0}
          </span>
        ),
      },
    ],
    [countKey]
  )

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={title} description={`共 ${data?.pagebar?.AllCount ?? 0} 项`} />
      <DataTable
        columns={columns}
        data={data?.list ?? []}
        pagebar={data?.pagebar}
        onPageChange={setPage}
        isLoading={isLoading}
      />
    </div>
  )
}

export function CategoriesPage() {
  return (
    <TermListPage
      mod="category"
      title={t("category_manage")}
      countKey="AllCount"
    />
  )
}

export function TagsPage() {
  return (
    <TermListPage mod="tag" title={t("tag_manage")} countKey="Count" />
  )
}
