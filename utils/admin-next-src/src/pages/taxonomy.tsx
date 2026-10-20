import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { Pencil, Plus, Search, Trash2 } from "lucide-react"
import { useMemo, useState } from "react"
import { toast } from "sonner"

import { apiForm, apiGet, apiPost, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { DataTable } from "@/components/data-table"
import { FormRow } from "@/components/form-row"
import { PageHeader } from "@/components/page-header"
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { useOptions } from "@/hooks/use-options"

interface TermRow {
  ID: number
  Name: string
  Alias: string
  Intro?: string
  Template?: string
  Order?: number
  ParentID?: number
  Count?: number
  AllCount?: number
}

interface TermListData {
  list: TermRow[]
  pagebar: Pagebar
}

interface TermForm {
  ID: number
  Name: string
  Alias: string
  Intro: string
  Template: string
  Order: string
  ParentID?: string
}

const emptyCategoryForm: TermForm = {
  ID: 0,
  Name: "",
  Alias: "",
  Intro: "",
  Template: "",
  Order: "0",
  ParentID: "0",
}

const emptyTagForm: TermForm = {
  ID: 0,
  Name: "",
  Alias: "",
  Intro: "",
  Template: "",
  Order: "0",
}

interface TaxonomyPageProps {
  mod: "category" | "tag"
  title: string
  countKey: "AllCount" | "Count"
}

export function TaxonomyPage({ mod, title, countKey }: TaxonomyPageProps) {
  const queryClient = useQueryClient()
  const [page, setPage] = useState(1)
  const [keyword, setKeyword] = useState("")
  const [editing, setEditing] = useState<TermForm | null>(null)
  const [deleting, setDeleting] = useState<TermRow | null>(null)

  const isCategory = mod === "category"
  const emptyForm = isCategory ? emptyCategoryForm : emptyTagForm

  const { data, isLoading } = useQuery({
    queryKey: [mod, "list", page],
    queryFn: () => apiGet<TermListData>(mod, "list", { page, perpage: 100 }),
  })

  const categoryOptions = useOptions(
    "category",
    "list",
    (row) => String(row.ID),
    (row) => String(row.SymbolName ?? row.Name)
  )

  const save = useMutation({
    mutationFn: (form: TermForm) =>
      apiPost<Record<string, unknown>>(mod, "post", {
        ID: form.ID,
        Type: 0,
        Name: form.Name.trim(),
        Alias: form.Alias.trim(),
        Intro: form.Intro,
        Template: form.Template,
        Order: form.Order,
        ...(isCategory ? { ParentID: form.ParentID ?? "0" } : {}),
      }),
    onSuccess: (_result, variables) => {
      toast.success(variables.ID > 0 ? "已保存" : "已创建")
      setEditing(null)
      queryClient.invalidateQueries({ queryKey: [mod, "list"] })
      queryClient.invalidateQueries({ queryKey: ["category", "list"] })
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const remove = useMutation({
    mutationFn: (id: number) =>
      apiForm<Record<string, unknown>>(mod, "delete", { id }),
    onSuccess: () => {
      toast.success("已删除")
      setDeleting(null)
      queryClient.invalidateQueries({ queryKey: [mod, "list"] })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setDeleting(null)
    },
  })

  const filtered = useMemo(() => {
    const list = data?.list ?? []
    const needle = keyword.trim().toLowerCase()
    if (!needle) {
      return list
    }
    return list.filter(
      (row) =>
        row.Name.toLowerCase().includes(needle) ||
        (row.Alias ?? "").toLowerCase().includes(needle)
    )
  }, [data?.list, keyword])

  const columns = useMemo<ColumnDef<TermRow, unknown>[]>(
    () => [
      {
        id: "ID",
        header: "ID",
        cell: ({ row }) => (
          <span className="tabular-nums text-muted-foreground">
            {row.original.ID}
          </span>
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
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button
              variant="ghost"
              size="icon-sm"
              aria-label="编辑"
              onClick={() =>
                setEditing({
                  ID: row.original.ID,
                  Name: row.original.Name,
                  Alias: row.original.Alias ?? "",
                  Intro: row.original.Intro ?? "",
                  Template: row.original.Template ?? "",
                  Order: String(row.original.Order ?? 0),
                  ...(isCategory
                    ? { ParentID: String(row.original.ParentID ?? 0) }
                    : {}),
                })
              }
            >
              <Pencil className="size-4" />
            </Button>
            <Button
              variant="ghost"
              size="icon-sm"
              aria-label="删除"
              onClick={() => setDeleting(row.original)}
            >
              <Trash2 className="size-4" />
            </Button>
          </div>
        ),
      },
    ],
    [countKey, isCategory]
  )

  const form = editing ?? emptyForm
  const isEdit = form.ID > 0
  const noun = isCategory ? "分类" : "标签"

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={title}
        description={`共 ${data?.pagebar?.AllCount ?? 0} 项`}
        actions={
          <Button size="sm" onClick={() => setEditing({ ...emptyForm })}>
            <Plus className="size-4" /> 新建
          </Button>
        }
      />

      <DataTable
        columns={columns}
        data={filtered}
        pagebar={data?.pagebar}
        onPageChange={setPage}
        isLoading={isLoading}
        toolbar={
          <div className="relative w-full sm:w-72">
            <Search className="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              value={keyword}
              onChange={(event) => setKeyword(event.target.value)}
              placeholder="按名称或别名筛选…"
              className="h-8 pl-8"
              aria-label="筛选"
            />
          </div>
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
            <DialogTitle>{isEdit ? `编辑${noun}` : `新建${noun}`}</DialogTitle>
          </DialogHeader>

          <div className="flex flex-col gap-3">
            <FormRow label="名称" htmlFor="term-name" required>
              <Input
                id="term-name"
                value={form.Name}
                onChange={(event) =>
                  setEditing({ ...form, Name: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="别名" htmlFor="term-alias" hint="留空时由系统生成">
              <Input
                id="term-alias"
                value={form.Alias}
                onChange={(event) =>
                  setEditing({ ...form, Alias: event.target.value })
                }
              />
            </FormRow>

            {isCategory ? (
              <FormRow label="父级分类" htmlFor="term-parent">
                <select
                  id="term-parent"
                  className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                  value={form.ParentID ?? "0"}
                  onChange={(event) =>
                    setEditing({ ...form, ParentID: event.target.value })
                  }
                >
                  <option value="0">（无）</option>
                  {categoryOptions.options
                    .filter((option) => option.value !== String(form.ID))
                    .map((option) => (
                      <option key={option.value} value={option.value}>
                        {option.label}
                      </option>
                    ))}
                </select>
              </FormRow>
            ) : null}

            <FormRow label="排序" htmlFor="term-order">
              <Input
                id="term-order"
                type="number"
                value={form.Order}
                onChange={(event) =>
                  setEditing({ ...form, Order: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="模板" htmlFor="term-template" hint="留空使用默认模板">
              <Input
                id="term-template"
                value={form.Template}
                onChange={(event) =>
                  setEditing({ ...form, Template: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="简介" htmlFor="term-intro">
              <Textarea
                id="term-intro"
                rows={4}
                value={form.Intro}
                onChange={(event) =>
                  setEditing({ ...form, Intro: event.target.value })
                }
              />
            </FormRow>
          </div>

          <DialogFooter>
            <Button variant="outline" onClick={() => setEditing(null)}>
              取消
            </Button>
            <Button
              disabled={!form.Name.trim() || save.isPending}
              onClick={() => save.mutate(form)}
            >
              {save.isPending ? "保存中…" : "保存"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <ConfirmDialog
        open={deleting !== null}
        onOpenChange={(open) => {
          if (!open) {
            setDeleting(null)
          }
        }}
        title={`删除「${deleting?.Name ?? ""}」？`}
        description={
          isCategory
            ? "子分类会被上移，分类下的文章不会被删除。"
            : "标签与文章的关联会一并移除。"
        }
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

export function CategoriesPage() {
  return (
    <TaxonomyPage
      mod="category"
      title={t("category_manage")}
      countKey="AllCount"
    />
  )
}

export function TagsPage() {
  return <TaxonomyPage mod="tag" title={t("tag_manage")} countKey="Count" />
}
