(function () {
    var jsPath = "";
    try {
        throw Error("Try to get JavaScript Path");
    } catch (ex) {
        if (ex.stack) { // Chrome & IE10+ & Firefox
            jsPath = ex.stack.match(/(at.+|\@)(http.+?js)(\?\d+)?\:\d+\:\d+/ || ['', '', ''])[2];

        } else { // IE6
            var scripts = document.getElementsByTagName("script");
            for (var item in scripts) {
                if (scripts[item].src && scripts[item].src.match("common\.js$")) {
                    jsPath = scripts[item].src;
                }
            }
        }
    }
    jsPath = jsPath.replace(/common\.js$/, "");
    var createElement = function (src) {
        document.write('<script src="' + jsPath + src + '"></script>');
    };
    createElement("jquery-1.8.3.min.js");
    createElement("zblogphp.js");
})();
var SetCookie = function () {
    return zbp.cookie.set.apply(null, arguments); };
var GetCookie = function () {
    return zbp.cookie.get.apply(null, arguments); };
var LoadRememberInfo = function () {
    zbp.userinfo.output.apply(null); return false;};
var SaveRememberInfo = function () {
    zbp.userinfo.saveFromHtml.apply(null); return false;};
var RevertComment = function () {
    zbp.comment.reply.apply(null, arguments); return false;} ;
var GetComments = function () {
    zbp.comment.get.apply(null, arguments); return false;} ;
var VerifyMessage = function () {
    zbp.comment.post.apply(null); return false;};
