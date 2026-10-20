import { create } from "zustand"
import { persist } from "zustand/middleware"

type Theme = "light" | "dark" | "system"

interface UiState {
  sidebarCollapsed: boolean
  theme: Theme
  toggleSidebar: () => void
  setSidebar: (collapsed: boolean) => void
  setTheme: (theme: Theme) => void
  applyTheme: () => void
}

function resolveDark(theme: Theme): boolean {
  if (theme === "dark") {
    return true
  }
  if (theme === "light") {
    return false
  }
  return window.matchMedia("(prefers-color-scheme: dark)").matches
}

export const useUiStore = create<UiState>()(
  persist(
    (set, get) => ({
      sidebarCollapsed: false,
      theme: "system",
      toggleSidebar: () => set({ sidebarCollapsed: !get().sidebarCollapsed }),
      setSidebar: (collapsed) => set({ sidebarCollapsed: collapsed }),
      setTheme: (theme) => {
        set({ theme })
        get().applyTheme()
      },
      applyTheme: () => {
        const dark = resolveDark(get().theme)
        document.documentElement.classList.toggle("dark", dark)
        // 首屏前脚本读取的键，保持两边一致
        try {
          localStorage.setItem("zbp-admin-theme", dark ? "dark" : "light")
        } catch {
          /* ignore */
        }
      },
    }),
    {
      name: "zbp-admin-ui",
      partialize: (state) => ({
        sidebarCollapsed: state.sidebarCollapsed,
        theme: state.theme,
      }),
    }
  )
)
