import { expect, test as base, type Page } from "@playwright/test"
import { randomUUID } from "node:crypto"

/**
 * 写操作测试共用的日志采集与隔离 fixture。
 *
 * 每个用例持有唯一 token；所有清理都在 fixture 的 finally 中执行，
 * afterEach 再兜底一次。清理任务本身必须幂等，且在任一项失败后继续尝试其余任务。
 */

interface ApiEnvelope<T> {
  code: number
  message?: string
  data: T
  error: unknown
}

interface ApiOptions {
  method?: "GET" | "POST"
  encoding?: "json" | "form"
  query?: Record<string, string | number | boolean | undefined>
  body?: Record<string, unknown>
}

export type AdminApi = <T = unknown>(
  mod: string,
  act: string,
  options?: ApiOptions
) => Promise<T>

export type CleanupTask = (api: AdminApi) => Promise<void>

export interface WriteFixture {
  token: string
  api: AdminApi
  cleanup: (task: CleanupTask) => void
}

interface FixtureState {
  tasks: CleanupTask[]
  cleaned: boolean
  promise?: Promise<void>
}

interface Pagebar {
  PageAll?: number
}

interface ListData<T> {
  list: T[] | Record<string, T>
  pagebar?: Pagebar
}

function listRows<T>(value: T[] | Record<string, T> | undefined): T[] {
  if (Array.isArray(value)) {
    return value
  }
  return value && typeof value === "object" ? Object.values(value) : []
}

export class ApiRequestError extends Error {
  status: number
  code: number

  constructor(message: string, status: number, code: number) {
    super(message)
    this.name = "ApiRequestError"
    this.status = status
    this.code = code
  }
}

const fixtureStates = new WeakMap<WriteFixture, FixtureState>()
const fixtureByTest = new WeakMap<object, WriteFixture>()

function createAdminApi(page: Page): AdminApi {
  return async <T = unknown>(
    mod: string,
    act: string,
    options: ApiOptions = {}
  ): Promise<T> => {
    const method = options.method ?? "GET"
    const query = Object.fromEntries(
      Object.entries(options.query ?? {}).filter(([, value]) => value !== undefined)
    )
    const result = await page.evaluate(
      async ({ mod, act, method, encoding, query, body }) => {
        const boot = (
          window as unknown as {
            __ZB__?: { api: string; csrf: { token: string } }
          }
        ).__ZB__
        if (!boot) {
          throw new Error("admin-next bootstrap data is unavailable")
        }

        const url = new URL(boot.api)
        url.searchParams.set("mod", mod)
        url.searchParams.set("act", act)
        for (const [key, value] of Object.entries(query)) {
          url.searchParams.set(key, String(value))
        }

        const headers: Record<string, string> = {
          "X-Requested-With": "XMLHttpRequest",
          "X-ZBP-Admin-Spa": "1",
        }
        let requestBody: string | undefined
        if (method === "POST") {
          const payload = { ...body, csrf_token: boot.csrf.token }
          if (encoding === "form") {
            headers["Content-Type"] = "application/x-www-form-urlencoded; charset=UTF-8"
            requestBody = new URLSearchParams(
              Object.entries(payload).map(([key, value]) => [key, String(value)])
            ).toString()
          } else {
            headers["Content-Type"] = "application/json"
            requestBody = JSON.stringify(payload)
          }
        }

        const response = await fetch(url, {
          method,
          credentials: "same-origin",
          headers,
          body: requestBody,
        })
        const text = await response.text()
        let payload: unknown
        try {
          payload = JSON.parse(text)
        } catch {
          payload = null
        }
        return { status: response.status, ok: response.ok, payload, text }
      },
      {
        mod,
        act,
        method,
        encoding: options.encoding ?? "json",
        query,
        body: options.body ?? {},
      }
    )

    const payload = result.payload as ApiEnvelope<T> | null
    if (!payload) {
      throw new ApiRequestError(
        `${mod}.${act} returned non-JSON (HTTP ${result.status}): ${result.text.slice(0, 300)}`,
        result.status,
        result.status
      )
    }

    if (!result.ok || payload.code !== 200 || payload.error !== null) {
      throw new ApiRequestError(
        `${mod}.${act} failed (HTTP ${result.status}, code ${payload.code}): ${payload.message ?? ""}`,
        result.status,
        Number(payload.code)
      )
    }

    return payload.data
  }
}

