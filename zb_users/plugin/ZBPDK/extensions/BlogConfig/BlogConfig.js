/**
 * 这么丑陋的代码
 * 我强烈建议你们不要看！！
 * 不要看！！！！
 * 再说一遍，不要看！！！
 */
var n;
n = false;

function read(e, d, f)
{
    $("#content").html("Loading");
    $.get("main.php", f,
        function (a) {
            $("#content").html(a);
            $('#content input.checkbox').css("display", "none");
            $('#content input.checkbox[value="1"]').after('<span class="imgcheck imgcheck-on"></span>');
            $('#content input.checkbox[value!="1"]').after('<span class="imgcheck"></span>');
            $('#content span.imgcheck').click(function () {
                ChangeCheckValue(this)
            })
            bmx2table();
        });
    window.setTimeout(function () {
        readleft();
    }, 1000);
    n = false
}

function run2(e, d, h)
{
    var g = {
        act: "e_" + e,
        name: d
    };
    switch (e) {
        case "new":
            var f;
            f = $("#configt tr").last().children("td:first").children("input").val();
            if (f == "NaN") {
                f = 0
            }
            f = f + 1;
            if (n == true) {
                $("#configt").append("<tr><td></td><td>请保存后再新建</td><td></td></tr>");
                return false
            }
            var s = '<img src="../../../../../zb_system/image/admin/page_edit.png" alt="编辑" title="编辑" width="16" />';
            if(zbp.options.blogversion>170000)
                s = '<i class="icon-check-circle-fill" style="color:green;" title="提交" ></i>';
            $("#configt").append("<tr><td><input type='hidden' value='" + (f) + "'/><input type='text' id='txt" + (f) + "'></td><td><textarea id='ta" + (f) + "' style='width:100%'></textarea></td><td><a href='javascript:;' onclick='run2(\"edit\",\"" + f + '",$(this).parents("#content").children("#name").html())\'>'+s+'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp</td></tr>');
            n = true;
            break;
        case "edit":
        case "del":
            g.post = $("#ta" + d).val();
            g.name1 = $("#txt" + d).text() || $("#txt" + d).val();
            g.name2 = $("#name").html();
            g.test = d;
            $("#content").html("Loading");
            $.post("main.php", g,
                function (a) {
                    $("#content").html(a);
                    $('#content input.checkbox').css("display", "none");
                    $('#content input.checkbox[value="1"]').after('<span class="imgcheck imgcheck-on"></span>');
                    $('#content input.checkbox[value!="1"]').after('<span class="imgcheck"></span>');
                    $('#content span.imgcheck').click(function () {
                        ChangeCheckValue(this)
                    })
                    bmx2table();
                });
            break
    }
}

function readleft()
{
    var a = {
        act: "readleft"
    };
    //$("#content").html("Loading");
    $.get("main.php", a,
        function (b) {
            //$("#content").html("");
            $("#tree ul").html(b);
            $.contextMenu({
                selector: '#tree ul li',
                items: {
                    "open": {
                        name: "打开"
                    },
                    "rename": {
                        name: "重命名"
                    },
                    "del": {
                        name: "删除"
                    }
                },
                callback: function (key, options) {
                    //					console.log(this);
                    run(key, $(this).find("a").attr("id"));
                }
            });
        })
}

function nb(b)
{
    var f = {
        act: "new"
    };
    var e = prompt("请输入项名");
    if (e != "" && e != null) {
        f.name = e;
        read(b, e, f);
        bmx2table();
    } else {
        return false
    }
}

function run(f, e)
{
    var h = {
        act: f,
        name: e
    };
    if (e == "BlogConfig") {
        nb("BlogConfig");
        return false
    }
    switch (f) {
        case "open":
            read(f, e, h);
            break;
        case "rename":
            var g = prompt("请输入新项名");
            if (g != "" && g != null) {
                if (confirm("确定要把" + e + "改为" + g + "吗？\n\n请注意，盲目修改名字可能会导致某个插件或整个博客无法打开！")) {
                    h.edit = g;
                    read(f, e, h)
                } else {
                    return false
                }
            } else {
                return false
            }
            break;
        case "del":
            if (window.confirm("单击“确定”继续。单击“取消”停止。")) {
                read(f, e, h)
            }
            break
    }

};

function clk(obj)
{
    $(".clicked").removeClass("clicked");
    $(obj).addClass("clicked");
}
