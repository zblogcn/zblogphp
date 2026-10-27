import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query"
import { ArrowLeft, Send, Trash2 } from "lucide-react"
import { useEffect, useRef, useState } from "react"
import { useNavigate, useParams } from "react-router-dom"
import { toast } from "sonner"

import { apiGet, apiPost } from "@/lib/api"
import { t } from "@/lib/i18n"
import { useOptions } from "@/hooks/use-options"
import { ConfirmDialog } from "@/components/confirm-dialog"
import { FormRow } from "@/components/form-row"
import { PageHeader } from "@/components/page-header"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Skeleton } from "@/components/ui/skeleton"
import { Textarea } from "@/components/ui/textarea"
import { usePluginEditor } from "@/plugin-host/editor"

interface PostDetail {
  ID: number
  Title: string
  Alias: string
  CateID: number
  Status: number
  IsTop: number
  IsLock: number
  Template: string
  Intro: string
  Content: string
  PostTime: number
  Tag?: { ID?: number; Name?: string }[]
  TagName?: string
  TagsName?: string
}

interface PostForm {
  ID: number
  Type: 0 | 1
  Title: string
  Alias: string
  CateID: string
  Status: string
  IsTop: string
  IsLock: string
  Template: string
  Intro: string
  Content: string
  Tag: string
}

const emptyForm = (type: 0 | 1): PostForm => ({
  ID: 0,
  Type: type,
  Title: "",
  Alias: "",
  CateID: "0",
  Status: "0",
  IsTop: "0",
  IsLock: "0",
  Template: "",
  Intro: "",
  Content: "",
  Tag: "",
})

