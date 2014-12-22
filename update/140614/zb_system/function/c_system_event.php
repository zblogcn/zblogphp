<?php
/**
 * 事件相关函数
 * @package Z-BlogPHP
 * @subpackage System\Event 操作事件
 * @copyright (C) RainbowSoft Studio
 */

/**
 * 自动加载类文件
 * @api Filter_Plugin_Autoload
 * @param string $classname 类名
 * @return mixed
 */
function AutoloadClass($classname){
	foreach ($GLOBALS['Filter_Plugin_Autoload'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($classname);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
	}
	if (is_readable($f=dirname(__FILE__) . '/lib/' . strtolower($classname) .'.php'))
		require $f;
}

/**
 * 记录日志
 * @param string $s
 */
function Logs($s) {
	global $zbp;
	$f = $zbp->usersdir . 'logs/' . $zbp->guid . '-log' . date("Ymd") . '.txt';
	$handle = @fopen($f, 'a+');
	@fwrite($handle, "[" . date('c') . "~" . current(explode(" ", microtime())) . "]" . "\r\n" . $s . "\r\n");
	@fclose($handle);
}

/**
 * 页面运行时长
 * @return array
 */
function RunTime() {
	global $zbp;

	$rt=array();
	$rt['time']=number_format(1000 * (microtime(1) - $_SERVER['_start_time']), 2);
	$rt['query']=$_SERVER['_query_count'];
	$rt['memory']=$_SERVER['_memory_usage'];
	$rt['error']=$_SERVER['_error_count'];
	if(function_exists('memory_get_usage')){
		$rt['memory']=(int)((memory_get_usage()-$_SERVER['_memory_usage'])/1024);
	}
	
	if(isset($zbp->option['ZC_RUNINFO_DISPLAY'])&&$zbp->option['ZC_RUNINFO_DISPLAY']==false)return $rt;

	echo '<!--' . $rt['time'] . 'ms , ';
	echo  $rt['query'] . ' query';
	if(function_exists('memory_get_usage'))
		echo ' , ' . $rt['memory'] . 'kb memory';
	echo  ' , ' . $rt['error'] . ' error';
	echo '-->';
	return $rt;
}


################################################################################################################
/**
 * 验证登录
 * @return bool
 */
function VerifyLogin() {
	global $zbp;

	if (isset($zbp->membersbyname[GetVars('username', 'POST')])) {
		if ($zbp->Verify_MD5(GetVars('username', 'POST'), GetVars('password', 'POST'))) {
			$un = GetVars('username', 'POST');
			$ps = md5($zbp->user->Password . $zbp->guid);
			$sd = (int)GetVars('savedate');
			if ( $sd == 0) {
				setcookie("username", $un, 0, $zbp->cookiespath);
				setcookie("password", $ps, 0, $zbp->cookiespath);
				setcookie("dishtml5", GetVars('dishtml5', 'POST'), 0, $zbp->cookiespath);
			} else {
				setcookie("username", $un, time() + 3600 * 24 * $sd, $zbp->cookiespath);
				setcookie("password", $ps, time() + 3600 * 24 * $sd, $zbp->cookiespath);
				setcookie("dishtml5", GetVars('dishtml5', 'POST'), time() + 3600 * 24 * $sd, $zbp->cookiespath);
			}

			return true;
		} else {
			$zbp->ShowError(8, __FILE__, __LINE__);
		}
	} else {
		$zbp->ShowError(8, __FILE__, __LINE__);
	}
}

/**
 * 注销登录
 */
function Logout() {
	global $zbp;

	setcookie('username', '', time() - 3600, $zbp->cookiespath);
	setcookie('password', '', time() - 3600, $zbp->cookiespath);
	setcookie("dishtml5", '', time() - 3600, $zbp->cookiespath);

}

################################################################################################################
/**
 * 获取文章
 * @param mixed $idorname 文章id 或 名称、别名
 * @param array $option|null
 * @return Post
 */
function GetPost($idorname, $option = null) {
	global $zbp;

	if (!is_array($option)) {
		$option = array();
	}

	if (!isset($option['only_article']))
		$option['only_article'] = false;
	if (!isset($option['only_page']))
		$option['only_page'] = false;

	if(is_string($idorname)){
		$w[] = array('array', array(array('log_Alias', $idorname), array('log_Title', $idorname)));
		if($option['only_article']==true){
			$w[]=array('=','log_Type','0');
		}
		elseif($option['only_page']==true){
			$w[]=array('=','log_Type','1');
		}
		$articles = $zbp->GetPostList('*', $w, null, 1, null);
		if (count($articles) == 0) {
			return new Post;
		}
		return $articles[0];
	}
	if(is_integer($idorname)){
		return $zbp->GetPostByID($idorname);
	}
}

/**
 * 获取文章列表
 * @param int $count 数量
 * @param null $cate 分类ID
 * @param null $auth 用户ID
 * @param null $date 日期
 * @param null $tags 标签
 * @param null $search 搜索关键词
 * @param null $option
 * @return array|mixed
 */
function GetList($count = 10, $cate = null, $auth = null, $date = null, $tags = null, $search = null, $option = null) {
	global $zbp;

	if (!is_array($option)) {
		$option = array();
	}

	if (!isset($option['only_ontop']))
		$option['only_ontop'] = false;
	if (!isset($option['only_not_ontop']))
		$option['only_not_ontop'] = false;
	if (!isset($option['has_subcate']))
		$option['has_subcate'] = false;
	if (!isset($option['is_related']))
		$option['is_related'] = false;

	if ($option['is_related']) {
		$at = $zbp->GetPostByID($option['is_related']);
		$tags = $at->Tags;
		if (!$tags)
			return array();
		$count = $count + 1;
	}

	$w = array();
	if ($option['only_ontop'] == true) {
		$w[] = array('=', 'log_IsTop', 1);
	} elseif ($option['only_not_ontop'] == true) {
		$w[] = array('=', 'log_IsTop', 0);
	}

	$w[] = array('=', 'log_Status', 0);

	$articles = array();

	if (!is_null($cate)) {
		$category = new Category;
		$category = $zbp->GetCategoryByID($cate);

		if ($category->ID > 0) {

			if (!$option['has_subcate']) {
				$w[] = array('=', 'log_CateID', $category->ID);
			} else {
				$arysubcate = array();
				$arysubcate[] = array('log_CateID', $category->ID);
				foreach ($zbp->categorys[$category->ID]->SubCategorys as $subcate) {
					$arysubcate[] = array('log_CateID', $subcate->ID);
				}
				$w[] = array('array', $arysubcate);

			}

		}
	}

	if (!is_null($auth)) {
		$author = new Member;
		$author = $zbp->GetMemberByID($auth);

		if ($author->ID > 0) {
			$w[] = array('=', 'log_AuthorID', $author->ID);
		}
	}

	if (!is_null($date)) {
		$datetime = strtotime($date);
		if ($datetime) {
			$datetitle = str_replace(array('%y%', '%m%'), array(date('Y', $datetime), date('n', $datetime)), $zbp->lang['msg']['year_month']);
			$w[] = array('BETWEEN', 'log_PostTime', $datetime, strtotime('+1 month', $datetime));
		}
	}

	if (!is_null($tags)) {
		$tag = new Tag;
		if (is_array($tags)) {
			$ta = array();
			foreach ($tags as $t) {
				$ta[] = array('log_Tag', '%{' . $t->ID . '}%');
			}
			$w[] = array('array_like', $ta);
			unset($ta);
		} else {
			if (is_int($tags)) {
				$tag = $zbp->GetTagByID($tags);
			} else {
				$tag = $zbp->GetTagByAliasOrName($tags);
			}
			if ($tag->ID > 0) {
				$w[] = array('LIKE', 'log_Tag', '%{' . $tag->ID . '}%');
			}
		}
	}

	if (is_string($search)) {
		$search=trim($search);
		if ($search!=='') {
			$w[] = array('search', 'log_Content', 'log_Intro', 'log_Title', $search);
		}
	}

	$articles = $zbp->GetArticleList('*', $w, array('log_PostTime' => 'DESC'), $count, null, false);

	if ($option['is_related']) {
		foreach ($articles as $k => $a) {
			if ($a->ID == $option['is_related'])
				unset($articles[$k]);
		}
		if (count($articles) == $count){
			array_pop($articles);
		}
	}

	return $articles;

}

################################################################################################################
/**
 * 显示索引页面(page、cate、auth、date、tags)
 * @api Filter_Plugin_ViewIndex_Begin
 * @return mixed
 */
function ViewIndex(){
	global $zbp,$action;
	
	PreViewIndex();
	
	foreach ($GLOBALS['Filter_Plugin_ViewIndex_Begin'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname();
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}

	switch ($action) {
	case 'feed':
		ViewFeed();
		break;
	case 'search':
		ViewSearch();
		break;
	case '':
	default:
		if( $zbp->currenturl==$zbp->cookiespath||
			$zbp->currenturl==$zbp->cookiespath . 'index.php' ){
			ViewList(null,null,null,null,null);
		}elseif(isset($_GET['id'])||isset($_GET['alias'])){
			ViewPost(GetVars('id','GET'),GetVars('alias','GET'));
		}elseif(isset($_GET['page'])||isset($_GET['cate'])||isset($_GET['auth'])||isset($_GET['date'])||isset($_GET['tags'])){
			ViewList(GetVars('page','GET'),GetVars('cate','GET'),GetVars('auth','GET'),GetVars('date','GET'),GetVars('tags','GET'));
		}else{
			ViewAuto($zbp->currenturl);
		}
	}
}

/**
 * 显示RSS2Feed
 * @api Filter_Plugin_ViewFeed_Begin
 * @return mixed
 */
function ViewFeed(){
	global $zbp;
	
	foreach ($GLOBALS['Filter_Plugin_ViewFeed_Begin'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname();
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}
	
	if(!$zbp->CheckRights($GLOBALS['action'])){Http404();die;}

	$rss2 = new Rss2($zbp->name,$zbp->host,$zbp->subname);

	$articles=$zbp->GetArticleList(
		'*',
		array(array('=','log_Status',0)),
		array('log_PostTime'=>'DESC'),
		$zbp->option['ZC_RSS2_COUNT'],
		null
	);

	foreach ($articles as $article) {
		$rss2->addItem($article->Title,$article->Url,($zbp->option['ZC_RSS_EXPORT_WHOLE']==true?$article->Content:$article->Intro),$article->PostTime);
	}

	header("Content-type:text/xml; Charset=utf-8");

	echo $rss2->saveXML();

}

/**
 * 展示搜索结果
 * @api Filter_Plugin_ViewSearch_Begin
 * @api Filter_Plugin_ViewPost_Template
 * @return mixed
 */
function ViewSearch(){
	global $zbp;
	
	foreach ($GLOBALS['Filter_Plugin_ViewSearch_Begin'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname();
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}
	
	if(!$zbp->CheckRights($GLOBALS['action'])){Redirect('./');}

	$q=trim(htmlspecialchars(GetVars('q','GET')));

	$article = new Post;
	$article->ID=0;
	$article->Title=$zbp->lang['msg']['search'] . ' &quot;' . $q . '&quot;';
	$article->IsLock=true;
	$article->Type=ZC_POST_TYPE_PAGE;

	if(isset($zbp->templates['search'])){
		$article->Template='search';
	}

	$w=array();
	$w[]=array('=','log_Type','0');
	if($q){
		$w[]=array('search','log_Content','log_Intro','log_Title',$q);
	}else{
		Redirect('./');
	}

	if(!($zbp->CheckRights('ArticleAll')&&$zbp->CheckRights('PageAll'))){
		$w[]=array('=','log_Status',0);
	}

	$array=$zbp->GetArticleList(
		'',
		$w,
		array('log_PostTime'=>'DESC'),
		array($zbp->searchcount),
		null
	);

	foreach ($array as $a) {
		$article->Content .= '<p><br/>' . $a->Title . '<br/>';
		$article->Content .= '<a href="' . $a->Url . '">' . $a->Url . '</a></p>';
	}

	$zbp->header .= '<meta name="robots" content="noindex,follow" />' . "\r\n";
	$zbp->template->SetTags('title',$article->Title);
	$zbp->template->SetTags('article',$article);
	$zbp->template->SetTags('type',$article->type=0?'article':'page');
	$zbp->template->SetTags('page',1);
	$zbp->template->SetTags('pagebar',null);
	$zbp->template->SetTags('comments',array());
	$zbp->template->SetTemplate($article->Template);

	foreach ($GLOBALS['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($zbp->template);
	}

	$zbp->template->Display();

}

################################################################################################################
/**
 * 根据Rewrite_url规则显示页面
 * @api Filter_Plugin_ViewAuto_Begin
 * @api Filter_Plugin_ViewAuto_End
 * @param string $inpurl 页面url
 * @return null|string
 */
function ViewAuto($inpurl) {
	global $zbp;

	foreach ($GLOBALS['Filter_Plugin_ViewAuto_Begin'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($url);
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}
	
	$url=GetValueInArray(explode('?',$inpurl),'0');

	if($zbp->cookiespath === substr($url, 0 , strlen($zbp->cookiespath)))
		$url = substr($url, strlen($zbp->cookiespath));

	if (isset($_SERVER['SERVER_SOFTWARE'])) {
		if ((strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) && (isset($_GET['rewrite']) == true)){
			//iis+httpd.ini下如果存在真实文件
			$realurl = $zbp->path . urldecode($url);
			if(is_readable($realurl)&&is_file($realurl)){
				die(file_get_contents($realurl));
			}
			unset($realurl);
		}
	}

	$url = urldecode($url);

	if($url==''||$url=='index.php'||trim($url,'/')==''){
		ViewList(null,null,null,null,null);
		return null;
	}
	
	if ($zbp->option['ZC_STATIC_MODE'] == 'ACTIVE') {
		$zbp->ShowError(2, __FILE__, __LINE__);
		return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_INDEX_REGEX'], 'index');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		ViewList($m[1], null, null, null, null, true);

		return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_DATE_REGEX'], 'date');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		ViewList($m[2], null, null, $m[1], null, true);

		return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_AUTHOR_REGEX'], 'auth');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		$result = ViewList($m[2], null, $m[1], null, null, true);
		if ($result == true)
			return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_TAGS_REGEX'], 'tags');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		$result = ViewList($m[2], null, null, null, $m[1], true);
		if ($result == true)
			return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_CATEGORY_REGEX'], 'cate');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		$result = ViewList($m[2], $m[1], null, null, null, true);
		if ($result == true)
			return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_ARTICLE_REGEX'], 'article');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		if (strpos($zbp->option['ZC_ARTICLE_REGEX'], '{%id%}') !== false) {
			$result = ViewPost($m[1], null, true);
		} else {
			$result = ViewPost(null, $m[1], true);
		}
		if ($result == false)
			$zbp->ShowError(2, __FILE__, __LINE__);

		return null;
	}

	$r = UrlRule::Rewrite_url($zbp->option['ZC_PAGE_REGEX'], 'page');
	$m = array();
	if (preg_match($r, $url, $m) == 1) {
		if (strpos($zbp->option['ZC_PAGE_REGEX'], '{%id%}') !== false) {
			$result = ViewPost($m[1], null, true);
		} else {
			$result = ViewPost(null, $m[1], true);
		}
		if ($result == false)
			$zbp->ShowError(2, __FILE__, __LINE__);

		return null;
	}

	foreach ($GLOBALS['Filter_Plugin_ViewAuto_End'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($url);
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}

	$zbp->ShowError(2, __FILE__, __LINE__);

}

/**
 * 显示列表页面
 * @api Filter_Plugin_ViewList_Begin
 * @api Filter_Plugin_ViewList_Template
 * @param int $page
 * @param int|string $cate
 * @param int|string $auth
 * @param string   $date
 * @param string $tags tags列表
 * @param bool $isrewrite 是否启用urlrewrite
 * @return string
 */
function ViewList($page, $cate, $auth, $date, $tags, $isrewrite = false) {
	global $zbp;

	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($page, $cate, $auth, $date, $tags);
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}

	$type = 'index';
	if ($cate !== null)
		$type = 'category';
	if ($auth !== null)
		$type = 'author';
	if ($date !== null)
		$type = 'date';
	if ($tags !== null)
		$type = 'tag';

	$category = null;
	$author = null;
	$datetime = null;
	$tag = null;

	$w = array();
	$w[] = array('=', 'log_IsTop', 0);
	$w[] = array('=', 'log_Status', 0);

	$page = (int)$page == 0 ? 1 : (int)$page;

	$articles = array();
	$articles_top = array();

	switch ($type) {
		########################################################################################################
		case 'index':
			$pagebar = new Pagebar($zbp->option['ZC_INDEX_REGEX']);
			$pagebar->Count = $zbp->cache->normal_article_nums;
			$category = new Metas;
			$author = new Metas;
			$datetime = new Metas;
			$tag = new Metas;
			$template = $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			if ($page == 1) {
				$zbp->title = $zbp->subname;
			} else {
				$zbp->title = str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
			}
			break;
		########################################################################################################
		case 'category':
			$pagebar = new Pagebar($zbp->option['ZC_CATEGORY_REGEX']);
			$author = new Metas;
			$datetime = new Metas;
			$tag = new Metas;

			$category = new Category;
			if (strpos($zbp->option['ZC_CATEGORY_REGEX'], '{%id%}') !== false) {
				$category = $zbp->GetCategoryByID($cate);
			}
			if (strpos($zbp->option['ZC_CATEGORY_REGEX'], '{%alias%}') !== false) {
				$category = $zbp->GetCategoryByAliasOrName($cate);
			}
			if ($category->ID == 0) {
				if ($isrewrite == true)
					return false;
				$zbp->ShowError(2, __FILE__, __LINE__);
			}
			if ($page == 1) {
				$zbp->title = $category->Name;
			} else {
				$zbp->title = $category->Name . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
			}
			$template = $category->Template;

			if (!$zbp->option['ZC_DISPLAY_SUBCATEGORYS']) {
				$w[] = array('=', 'log_CateID', $category->ID);
				$pagebar->Count = $category->Count;
			} else {
				$arysubcate = array();
				$arysubcate[] = array('log_CateID', $category->ID);
				foreach ($zbp->categorys[$category->ID]->SubCategorys as $subcate) {
					$arysubcate[] = array('log_CateID', $subcate->ID);
				}
				$w[] = array('array', $arysubcate);
			}

			$pagebar->UrlRule->Rules['{%id%}'] = $category->ID;
			$pagebar->UrlRule->Rules['{%alias%}'] = $category->Alias == '' ? urlencode($category->Name) : $category->Alias;
			break;
		########################################################################################################
		case 'author':
			$pagebar = new Pagebar($zbp->option['ZC_AUTHOR_REGEX']);
			$category = new Metas;
			$datetime = new Metas;
			$tag = new Metas;

			$author = new Member;
			if (strpos($zbp->option['ZC_AUTHOR_REGEX'], '{%id%}') !== false) {
				$author = $zbp->GetMemberByID($auth);
			}
			if (strpos($zbp->option['ZC_AUTHOR_REGEX'], '{%alias%}') !== false) {
				$author = $zbp->GetMemberByAliasOrName($auth);
			}
			if ($author->ID == 0) {
				if ($isrewrite == true)
					return false;
				$zbp->ShowError(2, __FILE__, __LINE__);
			}
			if ($page == 1) {
				$zbp->title = $author->StaticName;
			} else {
				$zbp->title = $author->StaticName . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
			}
			$template = $author->Template;
			$w[] = array('=', 'log_AuthorID', $author->ID);
			$pagebar->Count = $author->Articles;
			$pagebar->UrlRule->Rules['{%id%}'] = $author->ID;
			$pagebar->UrlRule->Rules['{%alias%}'] = $author->Alias == '' ? urlencode($author->Name) : $author->Alias;
			break;
		########################################################################################################
		case 'date':
			$pagebar = new Pagebar($zbp->option['ZC_DATE_REGEX']);
			$category = new Metas;
			$author = new Metas;
			$tag = new Metas;
			$datetime = strtotime($date);

			$datetitle = str_replace(array('%y%', '%m%'), array(date('Y', $datetime), date('n', $datetime)), $zbp->lang['msg']['year_month']);
			if ($page == 1) {
				$zbp->title = $datetitle;
			} else {
				$zbp->title = $datetitle . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
			}

			$zbp->modulesbyfilename['calendar']->Content = BuildModule_calendar(date('Y', $datetime) . '-' . date('n', $datetime));

			$template = $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			$w[] = array('BETWEEN', 'log_PostTime', $datetime, strtotime('+1 month', $datetime));
			$pagebar->UrlRule->Rules['{%date%}'] = $date;
			$datetime = Metas::ConvertArray(getdate($datetime));
			break;
		########################################################################################################
		case 'tag':
			$pagebar = new Pagebar($zbp->option['ZC_TAGS_REGEX']);
			$category = new Metas;
			$author = new Metas;
			$datetime = new Metas;
			$tag = new Tag;
			if (strpos($zbp->option['ZC_TAGS_REGEX'], '{%id%}') !== false) {
				$tag = $zbp->GetTagByID($tags);
			}
			if (strpos($zbp->option['ZC_TAGS_REGEX'], '{%alias%}') !== false) {
				$tag = $zbp->GetTagByAliasOrName($tags);
			}
			if ($tag->ID == 0) {
				if ($isrewrite == true)
					return false;
				$zbp->ShowError(2, __FILE__, __LINE__);
			}

			if ($page == 1) {
				$zbp->title = $tag->Name;
			} else {
				$zbp->title = $tag->Name . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
			}

			$template = $tag->Template;
			$w[] = array('LIKE', 'log_Tag', '%{' . $tag->ID . '}%');
			$pagebar->UrlRule->Rules['{%id%}'] = $tag->ID;
			$pagebar->UrlRule->Rules['{%alias%}'] = $tag->Alias == '' ? urlencode($tag->Name) : $tag->Alias;
			break;
	}

	$pagebar->PageCount = $zbp->displaycount;
	$pagebar->PageNow = $page;
	$pagebar->PageBarCount = $zbp->pagebarcount;
	$pagebar->UrlRule->Rules['{%page%}'] = $page;

	foreach ($GLOBALS['Filter_Plugin_ViewList_Core'] as $fpname => &$fpsignal) {
		$fpname($type, $page, $category, $author, $datetime, $tag, $w, $pagebar);
	}

	if(isset($zbp->option['ZC_LISTONTOP_TURNOFF'])&&$zbp->option['ZC_LISTONTOP_TURNOFF']==false){
		if ($type == 'index' && $page == 1) {
			$articles_top = $zbp->GetArticleList('*', array(array('=', 'log_IsTop', 1), array('=', 'log_Status', 0)), array('log_PostTime' => 'DESC'), null, null);
		}
	}

	$articles = $zbp->GetArticleList(
		'*', 
		$w,
		array('log_PostTime' => 'DESC'), array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount),
		array('pagebar' => $pagebar),
		true
	);

	$zbp->template->SetTags('title', $zbp->title);
	$zbp->template->SetTags('articles', array_merge($articles_top, $articles));
	if ($pagebar->PageAll == 0)
		$pagebar = null;
	$zbp->template->SetTags('pagebar', $pagebar);
	$zbp->template->SetTags('type', $type);
	$zbp->template->SetTags('page', $page);

	$zbp->template->SetTags('date', $datetime);
	$zbp->template->SetTags('tag', $tag);
	$zbp->template->SetTags('author', $author);
	$zbp->template->SetTags('category', $category);

	if (isset($zbp->templates[$template])) {
		$zbp->template->SetTemplate($template);
	} else {
		$zbp->template->SetTemplate('index');
	}

	foreach ($GLOBALS['Filter_Plugin_ViewList_Template'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($zbp->template);
	}

	$zbp->template->Display();
	
	return true;
}

/**
 * 显示文章
 * @param int $id 文章ID
 * @param string $alias 文章别名
 * @param bool $isrewrite 是否启用urlrewrite
 * @return string
 */
function ViewPost($id, $alias, $isrewrite = false) {
	global $zbp;
	foreach ($GLOBALS['Filter_Plugin_ViewPost_Begin'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($id, $alias);
		if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
		}
	}

	$w = array();

	if ($id !== null) {
		$w[] = array('=', 'log_ID', $id);
	} elseif ($alias !== null) {
		if($zbp->option['ZC_POST_ALIAS_USE_ID_NOT_TITLE']==false){
			$w[] = array('array', array(array('log_Alias', $alias), array('log_Title', $alias)));
		}else{
			$w[] = array('array', array(array('log_Alias', $alias), array('log_ID', $alias)));
		}
	} else {
		$zbp->ShowError(2, __FILE__, __LINE__);
		die();
	}

	if (!($zbp->CheckRights('ArticleAll') && $zbp->CheckRights('PageAll'))) {
		$w[] = array('=', 'log_Status', 0);
	}

	$articles = $zbp->GetPostList('*', $w, null, 1, null);
	if (count($articles) == 0) {
		if ($isrewrite == true)
			return false;
		$zbp->ShowError(2, __FILE__, __LINE__);
	}

	$article = $articles[0];

	if ($article->Type == 0) {
		$zbp->LoadTagsByIDString($article->Tag);
	}

	if (isset($zbp->option['ZC_VIEWNUMS_TURNOFF']) && $zbp->option['ZC_VIEWNUMS_TURNOFF']==false) {
		$article->ViewNums += 1;
		$sql = $zbp->db->sql->Update($zbp->table['Post'], array('log_ViewNums' => $article->ViewNums), array(array('=', 'log_ID', $article->ID)));
		$zbp->db->Update($sql);
	}

	$pagebar = new Pagebar('javascript:GetComments(\'' . $article->ID . '\',\'{%page%}\')', false);
	$pagebar->PageCount = $zbp->commentdisplaycount;
	$pagebar->PageNow = 1;
	$pagebar->PageBarCount = $zbp->pagebarcount;

	if ($zbp->option['ZC_COMMENT_TURNOFF']) {
		$article->IsLock = true;
	}
	
	$comments = array();

	if($article->IsLock==false && $zbp->socialcomment==null){
		$comments = $zbp->GetCommentList(
			'*', 
			array(
				array('=', 'comm_RootID', 0),
				array('=', 'comm_IsChecking', 0),
				array('=', 'comm_LogID', $article->ID)
			),
			array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
			array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount),
			array('pagebar' => $pagebar)
		);
		$rootid = array();
		foreach ($comments as &$comment) {
			$rootid[] = array('comm_RootID', $comment->ID);
		}
		$comments2 = $zbp->GetCommentList(
			'*', 
			array(
				array('array', $rootid),
				array('=', 'comm_IsChecking', 0),
				array('=', 'comm_LogID', $article->ID)
			),
			array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
			null,
			null
		);
		$floorid = ($pagebar->PageNow - 1) * $pagebar->PageCount;
		foreach ($comments as &$comment) {
			$floorid += 1;
			$comment->FloorID = $floorid;
			$comment->Content = TransferHTML($comment->Content, '[enter]') . '<label id="AjaxComment' . $comment->ID . '"></label>';
		}
		foreach ($comments2 as &$comment) {
			$comment->Content = TransferHTML($comment->Content, '[enter]') . '<label id="AjaxComment' . $comment->ID . '"></label>';
		}
	}
	
	$zbp->template->SetTags('title', ($article->Status == 0 ? '' : '[' . $zbp->lang['post_status_name'][$article->Status] . ']') . $article->Title);
	$zbp->template->SetTags('article', $article);
	$zbp->template->SetTags('type', ($article->Type == 0 ? 'article' : 'page'));
	$zbp->template->SetTags('page', 1);
	if ($pagebar->PageAll == 0 || $pagebar->PageAll == 1)
		$pagebar = null;
	$zbp->template->SetTags('pagebar', $pagebar);
	$zbp->template->SetTags('comments', $comments);

	if (isset($zbp->templates[$article->Template])) {
		$zbp->template->SetTemplate($article->Template);
	} else {
		$zbp->template->SetTemplate('single');
	}

	foreach ($GLOBALS['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($zbp->template);
	}

	$zbp->template->Display();

	return true;
}

/**
 * 显示文章下评论列表
 * @param int $postid 文章ID
 * @param int $page 页数
 * @return bool
 */
function ViewComments($postid, $page) {
	global $zbp;

	$post = New Post;
	$post->LoadInfoByID($postid);
	$page = $page == 0 ? 1 : $page;
	$template = 'comments';

	$pagebar = new Pagebar('javascript:GetComments(\'' . $post->ID . '\',\'{%page%}\')');
	$pagebar->PageCount = $zbp->commentdisplaycount;
	$pagebar->PageNow = $page;
	$pagebar->PageBarCount = $zbp->pagebarcount;

	$comments = array();

	$comments = $zbp->GetCommentList(
		'*',
		array(
			array('=', 'comm_RootID', 0),
			array('=', 'comm_IsChecking', 0),
			array('=', 'comm_LogID', $post->ID)
		),
		array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
		array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount),
		array('pagebar' => $pagebar)
	);
	$rootid = array();
	foreach ($comments as $comment) {
		$rootid[] = array('comm_RootID', $comment->ID);
	}
	$comments2 = $zbp->GetCommentList(
		'*',
		array(
			array('array', $rootid),
			array('=', 'comm_IsChecking', 0),
			array('=', 'comm_LogID', $post->ID)
		),
		array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
		null,
		null
	);

	$floorid = ($pagebar->PageNow - 1) * $pagebar->PageCount;
	foreach ($comments as &$comment) {
		$floorid += 1;
		$comment->FloorID = $floorid;
		$comment->Content = TransferHTML($comment->Content, '[enter]') . '<label id="AjaxComment' . $comment->ID . '"></label>';
	}
	foreach ($comments2 as &$comment) {
		$comment->Content = TransferHTML($comment->Content, '[enter]') . '<label id="AjaxComment' . $comment->ID . '"></label>';
	}

	$zbp->template->SetTags('title', $zbp->title);
	$zbp->template->SetTags('article', $post);
	$zbp->template->SetTags('type', 'comment');
	$zbp->template->SetTags('page', $page);
	if ($pagebar->PageAll == 1)
		$pagebar = null;
	$zbp->template->SetTags('pagebar', $pagebar);
	$zbp->template->SetTags('comments', $comments);

	$zbp->template->SetTemplate($template);

	foreach ($GLOBALS['Filter_Plugin_ViewComments_Template'] as $fpname => &$fpsignal) {
		$fpreturn = $fpname($zbp->template);
	}

	$s = $zbp->template->Output();

	$a = explode('<label id="AjaxCommentBegin"></label>', $s);
	$s = $a[1];
	$a = explode('<label id="AjaxCommentEnd"></label>', $s);
	$s = $a[0];

	echo $s;

	return true;
}

/**
 * 显示评论
 * @param int $id 评论ID
 */
function ViewComment($id) {
	global $zbp;

	$template = 'comment';
	$comment = $zbp->GetCommentByID($id);
	$post = new Post;
	$post->LoadInfoByID($comment->LogID);

	$comment->Content = TransferHTML(htmlspecialchars($comment->Content), '[enter]') . '<label id="AjaxComment' . $comment->ID . '"></label>';

	$zbp->template->SetTags('title', $zbp->title);
	$zbp->template->SetTags('comment', $comment);
	$zbp->template->SetTags('article', $post);
	$zbp->template->SetTags('type', 'comment');
	$zbp->template->SetTags('page', 1);
	$zbp->template->SetTemplate($template);

	$zbp->template->Display();

	return true;
}

################################################################################################################
/**
 * 提交文章数据
 * @return bool
 */
function PostArticle() {
	global $zbp;
	if (!isset($_POST['ID'])) return;

	if (isset($_COOKIE['timezone'])) {
		$tz = GetVars('timezone', 'COOKIE');
		if (is_numeric($tz)) {
			date_default_timezone_set('Etc/GMT' . sprintf('%+d', -$tz));
		}
		unset($tz);
	}

	if (isset($_POST['Tag'])) {
		$_POST['Tag'] = TransferHTML($_POST['Tag'], '[noscript]');
		$_POST['Tag'] = PostArticle_CheckTagAndConvertIDtoString($_POST['Tag']);
	}
	if (isset($_POST['Content'])) {
		$_POST['Content'] = str_replace('<hr class="more" />', '<!--more-->', $_POST['Content']);
		$_POST['Content'] = str_replace('<hr class="more"/>', '<!--more-->', $_POST['Content']);
		if (strpos($_POST['Content'], '<!--more-->') !== false) {
			if (isset($_POST['Intro'])) {
				$_POST['Intro'] = GetValueInArray(explode('<!--more-->', $_POST['Content']), 0);
			}
		} else {
			if (isset($_POST['Intro'])) {
				if ($_POST['Intro'] == '') {
					$_POST['Intro'] = SubStrUTF8($_POST['Content'], $zbp->option['ZC_ARTICLE_EXCERPT_MAX']);
					if (strpos($_POST['Intro'], '<') !== false) {
						$_POST['Intro'] = CloseTags($_POST['Intro']);
					}
				}
			}
		}
	}

	if (!isset($_POST['AuthorID'])) {
		$_POST['AuthorID'] = $zbp->user->ID;
	} else {
		if (($_POST['AuthorID'] != $zbp->user->ID) && (!$zbp->CheckRights('ArticleAll'))) {
			$_POST['AuthorID'] = $zbp->user->ID;
		}
		if ($_POST['AuthorID'] == 0)
			$_POST['AuthorID'] = $zbp->user->ID;
	}

	if (isset($_POST['Alias'])) {
		$_POST['Alias'] = TransferHTML($_POST['Alias'], '[noscript]');
	}

	if (isset($_POST['PostTime'])) {
		$_POST['PostTime'] = strtotime($_POST['PostTime']);
	}

	if (!$zbp->CheckRights('ArticleAll')) {
		unset($_POST['IsTop']);
	}

	$article = new Post();
	$pre_author = null;
	$pre_tag = null;
	$pre_category = null;
	if (GetVars('ID', 'POST') == 0) {
		if (!$zbp->CheckRights('ArticlePub')) {
			$_POST['Status'] = ZC_POST_STATUS_AUDITING;
		}
	} else {
		$article->LoadInfoByID(GetVars('ID', 'POST'));
		if (($article->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights('ArticleAll'))) {
			$zbp->ShowError(6, __FILE__, __LINE__);
		}
		if ((!$zbp->CheckRights('ArticlePub')) && ($article->Status == ZC_POST_STATUS_AUDITING)) {
			$_POST['Status'] = ZC_POST_STATUS_AUDITING;
		}
		$pre_author = $article->AuthorID;
		$pre_tag = $article->Tag;
		$pre_category = $article->CateID;
	}

	foreach ($zbp->datainfo['Post'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if (isset($_POST[$key])) {
			$article->$key = GetVars($key, 'POST');
		}
	}

	$article->Type = ZC_POST_TYPE_ARTICLE;

	foreach ($GLOBALS['Filter_Plugin_PostArticle_Core'] as $fpname => &$fpsignal) {
		$fpname($article);
	}

	FilterPost($article);
	FilterMeta($article);

	$article->Save();

	CountTagArrayString($pre_tag . $article->Tag);
	CountMemberArray(array($pre_author, $article->AuthorID));
	CountCategoryArray(array($pre_category, $article->CateID));
	CountPostArray(array($article->ID));
	CountNormalArticleNums();

	$zbp->AddBuildModule('previous');
	$zbp->AddBuildModule('calendar');
	$zbp->AddBuildModule('comments');
	$zbp->AddBuildModule('archives');
	$zbp->AddBuildModule('tags');
	$zbp->AddBuildModule('authors');

	foreach ($GLOBALS['Filter_Plugin_PostArticle_Succeed'] as $fpname => &$fpsignal)
		$fpname($article);

	return true;
}

/**
 * 删除文章
 * @return bool
 */
function DelArticle() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');

	$article = new Post();
	$article->LoadInfoByID($id);
	if ($article->ID > 0) {

		if (!$zbp->CheckRights('ArticleAll') && $article->AuthorID != $zbp->user->ID)
			$zbp->ShowError(6, __FILE__, __LINE__);

		$pre_author = $article->AuthorID;
		$pre_tag = $article->Tag;
		$pre_category = $article->CateID;

		$article->Del();

		DelArticle_Comments($article->ID);

		CountTagArrayString($pre_tag);
		CountMemberArray(array($pre_author));
		CountCategoryArray(array($pre_category));
		CountNormalArticleNums();

		$zbp->AddBuildModule('previous');
		$zbp->AddBuildModule('calendar');
		$zbp->AddBuildModule('comments');
		$zbp->AddBuildModule('archives');
		$zbp->AddBuildModule('tags');
		$zbp->AddBuildModule('authors');

		foreach ($GLOBALS['Filter_Plugin_DelArticle_Succeed'] as $fpname => &$fpsignal)
			$fpname($article);
	} else {

	}

	return true;
}

/**
 * 提交文章数据时检查tag数据，并将新tags转为标准格式返回
 * @param string $tagnamestring 提交的文章tag数据，可以:,，、等符号分隔
 * @return string 返回如'{1}{2}{3}{4}'的字符串
 */
function PostArticle_CheckTagAndConvertIDtoString($tagnamestring) {
	global $zbp;
	$s = '';
	$tagnamestring = str_replace(';', ',', $tagnamestring);
	$tagnamestring = str_replace('，', ',', $tagnamestring);
	$tagnamestring = str_replace('、', ',', $tagnamestring);
	$tagnamestring = strip_tags($tagnamestring);
	$tagnamestring = trim($tagnamestring);
	if ($tagnamestring == '')
		return '';
	if ($tagnamestring == ',')
		return '';
	$a = explode(',', $tagnamestring);
	$b = array();
	foreach ($a as &$value) {
		$value = trim($value);
		if ($value)	$b[] = $value;
	}
	$b = array_unique($b);
	$b = array_slice($b, 0, 20);
	$c = array();

	$t = $zbp->LoadTagsByNameString($tagnamestring);
	foreach ($t as $key => $value) {
		$c[] = $key;
	}
	$d = array_diff($b, $c);
	if ($zbp->CheckRights('TagNew')) {
		foreach ($d as $key) {
			$tag = new Tag;
			$tag->Name = $key;
			FilterTag($tag);
			$tag->Save();
			$zbp->tags[$tag->ID] = $tag;
			$zbp->tagsbyname[$tag->Name] =& $zbp->tags[$tag->ID];
		}
	}

	foreach ($b as $key) {
		if (!isset($zbp->tagsbyname[$key])) continue;
		$s .= '{' . $zbp->tagsbyname[$key]->ID . '}';
	}

	return $s;
}

/**
 * 删除文章下所有评论
 * @param int $id 文章ID
 */
function DelArticle_Comments($id) {
	global $zbp;

	$sql = $zbp->db->sql->Delete($zbp->table['Comment'], array(array('=', 'comm_LogID', $id)));
	$zbp->db->Delete($sql);
}

################################################################################################################
/**
 * 提交页面数据
 * @return bool
 */
function PostPage() {
	global $zbp;
	if (!isset($_POST['ID'])) return;

	if (isset($_POST['PostTime'])) {
		$_POST['PostTime'] = strtotime($_POST['PostTime']);
	}

	if (!isset($_POST['AuthorID'])) {
		$_POST['AuthorID'] = $zbp->user->ID;
	} else {
		if (($_POST['AuthorID'] != $zbp->user->ID) && (!$zbp->CheckRights('PageAll'))) {
			$_POST['AuthorID'] = $zbp->user->ID;
		}
	}

	if (isset($_POST['Alias'])) {
		$_POST['Alias'] = TransferHTML($_POST['Alias'], '[noscript]');
	}

	$article = new Post();
	$pre_author = null;
	if (GetVars('ID', 'POST') == 0) {
	} else {
		$article->LoadInfoByID(GetVars('ID', 'POST'));
		if (($article->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights('PageAll'))) {
			$zbp->ShowError(6, __FILE__, __LINE__);
		}
		$pre_author = $article->AuthorID;
	}

	foreach ($zbp->datainfo['Post'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if (isset($_POST[$key])) {
			$article->$key = GetVars($key, 'POST');
		}
	}

	$article->Type = ZC_POST_TYPE_PAGE;

	foreach ($GLOBALS['Filter_Plugin_PostPage_Core'] as $fpname => &$fpsignal) {
		$fpname($article);
	}

	FilterPost($article);
	FilterMeta($article);

	$article->Save();

	CountMemberArray(array($pre_author, $article->AuthorID));
	CountPostArray(array($article->ID));

	$zbp->AddBuildModule('comments');

	if (GetVars('AddNavbar', 'POST') == 0)
		$zbp->DelItemToNavbar('page', $article->ID);
	if (GetVars('AddNavbar', 'POST') == 1)
		$zbp->AddItemToNavbar('page', $article->ID, $article->Title, $article->Url);

	foreach ($GLOBALS['Filter_Plugin_PostPage_Succeed'] as $fpname => &$fpsignal)
		$fpname($article);

	return true;
}

/**
 * 删除页面
 * @return bool
 */
function DelPage() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');

	$article = new Post();
	$article->LoadInfoByID($id);
	if ($article->ID > 0) {

		if (!$zbp->CheckRights('PageAll') && $article->AuthorID != $zbp->user->ID)
			$zbp->ShowError(6, __FILE__, __LINE__);

		$pre_author = $article->AuthorID;

		$article->Del();

		DelArticle_Comments($article->ID);

		CountMemberArray(array($pre_author));

		$zbp->AddBuildModule('comments');

		$zbp->DelItemToNavbar('page', $article->ID);

		foreach ($GLOBALS['Filter_Plugin_DelPage_Succeed'] as $fpname => &$fpsignal)
			$fpname($article);
	} else {

	}

	return true;
}

################################################################################################################
/**
 * 提交评论
 * @return bool
 */
function PostComment() {
	global $zbp;

	$_POST['LogID'] = $_GET['postid'];

	if ($zbp->VerifyCmtKey($_GET['postid'], $_GET['key']) == false)
		$zbp->ShowError(43, __FILE__, __LINE__);

	if ($zbp->option['ZC_COMMENT_VERIFY_ENABLE']) {
		if ($zbp->user->ID == 0) {
			if ($zbp->CheckValidCode($_POST['verify'], 'cmt') == false)
				$zbp->ShowError(38, __FILE__, __LINE__);
		}
	}

	$replyid = (integer)GetVars('replyid', 'POST');

	if ($replyid == 0) {
		$_POST['RootID'] = 0;
		$_POST['ParentID'] = 0;
	} else {
		$_POST['ParentID'] = $replyid;
		$c = $zbp->GetCommentByID($replyid);
		if ($c->Level == 3) {
			$zbp->ShowError(52, __FILE__, __LINE__);
		}
		$_POST['RootID'] = Comment::GetRootID($c->ID);
	}

	$_POST['AuthorID'] = $zbp->user->ID;
	$_POST['Name'] = $_POST['name'];
	if($zbp->user->ID > 0)$_POST['Name'] = $zbp->user->Name;
	$_POST['Email'] = $_POST['email'];
	$_POST['HomePage'] = $_POST['homepage'];
	$_POST['Content'] = $_POST['content'];
	$_POST['PostTime'] = Time();
	$_POST['IP'] = GetGuestIP();
	$_POST['Agent'] = GetGuestAgent();

	$cmt = new Comment();

	foreach ($zbp->datainfo['Comment'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if ($key == 'IsChecking') continue;
		if (isset($_POST[$key])) {
			$cmt->$key = GetVars($key, 'POST');
		}
	}

	foreach ($GLOBALS['Filter_Plugin_PostComment_Core'] as $fpname => &$fpsignal) {
		$fpname($cmt);
	}

	FilterComment($cmt);

	if ($cmt->IsThrow == false) {

		$cmt->Save();

		if ($cmt->IsChecking == false) {

			CountPostArray(array($cmt->LogID));

			$zbp->AddBuildModule('comments');

			$zbp->comments[$cmt->ID] = $cmt;

			if (GetVars('isajax', 'POST')) {
				ViewComment($cmt->ID);
			}

			foreach ($GLOBALS['Filter_Plugin_PostComment_Succeed'] as $fpname => &$fpsignal)
				$fpname($cmt);

			return true;

		} else {

			$zbp->ShowError(53, __FILE__, __LINE__);

		}

	} else {

		$zbp->ShowError(14, __FILE__, __LINE__);

	}
}

/**
 * 删除评论
 * @return bool
 */
function DelComment() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');
	$cmt = $zbp->GetCommentByID($id);
	if ($cmt->ID > 0) {

		$comments = $zbp->GetCommentList('*', array(array('=', 'comm_LogID', $cmt->LogID)), null, null, null);

		DelComment_Children($cmt->ID);

		$cmt->Del();
		
		CountPostArray(array($cmt->LogID));

		$zbp->AddBuildModule('comments');

		foreach ($GLOBALS['Filter_Plugin_DelComment_Succeed'] as $fpname => &$fpsignal)
			$fpname($cmt);
	}

	return true;
}

/**
 * 删除评论下的子评论
 * @param int $id 父评论ID
 */
function DelComment_Children($id) {
	global $zbp;

	$cmt = $zbp->GetCommentByID($id);

	foreach ($cmt->Comments as $comment) {
		if (Count($comment->Comments) > 0) {
			DelComment_Children($comment->ID);
		}
		$comment->Del();
	}

}

/**
 * 删除评论保留其子评论
 * @param int $id 父评论ID
 * @param array $array 将子评论ID存入新数组
 */
function DelComment_Children_NoDel($id, &$array) {
	global $zbp;

	$cmt = $zbp->GetCommentByID($id);

	foreach ($cmt->Comments as $comment) {
		$array[] = $comment->ID;
		if (Count($comment->Comments) > 0) {
			DelComment_Children_NoDel($comment->ID, $array);
		}
	}

}

/**
 *检查评论数据并保存、更新计数、更新“最新评论”模块
 */
function CheckComment() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');
	$ischecking = (bool)GetVars('ischecking', 'GET');

	$cmt = $zbp->GetCommentByID($id);
	$cmt->IsChecking = $ischecking;

	$cmt->Save();

	CountPostArray(array($cmt->LogID));
	$zbp->AddBuildModule('comments');
}

/**
 * 评论批量处理（删除、通过审核、加入审核）
 */
function BatchComment() {
	global $zbp;
	if (isset($_POST['all_del'])) {
		$type = 'all_del';
	}
	if (isset($_POST['all_pass'])) {
		$type = 'all_pass';
	}
	if (isset($_POST['all_audit'])) {
		$type = 'all_audit';
	}
	$array = array();
	$array = $_POST['id'];
	if ($type == 'all_del') {
		$arrpost = array();
		foreach ($array as $i => $id) {
			$cmt = $zbp->GetCommentByID($id);
			if ($cmt->ID == 0)
				continue;
			$arrpost[] = $cmt->LogID;
		}
		$arrpost = array_unique($arrpost);
		foreach ($arrpost as $i => $id)
			$comments = $zbp->GetCommentList('*', array(array('=', 'comm_LogID', $id)), null, null, null);

		$arrdel = array();
		foreach ($array as $i => $id) {
			$cmt = $zbp->GetCommentByID($id);
			if ($cmt->ID == 0)
				continue;
			$arrdel[] = $cmt->ID;
			DelComment_Children_NoDel($cmt->ID, $arrdel);
		}
		foreach ($arrdel as $i => $id) {
			$cmt = $zbp->GetCommentByID($id);
			$cmt->Del();
		}
	}
	if ($type == 'all_pass')
		foreach ($array as $i => $id) {
			$cmt = $zbp->GetCommentByID($id);
			if ($cmt->ID == 0)
				continue;
			$cmt->IsChecking = false;
			$cmt->Save();
		}
	if ($type == 'all_audit')
		foreach ($array as $i => $id) {
			$cmt = $zbp->GetCommentByID($id);
			if ($cmt->ID == 0)
				continue;
			$cmt->IsChecking = true;
			$cmt->Save();
		}
}

################################################################################################################
/**
 * 提交分类数据
 * @return bool
 */
function PostCategory() {
	global $zbp;
	if (!isset($_POST['ID'])) return;

	if (isset($_POST['Alias'])) {
		$_POST['Alias'] = TransferHTML($_POST['Alias'], '[noscript]');
	}

	$parentid = (int)GetVars('ParentID', 'POST');
	if ($parentid > 0) {
		if ($zbp->categorys[$parentid]->Level > 2) {
			$_POST['ParentID'] = '0';
		}
	}

	$cate = new Category();
	if (GetVars('ID', 'POST') == 0) {
	} else {
		$cate->LoadInfoByID(GetVars('ID', 'POST'));
	}

	foreach ($zbp->datainfo['Category'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if (isset($_POST[$key])) {
			$cate->$key = GetVars($key, 'POST');
		}
	}

	foreach ($GLOBALS['Filter_Plugin_PostCategory_Core'] as $fpname => &$fpsignal) {
		$fpname($cate);
	}

	FilterCategory($cate);
	FilterMeta($cate);

	CountCategory($cate);

	$cate->Save();

	$zbp->LoadCategorys();
	$zbp->AddBuildModule('catalog');

	if (GetVars('AddNavbar', 'POST') == 0)
		$zbp->DelItemToNavbar('category', $cate->ID);
	if (GetVars('AddNavbar', 'POST') == 1)
		$zbp->AddItemToNavbar('category', $cate->ID, $cate->Name, $cate->Url);

	foreach ($GLOBALS['Filter_Plugin_PostCategory_Succeed'] as $fpname => &$fpsignal)
		$fpname($cate);

	return true;
}

/**
 * 删除分类
 * @return bool
 */
function DelCategory() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');
	$cate = $zbp->GetCategoryByID($id);
	if ($cate->ID > 0) {
		DelCategory_Articles($cate->ID);
		$cate->Del();

		$zbp->LoadCategorys();
		$zbp->AddBuildModule('catalog');
		$zbp->DelItemToNavbar('category', $cate->ID);

		foreach ($GLOBALS['Filter_Plugin_DelCategory_Succeed'] as $fpname => &$fpsignal)
			$fpname($cate);
	}

	return true;
}

/**
 * 删除分类下所有文章
 * @param int $id 分类ID
 */
function DelCategory_Articles($id) {
	global $zbp;

	$sql = $zbp->db->sql->Update($zbp->table['Post'], array('log_CateID' => 0), array(array('=', 'log_CateID', $id)));
	$zbp->db->Update($sql);
}

################################################################################################################
/**
 * 提交标签数据
 * @return bool
 */
function PostTag() {
	global $zbp;
	if (!isset($_POST['ID'])) return;

	if (isset($_POST['Alias'])) {
		$_POST['Alias'] = TransferHTML($_POST['Alias'], '[noscript]');
	}

	$tag = new Tag();
	if (GetVars('ID', 'POST') == 0) {
	} else {
		$tag->LoadInfoByID(GetVars('ID', 'POST'));
	}

	foreach ($zbp->datainfo['Tag'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if (isset($_POST[$key])) {
			$tag->$key = GetVars($key, 'POST');
		}
	}

	foreach ($GLOBALS['Filter_Plugin_PostTag_Core'] as $fpname => &$fpsignal) {
		$fpname($tag);
	}

	FilterTag($tag);
	FilterMeta($tag);

	CountTag($tag);

	$tag->Save();

	if (GetVars('AddNavbar', 'POST') == 0)
		$zbp->DelItemToNavbar('tag', $tag->ID);
	if (GetVars('AddNavbar', 'POST') == 1)
		$zbp->AddItemToNavbar('tag', $tag->ID, $tag->Name, $tag->Url);

	$zbp->AddBuildModule('tags');

	foreach ($GLOBALS['Filter_Plugin_PostTag_Succeed'] as $fpname => &$fpsignal)
		$fpname($tag);

	return true;
}

/**
 * 删除标签
 * @return bool
 */
function DelTag() {
	global $zbp;

	$tagid = (int)GetVars('id', 'GET');
	$tag = $zbp->GetTagByID($tagid);
	if ($tag->ID > 0) {
		$tag->Del();
		$zbp->DelItemToNavbar('tag', $tag->ID);
		$zbp->AddBuildModule('tags');
		foreach ($GLOBALS['Filter_Plugin_DelTag_Succeed'] as $fpname => &$fpsignal)
			$fpname($tag);
	}

	return true;
}

################################################################################################################
/**
 * 提交用户数据
 * @return bool
 */
function PostMember() {
	global $zbp;
	if (!isset($_POST['ID'])) return;

	if (!$zbp->CheckRights('MemberAll')) {
		unset($_POST['Level']);
		unset($_POST['Name']);
		unset($_POST['Status']);
	}
	if (isset($_POST['Password'])) {
		if ($_POST['Password'] == '') {
			unset($_POST['Password']);
		} else {
			if (strlen($_POST['Password']) < $zbp->option['ZC_PASSWORD_MIN'] || strlen($_POST['Password']) > $zbp->option['ZC_PASSWORD_MAX']) {
				$zbp->ShowError(54, __FILE__, __LINE__);
			}
			if (!CheckRegExp($_POST['Password'], '[password]')) {
				$zbp->ShowError(54, __FILE__, __LINE__);
			}
			$_POST['Password'] = Member::GetPassWordByGuid($_POST['Password'], $_POST['Guid']);
		}
	}

	if (isset($_POST['Name'])) {
		if (isset($zbp->membersbyname[$_POST['Name']])) {
			if ($zbp->membersbyname[$_POST['Name']]->ID <> $_POST['ID']) {
				$zbp->ShowError(62, __FILE__, __LINE__);
			}
		}
	}

	if (isset($_POST['Alias'])) {
		$_POST['Alias'] = TransferHTML($_POST['Alias'], '[noscript]');
	}

	$mem = new Member();
	if (GetVars('ID', 'POST') == 0) {
		if (isset($_POST['Password']) == false || $_POST['Password'] == '') {
			$zbp->ShowError(73, __FILE__, __LINE__);
		}
		$_POST['IP'] = GetGuestIP();
	} else {
		$mem->LoadInfoByID(GetVars('ID', 'POST'));
	}

	if ($zbp->CheckRights('MemberAll')) {
		if ($mem->ID == $zbp->user->ID) {
			unset($_POST['Level']);
			unset($_POST['Status']);
		}
	}

	foreach ($zbp->datainfo['Member'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if (isset($_POST[$key])) {
			$mem->$key = GetVars($key, 'POST');
		}
	}

	foreach ($GLOBALS['Filter_Plugin_PostMember_Core'] as $fpname => &$fpsignal) {
		$fpname($mem);
	}

	FilterMember($mem);
	FilterMeta($mem);

	CountMember($mem);

	$mem->Save();

	foreach ($GLOBALS['Filter_Plugin_PostMember_Succeed'] as $fpname => &$fpsignal)
		$fpname($mem);

	if (isset($_POST['Password'])) {
		if ($mem->ID == $zbp->user->ID) {
			Redirect($zbp->host . 'zb_system/cmd.php?act=login');
		}
	}

	return true;
}

/**
 * 删除用户
 * @return bool
 */
function DelMember() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');
	$mem = $zbp->GetMemberByID($id);
	if ($mem->ID > 0 && $mem->ID <> $zbp->user->ID) {
		DelMember_AllData($id);
		$mem->Del();
		foreach ($GLOBALS['Filter_Plugin_DelMember_Succeed'] as $fpname => &$fpsignal)
			$fpname($mem);
	} else {
		return false;
	}

	return true;
}

/**
 * 删除用户下所有数据（包括文章、评论、附件）
 * @param int $id 用户ID
 */
function DelMember_AllData($id) {
	global $zbp;

	$w = array();
	$w[] = array('=', 'log_AuthorID', $id);

	$articles = $zbp->GetPostList('*', $w);
	foreach ($articles as $a) {
		$a->Del();
	}

	$w = array();
	$w[] = array('=', 'comm_AuthorID', $id);
	$comments = $zbp->GetCommentList('*', $w);
	foreach ($comments as $c) {
		$c->AuthorID = 0;
		$c->Save();
	}

	$w = array();
	$w[] = array('=', 'ul_AuthorID', $id);
	$uploads = $zbp->GetUploadList('*', $w);
	foreach ($uploads as $u) {
		$u->Del();
		$u->DelFile();
	}

}

################################################################################################################
/**
 * 提交模块数据
 * @return bool
 */
function PostModule() {
	global $zbp;

	if (isset($_POST['catalog_style'])) {
		$zbp->option['ZC_MODULE_CATALOG_STYLE'] = $_POST['catalog_style'];
		$zbp->SaveOption();
	}

	if (!isset($_POST['ID'])) return;
	if (!GetVars('FileName', 'POST')) {
		$_POST['FileName'] = 'mod' . rand(1000, 2000);
	} else {
		$_POST['FileName'] = strtolower($_POST['FileName']);
	}
	if (!GetVars('HtmlID', 'POST')) {
		$_POST['HtmlID'] = $_POST['FileName'];
	}
	if (isset($_POST['MaxLi'])) {
		$_POST['MaxLi'] = (integer)$_POST['MaxLi'];
	}
	if (isset($_POST['IsHideTitle'])) {
		$_POST['IsHideTitle'] = (integer)$_POST['IsHideTitle'];
	}
	if (!isset($_POST['Type'])) {
		$_POST['Type'] = 'div';
	}
	if (isset($_POST['Content'])) {
		if ($_POST['Type'] != 'div') {
			$_POST['Content'] = str_replace(array("\r", "\n"), array('', ''), $_POST['Content']);
		}
	}
	if (isset($_POST['Source'])) {
		if ($_POST['Source'] == 'theme') {
			$c = GetVars('Content', 'POST');
			$d = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/';
			$f = $d . GetVars('FileName', 'POST') . '.php';
			if(!file_exists($d)){
				@mkdir($d,0755);
			}
			@file_put_contents($f, $c);
			return true;
		}
	}
	
	$mod = $zbp->GetModuleByID(GetVars('ID', 'POST'));

	foreach ($zbp->datainfo['Module'] as $key => $value) {
		if ($key == 'ID' || $key == 'Meta')	{continue;}
		if (isset($_POST[$key])) {
			$mod->$key = GetVars($key, 'POST');
		}
	}

	if (isset($_POST['NoRefresh'])) {
		$mod->NoRefresh = (bool)$_POST['NoRefresh'];
	}
	
	foreach ($GLOBALS['Filter_Plugin_PostModule_Core'] as $fpname => &$fpsignal) {
		$fpname($mod);
	}

	FilterModule($mod);

	$mod->Save();

	$zbp->AddBuildModule($mod->FileName);

	foreach ($GLOBALS['Filter_Plugin_PostModule_Succeed'] as $fpname => &$fpsignal)
		$fpname($mod);

	return true;
}

/**
 * 删除模块
 * @return bool
 */
function DelModule() {
	global $zbp;

	if (GetVars('source', 'GET') == 'theme') {
		if (GetVars('filename', 'GET')) {
			$f = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . GetVars('filename', 'GET') . '.php';
			if (file_exists($f))
				@unlink($f);

			return true;
		}

		return false;
	}

	$id = (int)GetVars('id', 'GET');
	$mod = $zbp->GetModuleByID($id);
	if ($mod->Source <> 'system') {
		$mod->Del();
		foreach ($GLOBALS['Filter_Plugin_DelModule_Succeed'] as $fpname => &$fpsignal)
			$fpname($mod);
	} else {
		return false;
	}

	return true;
}

################################################################################################################
/**
 * 附件上传
 */
function PostUpload() {
	global $zbp;

	foreach ($_FILES as $key => $value) {
		if ($_FILES[$key]['error'] == 0) {
			if (is_uploaded_file($_FILES[$key]['tmp_name'])) {
				$tmp_name = $_FILES[$key]['tmp_name'];
				$name = $_FILES[$key]['name'];

				$upload = new Upload;
				$upload->Name = $_FILES[$key]['name'];
				$upload->SourceName = $_FILES[$key]['name'];
				$upload->MimeType = $_FILES[$key]['type'];
				$upload->Size = $_FILES[$key]['size'];
				$upload->AuthorID = $zbp->user->ID;

				if (!$upload->CheckExtName())
					$zbp->ShowError(26, __FILE__, __LINE__);
				if (!$upload->CheckSize())
					$zbp->ShowError(27, __FILE__, __LINE__);

				$upload->SaveFile($_FILES[$key]['tmp_name']);
				$upload->Save();
			}
		}
	}
	if (isset($upload))
		CountMemberArray(array($upload->AuthorID));

}

/**
 * 删除附件
 * @return bool
 */
function DelUpload() {
	global $zbp;

	$id = (int)GetVars('id', 'GET');
	$u = $zbp->GetUploadByID($id);
	if ($zbp->CheckRights('UploadAll') || (!$zbp->CheckRights('UploadAll') && $u->AuthorID == $zbp->user->ID)) {
		$u->Del();
		CountMemberArray(array($u->AuthorID));
		$u->DelFile();
	} else {
		return false;
	}

	return true;
}

################################################################################################################
/**
 * 启用插件
 * @param string $name 插件ID
 * @return string 返回插件ID
 */
function EnablePlugin($name) {
	global $zbp;

	$app=$zbp->LoadApp('plugin',$name);
	$app->CheckCompatibility();

	$zbp->option['ZC_USING_PLUGIN_LIST'] = AddNameInString($zbp->option['ZC_USING_PLUGIN_LIST'], $name);
	$zbp->SaveOption();

	return $name;
}

/**
 * 禁用插件
 * @param string $name 插件ID
 */
function DisablePlugin($name) {
	global $zbp;
	$zbp->option['ZC_USING_PLUGIN_LIST'] = DelNameInString($zbp->option['ZC_USING_PLUGIN_LIST'], $name);
	$zbp->SaveOption();
}

/**
 * 设置当前主题样式
 * @param string $theme 主题ID
 * @param string $style 样式名
 * @return string 返回主题ID
 */
function SetTheme($theme, $style) {
	global $zbp;
	
	$app=$zbp->LoadApp('theme',$theme);
	$app->CheckCompatibility();
	
	$oldtheme = $zbp->option['ZC_BLOG_THEME'];

	if ($oldtheme != $theme) {
		$app = $zbp->LoadApp('theme', $theme);
		if ($app->sidebars_sidebar1 | $app->sidebars_sidebar2 | $app->sidebars_sidebar3 | $app->sidebars_sidebar4 | $app->sidebars_sidebar5) {
			$s1 = $zbp->option['ZC_SIDEBAR_ORDER'];
			$s2 = $zbp->option['ZC_SIDEBAR2_ORDER'];
			$s3 = $zbp->option['ZC_SIDEBAR3_ORDER'];
			$s4 = $zbp->option['ZC_SIDEBAR4_ORDER'];
			$s5 = $zbp->option['ZC_SIDEBAR5_ORDER'];
			$zbp->option['ZC_SIDEBAR_ORDER'] = $app->sidebars_sidebar1;
			$zbp->option['ZC_SIDEBAR2_ORDER'] = $app->sidebars_sidebar2;
			$zbp->option['ZC_SIDEBAR3_ORDER'] = $app->sidebars_sidebar3;
			$zbp->option['ZC_SIDEBAR4_ORDER'] = $app->sidebars_sidebar4;
			$zbp->option['ZC_SIDEBAR5_ORDER'] = $app->sidebars_sidebar5;
			$zbp->cache->ZC_SIDEBAR_ORDER1 = $s1;
			$zbp->cache->ZC_SIDEBAR_ORDER2 = $s2;
			$zbp->cache->ZC_SIDEBAR_ORDER3 = $s3;
			$zbp->cache->ZC_SIDEBAR_ORDER4 = $s4;
			$zbp->cache->ZC_SIDEBAR_ORDER5 = $s5;
			$zbp->SaveCache();
		} else {
			if ($zbp->cache->ZC_SIDEBAR_ORDER1 | $zbp->cache->ZC_SIDEBAR_ORDER2 | $zbp->cache->ZC_SIDEBAR_ORDER3 | $zbp->cache->ZC_SIDEBAR_ORDER4 | $zbp->cache->ZC_SIDEBAR_ORDER5) {
				$zbp->option['ZC_SIDEBAR_ORDER'] = $zbp->cache->ZC_SIDEBAR_ORDER1;
				$zbp->option['ZC_SIDEBAR2_ORDER'] = $zbp->cache->ZC_SIDEBAR_ORDER2;
				$zbp->option['ZC_SIDEBAR3_ORDER'] = $zbp->cache->ZC_SIDEBAR_ORDER3;
				$zbp->option['ZC_SIDEBAR4_ORDER'] = $zbp->cache->ZC_SIDEBAR_ORDER4;
				$zbp->option['ZC_SIDEBAR5_ORDER'] = $zbp->cache->ZC_SIDEBAR_ORDER5;
				$zbp->cache->ZC_SIDEBAR_ORDER1 = '';
				$zbp->cache->ZC_SIDEBAR_ORDER2 = '';
				$zbp->cache->ZC_SIDEBAR_ORDER3 = '';
				$zbp->cache->ZC_SIDEBAR_ORDER4 = '';
				$zbp->cache->ZC_SIDEBAR_ORDER5 = '';
				$zbp->SaveCache();
			}
		}

	}

	$zbp->option['ZC_BLOG_THEME'] = $theme;
	$zbp->option['ZC_BLOG_CSS'] = $style;

	$zbp->BuildTemplate();

	$zbp->SaveOption();

	if ($oldtheme != $theme) {
		UninstallPlugin($oldtheme);

		return $theme;
	}
}

/**
 * 设置侧栏
 */
function SetSidebar() {
	global $zbp;

	$zbp->option['ZC_SIDEBAR_ORDER'] = trim(GetVars('sidebar', 'POST'), '|');
	$zbp->option['ZC_SIDEBAR2_ORDER'] = trim(GetVars('sidebar2', 'POST'), '|');
	$zbp->option['ZC_SIDEBAR3_ORDER'] = trim(GetVars('sidebar3', 'POST'), '|');
	$zbp->option['ZC_SIDEBAR4_ORDER'] = trim(GetVars('sidebar4', 'POST'), '|');
	$zbp->option['ZC_SIDEBAR5_ORDER'] = trim(GetVars('sidebar5', 'POST'), '|');
	$zbp->SaveOption();
}

/**
 * 保存网站设置选项
 */
function SaveSetting() {
	global $zbp;

	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 2) !== 'ZC') continue;
		if ($key == 'ZC_PERMANENT_DOMAIN_ENABLE' || 
			$key == 'ZC_DEBUG_MODE' || 
			$key == 'ZC_COMMENT_TURNOFF' || 
			$key == 'ZC_COMMENT_REVERSE_ORDER' || 
			$key == 'ZC_DISPLAY_SUBCATEGORYS' || 
			$key == 'ZC_GZIP_ENABLE' ||
			$key == 'ZC_SYNTAXHIGHLIGHTER_ENABLE' ||
			$key == 'ZC_COMMENT_VERIFY_ENABLE'
		) {
			$zbp->option[$key] = (boolean)$value;
			continue;
		}
		if ($key == 'ZC_RSS2_COUNT' || 
			$key == 'ZC_UPLOAD_FILESIZE' || 
			$key == 'ZC_DISPLAY_COUNT' || 
			$key == 'ZC_SEARCH_COUNT' || 
			$key == 'ZC_PAGEBAR_COUNT' || 
			$key == 'ZC_COMMENTS_DISPLAY_COUNT' || 
			$key == 'ZC_MANAGE_COUNT'
		) {
			$zbp->option[$key] = (integer)$value;
			continue;
		}
		if ($key == 'ZC_UPLOAD_FILETYPE'){
			$value = strtolower($value);
			$value = DelNameInString($value, 'php');
			$value = DelNameInString($value, 'asp');
		}
		$zbp->option[$key] = trim(str_replace(array("\r", "\n"), array("", ""), $value));
	}

	$zbp->option['ZC_BLOG_HOST'] = trim($zbp->option['ZC_BLOG_HOST']);
	$zbp->option['ZC_BLOG_HOST'] = trim($zbp->option['ZC_BLOG_HOST'], '/') . '/';
	$lang = require($zbp->usersdir . 'language/' . $zbp->option['ZC_BLOG_LANGUAGEPACK'] . '.php');
	$zbp->option['ZC_BLOG_LANGUAGE'] = $lang['lang'];
	$zbp->option['ZC_BLOG_PRODUCT'] = 'Z-BlogPHP';	
	$zbp->SaveOption();
}

################################################################################################################
/**
 * 过滤扩展数据
 * @param $object
 */
function FilterMeta(&$object) {

	//$type=strtolower(get_class($object));

	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 5) == 'meta_') {
			$name = substr($key, 5 - strlen($key));
			$object->Metas->$name = $value;
		}
	}

	foreach ($object->Metas->Data as $key => $value) {
		if ($value == "")
			unset($object->Metas->Data[$key]);
	}

}

