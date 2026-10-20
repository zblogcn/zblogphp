import { rmSync } from "node:fs"
import { resolve } from "node:path"

/**
 * Vite 以 index.html 作为构建入口，会把入口 HTML 一并写进产物目录。
 * 那个文件不被 PHP 壳使用，删掉避免误导。
 */
rmSync(resolve(process.cwd(), "../../zb_system/admin-next/assets/index.html"), {
  force: true,
})
