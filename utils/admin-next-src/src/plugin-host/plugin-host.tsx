import { useEffect, useRef } from "react"

interface PluginHostProps {
  mount: (el: HTMLElement) => void | (() => void)
  onError: (error: Error) => void
  dataPlugin?: string
}

/**
 * 插件挂载容器。
 *
 * 插件 bundle 自己负责 createRoot 与渲染，宿主只提供干净的节点，
 * 卸载时调用 mount 返回的清理函数。
 */
export function PluginHost({ mount, onError, dataPlugin }: PluginHostProps) {
  const containerRef = useRef<HTMLDivElement>(null)
  const mountRef = useRef(mount)
  mountRef.current = mount
  const errorRef = useRef(onError)
  errorRef.current = onError

  useEffect(() => {
    const el = containerRef.current
    if (!el) {
      return
    }

    let cleanup: void | (() => void)

    try {
      cleanup = mountRef.current(el)
    } catch (error) {
      errorRef.current(error instanceof Error ? error : new Error(String(error)))
      return
    }

    return () => {
      try {
        if (typeof cleanup === "function") {
          cleanup()
        }
      } catch (error) {
        // 卸载阶段不打断路由切换
        console.error("plugin unmount failed", error)
      }
    }
  }, [])

  return (
    <div
      ref={containerRef}
      data-slot="plugin-root"
      data-plugin={dataPlugin}
      className="zbp-plugin-root min-w-0"
    />
  )
}
