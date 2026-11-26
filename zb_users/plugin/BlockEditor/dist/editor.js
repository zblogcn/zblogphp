/**
 * BlockEditor —— admin-next 的区块编辑器实现。
 *
 * 契约（由 <admin><editor> 声明加载）：
 *   export function mount(el, ctx) => { getHTML, setHTML, focus, destroy }
 *
 * 不使用外部依赖：内容以 contenteditable 承载，格式化命令走
 * document.execCommand，插入内容走 DOM Range。这样插件无构建步骤即可发布，
 * 体积也远小于完整 ProseMirror 打包。
 *
 * 必须保证的既有语义：
 *   - 分隔符在编辑器内显示为 <hr class="more" />，提交时由服务端转回 <!--more-->
 *   - 摘要由服务端推导，这里不重复计算
 */

const MORE_HTML = '<hr class="more" />'

export const SAFE_LINK_PROTOCOLS = new Set(['http:', 'https:', 'mailto:', 'tel:'])
export const SAFE_IMAGE_PROTOCOLS = new Set(['http:', 'https:'])

const ALLOWED_ELEMENTS = new Set([
  'p', 'div', 'span', 'br', 'h2', 'h3', 'h4', 'h5', 'h6',
  'strong', 'b', 'i', 'em', 'u', 's', 'strike', 'del',
  'blockquote', 'ul', 'ol', 'li', 'pre', 'code',
  'a', 'img', 'hr', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
  'figure', 'figcaption', 'sup', 'sub',
])

const DROP_WITH_CONTENT = new Set([
  'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template',
  'noscript', 'noembed', 'noframes', 'xmp', 'plaintext',
  'form', 'input', 'button', 'textarea', 'select', 'option', 'optgroup',
  'link', 'meta', 'base', 'title', 'head',
])

const ALLOWED_ATTRIBUTES = {
  a: ['href', 'title'],
  img: ['src', 'alt', 'width', 'height'],
  ol: ['start'],
  th: ['colspan', 'rowspan', 'scope'],
  td: ['colspan', 'rowspan'],
  hr: ['class'],
}

/** 解析并校验 URL；返回规范化后的安全地址，非法协议返回 null。 */
export function sanitizeUrl(
  value,
  baseURI = globalThis.location?.href ?? globalThis.document?.baseURI,
  protocols = SAFE_LINK_PROTOCOLS
) {
  if (typeof value !== 'string' || value.trim() === '') {
    return null
  }

  try {
    const url = new URL(value.trim(), baseURI)
    return protocols.has(url.protocol.toLowerCase()) ? url.href : null
  } catch {
    return null
  }
}

function removeComments(node) {
  for (const child of Array.from(node.childNodes)) {
    if (child.nodeType === 8) {
      child.remove()
    } else if (child.nodeType === 1 || child.nodeType === 11) {
      removeComments(child)
    }
  }
}

function sanitizeTree(root, baseURI) {
  for (const node of Array.from(root.querySelectorAll('*'))) {
    const tag = node.tagName.toLowerCase()

    if (DROP_WITH_CONTENT.has(tag)) {
      node.remove()
      continue
    }

    if (!ALLOWED_ELEMENTS.has(tag)) {
      node.replaceWith(...Array.from(node.childNodes))
      continue
    }

    const allowedAttributes = ALLOWED_ATTRIBUTES[tag] ?? []
    for (const attribute of Array.from(node.attributes)) {
      const name = attribute.name.toLowerCase()
      if (!allowedAttributes.includes(name)) {
        node.removeAttribute(attribute.name)
        continue
      }

      if (name === 'href' || name === 'src') {
        const protocols = name === 'src' ? SAFE_IMAGE_PROTOCOLS : SAFE_LINK_PROTOCOLS
        const safeUrl = sanitizeUrl(attribute.value, baseURI, protocols)
        if (safeUrl === null) {
          node.removeAttribute(attribute.name)
        } else {
          node.setAttribute(name, safeUrl)
        }
      } else if (name === 'class') {
        if (tag === 'hr' && attribute.value.split(/\s+/).includes('more')) {
          node.setAttribute('class', 'more')
        } else {
          node.removeAttribute(attribute.name)
        }
      } else if (['width', 'height', 'colspan', 'rowspan'].includes(name)) {
        if (!/^\d{1,4}$/.test(attribute.value)) {
          node.removeAttribute(attribute.name)
        }
      } else if (name === 'start' && !/^-?\d{1,4}$/.test(attribute.value)) {
        node.removeAttribute(attribute.name)
      } else if (name === 'scope' && !['col', 'row'].includes(attribute.value)) {
        node.removeAttribute(attribute.name)
      }
    }

    if (tag === 'img' && !node.hasAttribute('src')) {
      node.remove()
    }
  }

  removeComments(root)
}

