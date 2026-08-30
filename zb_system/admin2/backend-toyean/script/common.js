$(function () {
	// 主题初始化
	if (localStorage.getItem('zblog_admin_theme') === 'night') {
		$('body').addClass('night');
		$('.theme').removeClass('light').addClass('dark');
	}

	// 主题切换
	$(document).on('click', '.theme', function (e) {
		e.preventDefault();
		if ($('body').hasClass('night')) {
			$('body').removeClass('night');
			$(this).removeClass('dark').addClass('light');
			localStorage.setItem('zblog_admin_theme', 'light');
		} else {
			$('body').addClass('night');
			$(this).removeClass('light').addClass('dark');
			localStorage.setItem('zblog_admin_theme', 'night');
		}
	});

	$(document).on('click', '.userlink', function (e) {
		e.stopPropagation();
		$(".usermenu").toggleClass("on");
	});
	$(document).on("click", function (e) {
		if (!$(e.target).closest('.userlink, .usermenu').length) {
			$(".usermenu").removeClass("on");
		}
	});
	$(document).on('click', '.menuico', function (e) {
		e.stopPropagation();
		$(".side").toggleClass("on");
		$(this).toggleClass("on");
	});

	// 移动端侧边栏关闭按钮
	$(document).on('click', '.mobile-close', function (e) {
		e.stopPropagation();
		$(".side").removeClass("on");
		$(".menuico").removeClass("on");
	});

	$('<div class="menutip" id="menutip"></div>').appendTo('body');
	const $menu = $('.menu');
	const $menuItems = $menu.find('a');
	const $tooltip = $('#menutip');

	// 显示tips
	function showTooltip($link) {
		const title = $link.data('title');
		if (!title) return;
		// 获取菜单位置
		const linkRect = $link[0].getBoundingClientRect();
		// 设置tips内容
		$tooltip.text(title);
		// Y定位计算
		let top = linkRect.top + (linkRect.height / 2) - ($tooltip.outerHeight() / 2);
		// 确保tips不会超出屏幕
		const windowHeight = $(window).height();
		const tooltipHeight = $tooltip.outerHeight();
		if (top < 10) {
			top = 10;
		} else if (top + tooltipHeight > windowHeight - 10) {
			top = windowHeight - tooltipHeight - 10;
		}
		// 定位
		$tooltip.css({
			top: top
		});
		$tooltip.show();
	}

	// 隐藏tips
	function hideTooltip() {
		$tooltip.hide();
	}

	// 初始化
	$menuItems.each(function () {
		const $link = $(this);
		// 鼠标进入
		$link.on('mouseenter', function () {
			if ($('.side').hasClass('on')) {
				showTooltip($(this));
			}
		});
		// 鼠标离开
		$link.on('mouseleave', function () {
			hideTooltip();
		});
	});

	// 菜单滚动时隐藏tips
	$menu.on('scroll', function () {
		hideTooltip();
	});

	// 窗口大小变化重新计算
	$(window).on('resize', function () {
		if ($tooltip.is(':visible')) {
			let $activeLink = null;
			// 找到当前鼠标所在的菜单项
			$menuItems.each(function () {
				const $link = $(this);
				if ($link.is(':hover')) {
					$activeLink = $link;
					return false; // 退出循环
				}
			});
			if ($activeLink) {
				showTooltip($activeLink);
			} else {
				hideTooltip();
			}
		}
	});
});