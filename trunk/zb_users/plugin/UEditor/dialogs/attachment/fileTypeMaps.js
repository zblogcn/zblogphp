
//By zsx

//文件类型图标索引
function getIco(t){
	var Tag;
	t=t.split(".")[t.split(".").length-1];
	switch (t){
		case "jar":
		case "jad":		Tag="jar";break;
		case "txt":
		case "config":
		case "ini":
		case "inf":
		case "log": Tag="txt";break;
		case "doc":
		case "docx":
		case "docm":
		case "dot":
		case "dotx":
		case "docm":
		case "odt":
		case "wpd":
		case "rtf":
		case "wps": Tag="doc";break;
		case "ppt":
		case "pptx":
		case "pptm":
		case "ppsx":
		case "pps":
		case "ppsm":
		case "potx":
		case "pot":
		case "potm":
		case "odp": Tag="ppt";break;
		case "xls":
		case "xlsm":
		case "xlsb":
		case "xl":
		case "xlam":
		case "xltc":
		case "xltm":
		case "xla":
		case "odc":
		case "ods": Tag="xls";break;
		case "pdf": Tag="pdf";break;
		case "sql": Tag="sql";break;
		case "mp3":
		case "wma":
		case "wav":
		case "ogg": Tag="msc";break;
		case "mpg":
		case "mpeg":
		case "avi":
		case "rm":
		case "rmvb":
		case "vob":
		case "dat":
		case "mp4":
		case "3gp":
		case "flv":
		case "swf":
		case "mkv":
		case "mov": Tag="mov";break;
		case "exe":
		case "com": Tag="exe";break;
		case "dll":
		case "ocx":
		case "sys":
		case "db": Tag="dll";break;
		case "bat":
		case "cmd": Tag="bat";break;
		case "asp":
		case "php":
		case "jsp":
		case "js":
		case "css":
		case "inc":
		case "asa":
		case "asax":
		case "aspx":
		case "mhtml":
		case "shtml":
		case "py":  Tag="code";break;
		case "jpg":
		case "jpeg":
		case "gif":
		case "bmp":
		case "png":
		case "tiff":
		case "ico": Tag="img";break;
		case "htm":
		case "html":
		case "xml":  Tag="htm";break;
		case "rar":
		case "zip":
		case "7z":
		case "gz":  Tag="rar";break;
		case "mdb": Tag="acc";break;
		case "zba":
		case "zti":
		case "zpi": Tag="zba";break;
		default:Tag="no"
	}
	return "zb_system/image/filetype/"+Tag+".png"
}
