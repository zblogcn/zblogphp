import { useQuery } from "@tanstack/react-query"
import { useState } from "react"

import { apiGet } from "@/lib/api"
import { t } from "@/lib/i18n"
import { PageHeader } from "@/components/page-header"
import { Badge } from "@/components/ui/badge"
import { Card, CardContent } from "@/components/ui/card"
import { Skeleton } from "@/components/ui/skeleton"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"

interface SettingsData {
  list: Record<string, string | number | boolean>
}

const groups: Array<{
  id: string
  label: string
  keys: Array<[string, string]>
}> = [
  {
    id: "basic",
    label: "基础",
    keys: [
      ["ZC_BLOG_NAME", "站点名称"],
      ["ZC_BLOG_SUBNAME", "副标题"],
      ["ZC_BLOG_COPYRIGHT", "版权信息"],
    ],
  },
  {
    id: "global",
    label: "全局",
    keys: [
      ["ZC_TIME_ZONE_NAME", "时区"],
      ["ZC_BLOG_LANGUAGEPACK", "语言包"],
      ["ZC_DEBUG_MODE", "调试模式"],
      ["ZC_ADDITIONAL_SECURITY", "附加安全"],
    ],
  },
  {
    id: "page",
    label: "页面",
    keys: [
      ["ZC_DISPLAY_COUNT", "列表每页数量"],
      ["ZC_PAGEBAR_COUNT", "分页条数量"],
      ["ZC_SEARCH_COUNT", "搜索每页数量"],
      ["ZC_MANAGE_COUNT", "后台每页数量"],
    ],
  },
  {
    id: "comment",
    label: "评论",
    keys: [
      ["ZC_COMMENT_TURNOFF", "关闭评论"],
      ["ZC_COMMENT_AUDIT", "评论审核"],
      ["ZC_COMMENT_VERIFY_ENABLE", "评论验证码"],
      ["ZC_COMMENTS_DISPLAY_COUNT", "评论每页数量"],
    ],
  },
  {
    id: "api",
    label: "接口",
    keys: [
      ["ZC_API_ENABLE", "开放 API"],
      ["ZC_XMLRPC_ENABLE", "XML-RPC"],
    ],
  },
]

function display(value: string | number | boolean | undefined): string {
  if (value === undefined || value === null || value === "") {
    return "—"
  }
  if (typeof value === "boolean") {
    return value ? "是" : "否"
  }
  if (value === "1" || value === 1) {
    return "是"
  }
  if (value === "0" || value === 0) {
    return "否"
  }
  return String(value)
}

function Value({ value }: { value: string | number | boolean | undefined }) {
  const text = display(value)

  if (text === "是") {
    return <Badge variant="success">是</Badge>
  }
  if (text === "否") {
    return <Badge variant="muted">否</Badge>
  }

  return <span className="text-sm">{text}</span>
}

export function SettingsPage() {
  const [tab, setTab] = useState("basic")

  const { data, isLoading } = useQuery({
    queryKey: ["system", "get_setting"],
    queryFn: () => apiGet<SettingsData>("system", "get_setting"),
  })

  return (
    <div className="flex flex-col gap-4">
      <PageHeader
        title={t("web_settings")}
        description="当前为只读视图，保存功能将在下一阶段接入。"
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
                <dl>
                  {group.keys.map(([key, label]) => (
                    <div
                      key={key}
                      className="grid gap-1 border-b px-4 py-3 last:border-0 sm:grid-cols-[minmax(0,220px)_minmax(0,1fr)] sm:items-center sm:gap-4"
                    >
                      <dt className="text-sm text-muted-foreground">
                        {label}
                        <code className="ml-2 hidden text-[10px] sm:inline">
                          {key}
                        </code>
                      </dt>
                      <dd>
                        {isLoading ? (
                          <Skeleton className="h-5 w-40" />
                        ) : (
                          <Value value={data?.list?.[key]} />
                        )}
                      </dd>
                    </div>
                  ))}
                </dl>
              </CardContent>
            </Card>
          </TabsContent>
        ))}
      </Tabs>
    </div>
  )
}
