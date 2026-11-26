import { lstatSync, readFileSync, readdirSync } from "node:fs"
import path from "node:path"
import { fileURLToPath } from "node:url"

const projectRoot = fileURLToPath(new URL("../", import.meta.url))
const defaultAssetsDir = path.resolve(
  projectRoot,
  "../../zb_system/admin-next/assets"
)
const assetsDir = path.resolve(process.argv[2] ?? defaultAssetsDir)
const manifestPath = path.join(assetsDir, "manifest.json")
const requiredEntries = ["index.html", "src/plugin-host/runtime.ts"]

function fail(message) {
  throw new Error(message)
}

function isRecord(value) {
  return value !== null && typeof value === "object" && !Array.isArray(value)
}

function safeRelativePath(value, label) {
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

  return target
}

function listFiles(dir) {
  const files = []
  for (const entry of readdirSync(dir, { withFileTypes: true })) {
    const fullPath = path.join(dir, entry.name)
    if (entry.isSymbolicLink()) {
      fail(`产物目录不允许符号链接: ${path.relative(assetsDir, fullPath)}`)
    }
    if (entry.isDirectory()) {
      files.push(...listFiles(fullPath))
    } else if (entry.isFile()) {
      files.push(path.relative(assetsDir, fullPath).split(path.sep).join("/"))
    } else {
      fail(`产物包含非普通文件: ${path.relative(assetsDir, fullPath)}`)
    }
  }
  return files
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

for (const key of requiredEntries) {
  if (!isRecord(manifest[key])) {
    fail(`缺少必需清单入口: ${key}`)
  }
}

const expectedFiles = new Set()
const jsEntries = new Set()

function expectFile(value, label) {
  safeRelativePath(value, label)
  expectedFiles.add(value)
}

function expectReference(key, label) {
  if (typeof key !== "string" || !Object.hasOwn(manifest, key)) {
    fail(`${label} 引用了不存在的清单键: ${JSON.stringify(key)}`)
  }
}

for (const [key, entry] of Object.entries(manifest)) {
  if (!isRecord(entry)) {
    fail(`清单项 ${key} 必须是对象`)
  }

  if (typeof entry.file !== "string") {
    fail(`清单项 ${key} 缺少字符串 file`)
  }
  expectFile(entry.file, `${key}.file`)
  if (entry.file.endsWith(".js")) {
    jsEntries.add(key)
  }

  if (entry.css !== undefined) {
    if (!Array.isArray(entry.css)) {
      fail(`${key}.css 必须是数组`)
    }
    for (const css of entry.css) {
      expectFile(css, `${key}.css`)
    }
  }

  for (const field of ["imports", "dynamicImports"]) {
    if (entry[field] === undefined) {
      continue
    }
    if (!Array.isArray(entry[field])) {
      fail(`${key}.${field} 必须是数组`)
    }
    for (const reference of entry[field]) {
      expectReference(reference, `${key}.${field}`)
    }
  }
}

for (const file of expectedFiles) {
  const fullPath = safeRelativePath(file, `manifest file ${file}`)
  let stat
  try {
    stat = lstatSync(fullPath)
  } catch {
    fail(`manifest 指向的文件不存在: ${file}`)
  }
  if (!stat.isFile() || stat.isSymbolicLink()) {
    fail(`manifest 指向的不是普通文件: ${file}`)
  }
  if (stat.size === 0) {
    fail(`manifest 指向空文件: ${file}`)
  }
}

const reachable = new Set()
const pending = [...requiredEntries]
while (pending.length > 0) {
  const key = pending.pop()
  if (reachable.has(key)) {
    continue
  }
  reachable.add(key)
  const entry = manifest[key]
  for (const field of ["imports", "dynamicImports"]) {
    for (const reference of entry[field] ?? []) {
      if (!reachable.has(reference)) {
        pending.push(reference)
      }
    }
  }
}

const unreachableJs = [...jsEntries].filter((key) => !reachable.has(key))
if (unreachableJs.length > 0) {
  fail(`存在从入口/runtime 不可达的 JS 清单项: ${unreachableJs.join(", ")}`)
}

const actualFiles = listFiles(assetsDir).filter((file) => file !== "manifest.json")
const actualSet = new Set(actualFiles)
const unlisted = actualFiles.filter((file) => !expectedFiles.has(file))
const missing = [...expectedFiles].filter((file) => !actualSet.has(file))

if (unlisted.length > 0) {
  fail(`产物存在未登记文件: ${unlisted.join(", ")}`)
}
if (missing.length > 0) {
  fail(`manifest 登记文件在产物中缺失: ${missing.join(", ")}`)
}

console.log(
  `[verify-assets] OK: ${Object.keys(manifest).length} manifest entries, ` +
    `${expectedFiles.size} files, ${reachable.size} reachable entries`
)
