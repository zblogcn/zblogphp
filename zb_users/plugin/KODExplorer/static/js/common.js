/*
* iframe之间函数调用
*
* main frame中每个frame需要name和id，以便兼容多浏览器
* 如果需要提供给其他frame调用，则需要在body中加入
* <input id="FrameCall" type='hidden' action='' value='' onclick='FrameCall.api()'/>
* 调用例子：Frame.doFunction('main','goUrl','"'+url+'"');该frame调用id为main的兄弟frame的goUrl方法，参数为后面的
* 参数为字符串时需要加引号，否则传过去会被理解成一个未定义变量
*/
var FrameCall = (function(){
	var idName 		= "FrameCall";
	var idNameAll	= "#"+idName;
	var ie = !-[1,];//是否ie
	return{
		apiOpen:function(){
			var html = '<input id="FrameCall" type="hidden" action="1" value="1" onclick="FrameCall.api()" />';
			$(html).prependTo('body');
		},
		//其他窗口调用该窗口函数，调用另一个frame的方法
		api:function(){
			var action = $(idNameAll).attr('action');
			var value=$(idNameAll).attr('value');
			var fun=action+'('+value+');';//拼装执行语句，字符串转换到代码
			eval(fun);
		},

		//该窗口调用顶层窗口的子窗口api,调用iframe框架的js函数.封装控制器。
		top:function(iframe,action,value){
			//var obj = window.top.frames[iframe].document;
			var obj = window.parent.frames[iframe].document;
			obj=obj.getElementById(idName);		
			$(obj).attr("action",action);
			$(obj).attr("value",value);
			obj.click();
		},
		//该窗口调用父窗口的api
		father:function(action,value){
			if (ie){//获取兄弟frame的dom树
				var obj=window.parent.document;//IE
			}else{
				var obj=window.parent.document;//chrome safari firefox...
			}
			obj=obj.getElementById(idName);	
			$(obj).attr("action",action);
			$(obj).attr("value",value);
			obj.click();	
		},
		//___自定义通用方法，可在页面定义更多提供给接口使用的api。
		goUrl:function(url){
			window.location.href=url;
		},
		goRefresh:function(){
			window.location.reload(); 
		}
	}
})();

$(document).ready(function() {
	FrameCall.apiOpen();
});

//是否在数组中。
var inArray = function(arr,value) {
    for (var i=0,l = arr.length ; i <l ; i++) {
        if (arr[i] === value) {
            return true;
        }
    }
    return false;
}
var stopPP = function(e){//防止事件冒泡
	e = e || window.event;
	if (e.stopPropagation) {
		e.stopPropagation();
	} else {
		e.cancelBubble = true;
	}
	if (e.preventDefault) {
		e.preventDefault();
	} else {
		e.returnValue = false;
	}
}

//URL 编码,utf-8实现，同return encodeURIComponent(string);
//查看文章：http://www.ruanyifeng.com/blog/2010/02/url_encoding.html
var  urlEncode = function(string) {
	if(string == undefined || string == '') return '';
	var str = string.replace(/\r\n/g,"\n");
	var utftext = "";  
	for (var n = 0; n < str.length; n++) {  
		var c = str.charCodeAt(n);  
		if (c < 128) {
			utftext += String.fromCharCode(c);
		}
		else if((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}
	}	
	utftext=utftext.replace('+','%2B');
	//utftext=utftext.replace(' ','%20');
	return escape(utftext);
}
var urlDecode = function(string) {
	if(string == undefined || string == '') return '';
	var utftext=unescape(string);
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;  
	while ( i < utftext.length ) {  
		c = utftext.charCodeAt(i);  
		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		}
		else if((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		}
		else {
			c2 = utftext.charCodeAt(i+1);
			c3 = utftext.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}  
	}
	string=string.replace('%2B','+');
	return string;
}