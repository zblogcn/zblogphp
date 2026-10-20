/**
 * DemoPanel 的 admin-next 前端产物（参考实现，无构建步骤）。
 *
 * 正式插件应用 Vite 构建，并把 react / react-dom / react-dom/client
 * 标记为 external，由宿主 import map 提供，避免多份 React 实例。
 *
 * 不构建的写法：宿主会注入 window.__ZB_PLUGIN_RUNTIME__，
 * 其中提供 createElement / Fragment 与 createRoot。
 */

function h(type, props, ...children) {
  return window.__ZB_PLUGIN_RUNTIME__.createElement(type, props, ...children)
}

const css = {
  card: {
    border: "1px solid var(--border)",
    borderRadius: "10px",
    padding: "16px",
    background: "var(--card)",
    fontSize: "13px",
  },
  muted: { color: "var(--muted-foreground)" },
  row: {
    display: "grid",
    gridTemplateColumns: "auto 1fr",
    gap: "8px 16px",
  },
  input: {
    flex: 1,
    height: "36px",
    padding: "0 12px",
    borderRadius: "8px",
    border: "1px solid var(--input)",
    background: "transparent",
    color: "inherit",
  },
  button: {
    height: "36px",
    padding: "0 16px",
    borderRadius: "8px",
    border: "none",
    background: "var(--primary)",
    color: "var(--primary-foreground)",
    cursor: "pointer",
  },
}

function Panel({ ctx, stats, error, text, reply, onText, onSubmit }) {
  return h(
    "div",
    { style: { display: "grid", gap: "16px" } },
    h(
      "div",
      { style: css.card },
      h("div", { style: { fontSize: "16px", fontWeight: 600 } }, "插件面板"),
      h(
        "div",
        { style: { ...css.muted, marginTop: "6px" } },
        "这是 " + ctx.appId + " 通过 admin 声明接入的页面。"
      )
    ),
    error ? h("div", { style: { color: "var(--destructive)" } }, "加载失败：" + error) : null,
    stats
      ? h(
          "dl",
          { style: { ...css.card, ...css.row } },
          ...[
            ["站点", stats.site],
            ["版本", stats.version],
            ["文章数", stats.articles],
            ["PHP", stats.server],
          ].flatMap(function (pair) {
            return [
              h("dt", { key: pair[0] + "-l", style: css.muted }, pair[0]),
              h("dd", { key: pair[0] + "-v", style: { margin: 0, textAlign: "right" } }, String(pair[1])),
            ]
          })
        )
      : h("div", { style: css.muted }, "加载中…"),
    h(
      "form",
      {
        onSubmit: onSubmit,
        style: { ...css.card, display: "flex", gap: "8px", alignItems: "center" },
      },
      h("input", {
        value: text,
        onChange: function (event) {
          onText(event.target.value)
        },
        placeholder: "输入内容，测试写接口",
        style: css.input,
      }),
      h("button", { type: "submit", style: css.button }, "提交")
    ),
    reply ? h("div", { style: css.muted }, "服务端回显：" + reply) : null
  )
}

function About({ ctx }) {
  return h(
    "div",
    { style: { ...css.card, lineHeight: 1.8 } },
    h("div", { style: { fontWeight: 600, marginBottom: "8px" } }, "关于示例"),
    h("div", { style: css.muted }, "当前子路由：" + ctx.route),
    h("div", { style: css.muted }, "插件目录：" + ctx.baseUrl),
    h("div", { style: css.muted }, "两个菜单项共用同一个入口文件，由 ctx.route 区分。")
  )
}

let cleanup = null

export function mount(el, ctx) {
  const R = window.__ZB_PLUGIN_RUNTIME__
  const useState = R.useState
  const useEffect = R.useEffect

  function Root() {
    const statsState = useState(null)
    const stats = statsState[0]
    const setStats = statsState[1]

    const errState = useState(null)
    const error = errState[0]
    const setError = errState[1]

    const textState = useState("")
    const text = textState[0]
    const setText = textState[1]

    const replyState = useState("")
    const reply = replyState[0]
    const setReply = replyState[1]

    useEffect(function () {
      ctx.api
        .get("demopanel", "stats")
        .then(setStats)
        .catch(function (err) {
          setError(err.message)
        })
    }, [])

    const onSubmit = function (event) {
      event.preventDefault()
      ctx.api
        .post("demopanel", "echo", { message: text })
        .then(function (result) {
          setReply(result.message)
          ctx.toast.success("已提交")
        })
        .catch(function (err) {
          ctx.toast.error(err.message)
        })
    }

    if (ctx.route.endsWith("/about")) {
      return h(About, { ctx: ctx })
    }

    return h(Panel, {
      ctx: ctx,
      stats: stats,
      error: error,
      text: text,
      reply: reply,
      onText: setText,
      onSubmit: onSubmit,
    })
  }

  const root = R.createRoot(el)
  root.render(h(Root))

  cleanup = function () {
    root.unmount()
  }
}

export function unmount() {
  if (cleanup) {
    cleanup()
    cleanup = null
  }
}
