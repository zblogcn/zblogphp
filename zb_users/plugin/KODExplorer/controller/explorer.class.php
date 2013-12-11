<?php 
class explorer extends Controller{
    /**
     * 构造函数
     */
    function __construct()    {
        parent::__construct();
        $this->tpl = TEMPLATE.'explorer/';
        load_class('history');
    }

    function index(){
        if($this->in['path']!=''){
            $dir = $_GET['path'];
        }else if($_SESSION['this_path']!=''){
            $dir = $_SESSION['this_path'];
        }else{
            $dir = HOME;//首次进入系统,不带参数
        }
        $this->assign('dir',$dir);
        $this->display('index.php');
    }

    function pathInfo(){
        $path=urldecode($this->in['path']);
        $type=$this->in['type'];
        $path_this_name=get_path_this($path);
        $path_father_name=get_path_father($path);
        if ($type=="folder"){
            $path_info = path_info($path,"Y年m月d日 H:i:s");
            include($this->tpl.'fileinfo/pathinfo.php');
        }elseif($type=="file"){
            $file_info = file_info($path,"Y年m月d日 H:i:s");
            include($this->tpl.'fileinfo/fileinfo.php');
        }
    }
    function pathInfoMuti(){
        $info_list = json_decode($this->in['info_list'],true);
        foreach ($info_list as $val) {          
            $val['file'] = urldecode($info_list[$i]['file']);
        }
        $pathinfo = path_info_muti($info_list);
        include($this->tpl.'fileinfo/pathinfo_muti.php');
    }      
    function pathRname(){
        $app_path=iconv_system(urldecode($this->in['path']));
        if (!is_writable($app_path)) {
            echo "没有权限";return;
        }
        $rname_to=iconv_system(urldecode($this->in['rname_to']));
        if (file_exists($rname_to)) {
            echo "出错啦，该名称已存在！";return;
        }

        rename($app_path,$rname_to);
        echo "重命名成功！";
    }
    function pathList(){
        $session=$_SESSION['history'];
		$path=$this->in['path'];
		if (!dir_readable($path)) {
			echo '-1';return;
		}

        if (is_array($session)){
            $hi=new history($session);
            if ($path==""){
                $path=$hi->getFirst();
            }           
            else {
                $hi->add($path); 
                $_SESSION['history']=$hi->getHistory();
            }           
        }
        else {
            $hi=new history(array(),20);
            if ($path=="")  $path=HOME;
            $hi->add($path);
            $_SESSION['history']=$hi->getHistory();
        }
        $_SESSION['this_path']=$path;
        $folderlist=path_list(urldecode($path));
        $folderlist['history_status']= array('back'=>$hi->isback(),'next'=>$hi->isnext());
        echo json_encode($folderlist);
    }
    function desktop(){
        $folderlist=path_list(DESKTOP);
        $folderlist['desktop'] = array(
            array('name'=>'我的电脑','type'=>'computer'),           
            array('name'=>'回收站','type'=>'recycle'),
            array('name'=>'设置','type'=>'setting'),          
            array('name'=>'浏览器','type'=>'internet')     
        );
        echo json_encode($folderlist);
    }
    function folderListEditor(){
        if ($this->in['this_path'] !='') {
            $path=urldecode($this->in['this_path']);
        }else if($this->in['father'] !=''){
            $path=urldecode($this->in['father'].$this->in['name']).'/';
        }		
		if (!dir_readable($path)) {
			echo '-1';return;
		}

        $folderlist=tree_list($path,true);
        if ($folderlist == NULL) {
            echo "[]";return;
        }
        $listFolder=array();
        $listFile=array();      
        foreach($folderlist as $val){
            if ($val['fileType'] != '') {
                array_push($listFile,array(
                    'type'=>'file',
                    'name'=>$val['name'],
                    'father'=>$path,
                    'iconSkin'=>$val['fileType'],
                    'isParent'=>false
                    )
                );                  
            }else{
                array_push($listFolder,array(
                    'type'=>'folder',
                    'name'=>$val['name'],
                    'father'=>$path,
                    'isParent'=>$val['hasChildren']
                    )
                );          
            }
        }
        
        function sort_by_key($a, $b){
            if ($a['name'] == $b['name']) return 0;
            return ($a['name'] > $b['name']) ? 1 : -1;
        }
        usort($listFolder, "sort_by_key");
        usort($listFile, "sort_by_key");
        $list = array_merge($listFolder,$listFile);
		
		//根目录
		if ($this->in['root'] == '1'){	
			$list = array(
				array('name'	=> "根目录",
					'iconSkin'	=> "lib",
                    'this_path' => HOME,
                    'type'      => 'root',
					'open'		=> true,
					'children'	=> $list,
					'drop'		=> false,
                    'isParent'  => true,
					'drag'		=> false
				)
			);
		}
        echo json_encode($list);
    }
    function folderList(){        
        if ($this->in['father']=="" && $this->in['this_path']==''){
            $fav_array = unserialize(file_get_contents($this->config['fav_path']));
            $count = count($fav_array);
            for($i=0;$i<$count;$i++){
                $fav_array[$i] = array(             
                    'name'      => $fav_array[$i]['name'],                    
                    'this_path' => $fav_array[$i]['path'],
                    'isParent'  => true,
                    'drop'      => false,
                    'drag'      =>false
                );
            }
            $lib_array =  array(
               // array('name'=>"桌面",'iconSkin'=>"doc",'this_path'=> USER.'desktop/','isParent'=>true,'drop'=>false,'drag'=>false),
               // array('name'=>"我的文档",'iconSkin'=>"doc",'this_path'=> USER.'doc/','isParent'=>true,'drop'=>false,'drag'=>false),
               // array('name'=>"我的照片",'iconSkin'=>"pic",'this_path'=> USER.'image/','isParent'=>true,'drop'=>false,'drag'=>false),           
               // array('name'=>"我的音乐",'iconSkin'=>"music",'this_path'=> USER.'music/','isParent'=>true,'drop'=>false,'drag'=>false),
               // array('name'=>"我的视频",'iconSkin'=>"movie",'this_path'=> USER.'movie/','isParent'=>true,'drop'=>false,'drag'=>false), 
               // array('name'=>"我的下载",'iconSkin'=>"download",'this_path'=> USER.'download/','isParent'=>true,'drop'=>false,'drag'=>false),   
            );
            $tree_root = array(
               // array('name'=>"库",'iconSkin'=>"lib",'open'=>true,'children'=>$lib_array,'drop'=>false,'drag'=>false),
                //array('name'=>'根目录','open'=>false,'this_path'=> HOME,'isParent'=>true,'drop'=>false,'drag'=>false)
                
            );
            echo json_encode($tree_root);
            return;
        }else{
            if ($this->in['this_path'] !='') {
                $path=urldecode($this->in['this_path']).'/';
            }else if($this->in['father'] !=''){
                $path=urldecode($this->in['father'].$this->in['name']).'/';
            }           
        }

		if (!dir_readable($path)) {
			echo "[]";return;
		}
        $folderlist=tree_list($path,false);
        if ($folderlist == NULL) {
            echo "[]";return;
        }
        $list=array();
        foreach($folderlist as $val){
            array_push($list,array(
                'name'=>$val['name'],
                'father'=>$path,
                'isParent'=>$val['hasChildren']
                )
            );
        }
        echo json_encode($list);
    }

