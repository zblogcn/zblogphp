import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query"
import { Save, Undo2 } from "lucide-react"
import { useEffect, useMemo, useState } from "react"
import { toast } from "sonner"

import { apiGet, apiPost } from "@/lib/api"
import { t } from "@/lib/i18n"
import { PageHeader } from "@/components/page-header"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Skeleton } from "@/components/ui/skeleton"
import { Switch } from "@/components/ui/switch"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"

type FieldType = "text" | "number" | "switch" | "textarea" | "readonly"

interface Field {
  key: string
  label: string
  type: FieldType
  hint?: string
}

interface SettingsData {
  list: Record<string, string | number | boolean>
}

const groups: Array<{ id: string; label: string; fields: Field[] }> = [
  {
    id: "basic",
    label: "基础",
    fields: [
      { key: "ZC_BLOG_NAME", label: "站点名称", type: "text" },
      { key: "ZC_BLOG_SUBNAME", label: "副标题", type: "text" },
      { key: "ZC_BLOG_COPYRIGHT", label: "版权信息", type: "textarea" },
    ],
  },
  {
    id: "global",
    label: "全局",
    fields: [
      {
        key: "ZC_BLOG_HOST",
        label: "站点地址",
        type: "readonly",
        hint: "修改需要域名校验，请在旧版后台操作。",
      },
      { key: "ZC_TIME_ZONE_NAME", label: "时区", type: "text" },
      { key: "ZC_BLOG_LANGUAGEPACK", label: "语言包", type: "text" },
      { key: "ZC_DEBUG_MODE", label: "调试模式", type: "switch" },
      { key: "ZC_DEBUG_MODE_WARNING", label: "显示警告与错误", type: "switch" },
      { key: "ZC_ADDITIONAL_SECURITY", label: "附加安全", type: "switch" },
      { key: "ZC_CLOSE_SITE", label: "关闭站点", type: "switch" },
    ],
  },
  {
    id: "page",
    label: "页面",
    fields: [
      { key: "ZC_DISPLAY_COUNT", label: "列表每页数量", type: "number" },
      { key: "ZC_PAGEBAR_COUNT", label: "分页条数量", type: "number" },
      { key: "ZC_SEARCH_COUNT", label: "搜索每页数量", type: "number" },
      { key: "ZC_MANAGE_COUNT", label: "后台每页数量", type: "number" },
      { key: "ZC_DISPLAY_SUBCATEGORYS", label: "显示子分类文章", type: "switch" },
      {
        key: "ZC_SYNTAXHIGHLIGHTER_ENABLE",
        label: "代码高亮",
        type: "switch",
      },
    ],
  },
  {
    id: "comment",
    label: "评论",
    fields: [
      { key: "ZC_COMMENT_TURNOFF", label: "关闭评论", type: "switch" },
      { key: "ZC_COMMENT_AUDIT", label: "评论需审核", type: "switch" },
      { key: "ZC_COMMENT_REVERSE_ORDER", label: "评论倒序", type: "switch" },
      { key: "ZC_COMMENT_ORDERBY_TIME", label: "按时间排序", type: "switch" },
      { key: "ZC_COMMENT_VERIFY_ENABLE", label: "评论验证码", type: "switch" },
      {
        key: "ZC_COMMENTS_DISPLAY_COUNT",
        label: "评论每页数量",
        type: "number",
      },
    ],
  },
  {
    id: "api",
    label: "接口与登录",
    fields: [
      { key: "ZC_API_ENABLE", label: "开放 API", type: "switch" },
      { key: "ZC_XMLRPC_ENABLE", label: "XML-RPC", type: "switch" },
      { key: "ZC_API_DISPLAY_COUNT", label: "API 每页数量", type: "number" },
      { key: "ZC_API_THROTTLE_ENABLE", label: "API 限流", type: "switch" },
      {
        key: "ZC_API_THROTTLE_MAX_REQS_PER_MIN",
        label: "每分钟请求上限",
        type: "number",
      },
      { key: "ZC_LOGIN_VERIFY_ENABLE", label: "登录验证码", type: "switch" },
      {
        key: "ZC_LOGIN_CSRFCHECK_ENABLE",
        label: "登录 CSRF 校验",
        type: "switch",
      },
    ],
  },
]

function isOn(value: string | number | boolean | undefined): boolean {
  return value === "1" || value === "true" || value === 1 || value === true
}