/** 执行全部清理任务；重复调用不会重复删除。 */
export async function cleanupFixture(fixture: WriteFixture): Promise<void> {
  const state = fixtureStates.get(fixture)
  if (!state || state.cleaned) {
    return
  }
  if (state.promise) {
    return state.promise
  }

  state.promise = (async () => {
    const errors: unknown[] = []
    for (const task of [...state.tasks].reverse()) {
      try {
        await task(fixture.api)
      } catch (error) {
        errors.push(error)
      }
    }
    if (errors.length > 0) {
      throw new AggregateError(
        errors,
        `fixture ${fixture.token} cleanup failed: ${errors.map(String).join("; ")}`
      )
    }
    state.cleaned = true
  })()

  return state.promise
}

export function fixtureName(fixture: WriteFixture, kind: string): string {
  return `E2E-${kind}-${fixture.token}`
}

export function isNotFound(error: unknown): boolean {
  return (
    error instanceof ApiRequestError &&
    (error.status === 404 || error.code === 404)
  )
}

async function listAll<T>(
  api: AdminApi,
  mod: string,
  query: Record<string, string | number | boolean | undefined> = {}
): Promise<T[]> {
  const first = await api<ListData<T>>(mod, "list", {
    query: { ...query, perpage: 100 },
  })
  const rows = listRows(first.list)
  const pageAll = Math.max(1, Number(first.pagebar?.PageAll ?? 1))

  for (let page = 2; page <= pageAll; page += 1) {
    const next = await api<ListData<T>>(mod, "list", {
      query: { ...query, page, perpage: 100 },
    })
    rows.push(...listRows(next.list))
  }

  return rows
}

interface NamedRow {
  ID: number
  Name: string
}

/** 按精确名称删除分类/标签/模块，并确认没有残留。 */
export async function cleanupNamedRecords(
  api: AdminApi,
  mod: "category" | "tag" | "module",
  names: string[]
): Promise<void> {
  const wanted = new Set(names.filter(Boolean))
  if (wanted.size === 0) {
    return
  }

  const query = mod === "module" ? {} : { manage: 1 }
  const rows = await listAll<NamedRow>(api, mod, query)
  for (const row of rows) {
    if (!wanted.has(row.Name)) {
      continue
    }
    try {
      await api(mod, "delete", { method: "POST", body: { id: row.ID } })
    } catch (error) {
      if (!isNotFound(error)) {
        throw error
      }
    }
  }

  const remaining = await listAll<NamedRow>(api, mod, query)
  const leftovers = remaining.filter((row) => wanted.has(row.Name))
  if (leftovers.length > 0) {
    throw new Error(`${mod} fixture cleanup left: ${leftovers.map((row) => row.Name).join(", ")}`)
  }
}

interface PostRow {
  ID: number
  Title: string
}

/** 按测试跟踪的 ID 和精确标题删除文章/页面，并确认没有残留。 */
export async function cleanupPostRecords(
  api: AdminApi,
  type: 0 | 1,
  titles: string[],
  trackedIds: Set<number>
): Promise<void> {
  const wanted = new Set(titles.filter(Boolean))
  const query = { manage: 1, type, without_content: 1 }
  const rows = await listAll<PostRow>(api, "post", query)
  const ids = new Set(trackedIds)
  for (const row of rows) {
    if (wanted.has(row.Title)) {
      ids.add(row.ID)
    }
  }

  if (ids.size > 0) {
    try {
      await api("post", "batch", {
        method: "POST",
        body: { type, id: [...ids] },
      })
    } catch (error) {
      if (!isNotFound(error)) {
        throw error
      }
    }
  }

  const remaining = await listAll<PostRow>(api, "post", query)
  const leftovers = remaining.filter(
    (row) => wanted.has(row.Title) || ids.has(row.ID)
  )
  if (leftovers.length > 0) {
    throw new Error(`post fixture cleanup left: ${leftovers.map((row) => row.Title).join(", ")}`)
  }
}

interface UploadRow {
  ID: number
  Name: string
  SourceName?: string
}

interface UploadListData {
  list: UploadRow[]
  pagination?: Pagebar
}

async function listAllUploads(api: AdminApi): Promise<UploadRow[]> {
  const rows: UploadRow[] = []
  let page = 1
  let pageAll = 1

  do {
    const raw = await api<unknown>("upload", "list", {
      query: { page, perpage: 100, sortby: "ID", order: "DESC" },
    })
    const payload = (Array.isArray(raw) ? raw[0] : raw) as UploadListData
    rows.push(...(payload.list ?? []))
    pageAll = Math.max(1, Number(payload.pagination?.PageAll ?? 1))
    page += 1
  } while (page <= pageAll)

  return rows
}

