import { zbp } from "./zbp"

/**
 * 新后台自有文案。系统已有的文案走 __ZB__.lang.msg。
 */
const local: Record<string, string> = {
  app_name: "Z-BlogPHP",
  group_main: "",
  group_content: "内容",
  group_manage: "管理",
  group_extend: "扩展",
  group_system: "系统",
  toggle_sidebar: "折叠/展开侧栏",
  open_menu: "打开菜单",
  theme_toggle: "切换主题",
  view_site: "查看站点",
  logout: "退出登录",
  legacy_admin: "旧版后台",
  legacy_badge: "旧版",
  loading: "加载中…",
  error_title: "加载失败",
  error_retry: "重试",
  empty_title: "暂无数据",
  empty_description: "这里还没有内容。",
  page_prev: "上一页",
  page_next: "下一页",
  page_info: "共 {total} 条",
  total_count: "总数",
  no_permission: "没有访问权限",
  missing_assets: "前端资源未构建，请先执行 pnpm build。",
  dashboard: "仪表盘",
  statistics: "站点统计",
  environment: "环境信息",
  version: "版本",
  theme: "当前主题",
  quick_actions: "快捷入口",
  coming_soon: "该页面正在迁移中",
  coming_soon_desc: "暂时请使用旧版后台完成此操作。",
  open_legacy: "打开旧版页面",
  refresh: "刷新",
  filter_all: "全部",
  not_available: "—",
  publish_state: "状态",
}

export type TranslateParams = Record<string, string | number>

export function t(key: string, params?: TranslateParams): string {
  const value = local[key] ?? zbp.lang.msg[key] ?? key

  if (!params) {
    return value
  }

  return value.replace(/\{(\w+)\}/g, (_, name: string) =>
    String(params[name] ?? `{${name}}`)
  )
}

export function postStatusName(status: number | string): string {
  return zbp.lang.post_status_name[String(status)] ?? String(status)
}
