import {
  flexRender,
  getCoreRowModel,
  useReactTable,
  type ColumnDef,
} from "@tanstack/react-table"
import { ChevronLeft, ChevronRight, Inbox } from "lucide-react"
import type { ReactNode } from "react"

import { t } from "@/lib/i18n"
import type { Pagebar } from "@/lib/api"
import { Button } from "@/components/ui/button"
import { Empty } from "@/components/ui/empty"
import { Skeleton } from "@/components/ui/skeleton"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"

interface DataTableProps<T> {
  columns: ColumnDef<T, unknown>[]
  data: T[]
  pagebar?: Pagebar
  onPageChange?: (page: number) => void
  isLoading?: boolean
  emptyTitle?: string
  emptyDescription?: string
  toolbar?: ReactNode
}

export function DataTable<T>({
  columns,
  data,
  pagebar,
  onPageChange,
  isLoading = false,
  emptyTitle,
  emptyDescription,
  toolbar,
}: DataTableProps<T>) {
  const table = useReactTable({
    data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
  })

  const empty = !isLoading && data.length === 0
  const pageNow = pagebar?.PageNow ?? 1
  const pageAll = pagebar?.PageAll ?? 1

  return (
    <div className="rounded-lg border bg-card">
      {toolbar ? (
        <div className="flex flex-wrap items-center gap-2 border-b px-3 py-2.5">
          {toolbar}
        </div>
      ) : null}

      <Table>
        <TableHeader>
          {table.getHeaderGroups().map((headerGroup) => (
            <TableRow key={headerGroup.id} className="hover:bg-transparent">
              {headerGroup.headers.map((header) => (
                <TableHead key={header.id} className="h-10">
                  {header.isPlaceholder
                    ? null
                    : flexRender(
                        header.column.columnDef.header,
                        header.getContext()
                      )}
                </TableHead>
              ))}
            </TableRow>
          ))}
        </TableHeader>
        <TableBody>
          {isLoading ? (
            Array.from({ length: 6 }).map((_, rowIndex) => (
              <TableRow key={`skeleton-${rowIndex}`}>
                {columns.map((_, cellIndex) => (
                  <TableCell key={`skeleton-${rowIndex}-${cellIndex}`}>
                    <Skeleton className="h-4 w-full max-w-40" />
                  </TableCell>
                ))}
              </TableRow>
            ))
          ) : empty ? (
            <TableRow className="hover:bg-transparent">
              <TableCell colSpan={columns.length} className="p-0">
                <Empty
                  icon={Inbox}
                  title={emptyTitle ?? t("empty_title")}
                  description={emptyDescription ?? t("empty_description")}
                />
              </TableCell>
            </TableRow>
          ) : (
            table.getRowModel().rows.map((row) => (
              <TableRow key={row.id}>
                {row.getVisibleCells().map((cell) => (
                  <TableCell key={cell.id}>
                    {flexRender(cell.column.columnDef.cell, cell.getContext())}
                  </TableCell>
                ))}
              </TableRow>
            ))
          )}
        </TableBody>
      </Table>

      {pagebar && pagebar.AllCount > 0 ? (
        <div className="flex items-center justify-between gap-3 border-t px-3 py-2.5">
          <div className="text-xs text-muted-foreground">
            {t("page_info", { total: pagebar.AllCount })}
          </div>
          <div className="flex items-center gap-1.5">
            <Button
              variant="outline"
              size="sm"
              disabled={pageNow <= 1}
              onClick={() => onPageChange?.(pageNow - 1)}
            >
              <ChevronLeft className="size-4" />
              <span className="hidden sm:inline">{t("page_prev")}</span>
            </Button>
            <span className="min-w-14 text-center text-xs text-muted-foreground">
              {pageNow} / {pageAll}
            </span>
            <Button
              variant="outline"
              size="sm"
              disabled={pageNow >= pageAll}
              onClick={() => onPageChange?.(pageNow + 1)}
            >
              <span className="hidden sm:inline">{t("page_next")}</span>
              <ChevronRight className="size-4" />
            </Button>
          </div>
        </div>
      ) : null}
    </div>
  )
}
