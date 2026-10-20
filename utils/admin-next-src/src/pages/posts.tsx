import {
  keepPreviousData,
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { ExternalLink, Plus, Search, Trash2 } from "lucide-react"
import { useMemo, useState } from "react"
import { toast } from "sonner"

import { apiGet, apiPost, type Pagebar } from "@/lib/api"
import { postStatusName, t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { useDebouncedValue } from "@/hooks/use-debounced-value"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"

interface PostRow {
  ID: number
  Title: string
  Alias: string
  Status: number
  IsTop: number
  PostTime: number
  CommNums: number
  Url: string
  Category?: { Name?: string }
  Author?: { Name?: string; StaticName?: string }
}

interface PostListData {
  list: PostRow[]
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

interface PostListPageProps {
  type: 0 | 1
  title: string
  editAction: "ArticleEdt" | "PageEdt"
  manageAction: "ArticleMng" | "PageMng"
}

function PostListPage({
  type,
  title,
  editAction,
  manageAction,
}: PostListPageProps) {
  const queryClient = useQueryClient()
  const [page, setPage] = useState(1)
  const [keyword, setKeyword] = useState("")
  const [selected, setSelected] = useState<string[]>([])
  const [confirmOpen, setConfirmOpen] = useState(false)
  const search = useDebouncedValue(keyword)

  const { data, isLoading } = useQuery({
    queryKey: ["post", "list", type, page, search],
    queryFn: () =>
      apiGet<PostListData>("post", "list", {
        manage: 1,
        type,
        without_content: 1,
        with_relations: "Category,Author",
        page,
        perpage: zbp.options.manageCount,
        search,
      }),
    placeholderData: keepPreviousData,
  })

  const remove = useMutation({
    mutationFn: () =>
      apiPost<{ deleted: number }>("post", "batch", {
        type,
        id: selected.map(Number),
      }),
    onSuccess: (result) => {
      toast.success(`已删除 ${result.deleted ?? selected.length} 项`)
      setSelected([])
      setConfirmOpen(false)
      queryClient.invalidateQueries({ queryKey: ["post", "list"] })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setConfirmOpen(false)
    },
  })

  const columns = useMemo<ColumnDef<PostRow, unknown>[]>(() => {
    const base: ColumnDef<PostRow, unknown>[] = [
      {
        id: "ID",
        header: "ID",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">{row.original.ID}</span>
        ),
      },
      {
        id: "Category",
        header: "分类",
        cell: ({ row }) => (
          <span className="whitespace-nowrap text-muted-foreground">
            {row.original.Category?.Name ?? "—"}
          </span>
        ),
      },
    ]

    if (type === 0) {
      base.push({
        id: "Author",
        header: "作者",
        cell: ({ row }) => (
          <span className="whitespace-nowrap text-muted-foreground">
            {row.original.Author?.StaticName ?? row.original.Author?.Name ?? "—"}
          </span>
        ),
      })
    }

    base.push(
      {
        id: "Title",
        header: "标题",
        cell: ({ row }) => (
          <div className="flex min-w-0 items-center gap-2">
            <a
              href={row.original.Url}
              target="_blank"
              rel="noopener noreferrer"
              className="max-w-[46ch] truncate hover:underline"
              title={row.original.Title}
            >
              {row.original.Title || "(无标题)"}
            </a>
            {Number(row.original.IsTop) > 0 ? (
              <Badge variant="secondary">置顶</Badge>
            ) : null}
          </div>
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
        id: "CommNums",
        header: "评论",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {row.original.CommNums ?? 0}
          </span>
        ),
      },
      {
        id: "Status",
        header: t("publish_state"),
        cell: ({ row }) => (
          <Badge
            variant={
              Number(row.original.Status) === 0
                ? "success"
                : Number(row.original.Status) === 1
                  ? "secondary"
                  : "warning"
            }
          >
            {postStatusName(row.original.Status)}
          </Badge>
        ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end">
            <Button asChild variant="ghost" size="icon-sm">
              <a
                href={`${zbp.host}zb_system/cmd.php?act=${editAction}&id=${row.original.ID}`}
                target="_blank"
                rel="noopener noreferrer"
                title="在旧版编辑器中打开"
              >
                <ExternalLink className="size-4" />
              </a>
            </Button>
          </div>
        ),
      }
    )

    return base
  }, [type, editAction])

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={title}
        actions={
          <>
            <Button asChild variant="outline" size="sm">
              <a
                href={`${zbp.host}zb_system/cmd.php?act=${manageAction}`}
                target="_blank"
                rel="noopener noreferrer"
              >
                <ExternalLink className="size-4" />
                旧版（含插件列）
              </a>
            </Button>
            <Button asChild size="sm">
              <a
                href={`${zbp.host}zb_system/cmd.php?act=${editAction}`}
                target="_blank"
                rel="noopener noreferrer"
              >
                <Plus className="size-4" /> 新建
              </a>
            </Button>
          </>
        }
      />

      <DataTable
        columns={columns}
        data={data?.list ?? []}
        pagebar={data?.pagebar}
        onPageChange={(next) => {
          setSelected([])
          setPage(next)
        }}
        isLoading={isLoading}
        rowId={(row) => String(row.ID)}
        selection={{ selectedIds: selected, onChange: setSelected }}
        toolbar={
          <>
            <div className="relative w-full sm:w-72">
              <Search className="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                value={keyword}
                onChange={(event) => {
                  setKeyword(event.target.value)
                  setSelected([])
                  setPage(1)
                }}
                placeholder={`${t("search")}…`}
                className="h-8 pl-8"
                aria-label={t("search")}
              />
            </div>

            {selected.length > 0 ? (
              <Button
                variant="destructive"
                size="sm"
                onClick={() => setConfirmOpen(true)}
              >
                <Trash2 className="size-4" />
                删除所选 ({selected.length})
              </Button>
            ) : null}
          </>
        }
      />

      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={`删除 ${selected.length} 项？`}
        description="删除后无法恢复，相关评论会一并删除。"
        confirmText="删除"
        destructive
        onConfirm={() => remove.mutate()}
      />
    </div>
  )
}

export function PostsPage() {
  return (
    <PostListPage
      type={0}
      title={t("article_manage")}
      editAction="ArticleEdt"
      manageAction="ArticleMng"
    />
  )
}

export function PagesPage() {
  return (
    <PostListPage
      type={1}
      title={t("page_manage")}
      editAction="PageEdt"
      manageAction="PageMng"
    />
  )
}
