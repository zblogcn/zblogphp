export interface NavItem {
  id: string
  title: string
  url: string
  icon?: string
  group?: string
  order?: number
  require?: string | null
  badge?: string | null
  external?: boolean
  legacy?: boolean
}

export interface ZbzUser {
  id: number
  name: string
  staticName: string
  level: number
  levelName: string
  avatar: string
}

export interface BootData {
  host: string
  entry: string
  api: string
  legacy: string
  version: string
  debug: boolean
  user: ZbzUser
  rights: Record<string, boolean>
  nav: NavItem[]
  lang: {
    msg: Record<string, string>
    post_status_name: Record<string, string>
    lang_bcp47: string
    locale: string
  }
  csrf: { token: string; expiration: number; admin: string }
  options: {
    manageCount: number
    pagebarCount: number
    blogName: string
    blogSubname: string
  }
}

declare global {
  interface Window {
    __ZB__?: BootData
    __ZB_ASSETS_MISSING__?: boolean
  }
}

/** 开发模式（脱离 PHP 入口）下的假数据，仅用于调布局。 */
function createDevBoot(): BootData {
  const nav: NavItem[] = [
    { id: "dashboard", title: "仪表盘", url: "#/", icon: "house", group: "main", order: 0 },
    { id: "posts", title: "文章管理", url: "#/posts", icon: "files", group: "content", order: 10 },
    { id: "pages", title: "页面管理", url: "#/pages", icon: "file-text", group: "content", order: 20 },
    { id: "categories", title: "分类管理", url: "#/categories", icon: "folder", group: "content", order: 30 },
    { id: "tags", title: "标签管理", url: "#/tags", icon: "tags", group: "content", order: 40 },
    { id: "comments", title: "评论管理", url: "#/comments", icon: "message-square", group: "content", order: 50 },
    { id: "uploads", title: "附件管理", url: "#/uploads", icon: "inbox", group: "content", order: 60 },
    { id: "members", title: "用户管理", url: "#/members", icon: "users", group: "manage", order: 70 },
    { id: "themes", title: "主题管理", url: "#/themes", icon: "layout-grid", group: "extend", order: 80 },
    { id: "modules", title: "模块管理", url: "#/modules", icon: "layout-panel-left", group: "extend", order: 90 },
    { id: "plugins", title: "插件管理", url: "#/plugins", icon: "puzzle", group: "extend", order: 100 },
    { id: "settings", title: "网站设置", url: "#/settings", icon: "settings", group: "system", order: 110 },
  ]

  return {
    host: "http://localhost/",
    entry: "http://localhost/zb_system/admin-next/index.php",
    api: "/api.php",
    legacy: "http://localhost/zb_system/cmd.php?act=admin",
    version: "dev",
    debug: true,
    user: {
      id: 1,
      name: "admin",
      staticName: "管理员",
      level: 1,
      levelName: "管理员",
      avatar: "",
    },
    rights: {},
    nav,
    lang: {
      msg: {
        dashboard: "仪表盘",
        article_manage: "文章管理",
        page_manage: "页面管理",
        category_manage: "分类管理",
        tag_manage: "标签管理",
        comment_manage: "评论管理",
        upload_manage: "附件管理",
        member_manage: "用户管理",
        theme_manage: "主题管理",
        module_manage: "模块管理",
        plugin_manage: "插件管理",
        web_settings: "网站设置",
        search: "搜索",
        submit: "提交",
        operate: "操作",
        new_article: "新建文章",
      },
      post_status_name: { "0": "公开", "1": "草稿", "2": "待审核" },
      lang_bcp47: "zh-CN",
      locale: "zh-cn",
    },
    csrf: { token: "", expiration: 24, admin: "" },
    options: {
      manageCount: 20,
      pagebarCount: 10,
      blogName: "Z-BlogPHP",
      blogSubname: "",
    },
  }
}

export const isDevMock = typeof window !== "undefined" && !window.__ZB__

export const zbp: BootData =
  typeof window !== "undefined" && window.__ZB__ ? window.__ZB__ : createDevBoot()

export function hasRight(action: string | null | undefined): boolean {
  if (!action) {
    return true
  }
  // 开发模式下没有权限数据，默认放行
  if (isDevMock) {
    return true
  }
  return zbp.rights[action] === true
}
