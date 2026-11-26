import { lstatSync, readFileSync } from "node:fs"
import path from "node:path"
import { fileURLToPath } from "node:url"
import { gzipSync } from "node:zlib"

const projectRoot = fileURLToPath(new URL("../", import.meta.url))
const defaultAssetsDir = path.resolve(
  projectRoot,
  "../../zb_system/admin-next/assets"
)
const assetsDir = path.resolve(process.argv[2] ?? defaultAssetsDir)
const manifestPath = path.join(assetsDir, "manifest.json")
const requiredEntries = ["index.html", "src/plugin-host/runtime.ts"]
const firstScreenLimit = 180 * 1024
const pageChunkLimit = 80 * 1024
const gzipSizes = new Map()

function fail(message) {
  throw new Error(message)
}

function isRecord(value) {
  return value !== null && typeof value === "object" && !Array.isArray(value)
}

function safeFilePath(value, label) {
  if (
    typeof value !== "string" ||
    value.length === 0 ||
    value.includes("\\") ||
    value.includes("\0") ||
    path.posix.isAbsolute(value) ||
    /^[A-Za-z]:/.test(value)
  ) {
    fail(`${label} 不是安全的相对路径: ${JSON.stringify(value)}`)
  }

  const normalized = path.posix.normalize(value)
  if (
    normalized !== value ||
    normalized === "." ||
    normalized === ".." ||
    normalized.startsWith("../")
  ) {
    fail(`${label} 含非法路径穿越或未规范化片段: ${value}`)
  }

  const target = path.resolve(assetsDir, ...value.split("/"))
  if (target !== assetsDir && !target.startsWith(`${assetsDir}${path.sep}`)) {
    fail(`${label} 解析到产物目录之外: ${value}`)
  }

  let stat
  try {
    stat = lstatSync(target)
  } catch {
    fail(`manifest 指向的文件不存在: ${value}`)
  }
  if (!stat.isFile() || stat.isSymbolicLink()) {
    fail(`manifest 指向的不是普通文件: ${value}`)
  }

  return target
}

function manifestEntry(manifest, key) {
  if (!Object.hasOwn(manifest, key) || !isRecord(manifest[key])) {
    fail(`manifest.json 缺少有效入口: ${key}`)
  }
  return manifest[key]
}

function references(entry, key, field) {
  const value = entry[field]
  if (value === undefined) {
    return []
  }
  if (
    !Array.isArray(value) ||
    value.some((reference) => typeof reference !== "string")
  ) {
    fail(`${key}.${field} 必须是字符串数组`)
  }
  return value
}

function collectStaticFiles(manifest, entryKeys) {
  const files = new Set()
  const visited = new Set()
  const pending = [...entryKeys]

  while (pending.length > 0) {
    const key = pending.pop()
    if (visited.has(key)) {
      continue
    }
    visited.add(key)

    const entry = manifestEntry(manifest, key)
    safeFilePath(entry.file, `${key}.file`)
    files.add(entry.file)

    for (const css of references(entry, key, "css")) {
      safeFilePath(css, `${key}.css`)
      files.add(css)
    }

    for (const reference of references(entry, key, "imports")) {
      pending.push(reference)
    }
  }

  return files
}

function gzipSize(file) {
  if (!gzipSizes.has(file)) {
    const source = readFileSync(safeFilePath(file, `manifest file ${file}`))
    gzipSizes.set(file, gzipSync(source).length)
  }
  return gzipSizes.get(file)
}

function totalGzipSize(files) {
  let total = 0
  for (const file of files) {
    total += gzipSize(file)
  }
  return total
}

function formatKB(bytes) {
  return (bytes / 1024).toFixed(2)
}

let manifest
try {
  manifest = JSON.parse(readFileSync(manifestPath, "utf8"))
} catch (error) {
  fail(`无法读取有效 manifest.json: ${error.message}`)
}
if (!isRecord(manifest)) {
  fail("manifest.json 顶层必须是对象")
}

const firstScreenFiles = collectStaticFiles(manifest, requiredEntries)
const firstScreenBytes = totalGzipSize(firstScreenFiles)
console.log(
  `[verify-size] first-screen static gzip: ${formatKB(firstScreenBytes)} KB / ` +
    `${formatKB(firstScreenLimit)} KB (${firstScreenBytes} bytes, ` +
    `${firstScreenFiles.size} files; index.html + runtime)`
)

const dynamicEntries = Object.entries(manifest)
  .filter(([, entry]) => isRecord(entry) && entry.isDynamicEntry === true)
  .sort(([left], [right]) => left.localeCompare(right))
if (dynamicEntries.length === 0) {
  fail("manifest.json 未声明动态页面入口")
}

console.log(
  `[verify-size] dynamic page chunk gzip (limit ${formatKB(pageChunkLimit)} KB each):`
)
let largestPage = null
for (const [key, entry] of dynamicEntries) {
  const bytes = gzipSize(entry.file)
  console.log(
    `  ${key} -> ${entry.file}: ${formatKB(bytes)} KB (${bytes} bytes)`
  )
  if (largestPage === null || bytes > largestPage.bytes) {
    largestPage = { key, file: entry.file, bytes }
  }
}

console.log(
  `[verify-size] largest page chunk: ${largestPage.key} -> ${largestPage.file}, ` +
    `${formatKB(largestPage.bytes)} KB / ${formatKB(pageChunkLimit)} KB ` +
    `(${largestPage.bytes} bytes)`
)

const failures = []
if (firstScreenBytes > firstScreenLimit) {
  failures.push(
    `首屏静态 gzip ${firstScreenBytes} bytes 超过 ${firstScreenLimit} bytes`
  )
}
if (largestPage.bytes > pageChunkLimit) {
  failures.push(
    `单页 chunk ${largestPage.file} gzip ${largestPage.bytes} bytes 超过 ` +
      `${pageChunkLimit} bytes`
  )
}
if (failures.length > 0) {
  fail(failures.join("; "))
}

console.log(
  `[verify-size] OK: ${dynamicEntries.length} page chunks within ` +
    `${formatKB(pageChunkLimit)} KB gzip each (1 KB = 1024 bytes)`
)
