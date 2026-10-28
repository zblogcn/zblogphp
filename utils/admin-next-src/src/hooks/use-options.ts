import { useEffect, useRef, useState } from "react"

import { apiGet, type Pagebar } from "@/lib/api"

interface OptionsResponse {
  list: Array<Record<string, unknown>>
}

/** 选项数据在多个表单间复用，用模块级缓存避免重复请求。 */
const cache = new Map<
  string,
  Promise<Array<{ value: string; label: string }>>
>()

/**
 * 拉取一组下拉选项（分类 / 标签 / 用户 / 模块等）。
 *
 * key/label 是调用方每次渲染新建的闭包，不能放进 effect 依赖数组，
 * 否则每次渲染都会触发一次请求，形成请求风暴。
 * 这里用 ref 保存最新实现，依赖只保留真正决定「拉哪份数据」的字符串。
 */
export function useOptions(
  mod: string,
  act: string,
  key: (row: Record<string, unknown>) => string,
  label: (row: Record<string, unknown>) => string,
  perpage = 200
) {
  const [data, setData] = useState<Array<{ value: string; label: string }>>([])
  const [loading, setLoading] = useState(true)

  // 最新闭包，effect 内部读取时不建立依赖
  const mappers = useRef({ key, label })
  mappers.current = { key, label }

  const cacheKey = `${mod}.${act}:${perpage}`

  useEffect(() => {
    let cancelled = false
    setLoading(true)

    let request = cache.get(cacheKey)
    if (!request) {
      request = apiGet<OptionsResponse | Array<OptionsResponse>>(mod, act, {
        perpage,
      }).then((payload) => {
        // upload.list 之类的接口会把 data 再包一层数组
        const normalized = Array.isArray(payload) ? payload[0] : payload
        const list = (normalized?.list ?? []) as Array<
          Record<string, unknown>
        >
        const { key: toKey, label: toLabel } = mappers.current

        return list.map((row) => ({
          value: String(toKey(row) ?? ""),
          label: String(toLabel(row) ?? ""),
        }))
      })

      // 失败不缓存，下次进入页面可以重试
      request.catch(() => cache.delete(cacheKey))
      cache.set(cacheKey, request)
    }

    request
      .then((options) => {
        if (!cancelled) {
          setData(options)
        }
      })
      .catch(() => {
        if (!cancelled) {
          setData([])
        }
      })
      .finally(() => {
        if (!cancelled) {
          setLoading(false)
        }
      })

    return () => {
      cancelled = true
    }
  }, [mod, act, perpage, cacheKey])

  return { options: data, loading }
}

export type { Pagebar }
