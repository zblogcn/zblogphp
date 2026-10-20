/**
 * module.list 返回的 list 是以 ID 为键的对象，不是数组。
 * 统一转成数组，避免页面里各自处理。
 */
export function toRows<T>(value: unknown): T[] {
  if (Array.isArray(value)) {
    return value as T[]
  }
  if (value && typeof value === "object") {
    return Object.values(value as Record<string, T>)
  }
  return []
}
