import type { CSSProperties } from "react"
import { Toaster as Sonner, type ToasterProps } from "sonner"

function Toaster(props: ToasterProps) {
  return (
    <Sonner
      position="bottom-right"
      closeButton
      className="toaster group"
      style={
        {
          "--normal-bg": "var(--popover)",
          "--normal-text": "var(--popover-foreground)",
          "--normal-border": "var(--border)",
        } as CSSProperties
      }
      {...props}
    />
  )
}

export { Toaster }
