import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { LayoutPanelLeft, Pencil, Plus, Trash2 } from "lucide-react"
import { useMemo, useState } from "react"
import { toast } from "sonner"

import { apiForm, apiGet, apiPost } from "@/lib/api"
import { toRows } from "@/lib/collection"
import { t } from "@/lib/i18n"
import { hasRight } from "@/lib/zbp"
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
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"

interface ModuleRow {
  ID: number
  Name: string
  FileName: string
  HtmlID: string
  Type: string
  Content: string
  MaxLi: number
  Source: string
  IsHideTitle: number
}

interface ModuleListData {
  // 服务端以 ID 为键返回，可能是对象或数组
  list: ModuleRow[] | Record<string, ModuleRow>
}

interface ModuleForm {
  ID: number
  Name: string
  FileName: string
  HtmlID: string
  Type: string
  Content: string
  MaxLi: string
  IsHideTitle: string
}

const emptyForm: ModuleForm = {
  ID: 0,
  Name: "",
  FileName: "",
  HtmlID: "",
  Type: "div",
  Content: "",
  MaxLi: "10",
  IsHideTitle: "0",
}

export function ModulesPage() {
  const queryClient = useQueryClient()
  const [editing, setEditing] = useState<ModuleForm | null>(null)
  const [deleting, setDeleting] = useState<ModuleRow | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ["module", "list"],
    queryFn: () => apiGet<ModuleListData>("module", "list", { perpage: 200 }),
  })

  const rows = useMemo(() => toRows<ModuleRow>(data?.list), [data?.list])

  const save = useMutation({
    mutationFn: (form: ModuleForm) =>
      apiPost<Record<string, unknown>>("module", "post", {
        ID: form.ID,
        Name: form.Name.trim(),
        FileName: form.FileName.trim() || `mod${Date.now().toString(36)}`,
        HtmlID: form.HtmlID.trim(),
        Type: form.Type,
        Content: form.Content,
        MaxLi: form.MaxLi,
        IsHideTitle: form.IsHideTitle,
        Source: "user",
      }),
    onSuccess: (_result, variables) => {
      toast.success(variables.ID > 0 ? "已保存" : "已创建")
      setEditing(null)
      queryClient.invalidateQueries({ queryKey: ["module", "list"] })
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const remove = useMutation({
    mutationFn: (id: number) =>
      apiForm<Record<string, unknown>>("module", "delete", { id }),
    onSuccess: () => {
      toast.success("已删除")
      setDeleting(null)
      queryClient.invalidateQueries({ queryKey: ["module", "list"] })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setDeleting(null)
    },
  })

  const columns = useMemo<ColumnDef<ModuleRow, unknown>[]>(
    () => [
      {
        id: "Name",
        header: "名称",
        cell: ({ row }) => (
          <div className="flex items-center gap-2">
            <LayoutPanelLeft
              className="size-4 shrink-0 text-muted-foreground"
              strokeWidth={1.75}
            />
            <span className="font-medium">{row.original.Name}</span>
          </div>
        ),
      },
      {
        id: "FileName",
        header: "文件名",
        cell: ({ row }) => (
          <code className="text-xs text-muted-foreground">
            {row.original.FileName}
          </code>
        ),
      },
      {
        id: "Type",
        header: "类型",
        cell: ({ row }) => (
          <span className="text-muted-foreground">{row.original.Type}</span>
        ),
      },
      {
        id: "source",
        header: "来源",
        cell: ({ row }) =>
          (row.original.Source ?? "user") === "system" ? (
            <Badge variant="muted">系统</Badge>
          ) : (
            <Badge variant="secondary">自定义</Badge>
          ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => {
          const isSystem = (row.original.Source ?? "user") === "system"
          return (
            <div className="flex justify-end gap-1">
              <Button
                variant="ghost"
                size="icon-sm"
                aria-label="编辑"
                disabled={!hasRight("ModuleEdt")}
                onClick={() =>
                  setEditing({
                    ID: row.original.ID,
                    Name: row.original.Name,
                    FileName: row.original.FileName,
                    HtmlID: row.original.HtmlID ?? "",
                    Type: row.original.Type || "div",
                    Content: row.original.Content ?? "",
                    MaxLi: String(row.original.MaxLi ?? 10),
                    IsHideTitle: String(row.original.IsHideTitle ?? 0),
                  })
                }
              >
                <Pencil className="size-4" />
              </Button>
              <Button
                variant="ghost"
                size="icon-sm"
                aria-label="删除"
                disabled={!hasRight("ModuleDel") || isSystem}
                onClick={() => setDeleting(row.original)}
              >
                <Trash2 className="size-4" />
              </Button>
            </div>
          )
        },
      },
    ],
    []
  )

  const form = editing ?? emptyForm
  const isEdit = form.ID > 0

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={t("module_manage")}
        actions={
          <Button
            size="sm"
            disabled={!hasRight("ModuleEdt")}
            onClick={() => setEditing({ ...emptyForm })}
          >
            <Plus className="size-4" /> 新建
          </Button>
        }
      />

      <DataTable
        columns={columns}
        data={rows}
        isLoading={isLoading}
      />

      <Dialog
        open={editing !== null}
        onOpenChange={(open) => {
          if (!open) {
            setEditing(null)
          }
        }}
      >
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>{isEdit ? "编辑模块" : "新建模块"}</DialogTitle>
          </DialogHeader>

          <div className="flex flex-col gap-3">
            <FormRow label="名称" htmlFor="module-name" required>
              <Input
                id="module-name"
                value={form.Name}
                onChange={(event) =>
                  setEditing({ ...form, Name: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="文件名" htmlFor="module-filename" hint="留空自动生成">
              <Input
                id="module-filename"
                value={form.FileName}
                onChange={(event) =>
                  setEditing({ ...form, FileName: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="HTML ID" htmlFor="module-htmlid">
              <Input
                id="module-htmlid"
                value={form.HtmlID}
                onChange={(event) =>
                  setEditing({ ...form, HtmlID: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="输出类型" htmlFor="module-type">
              <select
                id="module-type"
                className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                value={form.Type}
                onChange={(event) =>
                  setEditing({ ...form, Type: event.target.value })
                }
              >
                <option value="div">div</option>
                <option value="ul">ul（列表）</option>
              </select>
            </FormRow>

            {form.Type === "ul" ? (
              <FormRow label="列表项上限" htmlFor="module-maxli">
                <Input
                  id="module-maxli"
                  type="number"
                  value={form.MaxLi}
                  onChange={(event) =>
                    setEditing({ ...form, MaxLi: event.target.value })
                  }
                />
              </FormRow>
            ) : null}

            <FormRow label="内容" htmlFor="module-content" hint="支持 HTML">
              <Textarea
                id="module-content"
                rows={10}
                className="min-h-40 font-mono text-xs"
                value={form.Content}
                onChange={(event) =>
                  setEditing({ ...form, Content: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="隐藏标题" htmlFor="module-hidetitle">
              <select
                id="module-hidetitle"
                className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                value={form.IsHideTitle}
                onChange={(event) =>
                  setEditing({ ...form, IsHideTitle: event.target.value })
                }
              >
                <option value="0">显示</option>
                <option value="1">隐藏</option>
              </select>
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
        title={`删除模块「${deleting?.Name ?? ""}」？`}
        description="已挂载到侧栏的模块会一并移除。"
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