/**
 * 过滤评论数据
 * @param $comment
 */
function FilterComment(&$comment) {
	global $zbp;

	if (!CheckRegExp($comment->Name, '[username]')) {
		$zbp->ShowError(15, __FILE__, __LINE__);
	}
	if ($comment->Email && (!CheckRegExp($comment->Email, '[email]'))) {
		$zbp->ShowError(29, __FILE__, __LINE__);
	}
	if ($comment->HomePage && (!CheckRegExp($comment->HomePage, '[homepage]'))) {
		$zbp->ShowError(30, __FILE__, __LINE__);
	}

	$comment->Name = substr($comment->Name, 0, 20);
	$comment->Email = substr($comment->Email, 0, 30);
	$comment->HomePage = substr($comment->HomePage, 0, 100);

	$comment->Content = TransferHTML($comment->Content, '[nohtml]');

	$comment->Content = substr($comment->Content, 0, 1000);
	$comment->Content = trim($comment->Content);
	if (strlen($comment->Content) == 0) {
		$zbp->ShowError(46, __FILE__, __LINE__);
	}
}

/**
 * 过滤文章数据
 * @param $article
 */
function FilterPost(&$article) {
	global $zbp;

	$article->Title = strip_tags($article->Title);
	$article->Alias = TransferHTML($article->Alias, '[normalname]');
	$article->Alias = str_replace(' ', '', $article->Alias);

	if ($article->Type == ZC_POST_TYPE_ARTICLE) {
		if (!$zbp->CheckRights('ArticleAll')) {
			$article->Content = TransferHTML($article->Content, '[noscript]');
			$article->Intro = TransferHTML($article->Intro, '[noscript]');
		}
	} elseif ($article->Type == ZC_POST_TYPE_PAGE) {
		if (!$zbp->CheckRights('PageAll')) {
			$article->Content = TransferHTML($article->Content, '[noscript]');
			$article->Intro = TransferHTML($article->Intro, '[noscript]');
		}
	}
}

