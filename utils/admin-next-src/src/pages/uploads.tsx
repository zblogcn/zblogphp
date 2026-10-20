import { keepPreviousData, useQuery } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { ExternalLink } from "lucide-react"
import { useMemo, useState } from "react"

import { apiGet, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { DataTable } from "@/components/data-table"
import { PageHeader } from "@/components/page-header"
import { Button } from "@/components/ui/button"

interface UploadRow {
  ID: number
  Name: string
  Size: number
  PostTime: number
  Url: string
  MimeType: string
}

interface UploadListData {
  list: UploadRow[]
  pagebar: Pagebar
}

function formatSize(bytes: number): string {
  if (!bytes) {
    return "—"
  }
  const units = ["B", "KB", "MB", "GB"]
  let value = bytes
  let unit = 0
  while (value >= 1024 && unit < units.length - 1) {
    value /= 1024
    unit += 1
  }
  return `${value.toFixed(value >= 100 || unit === 0 ? 0 : 1)} ${units[unit]}`
}

function formatTime(value: number): string {
  if (!value) {
    return "—"
  }
  const date = new Date(value * 1000)
  const pad = (n: number) => String(n).padStart(2, "0")
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

 function isImage(mime: string): boolean {
  return typeof mime === "string" && mime.startsWith("image/")
}

export function UploadsPage() {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ["upload", "list", page],
    queryFn: () =>
      apiGet<UploadListData>("upload", "list", {
        page,
        perpage: 40,
      }),
    placeholderData: keepPreviousData,
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
          <a
            href={row.original.Url}
            target="_blank"
            rel="noopener noreferrer"
            className="block max-w-[42ch] truncate hover:underline"
            title={row.original.Name}
          >
            {row.original.Name}
          </a>
        ),
      },
      {
        id: "MimeType",
        header: "类型",
        cell: ({ row }) => (
          <span className="text-xs text-muted-foreground">
            {row.original.MimeType || "—"}
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
            <Button asChild variant="ghost" size="icon-sm">
              <a
                href={row.original.Url}
                target="_blank"
                rel="noopener noreferrer"
                title="打开"
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
        title={t("upload_manage")}
        actions={
          <Button asChild variant="outline" size="sm">
            <a
              href={`${zbp.host}zb_system/cmd.php?act=UploadMng`}
              target="_blank"
              rel="noopener noreferrer"
            >
              <ExternalLink className="size-4" /> 旧版上传
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
        emptyTitle="暂无附件"
      />
    </div>
  )
}
