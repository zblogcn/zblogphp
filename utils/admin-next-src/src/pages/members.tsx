import {
  keepPreviousData,
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query"
import type { ColumnDef } from "@tanstack/react-table"
import { Pencil, Plus, Trash2 } from "lucide-react"
import { useMemo, useState } from "react"
import { toast } from "sonner"

import { apiForm, apiGet, apiPost, type Pagebar } from "@/lib/api"
import { t } from "@/lib/i18n"
import { hasRight, zbp } from "@/lib/zbp"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { DataTable } from "@/components/data-table"
import { FormRow } from "@/components/form-row"
import { PageHeader } from "@/components/page-header"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
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

interface MemberRow {
  ID: number
  Name: string
  StaticName: string
  Alias: string
  Email: string
  HomePage: string
  Intro: string
  Level: number
  Status: number
  PostTime: number
  Avatar?: string
}

interface MemberListData {
  list: MemberRow[]
  pagebar: Pagebar
}

interface MemberForm {
  ID: number
  Name: string
  Alias: string
  Email: string
  HomePage: string
  Intro: string
  Level: string
  Status: string
  Password: string
  PasswordRe: string
}

const emptyForm: MemberForm = {
  ID: 0,
  Name: "",
  Alias: "",
  Email: "",
  HomePage: "",
  Intro: "",
  Level: "5",
  Status: "0",
  Password: "",
  PasswordRe: "",
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
  const queryClient = useQueryClient()
  const [page, setPage] = useState(1)
  const [editing, setEditing] = useState<MemberForm | null>(null)
  const [deleting, setDeleting] = useState<MemberRow | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ["member", "list", page],
    queryFn: () =>
      apiGet<MemberListData>("member", "list", {
        page,
        perpage: zbp.options.manageCount,
      }),
    placeholderData: keepPreviousData,
  })

  const save = useMutation({
    mutationFn: (form: MemberForm) =>
      apiPost<Record<string, unknown>>("member", "post", {
        ID: form.ID,
        Name: form.Name.trim(),
        Alias: form.Alias.trim(),
        Email: form.Email.trim(),
        HomePage: form.HomePage.trim(),
        Intro: form.Intro,
        Level: form.Level,
        Status: form.Status,
        Password: form.Password,
        PasswordRe: form.PasswordRe,
      }),
    onSuccess: (_result, variables) => {
      toast.success(variables.ID > 0 ? "已保存" : "已创建")
      setEditing(null)
      queryClient.invalidateQueries({ queryKey: ["member", "list"] })
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const remove = useMutation({
    mutationFn: (id: number) =>
      apiForm<Record<string, unknown>>("member", "delete", { id }),
    onSuccess: () => {
      toast.success("已删除")
      setDeleting(null)
      queryClient.invalidateQueries({ queryKey: ["member", "list"] })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setDeleting(null)
    },
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
                <AvatarFallback>
                  {name.slice(0, 1).toUpperCase()}
                </AvatarFallback>
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
        id: "Email",
        header: "邮箱",
        cell: ({ row }) => (
          <span className="text-muted-foreground">
            {row.original.Email || "—"}
          </span>
        ),
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
      {
        id: "actions",
        header: "",
        cell: ({ row }) => {
          const isSelf = row.original.ID === zbp.user.id
          return (
            <div className="flex justify-end gap-1">
              <Button
                variant="ghost"
                size="icon-sm"
                aria-label="编辑"
                disabled={!hasRight(isSelf ? "MemberEdt" : "MemberAll")}
                onClick={() =>
                  setEditing({
                    ID: row.original.ID,
                    Name: row.original.Name,
                    Alias: row.original.Alias ?? "",
                    Email: row.original.Email ?? "",
                    HomePage: row.original.HomePage ?? "",
                    Intro: row.original.Intro ?? "",
                    Level: String(row.original.Level),
                    Status: String(row.original.Status),
                    Password: "",
                    PasswordRe: "",
                  })
                }
              >
                <Pencil className="size-4" />
              </Button>
              <Button
                variant="ghost"
                size="icon-sm"
                aria-label="删除"
                disabled={!hasRight("MemberDel") || isSelf}
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
        title={t("member_manage")}
        actions={
          <Button
            size="sm"
            disabled={!hasRight("MemberNew")}
            onClick={() => setEditing({ ...emptyForm })}
          >
            <Plus className="size-4" /> 新建
          </Button>
        }
      />

      <DataTable
        columns={columns}
        data={data?.list ?? []}
        pagebar={data?.pagebar}
        onPageChange={setPage}
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
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{isEdit ? "编辑用户" : "新建用户"}</DialogTitle>
          </DialogHeader>

          <div className="flex flex-col gap-3">
            <FormRow label="用户名" htmlFor="member-name" required>
              <Input
                id="member-name"
                value={form.Name}
                onChange={(event) =>
                  setEditing({ ...form, Name: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="显示名称" htmlFor="member-alias">
              <Input
                id="member-alias"
                value={form.Alias}
                onChange={(event) =>
                  setEditing({ ...form, Alias: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="邮箱" htmlFor="member-email">
              <Input
                id="member-email"
                type="email"
                value={form.Email}
                onChange={(event) =>
                  setEditing({ ...form, Email: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="个人主页" htmlFor="member-homepage">
              <Input
                id="member-homepage"
                value={form.HomePage}
                onChange={(event) =>
                  setEditing({ ...form, HomePage: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="密码" htmlFor="member-password" required={!isEdit}>
              <Input
                id="member-password"
                type="password"
                autoComplete="new-password"
                value={form.Password}
                onChange={(event) =>
                  setEditing({ ...form, Password: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="确认密码" htmlFor="member-password-re">
              <Input
                id="member-password-re"
                type="password"
                autoComplete="new-password"
                value={form.PasswordRe}
                onChange={(event) =>
                  setEditing({ ...form, PasswordRe: event.target.value })
                }
              />
            </FormRow>

            <FormRow label="等级" htmlFor="member-level">
              <select
                id="member-level"
                className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                value={form.Level}
                disabled={!hasRight("MemberAll")}
                onChange={(event) =>
                  setEditing({ ...form, Level: event.target.value })
                }
              >
                {Object.entries(levelNames)
                  .filter(([value]) => Number(value) <= zbp.user.level)
                  .map(([value, label]) => (
                    <option key={value} value={value}>
                      {label}
                    </option>
                  ))}
              </select>
            </FormRow>

            <FormRow label="状态" htmlFor="member-status">
              <select
                id="member-status"
                className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                value={form.Status}
                disabled={!hasRight("MemberAll")}
                onChange={(event) =>
                  setEditing({ ...form, Status: event.target.value })
                }
              >
                <option value="0">正常</option>
                <option value="1">待审核</option>
                <option value="2">禁用</option>
              </select>
            </FormRow>

            <FormRow label="简介" htmlFor="member-intro">
              <Textarea
                id="member-intro"
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
              disabled={
                !form.Name.trim() ||
                (!isEdit &&
                  (!form.Password || form.Password !== form.PasswordRe)) ||
                save.isPending
              }
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
        title={`删除用户「${deleting?.StaticName || deleting?.Name || ""}」？`}
        description="该用户的文章会保留，但作者信息将失效。"
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