    function historyBack(){
        $session=$_SESSION['history'];
        if (is_array($session)){
            $hi=new history($session);
            $path=$hi->goback();            
            $_SESSION['history']=$hi->getHistory();
            $folderlist=path_list($path);
            $_SESSION['this_path']=$path;
            echo json_encode(array(
                'history_status'=>array('back'=>$hi->isback(),'next'=>$hi->isnext()),
                'thispath'=>$path,
                'list'=>$folderlist
            ));
        }
    }
    function historyNext(){
        $session=$_SESSION['history'];
        if (is_array($session)){
            $hi=new history($session);
            $path=$hi->gonext();            
            $_SESSION['history']=$hi->getHistory();
            $folderlist=path_list($path);
            $_SESSION['this_path']=$path;
            echo json_encode(array(
                'history_status'=>array('back'=>$hi->isback(),'next'=>$hi->isnext()),
                'thispath'=>$path,
                'list'=>$folderlist
            ));
        }
    }
    function history(){
        if (!is_array($_SESSION['history'])){
            echo "no history";
        }else {
            pr($_SESSION['history']);
        }
    }
    function deletePath(){
        $delete_list = json_decode($this->in['delete_list'],true);
        $success = 0;
        $error   = 0;
        foreach ($delete_list as $val) {
            $path_full = iconv_system(urldecode($val['file']));
            if ($val['type'] == 'folder') {
                if(del_dir($path_full)) $success ++;
                else $error++;
            }else{
                if(del_file($path_full)) $success++;
                else $error++;
            }
        }
        if (count($delete_list) == 1) {
            if ($success == 1) echo "删除成功!";
            else echo "没有权限，删除失败!";
        }else{
            echo '删除操作完成，'.$success.'个成功，'.$error.'个失败';
        }       
    }
    function mkfile(){
        $newfile=iconv_system(urldecode($this->in['path']));
        if(touch($newfile)){
            echo "新建成功！";
        }else{
            echo "新建失败,请检查目录权限！";
        }
    }
    function mkdir(){
        $newfolder=iconv_system(urldecode($this->in['path']));
        if(mkdir($newfolder,0777)){
            echo "新建成功！";
        }else{
            echo "新建失败,请检查目录权限！";
        }
    }
    function pathCopy(){
        $copy_list = json_decode($this->in['copy_list'],true);
        $list_num = count($copy_list);
        for ($i=0; $i < $list_num; $i++) { 
            $copy_list[$i]['file'] =urldecode($copy_list[$i]['file']);
        }
        $_SESSION['path_copy']= json_encode($copy_list);            
        $_SESSION['path_copy_type']='copy';     
        echo "【复制】—— 覆盖剪贴板成功!";
    }
    function pathCute(){
        $cute_list = json_decode($this->in['cute_list'],true);
        $list_num = count($cute_list);
        for ($i=0; $i < $list_num; $i++) { 
            $cute_list[$i]['file'] = urldecode($cute_list[$i]['file']);
        }
        $_SESSION['path_copy']= json_encode($cute_list);            
        $_SESSION['path_copy_type']='cute';     
        echo "【剪切】—— 覆盖剪贴板成功!";
    }
    function pathCuteDrag(){
        $clipboard = json_decode($this->in['cute_list'],true);
        $path_past=iconv_system(urldecode($this->in['path']));
        if (!is_writable($path_past)) {
            echo "没有权限";return;          
        }
        foreach ($clipboard as $val) {
            $path_copy = iconv_system($val['file']);
            $filename  = get_path_this($path_copy);
            if ($clipboard[$i]['type'] == 'folder') {                   
                rename($path_copy,$path_past.$filename.'/');
                
            }else{
                rename($path_copy,$path_past.$filename);
                
            }
        }
        echo '移动成功！';
    }      
    function pathCopySee(){
        $clipboard = json_decode($_SESSION['path_copy'],true);
        if (count($clipboard) == 0){
            echo '<div style="padding:20px;">空!</div>';
        }else{          
            $msg='<div style="height:200px;overflow:auto;padding:10px;width:400px"><b>剪切板状态:'.($_SESSION['path_copy_type']=='cute'?'剪切':'复制').'</b><br/>';
            $len = 40;
            foreach ($clipboard as $val) {
                $path=(strlen($val['file'])<$len)?$val['file']:'...'.substr($val['file'],-$len);
                $msg.= '<br/>'.$val['type'].' :  '.$path;
            }
            echo $msg."</div>";
        }
    }
    function pathPast(){
        $clipboard = json_decode($_SESSION['path_copy'],true);
		if (count($clipboard) == 0){
            echo '{"msg":"剪贴板为空！","select":""}';return;
        }
        $copy_type = $_SESSION['path_copy_type'];       
        $path_past=iconv_system(urldecode($this->in['path']));
        if (!is_writable($path_past)) {
            $echojson['msg']="没有写权限!";
            echo json_encode($echojson);return;
        }

        $echojson=array('select'=>array(),'msg'=>'');
        $list_num = count($clipboard);
        if ($list_num == 0) {
            $echojson['msg']="剪贴板为空!";
            echo json_encode($echojson);
        }
        for ($i=0; $i < $list_num; $i++) {
            $path_copy = $clipboard[$i]['file'];            
            $path_copy = iconv_system($path_copy);
            $filename  = get_path_this($path_copy);
            $filename_out  = iconv_system($filename);

            if (!file_exists($path_copy) && !is_dir($path_copy)){
                $echojson['msg'] .=$path_copy."<li>{$filename_out}来源不存在!</li>";
                continue;
            }
            if ($clipboard[$i]['type'] == 'folder'){
                if ($path_copy == substr($path_past,0,strlen($path_copy))){
                    $echojson['msg'] .="<li>{$filename_out}目标文件夹是源文件夹的子文件夹!</li>";
                    continue;
                }
            }       
            if ($copy_type == 'copy') {
                if ($clipboard[$i]['type'] == 'folder') {
                    copy_dir($path_copy,$path_past.$filename);
                }else{
                    copy($path_copy,$path_past.$filename);
                }
                
            }else{
                if ($cute_list[$i]['type'] == 'folder') {
                    rename($path_copy,$path_past.$filename.'/');
                }else{
                    rename($path_copy,$path_past.$filename);
                }                
            }            
            $echojson['select'][] = array('type'=>$clipboard[$i]['type'],'name'=>$filename);
        }
        if ($copy_type == 'copy') {
            $echojson['msg']='<b>粘贴操作完成</b>'.$echojson['msg'];
        }else{
            $_SESSION['path_copy'] = json_encode(array());
            $_SESSION['path_copy_type'] = '';
            $echojson['msg']='<b>粘贴操作完成</b>(源文件被删除,剪贴板清空)'.$echojson['msg'];
        }
        echo json_encode($echojson);
    }