export function SettingsPage() {
  const queryClient = useQueryClient()
  const [tab, setTab] = useState("basic")
  const [draft, setDraft] = useState<Record<string, string | number | boolean>>(
    {}
  )

  const { data, isLoading } = useQuery({
    queryKey: ["system", "get_setting"],
    queryFn: () => apiGet<SettingsData>("system", "get_setting"),
  })

  useEffect(() => {
    if (data?.list) {
      setDraft({ ...data.list })
    }
  }, [data])

  const dirty = useMemo(() => {
    const original = data?.list ?? {}
    return Object.keys(draft).filter(
      (key) => String(draft[key]) !== String(original[key] ?? "")
    )
  }, [draft, data])

  const save = useMutation({
    mutationFn: (payload: Record<string, string>) =>
      apiPost<{ list: Record<string, string> }>(
        "system",
        "save_setting",
        payload
      ),
    onSuccess: () => {
      toast.success("设置已保存")
      queryClient.invalidateQueries({ queryKey: ["system", "get_setting"] })
    },
    onError: (error: Error) => toast.error(error.message),
  })

  const setValue = (key: string, value: string | number | boolean) => {
    setDraft((current) => ({ ...current, [key]: value }))
  }

  const renderField = (field: Field) => {
    const raw = draft[field.key]
    const value = raw === undefined || raw === null ? "" : String(raw)

    if (field.type === "readonly") {
      return (
        <div className="flex flex-col gap-1">
          <Input id={field.key} value={value} readOnly className="bg-muted/50" />
          {field.hint ? (
            <span className="text-xs text-muted-foreground">{field.hint}</span>
          ) : null}
        </div>
      )
    }

    if (field.type === "switch") {
      return (
        <Switch
          id={field.key}
          checked={isOn(raw)}
          onCheckedChange={(checked) => setValue(field.key, checked ? "1" : "0")}
        />
      )
    }

    if (field.type === "textarea") {
      return (
        <textarea
          id={field.key}
          value={value}
          onChange={(event) => setValue(field.key, event.target.value)}
          rows={4}
          className="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring"
        />
      )
    }

    return (
      <Input
        id={field.key}
        type={field.type === "number" ? "number" : "text"}
        value={value}
        onChange={(event) => setValue(field.key, event.target.value)}
        inputMode={field.type === "number" ? "numeric" : undefined}
      />
    )
  }

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={t("web_settings")}
        actions={
          <>
            <Button
              variant="outline"
              size="sm"
              disabled={dirty.length === 0 || save.isPending}
              onClick={() => setDraft({ ...(data?.list ?? {}) })}
            >
              <Undo2 className="size-4" /> 撤销
            </Button>
            <Button
              size="sm"
              disabled={dirty.length === 0 || save.isPending}
              onClick={() => {
                const payload: Record<string, string> = {}
                for (const key of dirty) {
                  payload[key] = String(draft[key] ?? "")
                }
                save.mutate(payload)
              }}
            >
              <Save className="size-4" />
              {save.isPending ? "保存中…" : `保存${dirty.length ? ` (${dirty.length})` : ""}`}
            </Button>
          </>
        }
      />

      <Tabs value={tab} onValueChange={setTab}>
        <TabsList className="w-full justify-start overflow-x-auto sm:w-fit">
          {groups.map((group) => (
            <TabsTrigger key={group.id} value={group.id}>
              {group.label}
            </TabsTrigger>
          ))}
        </TabsList>

        {groups.map((group) => (
          <TabsContent key={group.id} value={group.id}>
            <Card className="py-0">
              <CardContent className="px-0 py-0">
                <div>
                  {group.fields.map((field) => (
                    <div
                      key={field.key}
                      className="grid gap-1.5 border-b px-4 py-3 last:border-0 sm:grid-cols-[minmax(0,220px)_minmax(0,1fr)] sm:items-center sm:gap-4"
                    >
                      <Label
                        htmlFor={field.key}
                        className="text-sm font-normal text-muted-foreground"
                      >
                        {field.label}
                      </Label>
                      <div className="min-w-0">
                        {isLoading ? (
                          <Skeleton className="h-9 w-full max-w-md" />
                        ) : (
                          renderField(field)
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        ))}
      </Tabs>
    </div>
  )
}
