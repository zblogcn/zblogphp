//nice-select jQuery plugin
(function($) {
	$.fn.niceSelect = function(method) {
		// Methods
		if (typeof method == 'string') {
			if (method == 'update') {
				this.each(function() {
					var $select = $(this);
					var $dropdown = $(this).next('.nice-select');
					var open = $dropdown.hasClass('open');
					if ($dropdown.length) {
						$dropdown.remove();
						create_nice_select($select);
						if (open) {
							$select.next().trigger('click');
						}
					}
				});
			} else if (method == 'destroy') {
				this.each(function() {
				var $select = $(this);
				var $dropdown = $(this).next('.nice-select');

				if ($dropdown.length) {
					$dropdown.remove();
					$select.css('display', '');
				}
				});
				if ($('.nice-select').length == 0) {
					$(document).off('.nice_select');
				}
			} else {
				console.log('Method "' + method + '" does not exist.');
			}
			return this;
		}

		// Hide native select
		this.hide();

		// Create custom markup
		this.each(function() {
			var $select = $(this);
			if (!$select.next().hasClass('nice-select')) {
				create_nice_select($select);
			}
		});

		function create_nice_select($select) {
			var isMultiple = $select.attr('multiple');
			// 创建 nice-select 元素
			var $niceSelect = $('<div></div>')
			.addClass('nice-select')
			.addClass($select.attr('class') || '')
			.addClass($select.attr('disabled') ? 'disabled' : '')
			.attr('tabindex', $select.attr('disabled') ? null : '0')
			.html('<span class="current"></span><ul class="list"></ul>');

			// 检查 select 元素的 style 属性中是否设置了 width
			var styleAttr = $select.attr('style');
			if (styleAttr && styleAttr.includes('width')) {
				var selectWidth = $select.css('width');
				if (selectWidth) {
					$niceSelect.css('width', selectWidth);
				}
			}

			$select.after($niceSelect);

			var $dropdown = $select.next();
			var $options = $select.find('option');
			var $selected = $select.find('option:selected');

			if (isMultiple) {
				$dropdown.addClass('multiple');
				$dropdown.find('.current').html(tpure.lang.select);
				$dropdown.find('.list').prepend('<li class="option all" data-value="all">'+tpure.lang.selectall+'</li>');
			} else {
				$dropdown.find('.current').html($selected.data('display') || $selected.text());
			}

			$options.each(function(i) {
				var $option = $(this);
				var display = $option.data('display');
				$dropdown.find('ul').append($('<li></li>')
				.attr('data-value', $option.val())
				.attr('data-display', (display || null))
				.addClass('option' +
					($option.is(':selected') ? ' selected' : '') +
					($option.is(':disabled') ? ' disabled' : ''))
				.html($option.text())
				);
			});

			if (isMultiple) {
				updateMultipleCurrent($select, $dropdown);
				// 检查所有可勾选选项是否都已选中，更新全选选项状态
				var allOptions = $dropdown.find('.option:not(.disabled):not(.all)');
				var allSelected = allOptions.length > 0 && allOptions.filter('.selected').length === allOptions.length;
				$dropdown.find('.option.all').toggleClass('selected', allSelected);
				// 更新原生 select 中对应全选选项的状态
				$select.find('option[value="all"]').prop('selected', allSelected);
			}
		}

		function updateMultipleCurrent($select, $dropdown) {
			var selectedOptions = $select.find('option:selected:not(:disabled)'); // 过滤掉禁用的选项
			var selectedTexts = [];
			selectedOptions.each(function() {
				// 过滤掉全选选项，使用文本内容添加到选中文本数组
				if ($(this).val() !== 'all') {
					selectedTexts.push($(this).text());
				}
			});
			if (selectedTexts.length === 0) {
				$dropdown.find('.current').html(tpure.lang.select);
			} else if (selectedTexts.length === 1) {
				$dropdown.find('.current').html(selectedTexts[0]);
			} else {
				$dropdown.find('.current').html(selectedTexts.join(', '));
			}
			if (selectedTexts.length > 1) {
				$dropdown.find('.current').addClass('multiple-selected');
			} else {
				$dropdown.find('.current').removeClass('multiple-selected');
			}
		}

		/* Event listeners */

		// Unbind existing events in case that the plugin has been initialized before
		$(document).off('.nice_select');

		// Open/close
		$(document).on('click.nice_select', '.nice-select', function(event) {
			var $dropdown = $(this);
			$('.nice-select').not($dropdown).removeClass('open');
			$dropdown.toggleClass('open');
			if (!$dropdown.hasClass('multiple')) {
				if ($dropdown.hasClass('open')) {
					$dropdown.find('.option');
					$dropdown.find('.focus').removeClass('focus');
					$dropdown.find('.selected').addClass('focus');
				} else {
					$dropdown.focus();
				}
			}
		});

		// Close when clicking outside
		$(document).on('click.nice_select', function(event) {
			if ($(event.target).closest('.nice-select').length === 0) {
				$('.nice-select').removeClass('open').find('.option');
			}
		});

		// Option click
		$(document).on('click.nice_select', '.nice-select .option:not(.disabled)', function(event) {
			var $option = $(this);
			var $dropdown = $option.closest('.nice-select');
			var isMultiple = $dropdown.hasClass('multiple');

			if (isMultiple) {
				if ($option.hasClass('all')) {
					var options = $dropdown.find('.option:not(.disabled):not(.all)'); // 过滤掉禁用和全选选项
					var isAllSelected = options.filter('.selected').length === options.length;
					options.toggleClass('selected', !isAllSelected);
					$option.toggleClass('selected', !isAllSelected);
					$dropdown.prev('select').find('option:not(:disabled):not([value="all"])').prop('selected', !isAllSelected);
				} else {
					$option.toggleClass('selected');
					$dropdown.prev('select').find('option[value="' + $option.data('value') + '"]').prop('selected', $option.hasClass('selected'));

					// 检查所有可勾选选项是否都已选中
					var allOptions = $dropdown.find('.option:not(.disabled):not(.all)');
					var allSelected = allOptions.length > 0 && allOptions.filter('.selected').length === allOptions.length;
					$dropdown.find('.option.all').toggleClass('selected', allSelected);
					// 更新原生 select 中对应全选选项的状态
					$dropdown.prev('select').find('option[value="all"]').prop('selected', allSelected);
				}
				updateMultipleCurrent($dropdown.prev('select'), $dropdown);
				// 多选时不关闭列表
				event.stopPropagation();
			} else {
				$dropdown.find('.selected').removeClass('selected');
				$option.addClass('selected');
				var text = $option.data('display') || $option.text();
				$dropdown.find('.current').text(text);
				$dropdown.prev('select').val($option.data('value')).trigger('change');
			}
		});

		// Keyboard events
		$(document).on('keydown.nice_select', '.nice-select', function(event) {
			var $dropdown = $(this);
			var $focused_option = $($dropdown.find('.focus') || $dropdown.find('.list .option.selected'));

			// Space or Enter
			if (event.keyCode == 32 || event.keyCode == 13) {
				if ($dropdown.hasClass('open')) {
					$focused_option.trigger('click');
				} else {
					$dropdown.trigger('click');
				}
				return false;
			// Down
			} else if (event.keyCode == 40) {
				if (!$dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				} else {
					var $next = $focused_option.nextAll('.option:not(.disabled)').first();
					if ($next.length > 0) {
						$dropdown.find('.focus').removeClass('focus');
						$next.addClass('focus');
					}
				}
				return false;
			// Up
			} else if (event.keyCode == 38) {
				if (!$dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				} else {
					var $prev = $focused_option.prevAll('.option:not(.disabled)').first();
					if ($prev.length > 0) {
						$dropdown.find('.focus').removeClass('focus');
						$prev.addClass('focus');
					}
				}
				return false;
			// Esc
			} else if (event.keyCode == 27) {
				if ($dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				}
			// Tab
			} else if (event.keyCode == 9) {
				if ($dropdown.hasClass('open')) {
					return false;
				}
			}
		});

		// Detect CSS pointer-events support, for IE <= 10. From Modernizr.
		var style = document.createElement('a').style;
		style.cssText = 'pointer-events:auto';
		if (style.pointerEvents !== 'auto') {
			$('html').addClass('no-csspointerevents');
		}
		return this;
	};
}(jQuery));

