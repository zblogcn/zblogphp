import assert from "node:assert/strict"
import { readFile } from "node:fs/promises"
import test from "node:test"

import {
  canLoadPluginRoute,
  findAppRoute,
} from "../utils/admin-next-src/src/plugin-host/route.js"
const root = new URL("../", import.meta.url)
const editorSource = await readFile(
  new URL("zb_users/plugin/BlockEditor/dist/editor.js", root),
  "utf8"
)
const {
  SAFE_IMAGE_PROTOCOLS,
  SAFE_LINK_PROTOCOLS,
  createLink,
  sanitizeHTML,
  sanitizeUrl,
} = await import(
  new URL("../zb_users/plugin/BlockEditor/dist/editor.js", import.meta.url).href
)

const app = {
  appId: "DemoPanel",
  type: "plugin",
  name: "Demo",
  version: "1.0",
  baseUrl: "/zb_users/plugin/DemoPanel/",
  entry: "admin.js",
  style: "",
  legacy: "",
  menu: [
    { id: "panel", title: "Panel", route: "/plugin/DemoPanel", require: "admin" },
    { id: "about", title: "About", route: "/plugin/DemoPanel/about", require: null },
  ],
}

function hasRight(rights) {
  return (action) => action === null || rights[action] === true
}

test("plugin route uses an exact declared menu route", () => {
  const matched = findAppRoute([app], "/plugin/DemoPanel/about")

  assert.equal(matched.app, app)
  assert.equal(matched.route, "/about")
  assert.equal(matched.menu, app.menu[1])
  assert.equal(canLoadPluginRoute(matched, hasRight({})), true)
})

test("unknown plugin child route is denied by default", () => {
  const matched = findAppRoute([app], "/plugin/DemoPanel/not-declared")

  assert.equal(matched.app, app)
  assert.equal(matched.menu, null)
  assert.equal(canLoadPluginRoute(matched, hasRight({ admin: true })), false)
})

test("plugin bundle requires the menu permission before loading", () => {
  const matched = findAppRoute([app], "/plugin/DemoPanel")

  assert.equal(canLoadPluginRoute(matched, hasRight({})), false)
  assert.equal(canLoadPluginRoute(matched, hasRight({ admin: true })), true)
})

test("plugin namespace matching does not accept sibling prefixes", () => {
  assert.equal(findAppRoute([app], "/plugin/DemoPanelExtra"), null)
})

test("PluginPage guards dynamic import with route authorization", async () => {
  const source = await readFile(
    new URL("utils/admin-next-src/src/pages/plugin-page.tsx", root),
    "utf8"
  )
  const effectStart = source.indexOf("useEffect(() => {")
  const guard = source.indexOf("if (!app || !canLoad)", effectStart)
  const load = source.indexOf("loadPluginBundle(app)", effectStart)

  assert.ok(effectStart >= 0)
  assert.ok(guard > effectStart, "authorization guard must run inside the effect")
  assert.ok(load > guard, "bundle import must happen after the authorization guard")
})

test("link URLs allow only safe protocols and relative URLs", () => {
  const base = "https://site.example/posts/edit"

  for (const protocol of SAFE_LINK_PROTOCOLS) {
    assert.equal(sanitizeUrl(`${protocol}//site.example/path`, base), `${protocol}//site.example/path`)
  }
  assert.equal(sanitizeUrl("/relative", base), "https://site.example/relative")
  assert.equal(sanitizeUrl("#section", base), "https://site.example/posts/edit#section")
  assert.equal(sanitizeUrl("java\nscript:alert(1)", base), null)
  assert.equal(sanitizeUrl("data:text/html,<script>alert(1)</script>", base), null)
  assert.equal(sanitizeUrl("vbscript:msgbox(1)", base), null)
  assert.equal(sanitizeUrl("file:///etc/passwd", base), null)
})

test("inserted links reject active URLs and keep selection text inert", () => {
  const document = createTestDocument()

  assert.equal(createLink("javascript:alert(1)", "bad", document), null)
  assert.equal(createLink("data:text/html,x", "bad", document), null)

  const link = createLink("https://example.com/path", '<img src=x onerror="alert(1)">', document)
  assert.equal(link.getAttribute("href"), "https://example.com/path")
  assert.equal(link.textContent, '<img src=x onerror="alert(1)">')
  assert.equal(link.innerHTML, '&lt;img src=x onerror="alert(1)"&gt;')
})

test("image URLs reject non-HTTP protocols", () => {
  const base = "https://site.example/posts/edit"

  assert.equal(sanitizeUrl("/image.png", base, SAFE_IMAGE_PROTOCOLS), "https://site.example/image.png")
  assert.equal(sanitizeUrl("mailto:admin@example.com", base, SAFE_IMAGE_PROTOCOLS), null)
  assert.equal(sanitizeUrl("data:image/svg+xml,<svg onload=alert(1)>", base, SAFE_IMAGE_PROTOCOLS), null)
})

