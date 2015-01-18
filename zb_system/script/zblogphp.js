/** 
 * Z-BlogPHP JavaScript Framework 
 * @author zsx<zsx@zsxsoft.com>
 */  
(function () {
	/**
	 * Class ZBP
	 * @class ZBP
	 * @arguments options
	 * @arguments jquery
	 */
	var ZBP = function (options, jquery) {
		// Load jQuery library
		if (typeof jQuery == 'undefined' && typeof jquery == 'undefined') throw new Error('No jQuery!');
		this.$ = jquery || jQuery;

		// Init self
		var self = this;
		initMethods(this);

		// Init option
		options = options || {};
		options.cookiepath = options.cookiepath || "/";
		options.bloghost = options.bloghost || location.origin;
		options.ajaxurl = options.ajaxurl || location.origin;
		options.commentmaxlength = options.commentmaxlength || 1000;
		options.lang = options.lang || {};
		this.options = options;
		this.userinfo.username = this.cookie.get("name");
		this.userinfo.mail = this.cookie.get("email");
		this.userinfo.homepage = this.cookie.get("homepage");

		// Register system events
		this.plugin.on("userinfo.output", "system", function () {
			this.$("#inpName").val(this.userinfo.username);
			this.$("#inpEmail").val(this.userinfo.mail);
			this.$("#inpHomePage").val(this.userinfo.homepage);
		});

		this.plugin.on("userinfo.savefromhtml", "system", function () {
			this.userinfo.username = this.$("#inpName").val();
			this.userinfo.mail = this.$("#inpEmail").val();
			this.userinfo.homepage = this.$("#inpHomePage").val();
			this.userinfo.save();
		});

		this.plugin.on("userinfo.save", "system", function () {
			this.cookie.set("name", this.userinfo.username);
			this.cookie.set("email", this.userinfo.mail);
			this.cookie.set("homepage", this.userinfo.homepage);
		});

		this.plugin.on("comment.verifydata", "system", function (error, formData) {

			var regExList = {
				// [canBeEmpty, errorCode, RegExp]
				"name": [false, 72, /^[\.\_A-Za-z0-9\u4e00-\u9fa5]+$/ig],
				"email": [true, 29, /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/ig],
				"content": [false, 46, /./]
			};

			for (var item in regExList) {
				var object = regExList[item];
				if (((!object[0] && formData[item] == "")) || (!(object[2].test(formData[item])) && formData[item] != '')) {
					error.no = object[1];
					error.msg = this.options.lang.error[error.no];
					return error;
				}
			}
			
			var objSubmit = $("#inpId").parent("form").find(":submit");
			objSubmit.data("orig", objSubmit.val()).val("Waiting...").attr("disabled","disabled").addClass("loading");

		});

		this.plugin.on("comment.postsuccess", "system", function (formData, data, textStatus, jqXhr) {

			var objSubmit = $("#inpId").parent("form").find(":submit");
			objSubmit.removeClass("loading").removeAttr("disabled").val(objSubmit.data("orig"));
			
			if((data.search("faultCode") > 0) && (data.search("faultString")>0)) {
				var errorData = s.match("<string>.+?</string>")[0].replace("<string>","").replace("</string>","");
				alert(errorData);
				try {console.log(arguments); console.log("ERROR - " + errorData)} catch (e) { /* do nothing*/}
			}
			else {
				var cmt = data.match(/cmt\d+/);
				if(formData.replyid == 0){
					this.$(data).insertAfter("#AjaxCommentBegin");
				} else {
					this.$(data).insertAfter("#AjaxComment" + formData.replyid);
				}
				location.hash = "#" + cmt;
				this.$("#txaArticle").val("");
				
				this.userinfo.saveFromHtml();
			}

		});

		this.plugin.on("comment.get", "system", function (postid, page) {
			this.$.get(this.options.bloghost + "zb_system/cmd.php?act=getcmt&postid=" + postid + "&page=" + page, function (data, textStatus, jqXhr) { 
				this.plugin.emit("comment.got", [postid, page], data, textStatus, jqXhr)	
			});
		});

		this.plugin.on("comment.got", "system", function (formData, data, textStatus, jqXhr) {
			this.$('#AjaxCommentBegin').nextUntil('#AjaxCommentEnd').remove();
			this.$('#AjaxCommentBegin').after(data);
		});

		this.plugin.on("comment.revert", "system", function (id) {
			var me = this;
			this.$("#inpRevID").val(id);
			this.$("#cancel-reply").show().bind("click", function () {
				me.$("#inpRevID").val(0);
				me.$(this).hide();
				window.location.hash = "#comment";
				return false;
			});
			window.location.hash="#comment";
		});


		return this;

	};

	ZBP.prototype._plugins = {};

	var initMethods = function (self) {
				
		var PLUGIN = function () {};
		PLUGIN.prototype.bind = PLUGIN.prototype.on = PLUGIN.prototype.addListener = function (interfaceName, pluginName, callback) {
			if (typeof self._plugins[interfaceName] == 'undefined') self._plugins[interfaceName] = {};
			self._plugins[interfaceName][pluginName] = callback;
			return self;
		}
		PLUGIN.prototype.unbind = PLUGIN.prototype.removeListener = function (interfaceName, pluginName) {
			if (!pluginName) pluginName = "";
			if (pluginName == "") {
				self._plugins[interfaceName] = {};
			} else {
				self._plugins[interfaceName][pluginName] = null;
			}
			return self;
		}
		PLUGIN.prototype.emit = function (interfaceName) {
			// var argu = self.$.extend([], arguments);
			// argu.shift();
			// Let's fuck IE6 together!
			var argu = []
			for (var i = 1; i < arguments.length; i++) {
				argu.push(arguments[i]);
			}
			for (var item in self._plugins[interfaceName]) {
				self._plugins[interfaceName][item].apply(self, argu);
			}
			return self;
		}
		self.plugin = new PLUGIN();

		var COOKIE = function () {};
		COOKIE.prototype.get = function (sCookieName) {
			var arr = document.cookie.match(new RegExp("(^| )" + sCookieName + "=([^;]*)(;|$)"));
			return (arr ? unescape(arr[2]) : null);
		};
		COOKIE.prototype.set = function (sCookieName, sCookieValue, iExpireDays) {
			var dExpire = new Date();
			if (iExpireDays) {
				dExpire.setTime(dExpire.getTime() + parseInt(iExpireDays * 24 * 60 * 60 * 1000));
			}
			document.cookie = sCookieName + "=" + escape(sCookieValue) + "; " + (iExpireDays ? "expires=" + dExpire.toGMTString() + "; " : "") + "path=" + self.options.cookiepath;
			return self;
		}
		self.cookie = new COOKIE();

		var USERINFO = function () {};
		USERINFO.prototype.output = function () {
			self.plugin.emit("userinfo.output");
			return self;
		}
		USERINFO.prototype.save = function () {
			self.plugin.emit("userinfo.save");
			return self;
		}
		USERINFO.prototype.saveFromHtml = function () {
			self.plugin.emit("userinfo.savefromhtml");
			return self;
		}
		self.userinfo = new USERINFO();

		var COMMENT = function () {};
		COMMENT.prototype.get = function (postid, page) {
			self.plugin.emit("comment.get", postid, page);
			return self;
		}
		COMMENT.prototype.revert = function (id) {
			self.plugin.emit("comment.revert", id);
			return self;
		}
		COMMENT.prototype.post = function(formData) {

			formData = formData || {};
			formData.action   = formData.action   || $("#inpId").parent("form").attr("action");
			formData.postid   = formData.postid   || $("#inpId").val();
			formData.verify   = formData.verify   || $("#inpVerify").val();
			formData.name     = formData.name     || $("#inpName").val();
			formData.email    = formData.email    || $("#inpEmail").val();
			formData.content  = formData.content  || $("#txaArticle").val();
			formData.homepage = formData.homepage || $("#inpHomePage").val();
			formData.replyid  = formData.replyid  || $("#inpRevID").val();
			formData.isajax   = formData.isajax   || true;
			
			var error = {
				no: 0,
				msg: ""
			};

			self.plugin.emit("comment.verifydata", error, formData);

			if (error.no != 0) {
				alert(error.msg);
				try { console.log(formData); console.log("ERROR - " + error.msg)} catch (e) { /* do nothing */}
				return self;
			} 
			self.$.post(formData.action, formData, function (data, textStatus, jqXhr) {
				self.plugin.emit("comment.postsuccess", formData, data, textStatus, jqXhr);
			});

			return self;
		}
		self.comment = new COMMENT();
	};

	// AMD
	if (typeof define === "function" && define.amd) {
		define("zbp", [], function() {
			return ZBP;
		});
	} else if (typeof define === "function" && define.cmd) {
		define("zbp", [], function (require, exports, module) {
			module.exports = ZBP;
		});
	} else {
		window.ZBP = ZBP;
	}

})();