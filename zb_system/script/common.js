var SetCookie = function () { return zbp.cookie.set.apply(null, arguments); }
var GetCookie = function () { return zbp.cookie.get.apply(null, arguments); }
var LoadRememberInfo = function () { zbp.userInfo.output.apply(null); return false;}
var SaveRememberInfo = function () { zbp.userInfo.saveFromHtml.apply(null); return false;} 
var RevertComment = function () { zbp.comment.revert.apply(null); return false;} 
var GetComments = function () { zbp.comment.get.apply(null); return false;} 
var VerifyMessage = function () { zbp.comment.post.apply(null); return false;}