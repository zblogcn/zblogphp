$(function () {
    $("tr").each(function (index, val) {
        var $val = $(val);
        var $td = $val.find("td:eq(6)")
        var $origA = $td.find("a:eq(1)");
        if ($origA.length <= 0) {
            return;
        }
        var $a = $origA.clone();
        var idArray = $a.attr("href").match(/id=(\d+)/);
        var tokenArray = $a.attr("href").match(/token=([a-z0-9]+)/);
        if (idArray.length <= 0) {
            return;
        }
        if (tokenArray.length <= 0) {
            return;
        }
        var id = idArray[1];
        var token = tokenArray[1];
        $a.attr("href", "../../zb_users/plugin/Totoro/action.php?act=blockip&id=" + id + "&token=" + token);
        $a.find("img").attr("src", "../image/admin/exclamation.png").attr("alt", "拦截IP并加入审核").attr("title", "拦截IP并加入审核");
        $td.append("&nbsp;&nbsp;&nbsp;&nbsp;").append($a);
    })
});

