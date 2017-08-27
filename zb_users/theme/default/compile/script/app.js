const $ = require('jquery')
const zbp = require('zbp')
const templateName = 'default'

$(() => {
  $("#divNavBar a").each(function () {
    if (this.href == location.href.toString().split("#")[0]) {
      $(this).addClass("on")
      return false
    }
  })
})

zbp.plugin.unbind("comment.reply", "system")
zbp.plugin.on("comment.reply", templateName, id => {
  $("#inpRevID").val(id)
  const frm = $('#divCommentPost')
  const cancel = $("#cancel-reply")

  frm.before($("<div id='temp-frm' style='display:none'>")).addClass("reply-frm")
  $(`#AjaxComment${id}`).before(frm)

  cancel.show().click(function() {
    const temp = $('#temp-frm')
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

zbp.plugin.on("comment.get", templateName, (logid, page) => {
  $('span.commentspage').html("Waiting...")
})

zbp.plugin.on("comment.got", templateName, () => {
  $("#cancel-reply").click()
})

zbp.plugin.on("comment.postsuccess", templateName, () => {
  $("#cancel-reply").click()
})