	function fileDownload(){
        $path=iconv_system(urldecode($this->in['path']));
        file_download($path);
    }

	function fileOpen(){
        $path=HOST.iconv_system(urldecode($this->in['path']));
        $filename=get_path_this(urldecode($this->in['path']));
        $type=$this->in['type'];

        if ($type == 'oexe') {
            $filePath = WEB_ROOT.iconv_system(urldecode($this->in['path']));
            $exe_content = file_get_contents($filePath);
            $exe_config  = json_decode($exe_content,1);
            echo json_encode(
                array('title'   => $filename,
                      'content' => '<iframe width="100%" height="100%" frameborder=0  src="'.$exe_config['url'].'"/>',
                      'width'   => $exe_config['width'],
                      'height'  => $exe_config['height'],
                      'padding' => -10
                )
            );
        }else{
            echo json_encode(
                array('title'   => $filename,
                      'content' => '<iframe width="100%" height="100%" frameborder=0  src="'.HOST.urldecode($this->in['path']).'"/>',
                      'width'   => '70%',
                      'height'  => '70%',
                      'padding' => 0
                )
            );
        }
    }

    function zip(){
        load_class('zip');
        $zip_list = json_decode($this->in['zip_list'],true);
        $list_num = count($zip_list);
        for ($i=0; $i < $list_num; $i++) { 
            $zip_list[$i]['file'] = iconv_system(urldecode($zip_list[$i]['file']));
        }
        $basic_path =get_path_father($zip_list[0]['file']);     
        if ($list_num == 1) {
            $path_this_name=get_path_this($zip_list[0]['file']);
            $zipname = $basic_path.$path_this_name.'.zip';
            
        }else{
            $zipname = $basic_path.'temp_'.substr(md5(time()),5,3).'.zip';
        }
        $len = 25;
        $zipname_app = iconv_system($zipname);
        $zipname_app = strlen($zipname_app)>$len?'...'.substr($zipname_app, -$len):$zipname_app;
        $z = new zip($zipname, $basic_path);
        if (!$z -> fp){
            echo ("{$zipname}不能写入,检查路径或权限");
        }else {
            set_time_limit(0);
            for ($i=0; $i < $list_num; $i++) {
                $z -> addFileList($zip_list[$i]['file']);
            }
            
            $z -> zipAll() or die('没有选择的文件或目录.');
            echo "压缩完成,共 $z->file_count 个文件.<br/>";
            echo "压缩为：{$zipname_app} <br/>大小：{$z->sizeFormat(filesize($zipname))}";
        }
    }
    function unzip(){
        load_class('zip');
        $path=iconv_system(urldecode($this->in['path']));
        $path_this_name=str_replace('.zip','',get_path_this($path));
        $path_father_name=get_path_father($path);
        $unzip_to = $path_father_name.$path_this_name;
        set_time_limit(0);
        $z = new unZip;
        if ($z->Extract($path,$unzip_to) ==-1){
            echo("<br>文件 $zip_file 错误.<br>");
        }else {
            echo '解压完成！';
            clearstatcache();
        }
    }
    function image(){
        load_class('imageThumb');
        $image= iconv_system(urldecode($this->in['path']));
        $image_md5  = md5($image);
        $image_thum = $this->config['pic_thumb'].$image_md5.'.png';

        if (!is_dir($this->config['pic_thumb'])){
            mkdir($this->config['pic_thumb'],0777);
        }

        if (!file_exists($image_thum)){//如果拼装成的url不存在则没有生成过
            if ($_SESSION['this_path']==$this->config['pic_thumb']){//当前目录则不生成缩略图
                $image_thum=$this->in['path'];
            }
            else {
                $cm=new CreatMiniature();
                $cm->SetVar($image,'file');
                //$cm->Prorate($image_thum,72,64);//生成等比例缩略图
                $cm->BackFill($image_thum,72,64,true);//等比例缩略图，空白处填填充透明色
            }
        }
        if (!file_exists($image_thum)){//缩略图生成失败则用默认图标
            $image_thum='./static/style/skin/'.$this->config['theme'].'images/file/jpg.png';
        }
        //输出
        Header('Content-type:image/png');
        readfile($image_thum);        
    }
}