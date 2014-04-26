<?php
/*  TODO:
 *  原有配置不考虑进行转移或升级
 *  1. 【DONE】初始化配置并写入系统
 	   相关页面：include.php totoro.php
	   相关函数：InstallPlugin_Totoro()  Totoro_Class::init_config()
 *  2. 【DONE】保存配置页面
       相关页面：save_setting.php
 *  3. 与系统挂钩进行审核
       相关页面：include.php
	   相关函数：Totoro_PostComment_Core
 *  4. 黑词测试页面和正则测试界面
       详见ASP Totoro
 */
RegisterPlugin("Totoro","ActivePlugin_Totoro");
define('TOTORO_PATH', dirname(__FILE__));
define('TOTORO_INCPATH', TOTORO_PATH . '/inc/');


function Totoro_init()
{
	require(TOTORO_PATH . '/inc/totoro.php');
	global $Totoro;
	$Totoro = new Totoro_Class;
}

function ActivePlugin_Totoro()
{
	Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu','Totoro_Admin_CommentMng_SubMenu');
	//Add_Filter_Plugin('Filter_Plugin_PostComment_Core','Totoro_PostComment_Core');
}


function InstallPlugin_Totoro()
{
}


function Totoro_Admin_CommentMng_SubMenu(){
	global $zbp;
	echo '<a href="'. $zbp->host .'zb_users/plugin/Totoro/main.php"><span class="m-right">Totoro设置</span></a>';

}


function Totoro_Core_BlackWord(&$cmt){
	global $zbp;
	
	$BlackWord_List=trim($zbp->Config('Totoro')->BlackWord_List);
	$BlackWord_Audit=(int)$zbp->Config('Totoro')->Op_BlackWord_Audit;
	$BlackWord_Throw=(int)$zbp->Config('Totoro')->Op_BlackWord_Throw;

	if(!$BlackWord_List)return null;

	$array=array();
	preg_match_all('/'.$BlackWord_List.'/ui',$cmt->Content,$array);
	
	$array=array_unique($array);
	$i=count($array[0]);

	if($i>=$BlackWord_Audit) $cmt->IsChecking=true;
	if($i>=$BlackWord_Throw) $cmt->IsThrow=true;

}

function Totoro_Core_NoneChinese(&$cmt){
	global $zbp;

	$Chinese_None=(bool)$zbp->Config('Totoro')->Op_Chinese_None;

	if($Chinese_None){
		if(preg_match('/[\x{4e00}-\x{9fa5}]+/u',$cmt->Name . $cmt->Content)==0){
			$cmt->IsChecking=true;
		}
	}

}


function Totoro_PostComment_Core(&$cmt){
	global $zbp;

	Totoro_Core_NoneChinese($cmt);
	Totoro_Core_BlackWord($cmt);	

}