/**
 * 过滤用户数据
 * @param $member
 */
function FilterMember(&$member) {
	global $zbp;
	$member->Intro = TransferHTML($member->Intro, '[noscript]');
	$member->Alias = TransferHTML($member->Alias, '[normalname]');
	$member->Alias = str_replace('/', '', $member->Alias);
	$member->Alias = str_replace('.', '', $member->Alias);
	$member->Alias = str_replace(' ', '', $member->Alias);
	$member->Alias = str_replace('_', '', $member->Alias);
	if (strlen($member->Name) < $zbp->option['ZC_USERNAME_MIN'] || strlen($member->Name) > $zbp->option['ZC_USERNAME_MAX']) {
		$zbp->ShowError(77, __FILE__, __LINE__);
	}

	if (!CheckRegExp($member->Name, '[username]')) {
		$zbp->ShowError(77, __FILE__, __LINE__);
	}

	if (!CheckRegExp($member->Email, '[email]')) {
		$member->Email = 'null@null.com';
	}

	if (substr($member->HomePage, 0, 4) != 'http') {
		$member->HomePage = 'http://' . $member->HomePage;
	}

	if (!CheckRegExp($member->HomePage, '[homepage]')) {
		$member->HomePage = '';
	}

	if (strlen($member->Email) > $zbp->option['ZC_EMAIL_MAX']) {
		$zbp->ShowError(29, __FILE__, __LINE__);
	}

	if (strlen($member->HomePage) > $zbp->option['ZC_HOMEPAGE_MAX']) {
		$zbp->ShowError(30, __FILE__, __LINE__);
	}

}