function parseSanitizedBody(html, documentInstance) {
  const Parser = documentInstance?.defaultView?.DOMParser ?? globalThis.DOMParser
  if (typeof Parser !== 'function') {
    return null
  }

  const parsed = new Parser().parseFromString(String(html ?? ''), 'text/html')
  if (!parsed.body) {
    return null
  }

  sanitizeTree(parsed.body, documentInstance?.baseURI ?? globalThis.location?.href)
  return parsed.body
}

/** 返回净化后的 HTML 字符串，供源码模式使用。 */
export function sanitizeHTML(html, documentInstance = globalThis.document) {
  const body = parseSanitizedBody(html, documentInstance)
  return body ? body.innerHTML : ''
}

/** 返回净化后的 DocumentFragment，直接插入实时 DOM，避免序列化后再解析。 */
export function sanitizeFragment(html, documentInstance = globalThis.document) {
  const body = parseSanitizedBody(html, documentInstance)
  if (!body || typeof documentInstance?.createDocumentFragment !== 'function') {
    return null
  }

  const fragment = documentInstance.createDocumentFragment()
  while (body.firstChild) {
    fragment.appendChild(body.firstChild)
  }
  return fragment
}

/** 创建安全链接节点；URL 协议不安全时返回 null。 */
export function createLink(value, text, documentInstance = globalThis.document) {
  const url = sanitizeUrl(value, documentInstance?.baseURI)
  if (!url || typeof documentInstance?.createElement !== 'function') {
    return null
  }

  const link = documentInstance.createElement('a')
  link.setAttribute('href', url)
  link.textContent = String(text ?? '')
  return link
}

/** 用 hr.more 表示 <!--more-->，读出时反转。 */
function decodeMore(html) {
  return String(html || "")
    .replace(/<!--more-->/gi, MORE_HTML)
    .replace(/&lt;!--\s*more\s*--&gt;/gi, MORE_HTML)
}

function encodeMore(html) {
  return String(html || '').replace(/<hr class="more"\s*\/?>/gi, '<!--more-->')
}

/** 工具栏按钮定义：label 用图标化文本，title 提供可访问名称。 */
const TOOLBAR = [
  { cmd: 'formatBlock', value: 'h2', label: 'H2', title: '二级标题' },
  { cmd: 'formatBlock', value: 'h3', label: 'H3', title: '三级标题' },
  { cmd: 'formatBlock', value: 'p', label: '¶', title: '正文' },
  { sep: true },
  { cmd: 'bold', label: 'B', title: '加粗', className: 'font-bold' },
  { cmd: 'italic', label: 'I', title: '斜体', className: 'italic' },
  { cmd: 'strikeThrough', label: 'S', title: '删除线', className: 'line-through' },
  { sep: true },
  { cmd: 'insertUnorderedList', label: '•', title: '无序列表' },
  { cmd: 'insertOrderedList', label: '1.', title: '有序列表' },
  { cmd: 'formatBlock', value: 'blockquote', label: '❞', title: '引用' },
  { sep: true },
  { insert: 'link', label: '🔗', title: '插入链接' },
  { insert: 'image', label: '🖼', title: '插入图片' },
  { insert: 'code', label: '</>', title: '代码块' },
  { insert: 'more', label: '✂', title: '插入更多分隔符' },
  { sep: true },
  { action: 'source', label: '源码', title: '切换源码模式' },
  { action: 'undo', label: '↶', title: '撤销' },
  { action: 'redo', label: '↷', title: '重做' },
]