test("HTML sanitizer keeps allowlisted structure and removes active content", () => {
  const document = createTestDocument()
  const input = [
    '<p class="past" style="color:red" onclick="alert(1)">Hello <strong>world</strong></p>',
    '<script>alert(1)</script>',
    '<iframe src="https://evil.test"></iframe>',
    '<section><em>safe</em></section>',
  ].join("")

  assert.equal(
    sanitizeHTML(input, document),
    "<p>Hello <strong>world</strong></p><em>safe</em>"
  )
})

test("HTML sanitizer removes unsafe URL attributes and dangerous images", () => {
  const document = createTestDocument()
  const input = [
    '<a href="javascript:alert(1)" onclick="alert(1)">bad</a>',
    '<a href="https://example.com/path" title="ok">good</a>',
    '<img src="data:text/html,x" onerror="alert(1)">',
    '<img src="/safe.png" alt="safe">',
    '<hr class="more evil" onload="alert(1)">',
  ].join("")

  assert.equal(
    sanitizeHTML(input, document),
    '<a>bad</a><a href="https://example.com/path" title="ok">good</a>'
      + '<img src="https://site.example/safe.png" alt="safe"><hr class="more">'
  )
})

test("HTML sanitizer removes comments and foreign content", () => {
  const document = createTestDocument()

  assert.equal(
    sanitizeHTML('<!-- secret --><svg><script>alert(1)</script></svg><p>safe</p>', document),
    "<p>safe</p>"
  )
})