/**
 * 过滤模块数据
 * @param $module
 */
function FilterModule(&$module) {
	global $zbp;
	$module->FileName = TransferHTML($module->FileName, '[filename]');
	$module->HtmlID = TransferHTML($module->HtmlID, '[normalname]');
}

/**
 * 过滤分类数据
 * @param $category
 */
function FilterCategory(&$category) {
	global $zbp;
	$category->Name = strip_tags($category->Name);
	$category->Alias = TransferHTML($category->Alias, '[normalname]');
	//$category->Alias=str_replace('/','',$category->Alias);
	$category->Alias = str_replace('.', '', $category->Alias);
	$category->Alias = str_replace(' ', '', $category->Alias);
	$category->Alias = str_replace('_', '', $category->Alias);
}

/**
 * 过滤tag数据
 * @param $tag
 */
function FilterTag(&$tag) {
	global $zbp;
	$tag->Name = strip_tags($tag->Name);
	$tag->Alias = TransferHTML($tag->Alias, '[normalname]');
}

################################################################################################################
#统计函数
/**
 *统计公开文章数
 */
function CountNormalArticleNums() {
	global $zbp;
	$s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', 0), array('=', 'log_IsTop', 0), array('=', 'log_Status', 0)));
	$num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$zbp->cache->normal_article_nums = $num;
	$zbp->SaveCache();
}

