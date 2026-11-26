import { execFileSync } from "node:child_process"
import path from "node:path"
import { fileURLToPath } from "node:url"

const projectRoot = fileURLToPath(new URL("../", import.meta.url))
const repositoryRoot = path.resolve(projectRoot, "../..")
const revision = process.argv[2] ?? "HEAD"
const assetsDirectory = "zb_system/admin-next/assets"
const excludedDirectories = ["utils/admin-next-src", "tests"]

function fail(message) {
  throw new Error(message)
}

function isWithin(entry, directory) {
  const normalized = entry.endsWith("/") ? entry.slice(0, -1) : entry
  return normalized === directory || normalized.startsWith(`${directory}/`)
}

function commandError(error) {
  const stderr = error.stderr?.toString().trim()
  return stderr || error.message
}

if (revision.startsWith("-")) {
  fail(`非法 git revision: ${revision}`)
}

let archive
try {
  archive = execFileSync("git", ["archive", "--format=tar", revision], {
    cwd: repositoryRoot,
    maxBuffer: 256 * 1024 * 1024,
  })
} catch (error) {
  fail(`无法生成 git archive ${revision}: ${commandError(error)}`)
}

let listing
try {
  listing = execFileSync("tar", ["-tf", "-"], {
    input: archive,
    encoding: "utf8",
    maxBuffer: 64 * 1024 * 1024,
  })
} catch (error) {
  fail(`无法读取 git archive ${revision}: ${commandError(error)}`)
}

const entries = listing.split(/\r?\n/).filter(Boolean)
const assetEntries = entries.filter((entry) => isWithin(entry, assetsDirectory))
const assetFiles = assetEntries.filter((entry) => !entry.endsWith("/"))
const excludedEntries = excludedDirectories.map((directory) => ({
  directory,
  entries: entries.filter((entry) => isWithin(entry, directory)),
}))

const failures = []
if (assetFiles.length === 0) {
  failures.push(`归档缺少 ${assetsDirectory}/ 下的文件`)
}
for (const excluded of excludedEntries) {
  if (excluded.entries.length > 0) {
    failures.push(
      `归档错误包含 ${excluded.directory}/：${excluded.entries.slice(0, 5).join(", ")}`
    )
  }
}
if (failures.length > 0) {
  fail(`发布包边界校验失败：\n- ${failures.join("\n- ")}`)
}

console.log(
  `[verify-release] OK: git archive ${revision} contains ` +
    `${assetsDirectory}/ (${assetFiles.length} files) and excludes ` +
    `${excludedDirectories.join(" / ")}`
)
