// Define search commands. Depends on dialog.js or another
// implementation of the openDialog method.

// Replace works a little oddly -- it will do the replace on the next
// Ctrl-G (or whatever is bound to findNext) press. You prevent a
// replace by making sure the match is no longer selected when hitting
// Ctrl-G.

(function() {
  function searchOverlay(query) {
    if (typeof query == "string") return {token: function(stream) {
      if (stream.match(query)) return "searching";
      stream.next();
      stream.skipTo(query.charAt(0)) || stream.skipToEnd();
    }};
    return {token: function(stream) {
      if (stream.match(query)) return "searching";
      while (!stream.eol()) {
        stream.next();
        if (stream.match(query, false)) break;
      }
    }};
  }

  function SearchState() {
    this.posFrom = this.posTo = this.query = null;
    this.overlay = null;
  }
  function getSearchState(cm) {
    return cm._searchState || (cm._searchState = new SearchState());
  }
  function getSearchCursor(cm, query, pos) {
    return cm.getSearchCursor(query, pos, typeof query == "string" && query == query.toLowerCase());
  }
  function parseQuery(query) {
    var isRE = query.match(/^\/(.*)\/([a-z]*)$/);
    return isRE ? new RegExp(isRE[1], isRE[2].indexOf("i") == -1 ? "" : "i") : query;
  }
  function doSearch(cm, rev) {
  	console.log('search start,',editor_current.search,','+$('#code_find').val())
    var state = getSearchState(cm);
	if (state.query && editor_current.search == $('#code_find').val()){
		return findNext(cm, rev);
	}else{
		var selected_str=cm.getSelection();//选中内容
		// 搜索内容改变条件：选择后重新搜索、搜索框内容改变
		if (editor_current.search!= undefined
		 && selected_str != ''
		 && selected_str != editor_current.search) {
			$('#code_find').val(selected_str);
		}
		console.log('------,',editor_current.search,','+$('#code_find').val())

		$.dialog({
			title:"字符串查找替换",
			id:'code_find',
			close:function(){//搜索对话框被关闭，则清空搜索选中状态。
				editor_current.search = undefined;
				editor_current.focus();
				clearSearch(cm);
			},
			content:'<div class="findbox">'+
				'<div class="line"><span>查找：</span>'+
				'<input type="text" id="code_find" value="'+selected_str+'"/>'+
				'<button id="id_find_find" title="向下查找">查找</button>'+
				'<button id="id_find_findpre">上一个</button></div>'+
				'<div class="line"><span>替换：</span>'+
				'<input type="text" id="code_replace" />'+
				'<button id="id_find_replace">替换</button>'+
				'<button id="id_find_replaceall">替换全部</button></div>'+
				'<div class="info">查找后即高亮选中，可以替换</div></div>'
		});
		$('#code_find').focus();
		query = $('#code_find').val();
		if (query == '') return;
		editor_current.search = query;

		state.query = parseQuery(query);
        cm.removeOverlay(state.overlay);
        state.overlay = searchOverlay(query);
        cm.addOverlay(state.overlay);
        state.posFrom = state.posTo = cm.getCursor();
	}
  }
  function findNext(cm, rev) {
	cm.operation(function() {
		var state = getSearchState(cm);
		var cursor = getSearchCursor(cm, state.query, rev ? state.posFrom : state.posTo);
		if (!cursor.find(rev)) {
		  cursor = getSearchCursor(cm, state.query, rev ? CodeMirror.Pos(cm.lastLine()) : CodeMirror.Pos(cm.firstLine(), 0));
		  if (!cursor.find(rev)) return;
		}
		cm.setSelection(cursor.from(), cursor.to());		
		state.posFrom = cursor.from(); state.posTo = cursor.to();
	});
  }
  function clearSearch(cm) {cm.operation(function() {
    var state = getSearchState(cm);
    if (!state.query) return;
    state.query = null;
    cm.removeOverlay(state.overlay);
  });}
  function replace(cm,from,text, all) {
	if (!from) return;
	query = parseQuery(from);
	if (all) {
	  cm.operation(function() {
		for (var cursor = getSearchCursor(cm, query); cursor.findNext();) {
		  if (typeof query != "string") {
			var match = cm.getRange(cursor.from(), cursor.to()).match(query);
			cursor.replace(text.replace(/\$(\d)/, function(_, i) {return match[i];}));
		  } else cursor.replace(text);
		}
	  });
	}else {
	  clearSearch(cm);
	  var cursor = getSearchCursor(cm, query, cm.getCursor());
	  var advance = function() {		
		var start = cursor.from(),match;
		if (!(match = cursor.findNext())) {
		  cursor = getSearchCursor(cm, query);		  
		  if (!(match = cursor.findNext()) ||
			  (start && cursor.from().line == start.line && cursor.from().ch == start.ch)) return;
		}
		doReplace(match);
		cm.setSelection(cursor.from(), cursor.to());
	  };
	  var doReplace = function(match) {
          cursor.replace(typeof query == "string" ? text :
             text.replace(/\$(\d)/, function(_, i) {return match[i];}));		  
	  };
	  advance();
	}
  }
  CodeMirror.commands.find = function(cm) {clearSearch(cm); doSearch(cm);};
  CodeMirror.commands.findNext = doSearch;
  CodeMirror.commands.findPrev = function(cm) {doSearch(cm, true);};
  CodeMirror.commands.clearSearch = clearSearch;
  CodeMirror.commands.replace = replace;
  CodeMirror.commands.replaceAll = function(cm) {replace(cm, true);};



	//事件绑定。
	$('#id_find_find').unbind('click').live('click',function(){
		doSearch(editor_current);
		return false;
	});
	$('#id_find_findpre').unbind('click').live('click',function(){
		doSearch(editor_current,true);
		return false;
	});
	$('#id_find_replace').unbind('click').live('click',function(){
		var replace_from=$('#code_find').val();
		var replace_to=$('#code_replace').val();
		replace(editor_current,replace_from,replace_to);
		return false;
	});
	$('#id_find_replaceall').unbind('click').live('click',function(){
		var replace_from=$('#code_find').val();
		var replace_to=$('#code_replace').val();
		replace(editor_current,replace_from,replace_to,true);
		return false;
	});
})();