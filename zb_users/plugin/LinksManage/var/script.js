$(function() {
  if ($(".js-mod").length > 0) {
    let LinksManage = $(".js-mod")
        .val()
        .split("|"),
      mod;
    for (link in LinksManage) {
      if ((mod = LinksManage[link])) {
        $(".widget-list .widget_id_" + mod)
          .addClass("LinksManage")
          .find("a:first")
          .attr(
            "href",
            bloghost + "zb_users/plugin/LinksManage/main.php" + "?edit=" + mod
          );
        $(".SubMenu")
          .find("a[href$='" + mod + "']")
          .addClass("LinksManage")
          .attr(
            "href",
            bloghost + "zb_users/plugin/LinksManage/main.php" + "?edit=" + mod
          );
      }
    }
    // $(".LinksManage").click(function() {
    //   let x = confirm("此处仅能创建UL列表，DIV创建请用“新建模块”");
    //   if (!x) {
    //     return false;
    //   }
    // });
  }
  ///////////
  $(".js-add").click(function() {
    if ($(".LinksManageAdd").length === 1) {
      $("#LinksManageList").append($("tfoot .LinksManageAdd").clone());
    }
    $("#LinksManageList .LinksManageAdd").each(function() {
      $(this).removeClass("LinksManageAdd");
    });
    return;
  });
  $("#LinksManageList td").each(function() {
    let intWidth = $(this).width();
    $(this).css({ width: intWidth });
    // $(this).data("width", intWidth);
  });
  $("#LinksManageList").sortable({
    axis: "y",
    opacity: 0.5,
    cursor: "move",
    delay: 150,
    stop: function(event, ui) {
      $("input[name='sub[]']")
        .next(".imgcheck")
        .click(function() {
          var me = this;
          setTimeout(function() {
            $(me)
              .closest("tr")
              [$(me).hasClass("imgcheck-on") ? "addClass" : "removeClass"](
                "LinksManageSub"
              );
          }, 20);
        });
    }
  });

  $("#LinksManageDel").droppable({
    accept: "#LinksManageList tr",
    addClasses: false,
    create: function(event, ui) {
      $(this).css({
        height: "2em",
        lineHeight: "2em"
      });
    },
    drop: function(event, ui) {
      ui.draggable.remove();
    },
    over: function(event, ui) {
      $(this)
        .find("td")
        .html("松手删除");
    },
    out: function(event, ui) {
      $(this)
        .find("td")
        .html("拖入这里删除");
    },
    activeClass: "active",
    hoverClass: "hover"
  });
  $("input[name='sub[]']")
    .next(".imgcheck")
    .click(function() {
      let me = this;
      setTimeout(function() {
        $(me)
          .closest("tr")
          [$(me).hasClass("imgcheck-on") ? "addClass" : "removeClass"](
            "LinksManageSub"
          );
      }, 20);
    });
});
