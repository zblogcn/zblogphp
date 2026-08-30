(function (win, $) {
  'use strict';
  /**
   * 模块：Admin2 通用脚本（纯 JS 版本）
   * 作用：初始化 ZBP 实例、加载/应用后台配置、绑定常用交互与工具函数。
   * 设计：
   * - 优先使用前端注入的 `window.__ADMIN_JS_CONFIG__`；否则通过 Ajax 从后端接口拉取配置。
   * - 始终先以默认配置进行同步初始化，保证旧模板/脚本在 DOMReady 前即可使用必要变量。
   * - 暴露与旧版保持一致的全局函数以兼容历史模板（如 SetCookie、VerifyMessage 等）。
   */

  /**
   * 默认站点地址（结尾含斜杠）
   */
  const DEFAULT_HOST = (win.location && win.location.origin ? win.location.origin : '') + '/';

  /**
   * 默认配置对象（作为同步启动与后端接口失败时的兜底）
   * - options：ZBP 初始化所需选项
   * - lang：常用文案与错误提示
   * - extraScripts：后端可注入的额外脚本片段（字符串数组）
   */
  const DEFAULT_CONFIG = {
    options: {
      bloghost: DEFAULT_HOST,
      blogversion: '',
      ajaxurl: DEFAULT_HOST + 'zb_system/cmd.php?act=ajax&src=',
      cookiepath: '/',
      comment: {
        useDefaultEvents: false,
        inputs: {}
      }
    },
    lang: {
      msg: {
        notify: 'Notification',
        refresh_cache: 'Please refresh cache',
        operation_failed: 'Operation failed',
        batch_operation_in_progress: 'Batch operation in progress...'
      },
      error: {
        '94': 'Login status expired, please refresh and retry. (%s)'
      }
    },
    extraScripts: []
  };

  /**
   * 运行时状态容器：
   * - config：标准化后的配置对象
   * - zbp：ZBP 实例（通过配置初始化）
   * - lang：语言包引用，供 `getLang` 使用
   * - extraScripts：需在初始化后执行的脚本列表
   */
  const runtime = {
    config: null,
    zbp: null,
    lang: DEFAULT_CONFIG.lang,
    extraScripts: []
  };

  /**
   * 初始化就绪信号：Deferred 用于串联异步配置加载后的逻辑。
   */
  const runtimeReady = $.Deferred();

  // 先按默认配置进行同步初始化，保证基础变量与方法可用
  applyConfig(normalizeConfig(DEFAULT_CONFIG));

  // 异步加载配置，成功后再应用覆盖，并触发就绪
  loadConfig()
    .then(applyConfig)
    .done(function (cfg) {
      runtimeReady.resolve(cfg);
    })
    .fail(function (err) {
      console.warn('Admin JS config request failed, using defaults.', err);
      const cfg = normalizeConfig(DEFAULT_CONFIG);
      applyConfig(cfg);
      runtimeReady.resolve(cfg);
    });

  /**
   * 异步从后端获取管理端运行配置
   * 返回：Deferred Promise，resolve 值为标准化配置对象
   */
  function loadConfig() {
    const deferred = $.Deferred();

    if (win.__ADMIN_JS_CONFIG__) {
      deferred.resolve(normalizeConfig(win.__ADMIN_JS_CONFIG__));
      return deferred.promise();
    }

    const url = win.__ADMIN_JS_CONFIG_URL__ || `${DEFAULT_HOST}zb_system/cmd.php?act=ajax&src=admin2`;

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'json',
      cache: false
    }).done(function (resp) {
      deferred.resolve(normalizeConfig(resp && resp.data ? resp.data : resp));
    }).fail(function (xhr) {
      deferred.reject(xhr);
    });

    return deferred.promise();
  }

  /**
   * 标准化配置对象结构，确保 `options/lang/extraScripts` 字段完整。
   * @param {Object} config - 可能为空或字段不全的原始配置
   * @returns {Object} normalized - 可直接用于 `applyConfig` 的配置
   */
  function normalizeConfig(config) {
    const normalized = {
      options: assignDeep({}, DEFAULT_CONFIG.options, config && config.options),
      lang: assignDeep({}, DEFAULT_CONFIG.lang, config && config.lang),
      extraScripts: []
    };

    if (config && Array.isArray(config.extraScripts)) {
      normalized.extraScripts = config.extraScripts.slice();
    }

    return normalized;
  }

  /**
   * 简易深拷贝合并：将多个对象按层级合并到目标对象。
   * 数组采用浅拷贝（slice），对象递归合并，基本类型覆盖。
   */
  function assignDeep(target) {
    target = target || {};
    for (let i = 1; i < arguments.length; i++) {
      const source = arguments[i];
      if (!source || typeof source !== 'object') {
        continue;
      }
      Object.keys(source).forEach(function (key) {
        const value = source[key];
        if (Array.isArray(value)) {
          target[key] = value.slice();
        } else if (value && typeof value === 'object') {
          target[key] = assignDeep(target[key] && typeof target[key] === 'object' ? target[key] : {}, value);
        } else {
          target[key] = value;
        }
      });
    }
    return target;
  }

  /**
   * 应用配置并初始化 zbp 实例；将常用字段挂到 `window` 以兼容旧模板；
   * 同时执行后端注入的 `extraScripts` 片段。
   * @param {Object} config - 标准化后的配置
   * @returns {Object} config - 原样返回便于链式调用
   */
  function applyConfig(config) {
    runtime.config = config;
    runtime.lang = config.lang;
    runtime.extraScripts = config.extraScripts || [];

    runtime.zbp = new ZBP({
      bloghost: config.options.bloghost,
      blogversion: config.options.blogversion,
      ajaxurl: config.options.ajaxurl,
      cookiepath: config.options.cookiepath,
      comment: config.options.comment
    });

    win.zbp = runtime.zbp;
    win.bloghost = runtime.zbp.options.bloghost;
    win.cookiespath = runtime.zbp.options.cookiepath;
    win.ajaxurl = runtime.zbp.options.ajaxurl;

    runtime.extraScripts.forEach(runSnippet);

    return config;
  }

  /**
   * 执行后端注入的额外脚本片段
   * @param {string} code JS 源码字符串
   * 注意：仅用于可信后端注入，前端不接收用户输入以避免 XSS。
   */
  function runSnippet(code) {
    if (!code) {
      return;
    }
    try {
      new Function(code)();
    } catch (err) {
      console.error('Admin extra script execution failed.', err);
    }
  }

  /**
   * 语言项读取工具：按路径数组（如 ['msg','notify']）递进查找。
   * @param {Array<string>} path - 语言项路径
   * @param {*} fallback - 找不到时返回的默认值
   */
  function getLang(path, fallback) {
    let node = runtime.lang;
    for (let i = 0; i < path.length; i++) {
      if (node && typeof node === 'object' && path[i] in node) {
        node = node[path[i]];
      } else {
        return fallback;
      }
    }
    return node || fallback;
  }

  /**
   * 批量选择：触发列表中 `name='id[]'` 的选择动作（兼容旧模板行为）。
   */
  function BatchSelectAll() {
    $("input[name='id[]']").click();
  }

  // 收集已勾选的条目 ID 串到指定输入框
  function BatchDeleteAll(objEdit) {
    objEdit = document.getElementById(objEdit);
    objEdit.value = '';
    const aryChecks = document.getElementsByTagName('input');
    for (let i = 0; i < aryChecks.length; i++) {
      if ((aryChecks[i].type === 'checkbox') && (aryChecks[i].id.indexOf('edt') !== -1)) {
        if (aryChecks[i].checked) {
          objEdit.value = aryChecks[i].value + ',' + objEdit.value;
        }
      }
    }
  }

  /**
   * 侧边菜单激活：高亮指定菜单项，并替换其图标为“选中态”。
   */
  function ActiveLeftMenu(name) {
    name = '#' + name;
    $('#leftmenu li').removeClass('on');
    $(name).parent().addClass('on');
    let s = $(name).children('span').css('background-image');
    if (s !== undefined) {
      s = s.replace('1.png', '2.png');
      s = s.replace('1.svg', '2.svg');
      s = s.replace('1.gif', '2.gif');
      $(name).children('span').css('background-image', s);
    }
  }

  /**
   * 顶部菜单激活：高亮指定菜单项。
   */
  function ActiveTopMenu(name) {
    name = '#' + name;
    $('#topmenu li').removeClass('on');
    $(name).addClass('on');
  }

  // 表格斑马线占位（旧接口保留）
  function bmx2table() {}

  // 切换自定义复选框状态
  function ChangeCheckValue(obj) {
    if ($(obj).hasClass('imgcheck-disabled')) {
      return;
    }
    $(obj).toggleClass('imgcheck-on');

    if ($(obj).hasClass('imgcheck-on')) {
      $(obj).prev('input').val('1');
      $(obj).next('.off-hide').show();
    } else {
      $(obj).prev('input').val('0');
      $(obj).next('.off-hide').hide();
    }
  }

  /**
   * 桌面通知（旧 WebKit 接口）：有权限则显示 5 秒后关闭。
   */
  function notify(s) {
    const notifyTitle = getLang(['msg', 'notify'], 'Notification');
    const iconHost = runtime.zbp ? runtime.zbp.options.bloghost : DEFAULT_HOST;
    if (window.webkitNotifications) {
      if (window.webkitNotifications.checkPermission() === 0) {
        const zbNotifications = window.webkitNotifications.createNotification(iconHost + 'zb_system/image/admin/logo-16.png', notifyTitle, s);
        zbNotifications.show();
        zbNotifications.onclick = function () { top.focus(); this.cancel(); };
        zbNotifications.replaceId = 'Meteoric';
        setTimeout(function () { zbNotifications.cancel(); }, 5000);
      } else {
        window.webkitNotifications.requestPermission(notify);
      }
    }
  }

  /**
   * 站点统计刷新：调用后端接口刷新统计区块，并显示加载状态。
   */
  function statistic(s) {
    $('#statistic i').addClass('loading-status');
    $('#updatatime').hide();
    const refreshCache = getLang(['msg', 'refresh_cache'], 'Please refresh cache');
    const operationFailed = getLang(['msg', 'operation_failed'], 'Operation failed');

    $.ajax({
      type: 'GET',
      url: s + '&tm=' + Math.random(),
      data: {},
      error: function (xhr) {
        if (xhr.status === 500) {
          alert(refreshCache + '\n\r' + operationFailed);
        }
        setTimeout(function () {
          $('#statistic i').removeClass('loading-status');
        }, 500);
        $('#updatatime').show();
      },
      success: function (data) {
        $('#tbStatistic tr:first ~ tr').remove();
        $('#tbStatistic tr:first').after(data);
        setTimeout(function () {
          $('#statistic i').removeClass('loading-status');
        }, 500);
        $('#updatatime').show();
      }
    });
  }

  /**
   * 公告/更新信息刷新：调用后端接口刷新更新信息区块。
   */
  function updateinfo(s) {
    $('#tbUpdateInfo i').addClass('loading-status');
    $.get(s + '&tm=' + Math.random(), {}, function (data) {
      $('#tbUpdateInfo tr:first ~ tr').remove();
      $('#tbUpdateInfo tr:first').after(data);
      setTimeout(function () {
        $('#tbUpdateInfo i').removeClass('loading-status');
      }, 500);
    });
  }

  /**
   * 使用图片作为头部图标背景，并保持原文本。
   */
  function AddHeaderIcon(s) {
    const element = $('div.divHeader,div.divHeader2').first();
    element.css({ 'background-image': "url('" + s + "')" });
    element.html('<span>' + element.text() + '</span>');
  }

  // 使用字体图标替换头部背景
  function AddHeaderFontIcon(iconClass) {
    const element = $('div.divHeader,div.divHeader2').first();
    element.css('background-image', "url('" + (runtime.zbp ? runtime.zbp.options.bloghost : DEFAULT_HOST) + "zb_system/image/admin/none.gif')");
    const text = element.text();
    element.html('<i class="' + iconClass + '"></i> <span>' + text + '</span>');
  }

  // 自动隐藏提示气泡
  /**
   * 自动隐藏提示：对非常驻提示（无 `hint_always`）按自定义延时隐藏。
   */
  function AutoHideTips() {
    $('.hint-msg:visible').each(function () {
      if (!$(this).hasClass('hint_always')) {
        $(this).delay($(this).attr('data-delay')).hide(1500, function () {});
      }
    });
  }

  // CSRF 过期提示
  function ShowCSRFHint() {
    let hintHtml = '<div class="hint"><p class="hint_bad">' + getLang(['error', '94'], 'Login status expired, please refresh and retry. (%s)') + '</p></div>';
    hintHtml = hintHtml.replace('%s', $('meta[name=csrfExpiration]').attr('content'));
    const $hint = $(hintHtml);
    if ($('.hint-place').length > 0) {
      $('.hint-place').after($hint);
    } else {
      $('.main').prepend($hint);
    }
  }

  // DOM 初始化入口，绑定事件与样式
  function onDomReady() {
    $('.content-box .content-box-content div.tab-content').hide();
    $('ul.content-box-tabs li a.default-tab').addClass('current');
    $('.content-box-content div.default-tab').show();

    $('.content-box ul.content-box-tabs li a').click(function () {
      $(this).parent().siblings().find('a').removeClass('current');
      $(this).addClass('current');
      const currentTab = $(this).attr('href');
      $(currentTab).siblings().hide();
      $(currentTab).show();
      return false;
    });

    if ($('.SubMenu').find('span').length > 0) {
      $('.SubMenu').show();
    }

    $('input.checkbox[value="1"]').after('<span class="imgcheck imgcheck-on"></span>');
    $('input.checkbox[value!="1"]').after('<span class="imgcheck"></span>');
    $('input.checkbox').each(function () {
      $(this).next('span').css('display', $(this).css('display'));
      $(this).next('span').attr('alt', $(this).attr('alt'));
      $(this).next('span').attr('title', $(this).attr('title'));
      if ($(this).attr('disabled') === 'disabled') {
        $(this).next('span').addClass('imgcheck-disabled');
      }
    });
    $('input.checkbox').css('display', 'none');

    $('body').on('click', 'span.imgcheck', function () { ChangeCheckValue(this); });

    $('#batch a').bind('click', function () { BatchContinue(); $('#batch p').html(getLang(['msg', 'batch_operation_in_progress'], 'Batch operation in progress...')); });

    $('.SubMenu span.m-right').parent().css({ float: 'right' });

    $("img[width='16']").each(function () { if ($(this).parent().is('a')) { $(this).parent().addClass('button'); } });

    if ($('div.divHeader,div.divHeader2').first().css('background-image') === 'none') {
      AddHeaderFontIcon('icon-window-fill');
    }

    AutoHideTips();

    SetCookie('timezone', (new Date().getTimezoneOffset() / 60) * (-1));

    const s = $('div.divHeader,div.divHeader2').first().css('background-image');
    if ($('div.divHeader i,div.divHeader2 i').length <= 0 && (s !== undefined && s.indexOf('none.gif') !== -1)) {
      AddHeaderFontIcon('icon-window-fill');
    }

    const startTime = new Date().getTime();
    const csrfInterval = setInterval(function () {
      const timeout = $('meta[name=csrfExpiration]').attr('content') || 1;
      const timeDiff = new Date().getTime() - startTime;
      if (timeDiff > Math.floor(timeout) * 60 * 60 * 1000) {
        ShowCSRFHint();
        clearInterval(csrfInterval);
      }
    }, 30 * 60 * 1000);
  }

  $(function () {
    runtimeReady.done(onDomReady);
  });

  // 获取 zbp 实例
  function getZbpInstance() {
    return runtime.zbp;
  }

  // 兼容旧代码的 cookie 设置包装
  const SetCookie = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.cookie || !instance.cookie.set) {
      return undefined;
    }
    return instance.cookie.set.apply(instance.cookie, arguments);
  };

  // 兼容旧代码的 cookie 读取包装
  const GetCookie = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.cookie || !instance.cookie.get) {
      return undefined;
    }
    return instance.cookie.get.apply(instance.cookie, arguments);
  };

  // 兼容旧代码的用户信息输出包装
  const LoadRememberInfo = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.userinfo || !instance.userinfo.output) {
      return false;
    }
    instance.userinfo.output.apply(null);
    return false;
  };

  // 兼容旧代码的用户信息保存包装
  const SaveRememberInfo = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.userinfo || !instance.userinfo.saveFromHtml) {
      return false;
    }
    instance.userinfo.saveFromHtml.apply(null);
    return false;
  };

  // 兼容旧代码的评论回复包装
  const RevertComment = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.comment || !instance.comment.reply) {
      return false;
    }
    instance.comment.reply.apply(null, arguments);
    return false;
  };

  // 兼容旧代码的评论获取包装
  const GetComments = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.comment || !instance.comment.get) {
      return false;
    }
    instance.comment.get.apply(null, arguments);
    return false;
  };

  // 兼容旧代码的评论提交包装
  const VerifyMessage = function () {
    const instance = getZbpInstance();
    if (!instance || !instance.comment || !instance.comment.post) {
      return false;
    }
    instance.comment.post.apply(null);
    return false;
  };

  // 导出全局函数以兼容旧模板调用
  function exportGlobals() {
    const exports = {
      BatchSelectAll: BatchSelectAll,
      BatchDeleteAll: BatchDeleteAll,
      ActiveLeftMenu: ActiveLeftMenu,
      ActiveTopMenu: ActiveTopMenu,
      bmx2table: bmx2table,
      ChangeCheckValue: ChangeCheckValue,
      notify: notify,
      statistic: statistic,
      updateinfo: updateinfo,
      AddHeaderIcon: AddHeaderIcon,
      AddHeaderFontIcon: AddHeaderFontIcon,
      AutoHideTips: AutoHideTips,
      ShowCSRFHint: ShowCSRFHint,
      SetCookie: SetCookie,
      GetCookie: GetCookie,
      LoadRememberInfo: LoadRememberInfo,
      SaveRememberInfo: SaveRememberInfo,
      RevertComment: RevertComment,
      GetComments: GetComments,
      VerifyMessage: VerifyMessage,
      adminJsReady: adminJsReady
    };

    Object.keys(exports).forEach(function (key) {
      win[key] = exports[key];
    });
  }

  /**
   * 管理脚本就绪回调：当异步配置完成并应用后触发。
   * @param {Function} callback - 在就绪时执行的函数
   * @returns {Promise} 只读 Promise，可用于链式调用
   */
  function adminJsReady(callback) {
    if (typeof callback === 'function') {
      runtimeReady.done(callback);
    }
    return runtimeReady.promise();
  }

  exportGlobals();
})(window, jQuery);
