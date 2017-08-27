$(document).ready(function() {
  var s = document.location
  $("#divNavBar a").each(function() {
    if (this.href == s.toString().split("#")[0]) {
      $(this).addClass("on")
      return false
    }
  })
})

zbp.plugin.unbind("comment.reply", "system")
zbp.plugin.on("comment.reply", "default", function(id) {
  var i = id
  $("#inpRevID").val(i)
  var frm = $('#divCommentPost')
  var cancel = $("#cancel-reply")

  frm.before($("<div id='temp-frm' style='display:none'>")).addClass("reply-frm")
  $('#AjaxComment' + i).before(frm)

  cancel.show().click(function() {
    var temp = $('#temp-frm')
    $("#inpRevID").val(0)
    if (!temp.length || !frm.length) return
    temp.before(frm)
    temp.remove()
    $(this).hide()
    frm.removeClass("reply-frm")
    return false
  })
  try {
    $('#txaArticle').focus()
  } catch (e) {

  }
  return false
})

zbp.plugin.on("comment.get", "default", function (logid, page) {
  $('span.commentspage').html("Waiting...")
})

zbp.plugin.on("comment.got", "default", function () {
  $("#cancel-reply").click()
})

zbp.plugin.on("comment.postsuccess", "default", function () {
  $("#cancel-reply").click()
})