import { isDevMock, zbp } from "./zbp"

export interface Pagebar {
  AllCount: number
  CurrentCount: number
  PerPageCount: number
  PageAll: number
  PageNow: number
}

export interface ListResponse<T> {
  list: T[]
  pagebar?: Pagebar
}

interface Envelope<T> {
  code: number
  message: string
  data: T
  error: unknown
}

export class ApiError extends Error {
  code: number

  constructor(message: string, code: number) {
    super(message)
    this.name = "ApiError"
    this.code = code
  }
}

type Query = Record<string, string | number | boolean | null | undefined>

function buildUrl(mod: string, act: string, query: Query = {}): string {
  const params = new URLSearchParams({ mod, act })

  for (const [key, value] of Object.entries(query)) {
    if (value === null || value === undefined || value === "") {
      continue
    }
    params.set(key, String(value))
  }

  return `${zbp.api}?${params.toString()}`
}

function unwrap<T>(payload: Envelope<T>): T {
  if (payload.code >= 400) {
    throw new ApiError(payload.message || "请求失败", payload.code)
  }

  return payload.data
}

async function read<T>(response: Response): Promise<T> {
  const text = await response.text()

  let payload: Envelope<T>
  try {
    payload = JSON.parse(text) as Envelope<T>
  } catch {
    throw new ApiError(
      response.status === 401 ? "登录状态已失效" : "服务端返回了非 JSON 响应",
      response.status || 500
    )
  }

  if (response.status === 401) {
    throw new ApiError("登录状态已失效", 401)
  }

  if (response.status === 419 || payload.code === 419) {
    throw new ApiError("安全令牌已过期，请刷新页面", 419)
  }

  return unwrap(payload)
}

export async function apiGet<T>(
  mod: string,
  act: string,
  query: Query = {}
): Promise<T> {
  if (isDevMock) {
    return mockGet(mod, act) as T
  }

  const response = await fetch(buildUrl(mod, act, query), {
    method: "GET",
    credentials: "same-origin",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })

  return read<T>(response)
}

export async function apiPost<T>(
  mod: string,
  act: string,
  body: Record<string, unknown> = {},
  query: Query = {}
): Promise<T> {
  if (isDevMock) {
    return mockGet(mod, act) as T
  }

  const response = await fetch(buildUrl(mod, act, query), {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify({ ...body, csrf_token: zbp.csrf.token }),
  })

  return read<T>(response)
}

/* ------------------------------------------------------------------ */
/* 开发模式假数据                                                       */
/* ------------------------------------------------------------------ */

function mockPagebar(total = 0): Pagebar {
  return {
    AllCount: total,
    CurrentCount: total,
    PerPageCount: zbp.options.manageCount,
    PageAll: 1,
    PageNow: 1,
  }
}

function mockGet(mod: string, act: string): unknown {
  const pagebar = mockPagebar()

  if (mod === "system" && act === "get_info") {
    return {
      environment: "PHP 8.4 / 开发模式",
      version: "dev",
      full_version: "dev",
      articles: 128,
      categories: 6,
      pages: 4,
      comments: 356,
      views: 12800,
      members: 3,
      theme: "default",
      style: "default",
      https: false,
      debugmode: true,
    }
  }

  if (mod === "system" && act === "get_setting") {
    return {
      list: {
        ZC_BLOG_NAME: zbp.options.blogName,
        ZC_BLOG_SUBNAME: zbp.options.blogSubname,
        ZC_BLOG_COPYRIGHT: "",
        ZC_TIME_ZONE_NAME: "Asia/Shanghai",
        ZC_BLOG_LANGUAGEPACK: "zh-cn",
      },
    }
  }

  if (act === "list") {
    return { list: [], pagebar }
  }

  return {}
}