function el(tag, attrs = {}, children = []) {
  const node = document.createElement(tag)
  for (const [key, value] of Object.entries(attrs)) {
    if (key === 'className') {
      node.className = value
    } else if (key === 'text') {
      node.textContent = value
    } else if (key.startsWith('on') && typeof value === 'function') {
      node.addEventListener(key.slice(2).toLowerCase(), value)
    } else {
      node.setAttribute(key, value)
    }
  }
  for (const child of children) {
    node.appendChild(child)
  }
  return node
}

export function mount(container, ctx) {
  const api = ctx?.api
  let sourceMode = false
  let destroyed = false

  // ---- DOM 骨架 -------------------------------------------------------
  const toolbar = el('div', { className: 'zbpe-toolbar', role: 'toolbar' })

  const editor = el('div', {
    className: 'zbpe-content',
    contenteditable: 'true',
    role: 'textbox',
    'aria-multiline': 'true',
    'aria-label': '正文',
    spellcheck: 'false',
  })

  const source = el('textarea', {
    className: 'zbpe-source',
    'aria-label': '源码',
    spellcheck: 'false',
  })
  source.hidden = true

  const status = el('div', { className: 'zbpe-status', 'aria-live': 'polite' })

  const root = el('div', { className: 'zbpe' }, [toolbar, editor, source, status])
  container.appendChild(root)

  // ---- 命令 -----------------------------------------------------------
  function exec(cmd, value) {
    editor.focus()
    document.execCommand(cmd, false, value)
    updateStatus()
  }

  function insertFragment(fragment) {
    if (!fragment || !fragment.firstChild) {
      return
    }

    editor.focus()
    const selection = window.getSelection()
    let range = null

    if (selection && selection.rangeCount > 0) {
      const selectedRange = selection.getRangeAt(0)
      if (editor.contains(selectedRange.commonAncestorContainer)) {
        range = selectedRange
      }
    }

    if (!range) {
      range = document.createRange()
      range.selectNodeContents(editor)
      range.collapse(false)
    }

    const lastNode = fragment.lastChild
    range.deleteContents()
    range.insertNode(fragment)

    if (lastNode) {
      range.setStartAfter(lastNode)
      range.collapse(true)
      if (selection) {
        selection.removeAllRanges()
        selection.addRange(range)
      }
    }

    updateStatus()
  }

  function insertNode(node) {
    const fragment = document.createDocumentFragment()
    fragment.appendChild(node)
    insertFragment(fragment)
  }

  function selectedText(placeholder) {
    const selection = window.getSelection()
    return selection && selection.toString() ? selection.toString() : placeholder
  }

  async function insertImage() {
    // 走宿主的上传接口，选中文件后插入图片
    const input = el('input', { type: 'file', accept: 'image/*' })
    input.onchange = async () => {
      const file = input.files && input.files[0]
      if (!file) {
        return
      }
      if (!api || typeof api.upload !== 'function') {
        ctx?.toast?.error?.('当前环境不支持上传')
        return
      }
      try {
        const form = new FormData()
        form.append('file', file)
        form.set('auto_rename', '1')
        const result = await api.upload('upload', 'post', form)
        const url = sanitizeUrl(result?.upload?.Url, undefined, SAFE_IMAGE_PROTOCOLS)
        if (!url) {
          ctx?.toast?.error?.('上传返回了不安全的图片地址')
          return
        }

        const image = document.createElement('img')
        image.setAttribute('src', url)
        image.setAttribute('alt', '')
        insertNode(image)
      } catch (error) {
        ctx?.toast?.error?.(error.message || '上传失败')
      }
    }
    input.click()
  }

  function insertLink() {
    const value = window.prompt('链接地址')
    if (!value) {
      return
    }

    const link = createLink(value, selectedText('链接文字'))
    if (!link) {
      ctx?.toast?.error?.('链接地址协议不安全')
      return
    }

    insertNode(link)
  }

  function insertCode() {
    const pre = document.createElement('pre')
    const code = document.createElement('code')
    code.textContent = selectedText('代码')
    pre.appendChild(code)
    insertNode(pre)
  }

  function insertMore() {
    const separator = document.createElement('hr')
    separator.setAttribute('class', 'more')
    insertNode(separator)
  }

  // ---- 工具栏 ---------------------------------------------------------
  for (const item of TOOLBAR) {
    if (item.sep) {
      toolbar.appendChild(el('span', { className: 'zbpe-sep', 'aria-hidden': 'true' }))
      continue
    }

    const button = el('button', {
      type: 'button',
      className: 'zbpe-btn ' + (item.className || ''),
      title: item.title,
      'aria-label': item.title,
      text: item.label,
    })

    button.onclick = (event) => {
      event.preventDefault()
      if (item.cmd) {
        exec(item.cmd, item.value)
      } else if (item.insert === 'link') {
        insertLink()
      } else if (item.insert === 'image') {
        insertImage()
      } else if (item.insert === 'code') {
        insertCode()
      } else if (item.insert === 'more') {
        insertMore()
      } else if (item.action === 'source') {
        toggleSource()
      } else if (item.action === 'undo') {
        exec('undo')
      } else if (item.action === 'redo') {
        exec('redo')
      }
    }

    toolbar.appendChild(button)
  }

  // ---- 源码模式 -------------------------------------------------------
  function toggleSource() {
    sourceMode = !sourceMode
    if (sourceMode) {
      source.value = encodeMore(editor.innerHTML)
      source.hidden = false
      editor.hidden = true
      source.focus()
    } else {
      editor.replaceChildren(sanitizeFragment(decodeMore(source.value)))
      source.hidden = true
      editor.hidden = false
      editor.focus()
    }
    updateStatus()
  }

  // ---- 状态栏 ---------------------------------------------------------
  function updateStatus() {
    const html = sourceMode ? source.value : editor.innerHTML
    const chars = html.replace(/<[^>]*>/g, '').length
    status.textContent = `${chars} 字 · ${sourceMode ? '源码模式' : '编辑模式'}`
  }

  editor.addEventListener('input', updateStatus)
  source.addEventListener('input', updateStatus)

  // 粘贴 HTML 按白名单净化后直接插入，不使用正则清理或 HTML 字符串拼接
  editor.addEventListener('paste', (event) => {
    const html = event.clipboardData?.getData('text/html')
    if (!html) {
      return
    }
    event.preventDefault()
    insertFragment(sanitizeFragment(html))
  })

  // 快捷键：Ctrl/Cmd+S 交给宿主保存
  editor.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
      event.preventDefault()
      ctx?.save?.()
    }
  })

  updateStatus()

  // ---- 对外接口 -------------------------------------------------------
  return {
    getHTML() {
      return sourceMode ? encodeMore(sanitizeHTML(source.value)) : encodeMore(editor.innerHTML)
    },
    setHTML(html) {
      const decoded = decodeMore(html)
      if (sourceMode) {
        source.value = encodeMore(sanitizeHTML(decoded))
      } else {
        editor.replaceChildren(sanitizeFragment(decoded))
      }
      updateStatus()
    },
    focus() {
      ;(sourceMode ? source : editor).focus()
    },
    insertMore,
    destroy() {
      if (destroyed) {
        return
      }
      destroyed = true
      container.replaceChildren()
    },
  }
}
