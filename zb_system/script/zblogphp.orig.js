/**
 * Z-BlogPHP JavaScript Framework
 * @author zsx<zsx@zsxsoft.com>
 */
(function () {
  var SYSTEM_DEFAULT_EVENT_NAME = 'system-default'
  var deprecatedEvents = [ // then it will be concated by deprecatedMappings
    'comment.postsuccess',
    'comment.posterror'
  ]
  var deprecatedMappings = {}
  deprecatedMappings['comment.reply'] = 'comment.reply.start'
  deprecatedMappings['userinfo.savefromhtml'] = 'userinfo.readFromHtml'

  /**
   * Class ZBP
   *
   * @constructor
   * @global
   * @class ZBP
   * @param options {OPTION} Init option
   * @param jquery {object} jQuery
   */
  var ZBP = function (options, jquery) {
    /** Load jQuery library */
    if (typeof jQuery === 'undefined' && typeof jquery === 'undefined') {
      throw new Error('No jQuery!')
    }
    this.$ = jquery || window.jQuery

    // Some simple polyfills
    if (!Object.keys) {
      Object.keys = function (obj) {
        return this.$.map(obj, function (v, k) {
          return k
        })
      }
    }
    if (!console) {
      window.console = {}
      console.logs = []
      console.log = console.error = console.warn = function () {
        console.logs.push(arguments)
      }
    }

    deprecatedEvents = deprecatedEvents.concat(Object.keys(deprecatedMappings))

    /** Init self */
    var self = this
    initMethods(this)

    /** Init option */
    options = options || {}
    options.cookiepath = options.cookiepath || '/'
    options.bloghost = options.bloghost || location.origin
    options.ajaxurl = options.ajaxurl || location.origin
    options.commentmaxlength = options.commentmaxlength || 1000
    options.lang = options.lang || {}
    options.comment = options.comment || {}
    options.comment.inputs = options.comment.inputs || {}
    options.comment.useDefaultEvents = options.comment.useDefaultEvents || false

    this.eachOnCommentInputs = function (callback) {
      return self.$.each(options.comment.inputs, callback)
    }

    this.eachOnCommentInputs(function (key, value) {
      if (!value.getter && value.selector) {
        value.getter = function () {
          return self.$(value.selector).val()
        }
      }
      if (!value.setter && value.selector) {
        value.setter = function (val) {
          return self.$(value.selector).val(val)
        }
      }
      if (!value.validator) {
        value.validator = function (text, callback) {
          text = text || value.getter()
          while (true) {
            text = text || ''
            text = text.toString().trim()
            if (text === '') {
              if (value.required) {
                break
              } else {
                return callback(null)
              }
            }
            if (value.validateRule) {
              value.validateRule.lastIndex = 0
              if (!value.validateRule.test(text)) break
            }
            return callback(null)
          }
          var error = new Error(value.validateFailedMessage || self.options.lang.error[value.validateFailedErrorCode])
          error.code = value.validateFailedErrorCode
          return callback(error)
        }
      }
    })

    /**
       * Class option
       * @memberOf ZBP
       * @typedef {OPTION}
       * @property {string} cookiepath - Cookie Path
       * @property {string} bloghost - Blog Host
       * @property {string} ajaxurl - Ajax Url
       * @property {string} commentmaxlength - Maximum acceptable length for comment
       * @property {object} lang - Language
       * @type {object}
       */
    this.options = options

    /** Register system events */
    this.plugin.on('userinfo.output', 'system', function () {
      self.eachOnCommentInputs(function (key, value) {
        if (value.saveLocally) {
          self.userinfo[key] = self.cookie.get('zbp_userinfo_' + key)
          self.userinfo[key] && value.setter(self.userinfo[key])
        }
      })
    })

    this.plugin.on('userinfo.readFromHtml', 'system', function () {
      self.eachOnCommentInputs(function (key, value) {
        if (value.saveLocally) {
          self.userinfo[key] = value.getter()
        }
      })
      self.userinfo.save()
    })

    this.plugin.on('userinfo.save', 'system', function () {
      self.eachOnCommentInputs(function (key, value) {
        if (value.saveLocally) {
          self.userinfo[key] = value.getter()
          self.cookie.set('zbp_userinfo_' + key, self.userinfo[key])
        }
      })
    })

    this.plugin.on('comment.get', 'system', function (postid, page) {
      self.$.get(self.options.bloghost + 'zb_system/cmd.php?act=getcmt&postid=' + postid + '&page=' + page, function (data, textStatus, jqXhr) {
        self.plugin.emit('comment.got', [postid, page], data, textStatus, jqXhr)
      })
    })

    this.plugin.on('comment.post.validate', 'system', function (formData) {
      var verifyCount = 0
      var inError = false
      var callback = function (error) {
        if (inError) return
        if (error) {
          inError = true
          self.plugin.emit('comment.post.validate.error', error, formData)
          return
        }
        verifyCount++
        if (verifyCount === Object.keys(self.options.comment.inputs).length) {
          // Deprecated synchronization event here, just make it compatible.
          var err = {no: 0, msg: ''}
          self.plugin.emit('comment.verifydata', err, formData)
          if (err.no > 0) {
            var error = new Error(err.msg)
            error.code = err.no
            self.plugin.emit('comment.post.validate.error', error, formData)
            return
          }

          // Then now this is modern code.
          self.plugin.emit('comment.post.validate.success', formData)
        }
      }

      self.eachOnCommentInputs(function (key, value) {
        value.validator(formData[key], callback)
      })
    })

    this.plugin.on('comment.post.start', 'system', function (formData) {
      self.eachOnCommentInputs(function (key, value) {
        formData[key] = formData[key] || value.getter()
      })
      formData['commentKey'] = new Date().getTime() + '' + Math.random()
      self.plugin.emit('comment.post.validate', formData)
    })

    this.plugin.on('comment.post.validate.error', 'system', function (error, formData) {
      self.plugin.emit('comment.post.error', error, formData)
    })

    this.plugin.on('comment.post.validate.success', 'system', function (formData) {
      self.$.post(formData.action, formData).done(function (data, textStatus, jqXhr) {
        // 兼容性接口，未来删除
        self.plugin.emit('comment.postsuccess', formData, data, textStatus, jqXhr)
        var json = self.$.parseJSON(data)
        if (json.err && json.err.code > 0) {
          var error = new Error(json.err.msg)
          error.code = json.err.code
          self.plugin.emit('comment.post.error', error, formData, json, textStatus, jqXhr)
        } else {
          self.plugin.emit('comment.post.success', formData, json, textStatus, jqXhr)
        }
      }).fail(function (jqXhr, textStatus) {
        var error = new Error(textStatus)
        error.code = 255
        self.plugin.emit('comment.post.error', error, formData, textStatus, jqXhr)
      })
    })

    this.plugin.on('comment.post.success', 'system', function (formData, data, textStatus, jqXhr) {
      self.plugin.emit('comment.post.done', null, formData, data, textStatus, jqXhr)
    })

    this.plugin.on('comment.post.error', 'system', function (error, formData, data, textStatus, jqXhr) {
      // 兼容性接口
      self.plugin.emit("comment.posterror", {
        jqXHR: jqXhr,
        msg: error.message,
        code: error.code,
      }, formData);
      self.plugin.emit('comment.post.done', error, formData, data, textStatus, jqXhr)
    })

    if (this.options.comment.useDefaultEvents) {
      this.plugin.on('comment.reply.start', SYSTEM_DEFAULT_EVENT_NAME, function (id) {
        this.$('#inpRevID').val(id)
        this.$('#cancel-reply').show().bind('click', function () {
          self.plugin.emit('comment.reply.cancel')
          self.$('#inpRevID').val(0)
          self.$(this).hide()
          window.location.hash = '#comment'
          return false
        })
        window.location.hash = '#comment'
      })

      this.plugin.on('comment.got', SYSTEM_DEFAULT_EVENT_NAME, function (formData, data, textStatus, jqXhr) {
        this.$('#AjaxCommentBegin').nextUntil('#AjaxCommentEnd').remove()
        this.$('#AjaxCommentBegin').after(data)
      })

      this.plugin.on('comment.post.start', SYSTEM_DEFAULT_EVENT_NAME, function () {
        var objSubmit = self.$('#inpId').parent('form').find(':submit')
        objSubmit.data('orig', objSubmit.val()).val('Waiting...').attr('disabled', 'disabled').addClass('loading')
      })

      this.plugin.on('comment.post.done', SYSTEM_DEFAULT_EVENT_NAME, function (formData) {
        var objSubmit = self.$('#inpId').parent('form').find(':submit')
        objSubmit.removeClass('loading').removeAttr('disabled')
        if (objSubmit.data('orig')) {
          objSubmit.val(objSubmit.data('orig'))
        }
      })

      this.plugin.on('comment.post.success', SYSTEM_DEFAULT_EVENT_NAME, function (formData, data, textStatus, jqXhr) {
        if (formData.replyid.toString() === '0') {
          this.$(data.data.html).insertAfter('#AjaxCommentBegin')
        } else {
          this.$(data.data.html).insertAfter('#AjaxComment' + formData.replyid)
        }
        location.hash = '#cmt' + data.data.ID
        this.$('#txaArticle').val('')
        this.userinfo.readFromHtml()
      })

      this.plugin.on('comment.post.error', SYSTEM_DEFAULT_EVENT_NAME, function (err) {
        alert(err.message)
        throw new Error('ERROR - ' + err.message)
      })
    }

    return this
  }
  /**
   * Private _plugins
   *
   * @type {Object}
   */
  ZBP.prototype._plugins = {}
  /**
   * Init prototype for ZBP
   *
   * @param  object
   **/
  var initMethods = function (self) {
    self.utils = {}
    self.utils.getFromIndex = function (fromIndex, args) {
      var ret = []
      for (var i = fromIndex; i < args.length; i++) {
        ret.push(args[i])
      }
      return ret
    }

    /**
       * Plugin
       * @class Plugin
       */
    var Plugin = function () {}

    Plugin.prototype.checkIsInterfaceDeprecated = function (interfaceName) {
      if (deprecatedEvents.indexOf(interfaceName) >= 0) {
        console.warn("Interface '" + interfaceName + "' is deprecated in ZBP 1.6, please update your plugin or theme!")
        return true
      }
      return false
    }

    /**
       * Add listener
       * @function
       * @memberOf Plugin
       * @param interfaceName {string}
       * @param pluginName {string}
       * @param callback {Function}
       * @return {object} this
       */
    Plugin.prototype.bind = Plugin.prototype.on = Plugin.prototype.addListener = function (interfaceName, pluginName, callback) {
      if (this.checkIsInterfaceDeprecated(interfaceName)) {
        if (deprecatedMappings[interfaceName]) {
          interfaceName = deprecatedMappings[interfaceName]
        }
      }
      if (typeof self._plugins[interfaceName] === 'undefined') {
        self._plugins[interfaceName] = {}
      };
      self._plugins[interfaceName][pluginName] = callback
      return self
    }
    /**
       * Remove listener
       * @function
       * @memberOf Plugin
       * @param interfaceName {string}
       * @param pluginName {string}
       * @return {object} this
       */
    Plugin.prototype.unbind = Plugin.prototype.removeListener = function (interfaceName, pluginName) {
      if (!pluginName) {
        pluginName = ''
      }

      if (this.checkIsInterfaceDeprecated(interfaceName) && pluginName === 'system') {
        pluginName = SYSTEM_DEFAULT_EVENT_NAME
        if (deprecatedMappings[interfaceName]) {
          interfaceName = deprecatedMappings[interfaceName]
        }
      }

      if (pluginName === '') {
        self._plugins[interfaceName] = {}
      } else if (self._plugins[interfaceName]) {
        self._plugins[interfaceName][pluginName] = null
        delete self._plugins[interfaceName][pluginName]
      }
      return self
    }
    /**
       * Call listener
       * @function
       * @memberOf Plugin
       * @param interfaceName {string}
       * @return {object} this
       */
    Plugin.prototype.emit = function (interfaceName) {
      var args = self.utils.getFromIndex(1, arguments)
      for (var item in self._plugins[interfaceName]) {
        self._plugins[interfaceName][item].apply(self, args)
      }
      return self
    }

    Plugin.prototype.listenerCount = function (interfaceName) {
      return Object.keys(self._plugins[interfaceName])
    }
    /**
       * plugin
       * @memberOf ZBP
       * @type {Plugin}
       */
    self.plugin = new Plugin()

    /**
       * Cookie
       * @class Cookie
       */
    var Cookie = function () {}
    /**
       * Get Cookie
       * @memberOf Cookie
       * @param sCookieName {string} Cookie Key
       * @return cookieValue {string} Cookie Value
       */
    Cookie.prototype.get = function (sCookieName) {
      var arr = document.cookie.match(new RegExp('(^| )' + sCookieName + '=([^;]*)(;|$)'))
      return (arr ? unescape(arr[2]) : null)
    }
    /**
       * Set Cookie
       * @memberOf Cookie
       * @param sCookieName {string} Cookie Key
       * @param sCookieValue {string} Cookie Value
       * @param iExpireDays {int} Cookie Expires
       * @return ZBP {ZBP}
       */
    Cookie.prototype.set = function (sCookieName, sCookieValue, iExpireDays) {
      var dExpire = new Date()
      if (iExpireDays) {
        dExpire.setTime(dExpire.getTime() + parseInt(iExpireDays * 24 * 60 * 60 * 1000))
      }
      document.cookie = sCookieName + '=' + escape(sCookieValue) + '; ' + (iExpireDays ? 'expires=' + dExpire.toGMTString() + '; ' : '') + 'path=' + self.options.cookiepath
      return self
    }
    /**
       * cookie
       * @memberOf ZBP
       * @type {Cookie}
       */
    self.cookie = new Cookie()

    /**
       * UserInfo
       * @class UserInfo
       */
    var UserInfo = function () {}
    /**
       * Output user information to DOM
       * @memberOf UserInfo
       * @return ZBP {ZBP}
       */
    UserInfo.prototype.output = function () {
      self.plugin.emit('userinfo.output')
      return self
    }
    /**
       * Save user information from class
       * @memberOf UserInfo
       * @return ZBP {ZBP}
       */
    UserInfo.prototype.save = function () {
      self.plugin.emit('userinfo.save')
      return self
    }
    /**
       * Save user information from DOM
       * @memberOf UserInfo
       * @return ZBP {ZBP}
       */
    UserInfo.prototype.saveFromHtml = UserInfo.prototype.readFromHtml = function () {
      self.plugin.emit('userinfo.readFromHtml')
      return self
    }
    /**
       * userinfo
       * @type {UserInfo}
       * @memberOf ZBP
       */
    self.userinfo = new UserInfo()

    /**
       * Comment
       * @class Comment
       */
    var Comment = function () {}
    /**
       * Get comments
       * @memberOf Comment
       * @param postid {int} Article ID
       * @param page {int} Page
       * @return ZBP {ZBP}
       */
    Comment.prototype.get = function (postid, page) {
      self.plugin.emit('comment.get', postid, page)
    }
    /**
       * Reply Comment
       * @memberOf Comment
       * @param int {int} Comment ID
       * @return ZBP {ZBP}
       */
    Comment.prototype.reply = function (id) {
      self.plugin.emit('comment.reply.start', id)
    }
    /**
       * Post Comment
       * @memberOf Comment
       * @param formData {PostData}
       * @return ZBP {ZBP}
       */
    Comment.prototype.post = function (formData) {
      formData = formData || {}
      try {
        self.plugin.emit('comment.post.start', formData)
      } catch (e) {
        console.error(e) // to prevent default events
      }
      return false
    }
    /**
       * comment
       * @memberOf ZBP
       * @type {Comment}
       */
    self.comment = new Comment()
  }

  /**
   * ZBP Module
   * (AMD & CMD compatible)
   * @module zbp
   */
  if (typeof define === 'function' && define.amd) { // eslint-disable-line no-undef
    define('zbp', [], function () { // eslint-disable-line no-undef
      return ZBP
    })
  } else if (typeof define === 'function' && define.cmd) { // eslint-disable-line no-undef
    define('zbp', [], function (require, exports, module) { // eslint-disable-line no-undef
      module.exports = ZBP
    })
  } else if (typeof module !== 'undefined') {
    module.exports = ZBP
  } else {
    window.ZBP = ZBP
  }
})()
