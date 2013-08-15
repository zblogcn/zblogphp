<?php
require '../function/c_system_base.php';

if(isset($_GET['rsd'])){

header('Content-Type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
echo '<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">'."\n";
echo '  <service>'."\n";
echo '    <engineName>Z-Blog PHP</engineName>'."\n";
echo '    <engineLink>http://www.rainbowsoft.org/</engineLink>'."\n";
echo '    <homePageLink>'.$zbp->host.'</homePageLink>'."\n";
echo '    <apis>'."\n";
echo '      <api name="WordPress" blogID="1" preferred="true" apiLink="'.$zbp->host.'zb_system/xml-rpc/" />'."\n";
echo '      <api name="Movable Type" blogID="1" preferred="false" apiLink="'.$zbp->host.'zb_system/xml-rpc/" />'."\n";
echo '      <api name="MetaWeblog" blogID="1" preferred="false" apiLink="'.$zbp->host.'zb_system/xml-rpc/" />'."\n";
echo '      <api name="Blogger" blogID="1" preferred="false" apiLink="'.$zbp->host.'zb_system/xml-rpc/" />'."\n";
echo '    </apis>'."\n";
echo '  </service>'."\n";
echo '</rsd>'."\n";

die();

}






function zbp_getUsersBlogs(){
	global $zbp;

	$strXML='<?xml version="1.0" encoding="UTF-8"?><methodResponse><params><param><value><array><data><value><struct><member><name>url</name><value><string>$%#1#%$</string></value></member><member><name>blogid</name><value><string>$%#2#%$</string></value></member><member><name>blogName</name><value><string>$%#3#%$</string></value></member></struct></value></data></array></value></param></params></methodResponse>';

	$strXML=str_replace("$%#1#%$",htmlspecialchars($zbp->host),$strXML);
	$strXML=str_replace("$%#2#%$",htmlspecialchars($zbp->guid),$strXML);
	$strXML=str_replace("$%#3#%$",htmlspecialchars($zbp->name),$strXML);

	echo $strXML;
}

function zbp_getCategories(){
	global $zbp;

	$strXML='<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
	$strSingle='<value><struct>
<member><name>categoryId</name><value><string>$%#1#%$</string></value></member>
<member><name>parentId</name><value><string>$%#2#%$</string></value></member>
<member><name>categoryName</name><value><string>$%#3#%$</string></value></member>
<member><name>description</name><value><string>$%#4#%$</string></value></member>
<member><name>httpUrl</name><value><string>$%#5#%$</string></value></member>
<member><name>title</name><value><string>$%#6#%$</string></value></member>
</struct></value>';

	$strAll='';

	foreach ($zbp->categorysbyorder as $key => $value) {
		$s=$strSingle;
		$s=str_replace("$%#1#%$",htmlspecialchars($value->ID),$s);
		$s=str_replace("$%#2#%$",htmlspecialchars($value->ParentID),$s);
		$s=str_replace("$%#3#%$",htmlspecialchars($value->Name),$s);
		$s=str_replace("$%#4#%$",htmlspecialchars($value->Intro),$s);
		$s=str_replace("$%#5#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#6#%$",htmlspecialchars($value->Name),$s);

		$strAll .= $s;

	}

	$strXML=str_replace("$%#1#%$",$strAll,$strXML);
	logs($strXML);
	echo $strXML;

}


function zbp_getTags(){

	global $zbp;

	$strXML='<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
	$strSingle='<value><struct>
<member><name>tag_id</name><value><string>$%#1#%$</string></value></member>
<member><name>name</name><value><string>$%#2#%$</string></value></member>
<member><name>count</name><value><string>$%#3#%$</string></value></member>
<member><name>slug</name><value><string>$%#4#%$</string></value></member>
<member><name>html_url</name><value><string>$%#5#%$</string></value></member>
<member><name>rss_url</name><value><string>$%#6#%$</string></value></member>
</struct></value>';

	$strAll='';

	$array=$zbp->GetTagList(
		'',
		'',
		array('tag_Count'=>'ASC','tag_ID'=>'ASC'),
		array(50),
		''
	);

	foreach ($array as $key => $value) {
		$s=$strSingle;
		$s=str_replace("$%#1#%$",htmlspecialchars($value->ID),$s);
		$s=str_replace("$%#2#%$",htmlspecialchars($value->Name),$s);
		$s=str_replace("$%#3#%$",htmlspecialchars($value->Count),$s);
		$s=str_replace("$%#4#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#5#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#6#%$",htmlspecialchars($value->Url),$s);

		$strAll .= $s;

	}

	$strXML=str_replace("$%#1#%$",$strAll,$strXML);
	logs($strXML);
	echo $strXML;

}


function zbp_getAuthors(){

	global $zbp;

	$strXML='<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
	$strSingle='<value><struct>
<member><name>user_id</name><value><string>$%#1#%$</string></value></member>
<member><name>user_login</name><value><string>$%#2#%$</string></value></member>
<member><name>display_name</name><value><string>$%#3#%$</string></value></member>
</struct></value>';

	$strAll='';

	foreach ($zbp->members as $key => $value) {
		$s=$strSingle;
		$s=str_replace("$%#1#%$",htmlspecialchars($value->ID),$s);
		$s=str_replace("$%#2#%$",htmlspecialchars($value->Name),$s);
		$s=str_replace("$%#3#%$",htmlspecialchars($value->Name),$s);
		$strAll .= $s;

	}

	$strXML=str_replace("$%#1#%$",$strAll,$strXML);
	logs($strXML);
	echo $strXML;

}


function zbp_getRecentPosts($n){


	global $zbp;

	$strXML='<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
	$strSingle='<value><struct>
<member><name>title</name><value><string>$%#1#%$</string></value></member>
<member><name>description</name><value><string>$%#2#%$</string></value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>$%#3#%$</dateTime.iso8601></value></member>
<member><name>categories</name><value><array><data><value><string>$%#4#%$</string></value></data></array></value></member>
<member><name>postid</name><value><string>$%#5#%$</string></value></member>
<member><name>userid</name><value><string>$%#6#%$</string></value></member>
<member><name>link</name><value><string>$%#7#%$</string></value></member>
<member><name>permaLink</name><value><string>$%#8#%$</string></value></member>
<member><name>mt_keywords</name><value><string>$%#9#%$</string></value></member>
<member><name>wp_slug</name><value><string>$%#10#%$</string></value></member>
<member><name>mt_excerpt</name><value><string>$%#11#%$</string></value></member>
<member><name>mt_text_more</name><value><string>$%#12#%$</string></value></member>
<member><name>wp_more_text</name><value><string>$%#13#%$</string></value></member>
</struct></value>';


	$strAll='';

	$array=$zbp->GetArticleList(
		'',
		'',
		array('log_PostTime'=>'DESC'),
		array($n),
		''
	);

	foreach ($array as $key => $value) {
		$s=$strSingle;
		$s=str_replace("$%#1#%$",htmlspecialchars($value->Title),$s);
		$s=str_replace("$%#2#%$",htmlspecialchars($value->Content),$s);
		$s=str_replace("$%#3#%$",htmlspecialchars($value->Time('c')),$s);
		$s=str_replace("$%#4#%$",htmlspecialchars($value->Category->Name),$s);
		$s=str_replace("$%#5#%$",htmlspecialchars($value->ID),$s);
		$s=str_replace("$%#6#%$",htmlspecialchars($value->AuthorID),$s);
		$s=str_replace("$%#7#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#8#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#9#%$",htmlspecialchars($value->TagsToNameString()),$s);
		$s=str_replace("$%#10#%$",htmlspecialchars($value->Alias),$s);
		$s=str_replace("$%#11#%$",htmlspecialchars($value->Intro),$s);
		$s=str_replace("$%#12#%$",htmlspecialchars($value->Intro),$s);
		$s=str_replace("$%#13#%$",htmlspecialchars($value->Intro),$s);
		$strAll .= $s;

	}

	$strXML=str_replace("$%#1#%$",$strAll,$strXML);
	logs($strXML);
	echo $strXML;

}


function zbp_deletePost($id){

	$strXML='<methodResponse><params><param><value><boolean>$%#1#%$</boolean></value></param></params></methodResponse>';

	$_GET['id']=$id;

	if(DelArticle()==true){
		$strXML=str_replace("$%#1#%$",1,$strXML);
		echo $strXML;
	}else{
		$zbp->ShowError(0);
	}

}

function zbp_getPost($id){
	global $zbp;

	$strXML='<methodResponse><params><param>$%#1#%$</param></params></methodResponse>';
	$strSingle='<value><struct>
<member><name>title</name><value><string>$%#1#%$</string></value></member>
<member><name>description</name><value><string>$%#2#%$</string></value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>$%#3#%$</dateTime.iso8601></value></member>
<member><name>categories</name><value><array><data><value><string>$%#4#%$</string></value></data></array></value></member>
<member><name>postid</name><value><string>$%#5#%$</string></value></member>
<member><name>userid</name><value><string>$%#6#%$</string></value></member>
<member><name>link</name><value><string>$%#7#%$</string></value></member>
<member><name>permaLink</name><value><string>$%#8#%$</string></value></member>
<member><name>mt_keywords</name><value><string>$%#9#%$</string></value></member>
<member><name>wp_slug</name><value><string>$%#10#%$</string></value></member>
<member><name>mt_excerpt</name><value><string>$%#11#%$</string></value></member>
<member><name>mt_text_more</name><value><string>$%#12#%$</string></value></member>
<member><name>wp_more_text</name><value><string>$%#13#%$</string></value></member>
</struct></value>';

	$strAll='';

	$article= new Post;
	$article->LoadInfoByID($id);

	$array=array();
	$array[]=$article;

	foreach ($array as $key => $value) {
		$s=$strSingle;
		$s=str_replace("$%#1#%$",htmlspecialchars($value->Title),$s);
		$s=str_replace("$%#2#%$",htmlspecialchars($value->Content),$s);
		$s=str_replace("$%#3#%$",htmlspecialchars($value->Time('c')),$s);
		$s=str_replace("$%#4#%$",htmlspecialchars($value->Category->Name),$s);
		$s=str_replace("$%#5#%$",htmlspecialchars($value->ID),$s);
		$s=str_replace("$%#6#%$",htmlspecialchars($value->AuthorID),$s);
		$s=str_replace("$%#7#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#8#%$",htmlspecialchars($value->Url),$s);
		$s=str_replace("$%#9#%$",htmlspecialchars($value->TagsToNameString()),$s);
		$s=str_replace("$%#10#%$",htmlspecialchars($value->Alias),$s);
		$s=str_replace("$%#11#%$",htmlspecialchars($value->Intro),$s);
		$s=str_replace("$%#12#%$",htmlspecialchars($value->Intro),$s);
		$s=str_replace("$%#13#%$",htmlspecialchars($value->Intro),$s);
		$strAll .= $s;

	}

	$strXML=str_replace("$%#1#%$",$strAll,$strXML);
	logs($strXML);
	echo $strXML;


}


function zbp_getPostCategories($id){

	global $zbp;

	$strXML='<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
	$strSingle='<value><struct>
<member><name>categoryName</name><value><string>$%#1#%$</string></value></member>
<member><name>categoryId</name><value><string>$%#2#%$</string></value></member>
<member><name>isPrimary</name><value><dateTime.iso8601>$%#3#%$</dateTime.iso8601></value></member>
</struct></value>';


	$strAll='';

	$article= new Post;
	$article->LoadInfoByID($id);

	$array=array();
	$array[]=$article->Category;

	foreach ($array as $key => $value) {
		$s=$strSingle;
		$s=str_replace("$%#1#%$",htmlspecialchars($value->Name),$s);
		$s=str_replace("$%#2#%$",htmlspecialchars($value->ID),$s);
		$s=str_replace("$%#3#%$",htmlspecialchars(1),$s);

		$strAll .= $s;

	}

	$strXML=str_replace("$%#1#%$",$strAll,$strXML);
	logs($strXML);
	echo $strXML;

}






$zbp->Load();

$xmlstring = file_get_contents( 'php://input' );
logs($xmlstring);
$xml = simplexml_load_string($xmlstring);

Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','RespondError',PLUGIN_EXITSIGNAL_RETURN);

if($xml){
	$method=(string)$xml->methodName;

	switch ($method) {
		case 'blogger.getUsersBlogs':
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('admin')){
				zbp_getUsersBlogs();
			}else{
				$zbp->ShowError(6);
			}
			break;
		case 'wp.getCategories':
		case 'metaWeblog.getCategories':
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleEdt')){
				zbp_getCategories();
			}else{
				$zbp->ShowError(6);
			}
			break;
		case 'wp.getTags':
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleEdt')){
				zbp_getTags();
			}else{
				$zbp->ShowError(6);
			}
			break;
		case 'wp.getAuthors':
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleEdt')){
				zbp_getAuthors();
			}else{
				$zbp->ShowError(6);
			}
			break;
		case 'metaWeblog.getRecentPosts':
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleEdt')){
				zbp_getRecentPosts((integer)$xml->params->param[3]->value->int);
			}else{
				$zbp->ShowError(6);
			}
			break;
		case 'blogger.deletePost':
			$username=(string)$xml->params->param[2]->value->string;
			$password=(string)$xml->params->param[3]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleDel')){
				zbp_deletePost((integer)$xml->params->param[1]->value->string);
			}else{
				$zbp->ShowError(6);
			}
			break;
		case 'metaWeblog.getPost':
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleEdt')){
				zbp_getPost((integer)$xml->params->param[0]->value->string);
			}else{
				$zbp->ShowError(6);
			}
			break;

		case 'mt.getPostCategories':	
			$username=(string)$xml->params->param[1]->value->string;
			$password=(string)$xml->params->param[2]->value->string;
			if(!$zbp->Verify_Original($username,$password)){$zbp->ShowError(8);}
			if($zbp->CheckRights('ArticleEdt')){
				zbp_getPostCategories((integer)$xml->params->param[0]->value->string);
			}else{
				$zbp->ShowError(6);
			}
			break;
		default:
			logs($xmlstring);
			$zbp->ShowError(1);
			break;
	}
}


die();




?>