/** 按原始文件名或唯一 token 删除附件，并确认没有残留。 */
export async function cleanupUploadRecords(
  api: AdminApi,
  fileName: string,
  token: string
): Promise<void> {
  const rows = await listAllUploads(api)
  for (const row of rows) {
    if (row.SourceName !== fileName && !row.Name.includes(token)) {
      continue
    }
    try {
      await api("upload", "delete", { method: "POST", body: { id: row.ID } })
    } catch (error) {
      if (!isNotFound(error)) {
        throw error
      }
    }
  }

  const remaining = await listAllUploads(api)
  const leftovers = remaining.filter(
    (row) => row.SourceName === fileName || row.Name.includes(token)
  )
  if (leftovers.length > 0) {
    throw new Error(`upload fixture cleanup left: ${leftovers.map((row) => row.Name).join(", ")}`)
  }
}

interface CommentRow {
  ID: number | string
  Content: string
  AuthorID?: number
  Name?: string
  Email?: string
  Author?: { Name?: string }
}

/** 按固定评论 ID 和精确内容删除评论，并确认两种审核状态下均无残留。 */
export async function cleanupCommentRecords(
  api: AdminApi,
  commentId: number,
  contents: string[]
): Promise<void> {
  const wanted = new Set(contents.filter(Boolean))
  const ids = new Set(commentId > 0 ? [commentId] : [])

  for (const ischecking of [0, 1]) {
    const rows = await listAll<CommentRow>(api, "comment", {
      manage: 1,
      ischecking,
    })
    for (const row of rows) {
      if (wanted.has(row.Content)) {
        ids.add(Number(row.ID))
      }
    }
  }

  for (const id of ids) {
    try {
      await api("comment", "delete", {
        method: "POST",
        query: { id },
      })
    } catch (error) {
      if (!isNotFound(error)) {
        throw error
      }
    }
  }

  for (const ischecking of [0, 1]) {
    const rows = await listAll<CommentRow>(api, "comment", {
      manage: 1,
      ischecking,
    })
    const leftovers = rows.filter(
      (row) => ids.has(Number(row.ID)) || wanted.has(row.Content)
    )
    if (leftovers.length > 0) {
      throw new Error(
        `comment fixture cleanup left: ${leftovers.map((row) => row.Content).join(", ")}`
      )
    }
  }
}

/** 按固定评论 ID 还原审核状态，并确认目标状态下能读到同一 ID。 */
export async function restoreCommentFixture(
  api: AdminApi,
  commentId: number,
  ischecking: 0 | 1
): Promise<void> {
  await api("comment", "check", {
    method: "POST",
    encoding: "form",
    body: { id: commentId, ischecking },
  })
  const rows = await listAll<CommentRow>(api, "comment", { manage: 1, ischecking })
  if (!rows.some((row) => Number(row.ID) === commentId)) {
    throw new Error(`comment fixture ID ${commentId} was not restored to ${ischecking}`)
  }
}

export const test = base.extend<{ writeFixture: WriteFixture }>({
  page: async ({ page }, use) => {
    const log: string[] = []

    page.on("request", (request) => {
      if (request.url().includes("admin-next/api.php")) {
        log.push(`>> ${request.method()} ${request.url()}`)
      }
    })

    page.on("response", async (response) => {
      if (response.url().includes("admin-next/api.php")) {
        let body = ""
        try {
          body = (await response.text()).slice(0, 200)
        } catch {
          body = "(unreadable)"
        }
        log.push(`<< ${response.status()} ${body}`)
      }
    })

    try {
      await use(page)
    } finally {
      if (process.env.ZBP_E2E_DEBUG) {
        console.log(log.join("\n"))
      }
    }
  },

  writeFixture: async ({ page }, use, testInfo) => {
    const nonce = randomUUID().replaceAll("-", "").slice(0, 12)
    const project = testInfo.project.name.replace(/[^a-z0-9]+/gi, "-")
    const token = `${project}-w${testInfo.workerIndex}-r${testInfo.retry}-${nonce}`
    const api = createAdminApi(page)
    const state: FixtureState = { tasks: [], cleaned: false }
    const fixture: WriteFixture = {
      token,
      api,
      cleanup: (task) => state.tasks.push(task),
    }

    fixtureStates.set(fixture, state)
    fixtureByTest.set(testInfo, fixture)
    try {
      await use(fixture)
    } finally {
      await cleanupFixture(fixture)
    }
  },
})

test.afterEach(async ({}, testInfo) => {
  const fixture = fixtureByTest.get(testInfo)
  if (fixture) {
    await cleanupFixture(fixture)
  }
})

export { expect }
