/**
 * 插件共享运行时。
 *
 * 插件 bundle（无论用 Vite 构建还是手写 ESM）都通过两个通道拿到 React：
 *   1. 裸模块名 `react` / `react-dom/client` → 由 index.html 的 import map 指向这里
 *   2. 全局 `window.__ZB_PLUGIN_RUNTIME__` → 给不构建、直接写 ESM 的插件用
 *
 * 两条通道指向同一份模块实例，因此不会出现 "invalid hook call"。
 */
import * as React from "react"
import * as ReactDOM from "react-dom"
import * as ReactDOMClient from "react-dom/client"
import { toast } from "sonner"

declare global {
  interface Window {
    __ZB_PLUGIN_RUNTIME__?: typeof React & typeof ReactDOMClient
  }
}

export const runtime = {
  ...React,
  ...ReactDOMClient,
}

export const toastApi = toast

if (typeof window !== "undefined") {
  window.__ZB_PLUGIN_RUNTIME__ = runtime
}

export { React, ReactDOM, ReactDOMClient }
