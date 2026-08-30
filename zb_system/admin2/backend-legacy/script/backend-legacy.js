/* eslint-disable */

$(document).ready(function() {

  // 处理 jq-hidden 类，用于后续动画效果
  $('.jq-hidden').each(function() {
    $(this).hide();
    $(this).removeClass('hidden');
  });

});