/**
 * 统计文章下评论数
 * @param post $article
 */
function CountPost(&$article) {
	global $zbp;

	$id = $article->ID;

	$s = $zbp->db->sql->Count($zbp->table['Comment'], array(array('COUNT', '*', 'num')), array(array('=', 'comm_LogID', $id), array('=', 'comm_IsChecking', 0)));
	$num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$article->CommNums = $num;
}

/**
 * 批量统计指定文章下评论数并保存
 * @param array $array 记录文章ID的数组
 */
function CountPostArray($array) {
	global $zbp;
	$array = array_unique($array);
	foreach ($array as $value) {
		if ($value == 0) continue;
		$article = new Post;
		$article->LoadInfoByID($value);
		CountPost($article);
		$article->Save();
	}
}

/**
 * 统计分类下文章数
 * @param category &$category
 */
function CountCategory(&$category) {
	global $zbp;

	$id = $category->ID;

	$s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', 0), array('=', 'log_IsTop', 0), array('=', 'log_Status', 0), array('=', 'log_CateID', $id)));
	$num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$category->Count = $num;
}

/**
 * 批量统计指定分类下文章数并保存
 * @param array $array 记录分类ID的数组
 */
function CountCategoryArray($array) {
	global $zbp;
	$array = array_unique($array);
	foreach ($array as $value) {
		if ($value == 0) continue;
		CountCategory($zbp->categorys[$value]);
		$zbp->categorys[$value]->Save();
	}
}

