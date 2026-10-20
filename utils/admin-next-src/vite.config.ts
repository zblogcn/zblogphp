import path from "node:path"
import { defineConfig } from "vite"
import react from "@vitejs/plugin-react"
import tailwindcss from "@tailwindcss/vite"

export default defineConfig({
  plugins: [react(), tailwindcss()],
  base: "./",
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  build: {
    outDir: path.resolve(__dirname, "../../zb_system/admin-next/assets"),
    emptyOutDir: true,
    manifest: "manifest.json",
    rollupOptions: {
      input: {
        index: path.resolve(__dirname, "index.html"),
        // 插件共享运行时，固定文件名，供 import map 引用
        runtime: path.resolve(__dirname, "src/plugin-host/runtime.ts"),
      },
      output: {
        entryFileNames: (chunk) =>
          chunk.name === "runtime" ? "runtime.js" : "e/[name]-[hash].js",
        chunkFileNames: "e/[name]-[hash].js",
        assetFileNames: "e/[name]-[hash][extname]",
        manualChunks: {
          react: ["react", "react-dom", "react-router-dom"],
          query: ["@tanstack/react-query"],
          table: ["@tanstack/react-table", "@tanstack/react-virtual"],
        },
      },
    },
  },
})
