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
      input: path.resolve(__dirname, "index.html"),
      output: {
        entryFileNames: "e/[name]-[hash].js",
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