/**
 * 统计tag下的文章数
 * @param tag &$tag
 */
function CountTag(&$tag) {
	global $zbp;

	$id = $tag->ID;

	$s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('LIKE', 'log_Tag', '%{' . $id . '}%')));
	$num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$tag->Count = $num;
}

/**
 * 批量统计指定tag下文章数并保存
 * @param string $string 类似'{1}{2}{3}{4}{4}'的tagID串
 */
function CountTagArrayString($string) {
	global $zbp;
	$array = $zbp->LoadTagsByIDString($string);
	foreach ($array as &$tag) {
		CountTag($tag);
		$tag->Save();
	}
}

/**
 * 统计用户下的文章数、页面数、评论数、附件数等
 * @param $member
 */
function CountMember(&$member) {
	global $zbp;
	if(!($member  instanceof  Member))return;

	$id = $member->ID;

	$s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_AuthorID', $id), array('=', 'log_Type', 0)));
	$member_Articles = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_AuthorID', $id), array('=', 'log_Type', 1)));
	$member_Pages = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$s = $zbp->db->sql->Count($zbp->table['Comment'], array(array('COUNT', '*', 'num')), array(array('=', 'comm_AuthorID', $id)));
	$member_Comments = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$s = $zbp->db->sql->Count($zbp->table['Upload'], array(array('COUNT', '*', 'num')), array(array('=', 'ul_AuthorID', $id)));
	$member_Uploads = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

	$member->Articles = $member_Articles;
	$member->Pages = $member_Pages;
	$member->Comments = $member_Comments;
	$member->Uploads = $member_Uploads;
}

