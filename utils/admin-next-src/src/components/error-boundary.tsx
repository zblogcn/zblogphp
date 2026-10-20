import { Component, type ErrorInfo, type ReactNode } from "react"

import { t } from "@/lib/i18n"
import { Button } from "@/components/ui/button"

interface Props {
  children: ReactNode
}

interface State {
  error: Error | null
}

export class AppErrorBoundary extends Component<Props, State> {
  state: State = { error: null }

  static getDerivedStateFromError(error: Error): State {
    return { error }
  }

  componentDidCatch(error: Error, info: ErrorInfo) {
    // 保留在控制台，便于定位；不引入额外上报依赖
    console.error("admin-next render error", error, info.componentStack)
  }

  render() {
    if (this.state.error) {
      return (
        <div className="mx-auto flex max-w-md flex-col gap-3 px-6 py-16">
          <h1 className="text-base font-semibold">{t("error_title")}</h1>
          <p className="text-sm text-muted-foreground">
            {this.state.error.message}
          </p>
          <div className="flex gap-2">
            <Button size="sm" onClick={() => location.reload()}>
              {t("error_retry")}
            </Button>
            <Button
              size="sm"
              variant="outline"
              onClick={() => {
                location.href = location.pathname
              }}
            >
              返回首页
            </Button>
          </div>
        </div>
      )
    }

    return this.props.children
  }
}
