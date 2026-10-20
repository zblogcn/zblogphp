import { Link } from "react-router-dom"
import { Compass } from "lucide-react"

import { t } from "@/lib/i18n"
import { Button } from "@/components/ui/button"
import { Empty } from "@/components/ui/empty"

export function NotFoundPage() {
  return (
    <Empty
      icon={Compass}
      title="页面不存在"
      description="你访问的地址没有对应的页面。"
      action={
        <Button asChild variant="outline" size="sm">
          <Link to="/">{t("dashboard")}</Link>
        </Button>
      }
    />
  )
}
