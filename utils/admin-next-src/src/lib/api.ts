import { isDevMock, zbp } from "./zbp"

export interface Pagebar {
  AllCount: number
  CurrentCount: number
  PerPageCount: number
  PageAll: number
  PageNow: number
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
    // 数组按 id[] 传参，与 PHP 的 $_POST 结构保持一致
    if (Array.isArray(value)) {
      for (const item of value) {
        params.append(`${key}[]`, String(item))
      }
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
      "X-ZBP-Admin-Spa": "1",
    },
    body: JSON.stringify({ ...body, csrf_token: zbp.csrf.token }),
  })

  return read<T>(response)
}

/**
 * multipart 提交（附件上传）。
 *
 * 不能用 JSON：PostUpload() 依赖 $_FILES。
 * 也不要手动设置 Content-Type，浏览器需要自己补 boundary。
 */
export async function apiUpload<T>(
  mod: string,
  act: string,
  form: FormData,
  query: Query = {}
): Promise<T> {
  form.set("csrf_token", zbp.csrf.token)

  const response = await fetch(buildUrl(mod, act, query), {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "X-ZBP-Admin-Spa": "1",
    },
    body: form,
  })

  return read<T>(response)
}

/**
 * 表单编码提交。
 *
 * 删除类接口只从 $_GET 读 id，但 POST 请求里同名字段会合并进 $_REQUEST，
 * 与 JSON body 的行为不一样。用 application/x-www-form-urlencoded 提交，
 * 让 PHP 自己合并参数，避免逐个接口打补丁。
 */
export async function apiForm<T>(
  mod: string,
  act: string,
  body: Record<string, string | number> = {},
  query: Query = {}
): Promise<T> {
  const form = new URLSearchParams()
  for (const [key, value] of Object.entries(body)) {
    form.set(key, String(value))
  }
  form.set("csrf_token", zbp.csrf.token)

  const response = await fetch(buildUrl(mod, act, query), {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      "X-Requested-With": "XMLHttpRequest",
      "X-ZBP-Admin-Spa": "1",
    },
    body: form.toString(),
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
      info: {
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
      },
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
