zbp.plugin.unbind("comment.reply", "system");
zbp.plugin.on("comment.reply", "HTML5CSS3", function(i) {
    if ($("#inpRevID").val() != 0) return false;

    $("#inpRevID").val(i);
    $("#AjaxComment" + i).next().after("<dl id=\"reply\">" + $("#postcmt").html() + "</dl>");
    $("#postcmt").hide("slow").html("");
    $("#cancel-reply").show().bind("click", function() {
        $("#inpRevID").val(0);
        $(this).hide().prev().show();
        $("#postcmt").html($("#reply").html()).show("slow");
        $("#reply").remove();
        window.location.hash = "#comment";
        return false;
    }).prev().hide();
    $("#reply").show("slow");
    window.location.hash = "#reply";
});

zbp.plugin.on("comment.postsuccess", "default", function () {
	$("#cancel-reply").click();
});