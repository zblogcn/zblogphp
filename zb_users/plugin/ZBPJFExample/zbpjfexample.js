(function (zbp) {	
	
	alert("请按下F12，打开你的控制台，然后分别发布评论、回复评论测试！");

	console.log("zbp对象：")
	console.log(zbp);
	if (!zbp) throw new Error('zbp对象不存在，请检查是否成功创建。'); // 检测ZBP 
	

	// 评论字段校验规则，要求提交的内容必须有一个test字段
	zbp.options.comment.inputs.test = {
		required: true,
		getter: function () {
			return 'ZBPJF'
		},
		validator: function (text, callback) {
			if (text !== 'ZBPJF') {
				callback(new Error('No ZBPJF!'))
			} else {
				// null代表没问题
				callback(null)
			}
		}
	}

	// 挂接口

	// 获取评论数据
	zbp.plugin.on("comment.get", "ZBPJFExample", function (postId, page) {
		console.log('开始尝试评论数据')
	})

	// 得到评论数据
	zbp.plugin.on("comment.got", "ZBPJFExample", function (formData, data, textStatus, jqXhr) {
		console.log('获得评论数据')
		console.log(data)
	})
	
	// 评论开始接口，只能对formData进行读取和修改
	zbp.plugin.on("comment.post.start", "ZBPJFExample", function (formData) {
		console.log('开始评论！')
	})

	// 评论验证中接口
	zbp.plugin.on("comment.post.validate", "ZBPJFExample", function (formData) {
		console.log('评论验证中！')
	})

	// 评论验证失败接口
	zbp.plugin.on('comment.post.validate.error', 'ZBPJFExample', function (error, formData) {
		console.log('评论验证失败：' + error.code)
	})

	// 评论验证成功接口
	zbp.plugin.on('comment.post.validate.success', 'ZBPJFExample', function (formData) {
		console.log('评论验证成功！')
	})

	// 评论发送成功接口
	zbp.plugin.on('comment.post.success', 'ZBPJFExample', function (formData, data, textStatus, jqXhr) {
		console.log('评论发送成功！')
		console.log("系统返回数据：");
		console.log(data)
	})

	// 评论发送失败接口
	zbp.plugin.on('comment.post.error', 'ZBPJFExample', function (error, formData, data, textStatus, jqXhr) {
		console.log('评论发送失败，错误：' + error.code)
		console.log(error)
	})

	// 评论发送结束接口
	// 无论成功或失败，评论发送结束均会触发
	zbp.plugin.on('comment.post.done', 'ZBPJFExample', function (error, formData, data, textStatus, jqXhr) {
		console.log('评论发送结束')
	})

	// 评论回复接口，可能在为老版本使用的主题中会无效
	// 另外，通过zbp.plugin.unbind("comment.reply", "system-default")可以解绑系统的相关事件。
	zbp.plugin.on("comment.reply.start", "ZBPJFExample", function (id) {
		console.log("回复评论ID：" + id);
	});

	// 取消评论回复接口，可能在为老版本使用的主题中会无效，若解绑了系统有关事件可能会无效。
	// 可能需要主题配合
	zbp.plugin.on("comment.reply.cancel", "ZBPJFExample", function (id) {
		console.log("取消回复评论");
	});

	// 主题可设置zbp.options.comment.useDefaultEvents = false让系统的回复相关的控制DOM代码全部无效
	// 以实现更强的自定义

})(window.zbp);