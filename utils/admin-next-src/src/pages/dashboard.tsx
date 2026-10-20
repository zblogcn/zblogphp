import { useQuery } from "@tanstack/react-query"
import {
  Eye,
  FileText,
  Folder,
  MessageSquare,
  RefreshCw,
  Users,
  Files,
} from "lucide-react"
import { Link } from "react-router-dom"

import { apiGet } from "@/lib/api"
import { t } from "@/lib/i18n"
import { zbp } from "@/lib/zbp"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { PageHeader } from "@/components/page-header"
import { Skeleton } from "@/components/ui/skeleton"

interface SystemInfo {
  environment: string
  version: string
  full_version: string
  articles: number
  categories: number
  pages: number
  comments: number
  views: number
  members: number
  theme: string
  style: string
  https: boolean
  debugmode: boolean
}

const stats = [
  { key: "articles", label: "文章", icon: Files },
  { key: "pages", label: "页面", icon: FileText },
  { key: "comments", label: "评论", icon: MessageSquare },
  { key: "categories", label: "分类", icon: Folder },
  { key: "members", label: "用户", icon: Users },
  { key: "views", label: "浏览", icon: Eye },
] as const

export function DashboardPage() {
  const { data, isLoading, isError, refetch, isFetching } = useQuery({
    queryKey: ["system", "get_info"],
    queryFn: () => apiGet<{ info: SystemInfo }>("system", "get_info"),
  })

  const info = data?.info

  return (
    <div className="flex flex-col gap-5">
      <PageHeader
        title={t("dashboard")}
        description={zbp.options.blogName}
        actions={
          <Button
            variant="outline"
            size="sm"
            onClick={() => refetch()}
            disabled={isFetching}
          >
            <RefreshCw className={isFetching ? "size-4 animate-spin" : "size-4"} />
            {t("refresh")}
          </Button>
        }
      />

      {isError ? (
        <Card>
          <CardContent className="py-6 text-sm text-destructive">
            {t("error_title")}
          </CardContent>
        </Card>
      ) : null}

      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
        {stats.map((stat) => {
          const Icon = stat.icon
          return (
            <Card key={stat.key} className="gap-0 py-0">
              <CardContent className="flex items-center gap-3 px-4 py-3">
                <div className="flex size-9 shrink-0 items-center justify-center rounded-md bg-muted">
                  <Icon className="size-4 text-muted-foreground" strokeWidth={1.75} />
                </div>
                <div className="min-w-0">
                  <div className="text-xs text-muted-foreground">{stat.label}</div>
                  {isLoading ? (
                    <Skeleton className="mt-1 h-5 w-12" />
                  ) : (
                    <div className="text-lg font-semibold tabular-nums">
                      {(info?.[stat.key] ?? 0).toLocaleString()}
                    </div>
                  )}
                </div>
              </CardContent>
            </Card>
          )
        })}
      </div>

      <div className="grid gap-4 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>{t("environment")}</CardTitle>
            <CardDescription>运行环境与版本</CardDescription>
          </CardHeader>
          <CardContent>
            <dl className="grid min-w-0 gap-2 text-sm">
              <Row label={t("version")} value={info?.full_version ?? "—"} />
              <Row label="PHP" value={info?.environment ?? "—"} />
              <Row label={t("theme")} value={info?.theme ?? "—"} />
              <Row
                label="HTTPS"
                value={info ? (info.https ? "已启用" : "未启用") : "—"}
              />
              <Row
                label="Debug"
                value={info ? (info.debugmode ? "开启" : "关闭") : "—"}
              />
            </dl>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>{t("quick_actions")}</CardTitle>
          </CardHeader>
          <CardContent className="flex flex-wrap gap-2">
            <Button asChild variant="outline" size="sm">
              <Link to="/posts">
                <Files className="size-4" /> {t("article_manage")}
              </Link>
            </Button>
            <Button asChild variant="outline" size="sm">
              <Link to="/comments">
                <MessageSquare className="size-4" /> {t("comment_manage")}
              </Link>
            </Button>
            <Button asChild variant="outline" size="sm">
              <Link to="/settings">
                <RefreshCw className="size-4" /> {t("web_settings")}
              </Link>
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex min-w-0 items-start justify-between gap-4 border-b pb-2 last:border-0 last:pb-0">
      <dt className="shrink-0 text-muted-foreground">{label}</dt>
      <dd
        className="min-w-0 truncate text-right"
        title={value}
      >
        {value}
      </dd>
    </div>
  )
}
