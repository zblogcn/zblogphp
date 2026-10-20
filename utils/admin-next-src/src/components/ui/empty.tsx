import type { LucideIcon } from "lucide-react"
import type * as React from "react"

import { cn } from "@/lib/utils"

interface EmptyProps extends React.ComponentProps<"div"> {
  icon?: LucideIcon
  title: string
  description?: string
  action?: React.ReactNode
}

function Empty({
  className,
  icon: Icon,
  title,
  description,
  action,
  ...props
}: EmptyProps) {
  return (
    <div
      data-slot="empty"
      className={cn(
        "flex flex-col items-center justify-center gap-2 px-6 py-12 text-center",
        className
      )}
      {...props}
    >
      {Icon ? (
        <div className="mb-1 flex size-10 items-center justify-center rounded-full bg-muted">
          <Icon className="size-5 text-muted-foreground" strokeWidth={1.75} />
        </div>
      ) : null}
      <div className="text-sm font-medium">{title}</div>
      {description ? (
        <div className="max-w-sm text-xs text-muted-foreground">{description}</div>
      ) : null}
      {action ? <div className="mt-2">{action}</div> : null}
    </div>
  )
}

export { Empty }