test("editor handlers use sanitization instead of untrusted HTML concatenation", async () => {
  const source = await readFile(
    new URL("zb_users/plugin/BlockEditor/dist/editor.js", root),
    "utf8"
  )

  assert.doesNotMatch(source, /openTag\s*\+\s*text\s*\+\s*closeTag/)
  assert.doesNotMatch(source, /<a href="\$\{url\}">/)
  assert.match(source, /function insertLink\(\)[\s\S]*?createLink\(value, selectedText/)
  assert.doesNotMatch(source, /\.replace\(\/<\(script\|style\)/)
  assert.match(source, /addEventListener\(["']paste["'][\s\S]*?sanitizeFragment\(html\)/)
  assert.match(source, /editor\.replaceChildren\(sanitizeFragment\(decodeMore\(source\.value\)\)\)/)
  assert.match(source, /setHTML\(html\)[\s\S]*?sanitizeFragment\(decoded\)/)
})

/**
 * Minimal DOM used only by the dependency-free Node tests. Production uses
 * the browser DOMParser; this fixture exercises the sanitizer's tree walk.
 */
class TestNode {
  constructor(nodeType) {
    this.nodeType = nodeType
    this.parentNode = null
    this.childNodes = []
  }

  get firstChild() {
    return this.childNodes[0] ?? null
  }

  get lastChild() {
    return this.childNodes.at(-1) ?? null
  }

  appendChild(node) {
    node.remove()
    node.parentNode = this
    this.childNodes.push(node)
    return node
  }

  insertBefore(node, reference) {
    node.remove()
    node.parentNode = this
    const index = this.childNodes.indexOf(reference)
    this.childNodes.splice(index < 0 ? this.childNodes.length : index, 0, node)
    return node
  }

  removeChild(node) {
    const index = this.childNodes.indexOf(node)
    if (index >= 0) {
      this.childNodes.splice(index, 1)
      node.parentNode = null
    }
    return node
  }

  remove() {
    this.parentNode?.removeChild(this)
  }

  get textContent() {
    return this.childNodes.map((child) => child.textContent).join("")
  }
}

class TestText extends TestNode {
  constructor(data) {
    super(3)
    this.data = data
  }

  get textContent() {
    return this.data
  }
}

class TestComment extends TestNode {
  constructor(data) {
    super(8)
    this.data = data
  }

  get textContent() {
    return this.data
  }
}

class TestFragment extends TestNode {
  constructor() {
    super(11)
  }

  get innerHTML() {
    return this.childNodes.map(serializeNode).join("")
  }
}

class TestElement extends TestNode {
  constructor(tagName) {
    super(1)
    this.tagName = tagName.toUpperCase()
    this.attributes = []
  }

  setAttribute(name, value) {
    const attribute = this.attributes.find((item) => item.name === name)
    if (attribute) {
      attribute.value = String(value)
    } else {
      this.attributes.push({ name, value: String(value) })
    }
  }

  getAttribute(name) {
    return this.attributes.find((item) => item.name === name)?.value ?? null
  }

  hasAttribute(name) {
    return this.attributes.some((item) => item.name === name)
  }

  removeAttribute(name) {
    this.attributes = this.attributes.filter((item) => item.name !== name)
  }

  get textContent() {
    return super.textContent
  }

  set textContent(value) {
    this.childNodes = []
    if (String(value) !== "") {
      this.appendChild(new TestText(String(value)))
    }
  }

  get className() {
    return this.getAttribute("class") ?? ""
  }

  set className(value) {
    this.setAttribute("class", value)
  }

  replaceWith(...nodes) {
    if (!this.parentNode) {
      return
    }
    const parent = this.parentNode
    for (const node of nodes) {
      parent.insertBefore(node, this)
    }
    this.remove()
  }

  querySelectorAll(selector) {
    assert.equal(selector, "*")
    const result = []
    const visit = (node) => {
      for (const child of node.childNodes) {
        if (child.nodeType === 1) {
          result.push(child)
          visit(child)
        }
      }
    }
    visit(this)
    return result
  }

  get innerHTML() {
    return this.childNodes.map(serializeNode).join("")
  }

  set innerHTML(html) {
    this.childNodes = []
    parseInto(this, html)
  }
}

class TestDocument {
  constructor() {
    this.baseURI = "https://site.example/posts/edit"
    this.body = new TestElement("body")
    this.defaultView = {
      DOMParser: class {
        parseFromString(html) {
          const document = new TestDocument()
          parseInto(document.body, html)
          return document
        }
      },
    }
  }

  createElement(tagName) {
    return new TestElement(tagName)
  }

  createDocumentFragment() {
    return new TestFragment()
  }
}

const voidTags = new Set(["BR", "HR", "IMG"])

function parseInto(root, html) {
  const stack = [root]
  let index = 0

  while (index < html.length) {
    const current = stack.at(-1)
    const rawTextTag = current.tagName === "SCRIPT" || current.tagName === "STYLE"
    if (rawTextTag) {
      const close = html.toLowerCase().indexOf(`</${current.tagName.toLowerCase()}`, index)
      if (close < 0) {
        current.appendChild(new TestText(html.slice(index)))
        break
      }
      if (close > index) {
        current.appendChild(new TestText(html.slice(index, close)))
        index = close
        continue
      }
    }

    if (html.startsWith("<!--", index)) {
      const end = html.indexOf("-->", index + 4)
      const stop = end < 0 ? html.length : end + 3
      current.appendChild(new TestComment(html.slice(index + 4, end < 0 ? stop : end)))
      index = stop
      continue
    }

    if (html[index] === "<") {
      const close = findTagEnd(html, index + 1)
      if (close < 0) {
        current.appendChild(new TestText(html.slice(index)))
        break
      }
      const token = html.slice(index + 1, close)
      if (token.startsWith("/")) {
        const tagName = token.slice(1).trim().split(/\s/)[0].toUpperCase()
        for (let stackIndex = stack.length - 1; stackIndex > 0; stackIndex -= 1) {
          if (stack[stackIndex].tagName === tagName) {
            stack.length = stackIndex
            break
          }
        }
      } else if (!token.startsWith("!") && !token.startsWith("?")) {
        const selfClosing = token.endsWith("/")
        const name = token.match(/^([A-Za-z][\w:-]*)/)?.[1]
        if (name) {
          const element = new TestElement(name)
          const attributeSource = token.slice(name.length, selfClosing ? -1 : undefined)
          const attributePattern = /([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g
          for (const match of attributeSource.matchAll(attributePattern)) {
            element.setAttribute(match[1], match[2] ?? match[3] ?? match[4] ?? "")
          }
          current.appendChild(element)
          if (!selfClosing && !voidTags.has(element.tagName)) {
            stack.push(element)
          }
        }
      }
      index = close + 1
      continue
    }

    const next = html.indexOf("<", index)
    const stop = next < 0 ? html.length : next
    current.appendChild(new TestText(html.slice(index, stop)))
    index = stop
  }
}

function findTagEnd(html, start) {
  let quote = ""
  for (let index = start; index < html.length; index += 1) {
    const character = html[index]
    if (quote) {
      if (character === quote) {
        quote = ""
      }
    } else if (character === '"' || character === "'") {
      quote = character
    } else if (character === ">") {
      return index
    }
  }
  return -1
}

function serializeNode(node) {
  if (node.nodeType === 3) {
    return escapeText(node.data)
  }
  if (node.nodeType === 8) {
    return `<!--${node.data}-->`
  }
  if (node.nodeType !== 1) {
    return node.childNodes.map(serializeNode).join("")
  }

  const attributes = node.attributes
    .map(({ name, value }) => ` ${name}="${escapeAttribute(value)}"`)
    .join("")
  const open = `<${node.tagName.toLowerCase()}${attributes}>`
  if (voidTags.has(node.tagName)) {
    return open
  }
  return `${open}${node.innerHTML}</${node.tagName.toLowerCase()}>`
}

function escapeText(value) {
  return value.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")
}

function escapeAttribute(value) {
  return escapeText(value).replaceAll('"', "&quot;")
}

function createTestDocument() {
  return new TestDocument()
}
