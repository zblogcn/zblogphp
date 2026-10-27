import { useEffect, useRef, useState } from "react"

import { zbp, type EditorManifest } from "@/lib/zbp"

/** 插件编辑器暴露的接口。 */
export interface EditorInstance {
  getHTML(): string
  setHTML(html: string): void
  focus(): void
  insertMore?(): void
  destroy?(): void
}

interface EditorMountResult {
  getHTML?: () => string
  setHTML?: (html: string) => void
  focus?: () => void
  insertMore?: () => void
  destroy?: () => void
}

export type EditorMount = (
  el: HTMLElement,
  ctx: Record<string, unknown>
) => EditorMountResult | void

const cache = new Map<string, Promise<EditorMount>>()

function loadEditor(manifest: EditorManifest): Promise<EditorMount> {
  const url = manifest.asset
  const cached = cache.get(url)
  if (cached) {
    return cached
  }

  const promise = (async () => {
    const mod = (await import(/* @vite-ignore */ url)) as { mount?: EditorMount }
    if (typeof mod.mount !== "function") {
      throw new Error(`${manifest.appId}: 编辑器未导出 mount()`)
    }
    return mod.mount
  })()

  promise.catch(() => cache.delete(url))
  cache.set(url, promise)

  return promise
}

type Status = "loading" | "ready" | "unavailable" | "error"

interface UsePluginEditorResult {
  status: Status
  message?: string
  getHTML: () => string
  setHTML: (html: string) => void
  focus: () => void
  insertMore: () => void
}

/**
 * 挂载插件声明的编辑器。
 *
 * 没有声明编辑器时返回 unavailable，由调用方回退到内置 textarea。
 */
export function usePluginEditor(
  containerRef: React.RefObject<HTMLDivElement | null>,
  api: Record<string, unknown> | undefined
): UsePluginEditorResult {
  const manifest = zbp.editor
  const [status, setStatus] = useState<Status>(
    manifest ? "loading" : "unavailable"
  )
  const [message, setMessage] = useState<string>()
  const instanceRef = useRef<EditorMountResult | null>(null)
  const apiRef = useRef(api)
  apiRef.current = api

  useEffect(() => {
    if (!manifest) {
      return
    }

    let cancelled = false
    const el = containerRef.current

    if (!el) {
      return
    }

    loadEditor(manifest)
      .then((mount) => {
        if (cancelled || !containerRef.current) {
          return
        }
        const result = mount(containerRef.current, {
          appId: manifest.appId,
          api: apiRef.current,
        })
        instanceRef.current = result || null
        setStatus("ready")
      })
      .catch((error: Error) => {
        if (cancelled) {
          return
        }
        console.error("editor load failed", error)
        setMessage(error.message)
        setStatus("error")
      })

    return () => {
      cancelled = true
      instanceRef.current?.destroy?.()
      instanceRef.current = null
    }
    // 只在清单变化时重新加载
  }, [manifest, containerRef])

  return {
    status,
    message,
    getHTML: () => instanceRef.current?.getHTML?.() ?? "",
    setHTML: (html: string) => instanceRef.current?.setHTML?.(html),
    focus: () => instanceRef.current?.focus?.(),
    insertMore: () => instanceRef.current?.insertMore?.(),
  }
}
