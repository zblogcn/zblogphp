import type * as React from "react"

import { cn } from "@/lib/utils"

/**
 * 表单行：左侧标签，右侧控件。
 *
 * 用小屏纵向、中大屏横向的栅格，避免在手机上挤成两列。
 */
function FormRow({
  label,
  htmlFor,
  hint,
  required = false,
  className,
  children,
}: {
  label: string
  htmlFor?: string
  hint?: React.ReactNode
  required?: boolean
  className?: string
  children: React.ReactNode
}) {
  return (
    <div
      className={cn(
        "grid gap-1.5 sm:grid-cols-[minmax(0,180px)_minmax(0,1fr)] sm:items-start sm:gap-4",
        className
      )}
    >
      <label
        htmlFor={htmlFor}
        className="text-sm font-normal text-muted-foreground sm:pt-2"
      >
        {label}
        {required ? (
          <span className="ml-0.5 text-destructive" aria-hidden="true">
            *
          </span>
        ) : null}
      </label>

      <div className="flex min-w-0 flex-col gap-1">
        {children}
        {hint ? (
          <span className="text-xs text-muted-foreground">{hint}</span>
        ) : null}
      </div>
    </div>
  )
}

export { FormRow }