/** 时间戳转 datetime-local 需要的本地时间字符串 */
function toLocalInput(timestamp?: number): string {
  const date = timestamp ? new Date(timestamp * 1000) : new Date()
  const pad = (n: number) => String(n).padStart(2, "0")
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

interface PostEditorPageProps {
  type: 0 | 1
}

export function PostEditorPage({ type }: PostEditorPageProps) {
  const params = useParams()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const isPage = type === 1
  const postId = Number(params.id ?? 0)
  const isEdit = postId > 0
  const backTo = `/${isPage ? "pages" : "posts"}`

  const [form, setForm] = useState<PostForm>(() => emptyForm(type))
  const [postTime, setPostTime] = useState(() => toLocalInput())
  const [tagInput, setTagInput] = useState("")
  const editorRef = useRef<HTMLDivElement>(null)
  const [editorReady, setEditorReady] = useState(false)
  const [deleteOpen, setDeleteOpen] = useState(false)

  const categories = useOptions(
    "category",
    "list",
    (row) => String(row.ID),
    (row) => String(row.SymbolName ?? row.Name)
  )

  const { data: detail, isLoading } = useQuery({
    queryKey: ["post", "detail", postId],
    queryFn: () =>
      apiGet<{ post: PostDetail }>("post", "get", {
        id: postId,
        with_relations: "Tags",
      }),
    enabled: isEdit,
  })

  const editor = usePluginEditor(editorRef, {
    get: apiGet,
    post: apiPost,
    upload: async () => {
      throw new Error("请通过附件管理上传")
    },
  })

  /** 详情只回填一次，之后用户编辑不应被覆盖。 */
  const filledDetail = useRef(false)

  // 详情加载后写入编辑器：编辑器可能晚于详情就绪，两者任一到达都尝试一次
  const fillEditor = useRef<(html: string) => void>(() => {})
  fillEditor.current = (html: string) => {
    if (editor.status === "ready" && !editorReady) {
      editor.setHTML(html)
      setEditorReady(true)
    }
  }

  // 编辑器就绪后把内容写进去（仅首次，之后由详情回填负责）
  useEffect(() => {
    if (editor.status === "ready") {
      fillEditor.current(form.Content)
    }
  }, [editor.status, form.Content])

  useEffect(() => {
    if (!detail?.post || filledDetail.current) {
      return
    }
    filledDetail.current = true
    const post = detail.post
    const tagNames =
      post.TagsName ??
      (Array.isArray(post.Tag)
        ? post.Tag.map((item) => item.Name).filter(Boolean).join(",")
        : post.TagName ?? "")

    setForm({
      ID: post.ID,
      Type: type,
      Title: post.Title ?? "",
      Alias: post.Alias ?? "",
      CateID: String(post.CateID ?? 0),
      Status: String(post.Status ?? 0),
      IsTop: String(post.IsTop ?? 0),
      IsLock: String(post.IsLock ?? 0),
      Template: post.Template ?? "",
      Intro: post.Intro ?? "",
      Content: post.Content ?? "",
      Tag: tagNames,
    })
    setPostTime(toLocalInput(post.PostTime))
    fillEditor.current(post.Content ?? "")
  }, [detail])

  const remove = useMutation({
    mutationFn: (id: number) =>
      apiPost<{ deleted: number }>("post", "batch", { type, id: [id] }),
    onSuccess: () => {
      toast.success("已删除")
      setDeleteOpen(false)
      queryClient.invalidateQueries({ queryKey: ["post", "list"] })
      navigate(backTo, { replace: true })
    },
    onError: (error: Error) => {
      toast.error(error.message)
      setDeleteOpen(false)
    },
  })

  const save = useMutation({
    mutationFn: (payload: PostForm) =>
      apiPost<{ post: PostDetail }>("post", "post", {
        ID: payload.ID,
        Type: payload.Type,
        Title: payload.Title.trim(),
        Alias: payload.Alias.trim(),
        CateID: Number(payload.CateID) || 0,
        Status: Number(payload.Status),
        IsTop: payload.IsTop,
        IsLock: payload.IsLock,
        Template: payload.Template,
        Intro: payload.Intro,
        Content: payload.Content,
        Tag: payload.Tag,
        PostTime: postTime.replace("T", " "),
      }),
    onSuccess: (result) => {
      toast.success(isEdit ? "已保存" : "已发布")
      queryClient.invalidateQueries({ queryKey: ["post", "list"] })
      // 新建成功后转到编辑态，便于继续修改
      if (!isEdit && result?.post?.ID) {
        navigate(`/${isPage ? "pages" : "posts"}/${result.post.ID}/edit`, {
          replace: true,
        })
      }
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const submit = (status?: string) => {
    const content = editor.status === "ready" ? editor.getHTML() : form.Content
    if (!form.Title.trim()) {
      toast.error("请填写标题")
      return
    }
    save.mutate({
      ...form,
      Content: content,
      Status: status ?? form.Status,
      Tag: form.Tag || tagInput,
    })
  }

  // 编辑器里插入的标签文本与表单标签合并
  const appendTag = () => {
    const value = tagInput.trim()
    if (!value) {
      return
    }
    const current = form.Tag ? form.Tag.split(",").map((s) => s.trim()) : []
    if (!current.includes(value)) {
      current.push(value)
    }
    setForm({ ...form, Tag: current.join(",") })
    setTagInput("")
  }

  const title = isPage ? t("page_manage") : t("article_manage")

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={
          isEdit
            ? `编辑${isPage ? "页面" : "文章"}`
            : `新建${isPage ? "页面" : "文章"}`
        }
        description={title}
        actions={
          <>
            <Button
              variant="outline"
              size="sm"
              onClick={() => navigate(backTo)}
            >
              <ArrowLeft className="size-4" /> 返回列表
            </Button>
            <Button
              variant="ghost"
              size="sm"
              className="text-destructive hover:text-destructive"
              disabled={!isEdit || remove.isPending}
              onClick={() => setDeleteOpen(true)}
            >
              <Trash2 className="size-4" /> 删除
            </Button>
            <Button
              variant="outline"
              size="sm"
              disabled={save.isPending}
              onClick={() => submit("1")}
            >
              存为草稿
            </Button>
            <Button
              size="sm"
              disabled={save.isPending}
              onClick={() => submit("0")}
            >
              {save.isPending ? (
                "保存中…"
              ) : (
                <>
                  <Send className="size-4" /> 发布
                </>
              )}
            </Button>
          </>
        }
      />

      {isLoading ? (
        <Skeleton className="h-[520px] w-full" />
      ) : (
        <div className="grid gap-4 lg:grid-cols-[minmax(0,1fr)_300px]">
          <Card className="py-0">
            <CardContent className="flex flex-col gap-3 px-4 py-4">
              <Input
                value={form.Title}
                onChange={(event) =>
                  setForm({ ...form, Title: event.target.value })
                }
                placeholder="标题"
                className="h-11 border-0 border-b px-0 text-lg font-semibold shadow-none focus-visible:ring-0"
                aria-label="标题"
              />

              <div ref={editorRef} />

              {editor.status === "unavailable" || editor.status === "error" ? (
                <div className="flex flex-col gap-1">
                  <Textarea
                    value={form.Content}
                    onChange={(event) =>
                      setForm({ ...form, Content: event.target.value })
                    }
                    rows={16}
                    className="font-mono text-xs"
                    placeholder="正文（HTML）"
                    aria-label="正文"
                  />
                  {editor.status === "error" ? (
                    <span className="text-xs text-destructive">
                      编辑器加载失败：{editor.message}
                    </span>
                  ) : (
                    <span className="text-xs text-muted-foreground">
                      未安装区块编辑器，已回退到纯文本编辑。
                    </span>
                  )}
                </div>
              ) : null}
            </CardContent>
          </Card>

          <div className="flex flex-col gap-4">
            <Card>
              <CardContent className="flex flex-col gap-3 px-4 py-4">
                <FormRow label="分类" htmlFor="post-cate">
                  <select
                    id="post-cate"
                    className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                    value={form.CateID}
                    onChange={(event) =>
                      setForm({ ...form, CateID: event.target.value })
                    }
                  >
                    <option value="0">未分类</option>
                    {categories.options.map((option) => (
                      <option key={option.value} value={option.value}>
                        {option.label}
                      </option>
                    ))}
                  </select>
                </FormRow>

                <FormRow label="标签" htmlFor="post-tag">
                  <div className="flex gap-2">
                    <Input
                      id="post-tag"
                      value={tagInput}
                      onChange={(event) => setTagInput(event.target.value)}
                      onKeyDown={(event) => {
                        if (event.key === "Enter") {
                          event.preventDefault()
                          appendTag()
                        }
                      }}
                      placeholder="输入后回车"
                    />
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={appendTag}
                    >
                      添加
                    </Button>
                  </div>
                  {form.Tag ? (
                    <div className="mt-1 flex flex-wrap gap-1">
                      {form.Tag.split(",")
                        .filter(Boolean)
                        .map((tag) => (
                          <span
                            key={tag}
                            className="inline-flex items-center gap-1 rounded bg-muted px-2 py-0.5 text-xs"
                          >
                            {tag.trim()}
                            <button
                              type="button"
                              aria-label={`移除 ${tag.trim()}`}
                              onClick={() =>
                                setForm({
                                  ...form,
                                  Tag: form.Tag.split(",")
                                    .filter((item) => item !== tag)
                                    .join(","),
                                })
                              }
                              className="text-muted-foreground hover:text-foreground"
                            >
                              ×
                            </button>
                          </span>
                        ))}
                    </div>
                  ) : null}
                </FormRow>

                <FormRow label="状态" htmlFor="post-status">
                  <select
                    id="post-status"
                    className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                    value={form.Status}
                    onChange={(event) =>
                      setForm({ ...form, Status: event.target.value })
                    }
                  >
                    <option value="0">公开</option>
                    <option value="1">草稿</option>
                    <option value="2">待审核</option>
                  </select>
                </FormRow>

                <FormRow label="发布时间" htmlFor="post-time">
                  <Input
                    id="post-time"
                    type="datetime-local"
                    value={postTime}
                    onChange={(event) => setPostTime(event.target.value)}
                  />
                </FormRow>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="flex flex-col gap-3 px-4 py-4">
                <FormRow label="别名" htmlFor="post-alias">
                  <Input
                    id="post-alias"
                    value={form.Alias}
                    onChange={(event) =>
                      setForm({ ...form, Alias: event.target.value })
                    }
                    placeholder="留空自动生成"
                  />
                </FormRow>

                <FormRow label="模板" htmlFor="post-template">
                  <Input
                    id="post-template"
                    value={form.Template}
                    onChange={(event) =>
                      setForm({ ...form, Template: event.target.value })
                    }
                    placeholder="留空使用默认"
                  />
                </FormRow>

                <FormRow label="选项">
                  <div className="flex flex-col gap-2 text-sm">
                    <label className="flex items-center gap-2">
                      <input
                        type="checkbox"
                        checked={form.IsTop === "1"}
                        onChange={(event) =>
                          setForm({
                            ...form,
                            IsTop: event.target.checked ? "1" : "0",
                          })
                        }
                      />
                      置顶
                    </label>
                    <label className="flex items-center gap-2">
                      <input
                        type="checkbox"
                        checked={form.IsLock === "1"}
                        onChange={(event) =>
                          setForm({
                            ...form,
                            IsLock: event.target.checked ? "1" : "0",
                          })
                        }
                      />
                      禁止评论
                    </label>
                  </div>
                </FormRow>

                <FormRow
                  label="摘要"
                  htmlFor="post-intro"
                  hint="留空由服务端自动生成"
                >
                  <Textarea
                    id="post-intro"
                    rows={5}
                    value={form.Intro}
                    onChange={(event) =>
                      setForm({ ...form, Intro: event.target.value })
                    }
                  />
                </FormRow>
              </CardContent>
            </Card>
          </div>
        </div>
      )}

      <ConfirmDialog
        open={deleteOpen}
        onOpenChange={setDeleteOpen}
        title={`删除「${form.Title}」？`}
        description="删除后无法恢复，相关评论会一并删除。"
        confirmText="删除"
        destructive
        onConfirm={() => remove.mutate(postId)}
      />
    </div>
  )
}

export function PostEditPage() {
  return <PostEditorPage type={0} />
}

export function PageEditPage() {
  return <PostEditorPage type={1} />
}