$(function(){

  // 处理 jq-hidden 类，用于后续动画效果
  $('.jq-hidden').each(function() {
    $(this).hide();
    $(this).removeClass('hidden');
  });

	$("select").niceSelect(); //接管select菜单

	//右上角用户菜单
	$(document).on('click', '.userlink', function(e){
		e.stopPropagation();
		$(".userlink,.usermenu").toggleClass("on");
	});
	$(document).on("click",function(e){
		if (!$(e.target).closest('.userlink, .usermenu').length) {
			$(".userlink,.usermenu").removeClass("on");
		}
	});

	!function(){
		const nightValue = (/;\s*night=([^;]*)/.exec(';'+document.cookie)||[,])[1];
		if (nightValue === '1') {
			document.body.classList.add('night');
		}
	}();
	if(toyean.night){
		if((new Date().getHours() > toyean.setnightstart || new Date().getHours() < toyean.setnightover) && toyean.setnightauto){
			$(".head .theme").hide();
			zbp.cookie.set('night','1');
			$('body').addClass('night');
			$(".head .theme").attr("title","开灯").addClass("dark");
			console.log('夜间模式自动开启');
		}else if(toyean.setnightauto){
			$(".head .theme").hide();
			zbp.cookie.set('night','0');
			$('body').removeClass('night');
			$(".head .theme").attr("title","关灯").removeClass("dark");
			console.log('夜间模式自动关闭');
		}else{
			$(".head .theme").show();
		}
		if(zbp.cookie.get('night') == '1' || $('body').hasClass('night')){
			$(".head .theme").attr("title","开灯").addClass("dark");
		}else{
			$(".head .theme").attr("title","关灯").removeClass("dark");
		}
		$(".head .theme").on("click",function(){
			if(zbp.cookie.get('night') == '1' || $('body').hasClass('night')){
				zbp.cookie.set('night','0');
				$('body').removeClass('night');
				$(".head .theme").attr("title","关灯").removeClass("dark");
				console.log('夜间模式关闭');
			}else{
				zbp.cookie.set('night','1');
				$('body').addClass('night');
				$(".head .theme").attr("title","开灯").addClass("dark");
				console.log('夜间模式开启');
			}
		});
	}

	//菜单展开与折叠
	$(document).on('click', '.menuico', function (e) {
		e.stopPropagation();
		const flag = !$('.side').hasClass('on');   // 点击后要变成的状态
		$(".side,.fademask,.main").toggleClass('on', flag);
		zbp.cookie.set('side', flag ? '1' : '0');   // 你自己的封装
	});

	$(document).on('click', '.sideclose,.fademask', function (e) {
		e.stopPropagation();
		const flag = !$('.side').hasClass('on');   // 点击后要变成的状态
		$(".side,.fademask,.main").toggleClass('on', flag);
		zbp.cookie.set('side', flag ? '1' : '0');   // 你自己的封装
	});


	$(window).resize(function(){
		if($(window).width() > 860){
			/* 读：DOM 就绪后恢复 */
			const stored = zbp.cookie.get('side');
			//页面宽度大于860时记忆菜单状态
			if (stored === '1') {
				$('.side').addClass('on');
				$('.fademask').addClass('on');
				$('.main').addClass('on');
			}
		}
	});


	//主菜单tips
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
	$menuItems.each(function() {
		const $link = $(this);
		// 鼠标进入
		$link.on('mouseenter', function() {
			if ($('.side').hasClass('on')) {
				showTooltip($(this));
			}
		});
		// 鼠标离开
		$link.on('mouseleave', function() {
			hideTooltip();
		});
	});

	// 菜单滚动时隐藏tips
	$menu.on('scroll', function() {
		hideTooltip();
	});

	//最新动态时间分离
  $('.listcard.two a').each(function() {
        const UpInfotext = $(this).html();
        // 匹配日期格式 (YYYY-MM-DD)
        const dateRegex = /\((\d{4}-\d{2}-\d{2})\)/g;
        const newUpInfotext = UpInfotext.replace(dateRegex, '<span>$1</span>');
        // 更新链接内容
        if (newUpInfotext !== UpInfotext) {
            $(this).html(newUpInfotext);
        }
  });

});
