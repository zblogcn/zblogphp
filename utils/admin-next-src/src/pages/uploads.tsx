import {
  keepPreviousData,
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { Trash2, Upload } from "lucide-react"
import { useMemo, useRef, useState } from "react"
import { toast } from "sonner"

import { apiForm, apiGet, apiUpload, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { hasRight } from "@/lib/zbp"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Button } from "@/components/ui/button"

interface UploadRow {
  ID: number
  Name: string
  SourceName?: string
  Size: number
  PostTime: number
  Url: string
  MimeType: string
}

interface UploadListData {
  list: UploadRow[]
  pagebar?: Pagebar
}

const EMPTY = "—"

/**
 * upload.list 的返回结构与其他 mod 不同（data 再包一层数组），
 * 这里统一成 { list, pagebar }。
 */
function normalize(payload: unknown): UploadListData {
  while (Array.isArray(payload) && payload.length > 0) {
    payload = payload[0]
  }

  if (payload && typeof payload === "object") {
    const record = payload as Record<string, unknown>
    const list = Array.isArray(record.list) ? (record.list as UploadRow[]) : []
    const pagebar =
      record.pagebar && typeof record.pagebar === "object"
        ? (record.pagebar as Pagebar)
        : record.pagination && typeof record.pagination === "object"
          ? (record.pagination as Pagebar)
          : undefined
    return { list, pagebar }
  }

  return { list: [] }
}

function formatSize(raw: number | string): string {
  // 接口把数字序列化成字符串，这里统一转成数值再格式化
  let value = Number(raw)
  if (!value || Number.isNaN(value)) {
    return EMPTY
  }
  const units = ["B", "KB", "MB", "GB"]
  let unit = 0
  while (value >= 1024 && unit < units.length - 1) {
    value /= 1024
    unit += 1
  }
  return `${value.toFixed(value >= 100 || unit === 0 ? 0 : 1)} ${units[unit]}`
}

function formatTime(value: number): string {
  if (!value) {
    return EMPTY
  }
  const date = new Date(value * 1000)
  const pad = (n: number) => String(n).padStart(2, "0")
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

function isImage(mime: string): boolean {
  return typeof mime === "string" && mime.startsWith("image/")
}

export function UploadsPage() {
  const queryClient = useQueryClient()
  const inputRef = useRef<HTMLInputElement>(null)
  const [page, setPage] = useState(1)
  const [deleting, setDeleting] = useState<UploadRow | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ["upload", "list", page],
    queryFn: async () =>
      normalize(
        await apiGet<unknown>("upload", "list", {
          page,
          perpage: 40,
          sortby: "ID",
          order: "DESC",
        })
      ),
    placeholderData: keepPreviousData,
  })

  const upload = useMutation({
    mutationFn: async (files: File[]) => {
      const results: UploadRow[] = []
      for (const file of files) {
        const form = new FormData()
        form.append("file", file)
        form.set("auto_rename", "1")
        const result = await apiUpload<{ upload: UploadRow }>(
          "upload",
          "post",
          form
        )
        results.push(result.upload)
      }
      return results
    },
    onSuccess: (results) => {
      toast.success(`已上传 ${results.length} 个文件`)
      // 回到第一页；同页时 setPage 不会触发查询，因此再主动重取一次
      setPage(1)
      void queryClient.refetchQueries({ queryKey: ["upload", "list"] })
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const remove = useMutation({
    mutationFn: (id: number) =>
      apiForm<Record<string, unknown>>("upload", "delete", { id }),
    onSuccess: () => {
      toast.success("已删除")
      setDeleting(null)
      queryClient.invalidateQueries({ queryKey: ["upload", "list"] })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setDeleting(null)
    },
  })

  const columns = useMemo<ColumnDef<UploadRow, unknown>[]>(
    () => [
      {
        id: "preview",
        header: "",
        cell: ({ row }) => (
          <div className="flex size-9 items-center justify-center overflow-hidden rounded border bg-muted">
            {isImage(row.original.MimeType || "") ? (
              <img
                src={row.original.Url}
                alt=""
                loading="lazy"
                className="size-full object-cover"
              />
            ) : (
              <span className="text-[10px] text-muted-foreground">FILE</span>
            )}
          </div>
        ),
      },
      {
        id: "Name",
        header: "文件名",
        cell: ({ row }) => (
          <div className="min-w-0">
            <a
              href={row.original.Url}
              target="_blank"
              rel="noopener noreferrer"
              className="block max-w-[42ch] truncate hover:underline"
              title={row.original.Name}
            >
              {row.original.Name}
            </a>
            {row.original.SourceName ? (
              <div className="truncate text-xs text-muted-foreground">
                {row.original.SourceName}
              </div>
            ) : null}
          </div>
        ),
      },
      {
        id: "MimeType",
        header: "类型",
        cell: ({ row }) => (
          <span className="text-xs text-muted-foreground">
            {row.original.MimeType || EMPTY}
          </span>
        ),
      },
      {
        id: "Size",
        header: "大小",
        cell: ({ row }) => (
          <span className="whitespace-nowrap tabular-nums text-muted-foreground">
            {formatSize(row.original.Size)}
          </span>
        ),
      },
      {
        id: "PostTime",
        header: "上传时间",
        cell: ({ row }) => (
          <span className="whitespace-nowrap tabular-nums text-muted-foreground">
            {formatTime(row.original.PostTime)}
          </span>
        ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end">
            <Button
              variant="ghost"
              size="icon-sm"
              aria-label="删除"
              disabled={!hasRight("UploadMng")}
              onClick={() => setDeleting(row.original)}
            >
              <Trash2 className="size-4" />
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
        title={t("upload_manage")}
        actions={
          <>
            <Button asChild size="sm" disabled={upload.isPending}>
              <label
                className={
                  !hasRight("UploadPst") || upload.isPending
                    ? "pointer-events-none opacity-50"
                    : "cursor-pointer"
                }
              >
                <input
                  ref={inputRef}
                  type="file"
                  multiple
                  className="sr-only"
                  disabled={!hasRight("UploadPst") || upload.isPending}
                  onChange={(event) => {
                    // 合成事件在异步链路里会被复用，先把文件快照成数组
                    const files = Array.from(event.target.files ?? [])
                    event.target.value = ""
                    if (files.length > 0) {
                      upload.mutate(files)
                    }
                  }}
                />
                <Upload className="size-4" />
                {upload.isPending ? "上传中…" : "上传"}
              </label>
            </Button>
          </>
        }
      />

      <DataTable
        columns={columns}
        data={data?.list ?? []}
        pagebar={data?.pagebar}
        onPageChange={setPage}
        isLoading={isLoading}
        emptyTitle="暂无附件"
        emptyDescription="点击右上角的上传按钮添加文件。"
      />

      <ConfirmDialog
        open={deleting !== null}
        onOpenChange={(open) => {
          if (!open) {
            setDeleting(null)
          }
        }}
        title={`删除「${deleting?.Name ?? ""}」？`}
        description="文件会从服务器上移除，引用它的文章会显示为失效链接。"
        confirmText="删除"
        destructive
        onConfirm={() => {
          if (deleting) {
            remove.mutate(deleting.ID)
          }
        }}
      />
    </div>
  )
}
