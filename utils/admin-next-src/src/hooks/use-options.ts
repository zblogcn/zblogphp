import { useEffect, useState } from "react"

import { apiGet, type Pagebar } from "@/lib/api"

interface OptionsResponse {
  list: Array<Record<string, unknown>>
}

/**
 * 拉取一组下拉选项（分类 / 标签 / 用户 / 模块等）。
 *
 * 这些数据在多个表单里复用，统一做一次缓存查询即可。
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

  useEffect(() => {
    let cancelled = false

    apiGet<OptionsResponse | Array<OptionsResponse>>(mod, act, { perpage })
      .then((payload) => {
        if (cancelled) {
          return
        }
        // upload.list 之类的接口会把 data 再包一层数组
        const normalized = Array.isArray(payload) ? payload[0] : payload
        const list = (normalized?.list ?? []) as Array<
          Record<string, unknown>
        >
        setData(
          list.map((row) => ({
            value: String(key(row) ?? ""),
            label: String(label(row) ?? ""),
          }))
        )
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
  }, [mod, act, perpage, key, label])

  return { options: data, loading }
}

export type { Pagebar }
