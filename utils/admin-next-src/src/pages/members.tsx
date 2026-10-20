import { keepPreviousData, useQuery } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { useMemo, useState } from "react"

import { apiGet, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"

interface MemberRow {
  ID: number
  Name: string
  StaticName: string
  Level: number
  Status: number
  PostTime: number
  Avatar?: string
}

interface MemberListData {
  list: MemberRow[]
  pagebar: Pagebar
}

const levelNames: Record<number, string> = {
  1: "管理员",
  2: "网站编辑",
  3: "作者",
  4: "贡献者",
  5: "订阅者",
  6: "游客",
}

function formatDate(value: number): string {
  if (!value) {
    return "—"
  }
  const date = new Date(value * 1000)
  const pad = (n: number) => String(n).padStart(2, "0")
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

export function MembersPage() {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ["member", "list", page],
    queryFn: () =>
      apiGet<MemberListData>("member", "list", {
        page,
        perpage: zbp.options.manageCount,
      }),
    placeholderData: keepPreviousData,
  })

  const columns = useMemo<ColumnDef<MemberRow, unknown>[]>(
    () => [
      {
        id: "Name",
        header: "用户",
        cell: ({ row }) => {
          const name = row.original.StaticName || row.original.Name
          return (
            <div className="flex items-center gap-2">
              <Avatar className="size-7">
                {row.original.Avatar ? (
                  <AvatarImage src={row.original.Avatar} alt="" />
                ) : null}
                <AvatarFallback>{name.slice(0, 1).toUpperCase()}</AvatarFallback>
              </Avatar>
              <div className="min-w-0">
                <div className="truncate font-medium">{name}</div>
                <div className="truncate text-xs text-muted-foreground">
                  @{row.original.Name}
                </div>
              </div>
            </div>
          )
        },
      },
      {
        id: "Level",
        header: "等级",
        cell: ({ row }) => (
          <span className="text-muted-foreground">
            {levelNames[row.original.Level] ?? row.original.Level}
          </span>
        ),
      },
      {
        id: "Status",
        header: t("publish_state"),
        cell: ({ row }) =>
          Number(row.original.Status) === 0 ? (
            <Badge variant="success">正常</Badge>
          ) : Number(row.original.Status) === 2 ? (
            <Badge variant="destructive">已禁用</Badge>
          ) : (
            <Badge variant="warning">待审核</Badge>
          ),
      },
      {
        id: "PostTime",
        header: "注册时间",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {formatDate(row.original.PostTime)}
          </span>
        ),
      },
    ],
    []
  )

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("member_manage")} />
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
