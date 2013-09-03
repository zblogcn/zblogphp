<?php
	

require '../../../../zb_system/function/c_system_base.php';

$zbp->Load();

if(!$zbp->CheckRights('UploadPst'))die();


    $title = $_POST['pictitle'];

    //上传配置
    $config = array(
        "savePath" => $zbp->usersdir . 'upload/',
        "maxSize" => $zbp->option['ZC_UPLOAD_FILESIZE'],
        "allowFiles" => "gif|png|jpg|jpeg|bmp"
    );


foreach ($_FILES as $key => $value) {
	if($_FILES[$key]['error']==0){
		if (is_uploaded_file($_FILES[$key]['tmp_name'])) {


	$upload = new Upload;
	$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . GetFileExt($_FILES[$key]['name']);
	$upload->SourceName = $_FILES[$key]['name'];
	$upload->MimeType = $_FILES[$key]['type'];
	$upload->Size =$_FILES[$key]['size'];
	$upload->AuthorID = $zbp->user->ID;

	$upload->SaveFile($_FILES[$key]['tmp_name']);
	$upload->Save();
	
	$info=array();
	$info["url"]=$upload->Name;
	$info["title"]=$title;	
	$info["originalName"]=$upload->SourceName;
	$info["state"]='SUCCESS';
	
    /**
     * 向浏览器返回数据json数据
     * {
     *   'url'      :'a.jpg',   //保存后的文件路径
     *   'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
     *   'original' :'b.jpg',   //原始文件名
     *   'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
     * }
     */
    echo "{'url':'" . $info["url"] . "','title':'" .  $info["title"] . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";
		

		
		
		}
	}
}



