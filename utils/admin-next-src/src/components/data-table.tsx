import {
  flexRender,
  getCoreRowModel,
  useReactTable,
  type ColumnDef,
  type RowSelectionState,
} from "@tanstack/react-table"
import { ChevronLeft, ChevronRight, Inbox } from "lucide-react"
import { useMemo, type ReactNode } from "react"

import { t } from "@/lib/i18n"
import type { Pagebar } from "@/lib/api"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
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

interface Selection {
  selectedIds: string[]
  onChange: (ids: string[]) => void
}

interface DataTableProps<T> {
  columns: ColumnDef<T, unknown>[]
  data: T[]
  pagebar?: Pagebar
  onPageChange?: (page: number) => void
  isLoading?: boolean
  emptyTitle?: string
  emptyDescription?: string
  toolbar?: ReactNode
  rowId?: (row: T) => string
  selection?: Selection
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
  rowId,
  selection,
}: DataTableProps<T>) {
  const rowSelection = useMemo<RowSelectionState>(() => {
    const state: RowSelectionState = {}
    for (const id of selection?.selectedIds ?? []) {
      state[id] = true
    }
    return state
  }, [selection?.selectedIds])

  const tableColumns = useMemo<ColumnDef<T, unknown>[]>(() => {
    if (!selection || !rowId) {
      return columns
    }

    const selectColumn: ColumnDef<T, unknown> = {
      id: "__select__",
      header: ({ table }) => (
        <Checkbox
          aria-label="全选"
          checked={
            table.getIsAllRowsSelected()
              ? true
              : table.getIsSomeRowsSelected()
                ? "indeterminate"
                : false
          }
          onCheckedChange={(value) =>
            table.toggleAllRowsSelected(value === true)
          }
        />
      ),
      cell: ({ row }) => (
        <Checkbox
          aria-label="选择此行"
          checked={row.getIsSelected()}
          onCheckedChange={(value) => row.toggleSelected(value === true)}
        />
      ),
      size: 32,
      enableSorting: false,
    }

    return [selectColumn, ...columns]
  }, [columns, selection, rowId])

  const table = useReactTable({
    data,
    columns: tableColumns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    enableRowSelection: Boolean(selection),
    state: { rowSelection },
    getRowId: rowId ? (row) => rowId(row) : undefined,
    onRowSelectionChange: (updater) => {
      const next =
        typeof updater === "function" ? updater(rowSelection) : updater
      selection?.onChange(
        Object.keys(next).filter((key) => next[key])
      )
    },
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
                <TableHead
                  key={header.id}
                  className="h-10"
                  style={
                    header.column.id === "__select__"
                      ? { width: 40 }
                      : undefined
                  }
                >
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
                {tableColumns.map((_, cellIndex) => (
                  <TableCell key={`skeleton-${rowIndex}-${cellIndex}`}>
                    <Skeleton className="h-4 w-full max-w-40" />
                  </TableCell>
                ))}
              </TableRow>
            ))
          ) : empty ? (
            <TableRow className="hover:bg-transparent">
              <TableCell colSpan={tableColumns.length} className="p-0">
                <Empty
                  icon={Inbox}
                  title={emptyTitle ?? t("empty_title")}
                  description={emptyDescription ?? t("empty_description")}
                />
              </TableCell>
            </TableRow>
          ) : (
            table.getRowModel().rows.map((row) => (
              <TableRow
                key={row.id}
                data-state={row.getIsSelected() ? "selected" : undefined}
              >
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