/**
 * 批量统计指定用户数据并保存
 * @param array $array 记录用户ID的数组
 */
function CountMemberArray($array) {
	global $zbp;
	$array = array_unique($array);
	foreach ($array as $value) {
		if ($value == 0) continue;
		if(isset($zbp->members[$value])){
			CountMember($zbp->members[$value]);
			$zbp->members[$value]->Save();
		}
	}
}

################################################################################################################
#BuildModule
/**
 * 导出网站分类模块数据
 * @return string 模块内容
 */
function BuildModule_catalog() {
	global $zbp;
	$s = '';

	if ($zbp->option['ZC_MODULE_CATALOG_STYLE'] == '2') {

		foreach ($zbp->categorysbyorder as $key => $value) {
			if ($value->Level == 0) {
				$s .= '<li class="li-cate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li>';
			}
		}
		foreach ($zbp->categorysbyorder as $key => $value) {
			if ($value->Level == 1) {
				$s = str_replace('<!--' . $value->ParentID . 'end-->', '<li class="li-subcate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li><!--' . $value->ParentID . 'end-->', $s);
			}
		}
		foreach ($zbp->categorysbyorder as $key => $value) {
			if ($value->Level == 2) {
				$s = str_replace('<!--' . $value->ParentID . 'end-->', '<li class="li-subcate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li><!--' . $value->ParentID . 'end-->', $s);
			}
		}
		foreach ($zbp->categorysbyorder as $key => $value) {
			if ($value->Level == 3) {
				$s = str_replace('<!--' . $value->ParentID . 'end-->', '<li class="li-subcate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li><!--' . $value->ParentID . 'end-->', $s);
			}
		}

		foreach ($zbp->categorysbyorder as $key => $value) {
			$s = str_replace('<!--' . $value->ID . 'begin--><!--' . $value->ID . 'end-->', '', $s);
		}
		foreach ($zbp->categorysbyorder as $key => $value) {
			$s = str_replace('<!--' . $value->ID . 'begin-->', '<ul class="ul-subcates">', $s);
			$s = str_replace('<!--' . $value->ID . 'end-->', '</ul>', $s);
		}

	} elseif ($zbp->option['ZC_MODULE_CATALOG_STYLE'] == '1') {
		foreach ($zbp->categorysbyorder as $key => $value) {
			$s .= '<li>' . $value->Symbol . '<a href="' . $value->Url . '">' . $value->Name . '</a></li>';
		}
	} else {
		foreach ($zbp->categorysbyorder as $key => $value) {
			$s .= '<li><a href="' . $value->Url . '">' . $value->Name . '</a></li>';
		}
	}

	return $s;
}

/**
 * 导出日历模块数据
 * @param string $date 日期
 * @return string 模块内容
 */
function BuildModule_calendar($date = '') {
	global $zbp;

	if ($date == '')
		$date = date('Y-m', time());

	$s = '<table id="tbCalendar"><caption>';

	$url = new UrlRule($zbp->option['ZC_DATE_REGEX']);
	$value = strtotime('-1 month', strtotime($date));
	$url->Rules['{%date%}'] = date('Y-n', $value);
	$url->Rules['{%year%}'] = date('Y', $value);
	$url->Rules['{%month%}'] = date('n', $value);

	$url->Rules['{%day%}'] = 1;
	$s .= '<a href="' . $url->Make() . '">«</a>';

	$value = strtotime($date);
	$url->Rules['{%date%}'] = date('Y-n', $value);
	$url->Rules['{%year%}'] = date('Y', $value);
	$url->Rules['{%month%}'] = date('n', $value);
	$s .= '&nbsp;&nbsp;&nbsp;<a href="' . $url->Make() . '">' . str_replace(array('%y%', '%m%'), array(date('Y', $value), date('n', $value)), $zbp->lang['msg']['year_month']) . '</a>&nbsp;&nbsp;&nbsp;';

	$value = strtotime('+1 month', strtotime($date));
	$url->Rules['{%date%}'] = date('Y-n', $value);
	$url->Rules['{%year%}'] = date('Y', $value);
	$url->Rules['{%month%}'] = date('n', $value);
	$s .= '<a href="' . $url->Make() . '">»</a></caption>';

	$s .= '<thead><tr>';
	for ($i = 1; $i < 8; $i++) {
		$s .= '<th title="' . $zbp->lang['week'][$i] . '" scope="col"><small>' . $zbp->lang['week_abbr'][$i] . '</small></th>';
	}

	$s .= '</tr></thead>';
	$s .= '<tbody>';
	$s .= '<tr>';

	$a = 1;
	$b = date('t', strtotime($date));
	$j = date('N', strtotime($date . '-1'));
	$k = 7 - date('N', strtotime($date . '-' . date('t', strtotime($date))));

	if ($j > 1) {
		$s .= '<td class="pad" colspan="' . ($j - 1) . '"> </td>';
	} elseif ($j = 1) {
		$s .= '';
	}

	$l = $j - 1;
	for ($i = $a; $i < $b + 1; $i++) {
		$s .= '<td>' . $i . '</td>';

		$l = $l + 1;
		if ($l % 7 == 0)
			$s .= '</tr><tr>';
	}

	if ($k > 1) {
		$s .= '<td class="pad" colspan="' . ($k) . '"> </td>';
	} elseif ($k = 1) {
		$s .= '';
	}

	$s .= '</tr></tbody>';
	$s .= '</table>';
	$s = str_replace('<tr></tr>', '', $s);

	$fdate = strtotime($date);
	$ldate = (strtotime(date('Y-m-t', strtotime($date))) + 60 * 60 * 24);
	$sql = $zbp->db->sql->Select(
		$zbp->table['Post'],
		array('log_ID', 'log_PostTime'),
		array(
			array('=', 'log_Type', '0'),
			array('=', 'log_Status', '0'),
			array('BETWEEN', 'log_PostTime', $fdate, $ldate)
		),
		array('log_PostTime' => 'ASC'),
		null,
		null
	);
	$array = $zbp->db->Query($sql);
	$arraydate = array();
	$arrayid = array();
	foreach ($array as $key => $value) {
		$arraydate[date('j', $value['log_PostTime'])] = $value['log_ID'];
	}
	if (count($arraydate) > 0) {
		foreach ($arraydate as $key => $value) {
			$arrayid[] = array('log_ID', $value);
		}
		$articles = $zbp->GetArticleList('*', array(array('array', $arrayid)),null,null,null,false);
		foreach ($arraydate as $key => $value) {
			$a = $zbp->GetPostByID($value);
			$s = str_replace('<td>' . $key . '</td>', '<td><a href="' . $a->Url . '">' . $key . '</a></td>', $s);
		}
	}

	return $s;

}

