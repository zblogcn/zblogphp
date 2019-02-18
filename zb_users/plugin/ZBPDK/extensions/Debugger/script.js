/**
 * Debugger script
 */
if (window.jQuery) {
    //纯Javascript版本，仅兼容现代浏览器
    document.addEventListener("DOMContentLoaded", function () {
        var debug_modal = document.getElementById("debug-container");
        if (debug_modal) {
            //从localStorage获取并设置初始状态
            //TAB位置
            var tab_index = parseInt(localStorage.getItem("zbpdk_debugger_tabindex"));
            if (!isNaN(tab_index) && tab_index >= 0 && tab_index < 5) {
                document.querySelector("#debug-tabs li:nth-of-type(" + (tab_index + 1) + ")").classList.add("debug-tab-on");
                document.querySelector("#debug-content li:nth-of-type(" + (tab_index + 1) + ")").style.display = "block";
            } else {
                document.querySelector("#debug-tabs>li:first-child").classList.add("debug-tab-on");
                document.querySelector("#debug-content>li:first-child").style.display = "block";
            }

            //调试器窗口大小
            var debugger_size = localStorage.getItem("zbpdk_debugger_size");
            if (typeof debugger_size == "string" && debugger_size == "true") {
                debug_modal.style.height = "100%";
                document.getElementById("debug-ctl-size").innerHTML = "&or;";
                document.getElementById("debug-ctl-size").setAttribute("title", "收起");
            }

            //调试器显示状态
            var debugger_show = localStorage.getItem("zbpdk_debugger_show");
            if (typeof debugger_show == "string" && debugger_show == "true") {
                debug_modal.style.display = "block";
                document.getElementById("debug-ctl-show").innerHTML = "●";
                document.getElementById("debug-ctl-show").setAttribute("title", "取消固定");
            }

            /**
             * 绑定点击事件
             * 记录显示状态并储存至localStorage
             */
            //打开按钮
            document.getElementById("debug-ctl-open").addEventListener("click", function () {
                debug_modal.style.display = "block";
            });

            //关闭按钮
            document.getElementById("debug-ctl-close").addEventListener("click", function () {
                debug_modal.style.display = "none";
            });

            //固定显示按钮
            document.getElementById("debug-ctl-show").addEventListener("click", function () {
                if (this.innerText == "○") {
                    this.innerHTML = "●";
                    this.setAttribute("title", "取消固定");
                    localStorage.setItem("zbpdk_debugger_show", true);
                } else {
                    this.innerHTML = "○";
                    this.setAttribute("title", "固定");
                    localStorage.setItem("zbpdk_debugger_show", false);
                }
            });

            //窗口大小调整按钮
            document.getElementById("debug-ctl-size").addEventListener("click", function () {
                if (this.innerText == "∧") {
                    debug_modal.style.height = "100%";
                    this.innerHTML = "&or;";
                    this.setAttribute("title", "收起");
                    document.getElementsByTagName("html")[0].style.overflow = "hidden";
                    localStorage.setItem("zbpdk_debugger_size", true);
                } else {
                    debug_modal.style.height = "50%";
                    this.innerHTML = "&and;";
                    this.setAttribute("title", "展开");
                    document.getElementsByTagName("html")[0].style.overflow = "auto";
                    localStorage.setItem("zbpdk_debugger_size", false);
                }
            });

            //TAB按钮
            document.querySelectorAll("#debug-tabs li").forEach(function (element) {
                element.addEventListener("click", function () {
                    if (!this.classList.contains("debug-tab-on")) {
                        const siblings = Array.from(this.parentElement.children);
                        var index = siblings.indexOf(this);
                        siblings.forEach(function (el) {
                            el.classList.remove("debug-tab-on");
                        });
                        this.classList.add("debug-tab-on");
                        document.querySelectorAll("#debug-content li").forEach(function (e) {
                            e.style.display = "none";
                        });
                        document.querySelector("#debug-content li:nth-of-type(" + (index + 1) + ")").style.display = "block";
                        localStorage.setItem("zbpdk_debugger_tabindex", index);
                    }
                })
            });
        }

        //AJAX获取接口详情
        document.querySelectorAll(".debug-plg-detail").forEach(function (element) {
            element.addEventListener("click", function () {
                if (element.classList.contains("debug-plg-detail-show")) {
                    element.parentElement.parentElement.nextElementSibling.style.display = "none";
                } else {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            element.parentElement.parentElement.nextElementSibling.children[0].innerHTML = JSON.parse(this.responseText)[1];
                        }
                    };
                    xhttp.open("GET", window.bloghost + "zb_users/plugin/ZBPDK/extensions/Debugger/api.php?action=detail&interface=" + element.getAttribute("interface") + "&func=" + element.getAttribute("func"), true);
                    xhttp.send();
                    element.parentElement.parentElement.nextElementSibling.removeAttribute("style");
                }
                element.classList.toggle("debug-plg-detail-show");
            });
        });
    });
} else {
    /**
     * jQuery版本，兼容性由jQuery版本决定
     * 代码结构与纯js版类似，注释请参照纯js版
      */
    $(function () {
        var $debug_modal = $("#debug-container");

        if ($debug_modal.length) {
            var tab_index = parseInt(localStorage.getItem("zbpdk_debugger_tabindex"));
            if (!isNaN(tab_index) && tab_index >= 0 && tab_index < 5) {
                $("#debug-tabs li:eq(" + tab_index + ")").addClass("debug-tab-on");
                $("#debug-content li:eq(" + tab_index + ")").css("display", "block");
            } else {
                $("#debug-tabs>li:first-child").addClass("debug-tab-on");
                $("#debug-content>li:first-child").css("display", "block");
            }

            var debugger_size = localStorage.getItem("zbpdk_debugger_size");
            if (typeof debugger_size == "string" && debugger_size == "true") {
                $debug_modal.css("height", "100%");
                $("#debug-ctl-size").html("&or;").attr("title", "收起");
                $("html").css("overflow", "hidden");
            }
            
            var debugger_show = localStorage.getItem("zbpdk_debugger_show");
            if (typeof debugger_show == "string" && debugger_show == "true") {
                $debug_modal.css("display", "block");
                $("#debug-ctl-show").html("●").attr("title", "取消固定");
            }

            $("#debug-ctl-open").click(function () {
                $debug_modal.css("display", "block");
            });

            $("#debug-ctl-close").click(function () {
                $debug_modal.css("display", "none");
            });

            $("#debug-ctl-show").click(function () {
                if ($(this).text() == "○") {
                    $(this).html("●").attr("title", "取消固定");
                    localStorage.setItem("zbpdk_debugger_show", true);
                } else {
                    $(this).html("○").attr("title", "固定");
                    localStorage.setItem("zbpdk_debugger_show", false);
                }
            });

            $("#debug-ctl-size").click(function () {
                if ($(this).text() == "∧") {
                    $debug_modal.css("height", "100%");
                    $(this).html("&or;").attr("title", "收起");
                    $("html").css("overflow", "hidden");
                    localStorage.setItem("zbpdk_debugger_size", true);
                } else {
                    $debug_modal.css("height", "50%");
                    $(this).html("&and;").attr("title", "展开");
                    $("html").css("overflow", "auto");
                    localStorage.setItem("zbpdk_debugger_size", false);
                }
            });

            $("#debug-tabs li").click(function () {
                var index = $(this).index();
                $(this).siblings("li").removeClass("debug-tab-on");
                $(this).addClass("debug-tab-on");
                $("#debug-content").children("li").hide();
                $("#debug-content li:eq(" + index + ")").css("display", "block");
                localStorage.setItem("zbpdk_debugger_tabindex", index);
            });
        }

        $(".debug-plg-detail").click(function () {
            if ($(this).hasClass("debug-plg-detail-show")) {
                $(this).parents("tr").next("tr").hide();
            } else {
                var that = this;
                $.get(window.bloghost + "zb_users/plugin/ZBPDK/extensions/Debugger/api.php?action=detail&interface=" + $(this).attr("interface") + "&func=" + $(this).attr("func"), function (res) {
                    $(that).parents("tr").next("tr").children().html(res[1]);
                });
                $(this).parents("tr").next("tr").show();
            }
            $(this).toggleClass("debug-plg-detail-show");
        });
    });
}