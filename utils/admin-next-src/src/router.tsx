import { lazy, type ComponentType } from "react"
import { HashRouter, Route, Routes } from "react-router-dom"

import { AppShell } from "@/components/app-shell"

/**
 * 按命名导出做路由级懒加载，避免首屏把全部页面打进主包。
 */
function lazyNamed<T extends Record<string, unknown>>(
  loader: () => Promise<T>,
  name: keyof T & string
) {
  return lazy(async () => {
    const mod = await loader()
    return { default: mod[name] as ComponentType }
  })
}

const DashboardPage = lazyNamed(() => import("@/pages/dashboard"), "DashboardPage")
const PostsPage = lazyNamed(() => import("@/pages/posts"), "PostsPage")
const PagesPage = lazyNamed(() => import("@/pages/posts"), "PagesPage")
const CategoriesPage = lazyNamed(() => import("@/pages/taxonomy"), "CategoriesPage")
const TagsPage = lazyNamed(() => import("@/pages/taxonomy"), "TagsPage")
const CommentsPage = lazyNamed(() => import("@/pages/comments"), "CommentsPage")
const UploadsPage = lazyNamed(() => import("@/pages/uploads"), "UploadsPage")
const MembersPage = lazyNamed(() => import("@/pages/members"), "MembersPage")
const ThemesPage = lazyNamed(() => import("@/pages/apps"), "ThemesPage")
const ModulesPage = lazyNamed(() => import("@/pages/apps"), "ModulesPage")
const PluginsPage = lazyNamed(() => import("@/pages/apps"), "PluginsPage")
const SettingsPage = lazyNamed(() => import("@/pages/settings"), "SettingsPage")
const NotFoundPage = lazyNamed(() => import("@/pages/not-found"), "NotFoundPage")

export function AppRouter() {
  return (
    <HashRouter>
      <Routes>
        <Route element={<AppShell />}>
          <Route index element={<DashboardPage />} />
          <Route path="posts" element={<PostsPage />} />
          <Route path="pages" element={<PagesPage />} />
          <Route path="categories" element={<CategoriesPage />} />
          <Route path="tags" element={<TagsPage />} />
          <Route path="comments" element={<CommentsPage />} />
          <Route path="uploads" element={<UploadsPage />} />
          <Route path="members" element={<MembersPage />} />
          <Route path="themes" element={<ThemesPage />} />
          <Route path="modules" element={<ModulesPage />} />
          <Route path="plugins" element={<PluginsPage />} />
          <Route path="settings" element={<SettingsPage />} />
          <Route path="*" element={<NotFoundPage />} />
        </Route>
      </Routes>
    </HashRouter>
  )
}
