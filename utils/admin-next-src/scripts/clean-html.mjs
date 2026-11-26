import { readdirSync, readFileSync, rmSync, writeFileSync } from "node:fs"
import { resolve } from "node:path"

const assetsDir = resolve(process.cwd(), "../../zb_system/admin-next/assets")

/**
 * Vite 以 index.html 作为构建入口，会把入口 HTML 一并写进产物目录。
 * 那个文件不被 PHP 壳使用，删掉避免误导。
 */
rmSync(resolve(assetsDir, "index.html"), {
  force: true,
})

/**
 * 依赖注入的 CSS 模板可能生成仅含空格的空行。规范化这些产物空行，
 * 避免提交构建产物时触发 git diff --check。
 */
function cleanWhitespaceOnlyLines(dir) {
  for (const entry of readdirSync(dir, { withFileTypes: true })) {
    const fullPath = resolve(dir, entry.name)
    if (entry.isDirectory()) {
      cleanWhitespaceOnlyLines(fullPath)
    } else if (entry.isFile() && /\.(?:css|js)$/.test(entry.name)) {
      const source = readFileSync(fullPath, "utf8")
      const cleaned = source.replace(/^[ \t]+(?=\r?$)/gm, "")
      if (cleaned !== source) {
        writeFileSync(fullPath, cleaned)
      }
    }
  }
}

cleanWhitespaceOnlyLines(assetsDir)
