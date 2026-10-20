import {
  keepPreviousData,
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { Check, Pencil, ShieldQuestion, Trash2 } from "lucide-react"
import { useMemo, useState } from "react"
import { toast } from "sonner"

import { apiGet, apiPost, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { DataTable } from "@/components/data-table"
import { FormRow } from "@/components/form-row"
import { PageHeader } from "@/components/page-header"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Textarea } from "@/components/ui/textarea"

interface CommentRow {
  ID: number
  Content: string
  AuthorID: number
  PostTime: number
  IsChecking: number
  Name?: string
  Email?: string
  HomePage?: string
  Post?: { ID?: number; Title?: string }
  Author?: { Name?: string; StaticName?: string }
}

interface CommentListData {
  list: CommentRow[]
  pagebar: Pagebar
}

type BatchAction = "all_pass" | "all_audit" | "all_del"

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
  const queryClient = useQueryClient()
  const [page, setPage] = useState(1)
  const [selected, setSelected] = useState<string[]>([])
  const [pendingAction, setPendingAction] = useState<BatchAction | null>(null)
  const [editing, setEditing] = useState<CommentRow | null>(null)
  const [draft, setDraft] = useState("")
  const [authorName, setAuthorName] = useState("")
  const [authorEmail, setAuthorEmail] = useState("")

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

  const refresh = () => {
    setSelected([])
    queryClient.invalidateQueries({ queryKey: ["comment", "list"] })
  }

  const save = useMutation({
    mutationFn: (row: CommentRow) =>
      apiPost<Record<string, unknown>>("comment", "post", {
        ID: row.ID,
        LogID: row.Post?.ID ?? 0,
        ParentID: 0,
        RootID: 0,
        Content: draft,
        Name: authorName,
        Email: authorEmail,
        AuthorID: row.AuthorID,
        PostTime: row.PostTime,
      }),
    onSuccess: () => {
      toast.success("评论已保存")
      setEditing(null)
      refresh()
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const check = useMutation({
    mutationFn: (id: number) =>
      apiPost<Record<string, unknown>>("comment", "check", { id }),
    onSuccess: () => {
      toast.success("审核状态已更新")
      refresh()
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const batch = useMutation({
    mutationFn: (action: BatchAction) =>
      apiPost<Record<string, never>>("comment", "batch", {
        [action]: 1,
        id: selected.map(Number),
      }),
    onSuccess: () => {
      toast.success("操作完成")
      setPendingAction(null)
      refresh()
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setPendingAction(null)
    },
  })

  const columns = useMemo<ColumnDef<CommentRow, unknown>[]>(
    () => [
      {
        id: "Author",
        header: "作者",
        cell: ({ row }) => (
          <div className="min-w-0">
            <div className="whitespace-nowrap font-medium">
              {row.original.Author?.StaticName ??
                row.original.Author?.Name ??
                row.original.Name ??
                "游客"}
            </div>
            {row.original.Email ? (
              <div className="truncate text-xs text-muted-foreground">
                {row.original.Email}
              </div>
            ) : null}
          </div>
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
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button
              variant="ghost"
              size="icon-sm"
              aria-label="审核"
              onClick={() => check.mutate(row.original.ID)}
            >
              {Number(row.original.IsChecking) > 0 ? (
                <Check className="size-4" />
              ) : (
                <ShieldQuestion className="size-4" />
              )}
            </Button>
            <Button
              variant="ghost"
              size="icon-sm"
              aria-label="编辑"
              onClick={() => {
                setEditing(row.original)
                setDraft(row.original.Content ?? "")
                setAuthorName(row.original.Author?.Name ?? row.original.Name ?? "")
                setAuthorEmail(row.original.Email ?? "")
              }}
            >
              <Pencil className="size-4" />
            </Button>
            <Button
              variant="ghost"
              size="icon-sm"
              aria-label="删除"
              onClick={() => {
                setSelected([String(row.original.ID)])
                setPendingAction("all_del")
              }}
            >
              <Trash2 className="size-4" />
            </Button>
          </div>
        ),
      },
    ],
    [check]
  )

  const hasSelection = selected.length > 0

  return (
    <div className="flex flex-col gap-4">
      <PageHeader title={t("comment_manage")} />

      <DataTable
        columns={columns}
        data={data?.list ?? []}
        pagebar={data?.pagebar}
        onPageChange={(next) => {
          setSelected([])
          setPage(next)
        }}
        isLoading={isLoading}
        emptyTitle="暂无评论"
        rowId={(row) => String(row.ID)}
        selection={{ selectedIds: selected, onChange: setSelected }}
        toolbar={
          hasSelection ? (
            <>
              <span className="text-xs text-muted-foreground">
                已选 {selected.length} 项
              </span>
              <div className="flex flex-wrap gap-2">
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => batch.mutate("all_pass")}
                >
                  <Check className="size-4" /> 通过
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => batch.mutate("all_audit")}
                >
                  <ShieldQuestion className="size-4" /> 转待审
                </Button>
                <Button
                  size="sm"
                  variant="destructive"
                  onClick={() => setPendingAction("all_del")}
                >
                  <Trash2 className="size-4" /> 删除
                </Button>
              </div>
            </>
          ) : (
            <span className="text-xs text-muted-foreground">
              勾选评论后可批量操作
            </span>
          )
        }
      />

      <Dialog
        open={editing !== null}
        onOpenChange={(open) => {
          if (!open) {
            setEditing(null)
          }
        }}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>编辑评论</DialogTitle>
          </DialogHeader>

          <div className="flex flex-col gap-3">
            <FormRow label="所属文章">
              <div className="text-sm">{editing?.Post?.Title ?? "—"}</div>
            </FormRow>

            <FormRow label="评论内容" htmlFor="comment-content" required>
              <Textarea
                id="comment-content"
                rows={6}
                value={draft}
                onChange={(event) => setDraft(event.target.value)}
              />
            </FormRow>

            <FormRow label="作者名称" htmlFor="comment-name">
              <input
                id="comment-name"
                className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                value={authorName}
                onChange={(event) => setAuthorName(event.target.value)}
              />
            </FormRow>

            <FormRow label="作者邮箱" htmlFor="comment-email">
              <input
                id="comment-email"
                type="email"
                className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                value={authorEmail}
                onChange={(event) => setAuthorEmail(event.target.value)}
              />
            </FormRow>
          </div>

          <DialogFooter>
            <Button variant="outline" onClick={() => setEditing(null)}>
              取消
            </Button>
            <Button
              disabled={!draft.trim() || save.isPending}
              onClick={() => {
                if (editing) {
                  save.mutate(editing)
                }
              }}
            >
              {save.isPending ? "保存中…" : "保存"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <ConfirmDialog
        open={pendingAction === "all_del"}
        onOpenChange={(open) => setPendingAction(open ? "all_del" : null)}
        title={`删除 ${selected.length} 条评论？`}
        description="删除后无法恢复。"
        confirmText="删除"
        destructive
        onConfirm={() => batch.mutate("all_del")}
      />
    </div>
  )
}
