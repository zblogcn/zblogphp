import { keepPreviousData, useQuery } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { ExternalLink } from "lucide-react"
import { useMemo, useState } from "react"

import { apiGet, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"

interface CommentRow {
  ID: number
  Content: string
  AuthorID: number
  PostTime: number
  IsChecking: number
  Post?: { ID?: number; Title?: string }
  Author?: { Name?: string; StaticName?: string }
}

interface CommentListData {
  list: CommentRow[]
  pagebar: Pagebar
}

function formatTime(value: number): string {
  if (!value) {
    return "—"
  }
  const date = new Date(value * 1000)
  const pad = (n: number) => String(n).padStart(2, "0")
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
    date.getDate()
  )} ${pad(date.getHours())}:${pad(date.getMinutes())}`
}

function stripTags(html: string): string {
  return html.replace(/<[^>]*>/g, "").trim()
}

export function CommentsPage() {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ["comment", "list", page],
    queryFn: () =>
      apiGet<CommentListData>("comment", "list", {
        manage: 1,
        with_relations: "Post,Author",
        page,
        perpage: zbp.options.manageCount,
      }),
    placeholderData: keepPreviousData,
  })

  const columns = useMemo<ColumnDef<CommentRow, unknown>[]>(
    () => [
      {
        id: "Author",
        header: "作者",
        cell: ({ row }) => (
          <span className="whitespace-nowrap font-medium">
            {row.original.Author?.StaticName ?? row.original.Author?.Name ?? "游客"}
          </span>
        ),
      },
      {
        id: "Content",
        header: "内容",
        cell: ({ row }) => (
          <span className="block max-w-[52ch] truncate text-muted-foreground">
            {stripTags(row.original.Content) || "—"}
          </span>
        ),
      },
      {
        id: "Post",
        header: "文章",
        cell: ({ row }) => (
          <span className="block max-w-[22ch] truncate text-muted-foreground">
            {row.original.Post?.Title ?? "—"}
          </span>
        ),
      },
      {
        id: "PostTime",
        header: "时间",
        cell: ({ row }) => (
          <span className="whitespace-nowrap text-muted-foreground tabular-nums">
            {formatTime(row.original.PostTime)}
          </span>
        ),
      },
      {
        id: "IsChecking",
        header: t("publish_state"),
        cell: ({ row }) =>
          Number(row.original.IsChecking) > 0 ? (
            <Badge variant="warning">待审核</Badge>
          ) : (
            <Badge variant="success">已通过</Badge>
          ),
      },
      {
        id: "actions",
        header: "",
        cell: () => (
          <div className="flex justify-end">
            <Button asChild variant="ghost" size="icon-sm">
              <a
                href={`${zbp.host}zb_system/cmd.php?act=CommentMng`}
                target="_blank"
                rel="noopener noreferrer"
                title="在旧版中管理"
              >
                <ExternalLink className="size-4" />
              </a>
            </Button>
          </div>
        ),
      },
    ],
    []
  )

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={t("comment_manage")}
        actions={
          <Button asChild variant="outline" size="sm">
            <a
              href={`${zbp.host}zb_system/cmd.php?act=CommentMng`}
              target="_blank"
              rel="noopener noreferrer"
            >
              <ExternalLink className="size-4" /> 旧版管理
            </a>
          </Button>
        }
      />

      <DataTable
        columns={columns}
        data={data?.list ?? []}
        pagebar={data?.pagebar}
        onPageChange={setPage}
        isLoading={isLoading}
        emptyTitle="暂无评论"
      />
    </div>
  )
}