/**
 * 导出最新留言模块数据
 * @return string 模块内容
 */
function BuildModule_comments() {
	global $zbp;

	$i = $zbp->modulesbyfilename['comments']->MaxLi;
	if ($i == 0) $i = 10;
	$comments = $zbp->GetCommentList('*', array(array('=', 'comm_IsChecking', 0)), array('comm_PostTime' => 'DESC'), $i, null);

	$s = '';
	foreach ($comments as $comment) {
		$s .= '<li><a href="' . $comment->Post->Url . '#cmt' . $comment->ID . '" title="' . htmlspecialchars($comment->Author->StaticName . ' @ ' . $comment->Time()) . '">' . TransferHTML($comment->Content, '[noenter]') . '</a></li>';
	}

	return $s;
}

/**
 * 导出最近发表文章模块数据
 * @return string 模块内容
 */
function BuildModule_previous() {
	global $zbp;

	$i = $zbp->modulesbyfilename['previous']->MaxLi;
	if ($i == 0) $i = 10;
	$articles = $zbp->GetArticleList('*', array(array('=', 'log_Type', 0), array('=', 'log_Status', 0)), array('log_PostTime' => 'DESC'), $i, null,false);
	$s = '';
	foreach ($articles as $article) {
		$s .= '<li><a href="' . $article->Url . '">' . $article->Title . '</a></li>';
	}

	return $s;
}

/**
 * 导出文章归档模块数据
 * @return string 模块内容
 */
function BuildModule_archives() {
	global $zbp;

	$i = $zbp->modulesbyfilename['archives']->MaxLi;
	if($i<0)return '';

	$fdate;
	$ldate;

	$sql = $zbp->db->sql->Select($zbp->table['Post'], array('log_PostTime'), null, array('log_PostTime' => 'DESC'), array(1), null);

	$array = $zbp->db->Query($sql);

	if (count($array) == 0)
		return '';

	$ldate = array(date('Y', $array[0]['log_PostTime']), date('m', $array[0]['log_PostTime']));

	$sql = $zbp->db->sql->Select($zbp->table['Post'], array('log_PostTime'), null, array('log_PostTime' => 'ASC'), array(1), null);

	$array = $zbp->db->Query($sql);

	if (count($array) == 0)
		return '';

	$fdate = array(date('Y', $array[0]['log_PostTime']), date('m', $array[0]['log_PostTime']));

	$arraydate = array();

	for ($i = $fdate[0]; $i < $ldate[0] + 1; $i++) {
		for ($j = 1; $j < 13; $j++) {
			$arraydate[] = strtotime($i . '-' . $j);
		}
	}

	foreach ($arraydate as $key => $value) {
		if ($value - strtotime($ldate[0] . '-' . $ldate[1]) > 0)
			unset($arraydate[$key]);
		if ($value - strtotime($fdate[0] . '-' . $fdate[1]) < 0)
			unset($arraydate[$key]);
	}

	$arraydate = array_reverse($arraydate);

	$s = '';

	foreach ($arraydate as $key => $value) {
		$url = new UrlRule($zbp->option['ZC_DATE_REGEX']);
		$url->Rules['{%date%}'] = date('Y-n', $value);
		$url->Rules['{%year%}'] = date('Y', $value);
		$url->Rules['{%month%}'] = date('n', $value);
		$url->Rules['{%day%}'] = 1;

		$fdate = $value;
		$ldate = (strtotime(date('Y-m-t', $value)) + 60 * 60 * 24);
		$sql = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', '0'), array('=', 'log_Status', '0'), array('BETWEEN', 'log_PostTime', $fdate, $ldate)));
		$n = GetValueInArrayByCurrent($zbp->db->Query($sql), 'num');
		if ($n > 0) {
			$s .= '<li><a href="' . $url->Make() . '">' . str_replace(array('%y%', '%m%'), array(date('Y', $fdate), date('n', $fdate)), $zbp->lang['msg']['year_month']) . ' (' . $n . ')</a></li>';
		}
	}

	return $s;

}

/**
 * 导出导航模块数据
 * @return string 模块内容
 */
function BuildModule_navbar() {
	global $zbp;

	$s = $zbp->modulesbyfilename['navbar']->Content;

	$a = array();
	preg_match_all('/<li id="navbar-(page|category|tag)-(\d+)">/', $s, $a);

	$b = $a[1];
	$c = $a[2];
	foreach ($b as $key => $value) {

		if ($b[$key] == 'page') {

			$type = 'page';
			$id = $c[$key];
			$o = $zbp->GetPostByID($id);
			$url = $o->Url;
			$name = $o->Title;

			$a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
			$s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/a><\/li>/', $a, $s);

		}
		if ($b[$key] == 'category') {

			$type = 'category';
			$id = $c[$key];
			$o = $zbp->GetCategoryByID($id);
			$url = $o->Url;
			$name = $o->Name;

			$a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
			$s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/a><\/li>/', $a, $s);

		}
		if ($b[$key] == 'tag') {

			$type = 'tag';
			$id = $c[$key];
			$o = $zbp->GetTagByID($id);
			$url = $o->Url;
			$name = $o->Name;

			$a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
			$s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/a><\/li>/', $a, $s);

		}
	}

	return $s;
}

/**
 * 导出tags模块数据
 * @return string 模块内容
 */
function BuildModule_tags() {
	global $zbp;
	$s = '';
	$i = $zbp->modulesbyfilename['tags']->MaxLi;
	if ($i == 0) $i = 25;
	$array = $zbp->GetTagList('*', '', array('tag_Count' => 'DESC'), $i, null);
	$array2 = array();
	foreach ($array as $tag) {
		$array2[$tag->ID] = $tag;
	}
	ksort($array2);

	foreach ($array2 as $tag) {
		$s .= '<li><a href="' . $tag->Url . '">' . $tag->Name . '<span class="tag-count"> (' . $tag->Count . ')</span></a></li>';
	}

	return $s;
}

/**
 * 导出用户列表模块数据
 * @param int $level 要导出的用户最低等级，默认为4（即协作者）
 * @return string 模块内容
 */
function BuildModule_authors($level = 4) {
	global $zbp;
	$s = '';

	$w = array();
	$w[] = array('<=', 'mem_Level', $level);

	$array = $zbp->GetMemberList('*', $w, array('mem_ID' => 'ASC'), null, null);

	foreach ($array as $member) {
		$s .= '<li><a href="' . $member->Url . '">' . $member->Name . '<span class="article-nums"> (' . $member->Articles . ')</span></a></li>';
	}

	return $s;
}

/**
 * 导出网站统计模块数据
 * @param array $array
 * @return string 模块内容
 */
function BuildModule_statistics($array = array()) {
	global $zbp;
	$all_artiles = 0;
	$all_pages = 0;
	$all_categorys = 0;
	$all_tags = 0;
	$all_views = 0;
	$all_comments = 0;

	if (count($array) == 0) {
		return $zbp->modulesbyfilename['statistics']->Content;
	}

	if (isset($array[0])) $all_artiles = $array[0];
	if (isset($array[1])) $all_pages = $array[1];
	if (isset($array[2])) $all_categorys = $array[2];
	if (isset($array[3])) $all_tags = $array[3];
	if (isset($array[4])) $all_views = $array[4];
	if (isset($array[5])) $all_comments = $array[5];

	$s = "";
	$s .= "<li>{$zbp->lang['msg']['all_artiles']}:{$all_artiles}</li>";
	$s .= "<li>{$zbp->lang['msg']['all_pages']}:{$all_pages}</li>";
	$s .= "<li>{$zbp->lang['msg']['all_categorys']}:{$all_categorys}</li>";
	$s .= "<li>{$zbp->lang['msg']['all_tags']}:{$all_tags}</li>";
	$s .= "<li>{$zbp->lang['msg']['all_comments']}:{$all_comments}</li>";
	if($zbp->option['ZC_VIEWNUMS_TURNOFF']==false){
		$s .= "<li>{$zbp->lang['msg']['all_views']}:{$all_views}</li>";
	}

	$zbp->modulesbyfilename['statistics']->Type = "ul";

	return $s;

}

################################################################################################################
/**
 * 显示404页面
 *
 * 可通过主题中的404.php模板自定义显示效果
 * @api Filter_Plugin_Zbp_ShowError
 * @param $idortext
 * @param $file
 * @param $line
 */
function ShowError404($idortext,$file,$line){
	global $zbp;

	if(!in_array( "Status: 404 Not Found" ,  headers_list() )) return;

	$zbp->template->SetTags('title', $zbp->title);

	$zbp->template->SetTemplate('404');

	$zbp->template->Display();

	$GLOBALS['Filter_Plugin_Zbp_ShowError']['ShowError404'] = PLUGIN_EXITSIGNAL_RETURN;

	exit;
}

/**
 * ViewIndex的预处理
 */
function PreViewIndex(){
	global $zbp;
	if(isset($zbp->templates['404']))Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','ShowError404');
	$t=array();
	$o=array();
	foreach($zbp->templatetags as $k => $v){
		if(is_string($v) || is_numeric($v) || is_bool($v) )
			$t['{$' . $k . '}']=$v;
	}
	foreach($zbp->option as $k => $v){
		if($k!='ZC_BLOG_CLSID' && $k!='ZC_SQLITE_NAME' && $k!='ZC_SQLITE3_NAME' && $k!='ZC_MYSQL_USERNAME' && $k!='ZC_MYSQL_PASSWORD' && $k!='ZC_MYSQL_NAME')
			$o['{#' . $k . '#}']=$v;
	}
	foreach($zbp->modulesbyfilename as $m){
		$m->Content = str_replace(array_keys($t),array_values($t),$m->Content);
		$m->Content = str_replace(array_keys($o),array_values($o),$m->Content);
	}
}

/**
 * 通过文件获取应用URL地址
 * @param string $file 文件名
 * @return string 返回URL地址
 */
function plugin_dir_url($file) {
	global $zbp;
	$s1=$zbp->path;
	$s2=str_replace('\\','/',dirname($file).'/');
	$s3='';
	$s=substr($s2,strspn($s1,$s2,0));
	if(strpos($s,'zb_users/plugin/')!==false){
		$s=substr($s,strspn($s,$s3='zb_users/plugin/',0));
	}else{
		$s=substr($s,strspn($s,$s3='zb_users/theme/',0));
	}
	$a=explode('/',$s);
	$s=$a[0];
	$s=$zbp->host . $s3 . $s . '/';
	return $s;
}

/**
 * 通过文件获取应用目录路径
 * @param $file
 * @return string
 */
function plugin_dir_path($file) {
	global $zbp;
	$s1=$zbp->path;
	$s2=str_replace('\\','/',dirname($file).'/');
	$s3='';
	$s=substr($s2,strspn($s1,$s2,0));
	if(strpos($s,'zb_users/plugin/')!==false){
		$s=substr($s,strspn($s,$s3='zb_users/plugin/',0));
	}else{
		$s=substr($s,strspn($s,$s3='zb_users/theme/',0));
	}
	$a=explode('/',$s);
	$s=$a[0];
	$s=$zbp->path . $s3 . $s . '/';
	return $s;
}