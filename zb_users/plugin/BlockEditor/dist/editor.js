/**
 * BlockEditor —— admin-next 的区块编辑器实现。
 *
 * 契约（由 <admin><editor> 声明加载）：
 *   export function mount(el, ctx) => { getHTML, setHTML, focus, destroy }
 *
 * 不使用外部依赖：内容以 contenteditable 承载，命令走 document.execCommand
 * 的现代替代（insertHTML / formatBlock）。这样插件无构建步骤即可发布，
 * 体积也远小于完整 ProseMirror 打包。
 *
 * 必须保证的既有语义：
 *   - 分隔符在编辑器内显示为 <hr class="more" />，提交时由服务端转回 <!--more-->
 *   - 摘要由服务端推导，这里不重复计算
 */

const MORE_HTML = '<hr class="more" />'

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

  function insertHTML(html) {
    editor.focus()
    document.execCommand('insertHTML', false, html)
    updateStatus()
  }

  function wrapSelection(openTag, closeTag, placeholder) {
    const selection = window.getSelection()
    const text = selection && selection.toString() ? selection.toString() : placeholder
    insertHTML(openTag + text + closeTag)
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
        const url = result?.upload?.Url
        if (url) {
          insertHTML(`<img src="${url}" alt="" />`)
        }
      } catch (error) {
        ctx?.toast?.error?.(error.message || '上传失败')
      }
    }
    input.click()
  }

  function insertLink() {
    const url = window.prompt('链接地址')
    if (!url) {
      return
    }
    wrapSelection(`<a href="${url}">`, '</a>', '链接文字')
  }

  function insertCode() {
    wrapSelection('<pre><code>', '</code></pre>', '代码')
  }

  function insertMore() {
    insertHTML(MORE_HTML)
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
      editor.innerHTML = decodeMore(source.value)
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

  // 粘贴时去掉外部样式，只保留基本结构
  editor.addEventListener('paste', (event) => {
    const html = event.clipboardData?.getData('text/html')
    if (!html) {
      return
    }
    event.preventDefault()
    const cleaned = html
      .replace(/<(script|style)[\s\S]*?<\/\1>/gi, '')
      .replace(/\s(class|id|style|on\w+)="[^"]*"/gi, '')
    insertHTML(cleaned)
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
      return sourceMode ? source.value : encodeMore(editor.innerHTML)
    },
    setHTML(html) {
      const decoded = decodeMore(html)
      if (sourceMode) {
        source.value = encodeMore(decoded)
      } else {
        editor.innerHTML = decoded
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
      container.innerHTML = ''
    },
  }
}
