/**
 * 构造强制进入旧后台的文章/页面编辑 URL。
 *
 * @param {string} host
 * @param {0 | 1} type
 * @param {number} [id]
 * @returns {string}
 */
export function legacyEditUrl(host, type, id = 0) {
  const base = host.endsWith("/") ? host : `${host}/`
  const url = new URL("zb_system/admin/edit.php", base)
  url.searchParams.set("act", type === 1 ? "PageEdt" : "ArticleEdt")
  if (Number.isInteger(id) && id > 0) {
    url.searchParams.set("id", String(id))
  }
  url.searchParams.set("force", "1")
  return url.toString()
}
