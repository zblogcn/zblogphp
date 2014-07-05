<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";

/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}

/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */


return json_encode($up->getFileInfo());

function exit_output()
{
	global $result;
	echo json_encode($result);
	exit();
}



foreach ($_FILES as $key => $value) {
	if($_FILES[$key]['error']==0){
		if (is_uploaded_file($_FILES[$key]['tmp_name'])) {

			$upload = new Upload;
			$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . GetFileExt($_FILES[$key]['name']);
			$upload->SourceName = $_FILES[$key]['name'];
			$upload->MimeType = $_FILES[$key]['type'];
			$upload->Size =$_FILES[$key]['size'];
			$upload->AuthorID = $zbp->user->ID;
		
			if(!$upload->CheckExtName($config['allowFiles']))
			{
				$result['state'] = $lang['error'][26];
				exit_output();
			}
			
			if(!$upload->CheckSize())
			{
				$result['state'] = $lang['error'][27];
				exit_output();
			}
		
			$upload->SaveFile($_FILES[$key]['tmp_name']);
			$upload->Save();
			
			$result["url"] = $upload->Url;
			$result["title"] = $title;
			$result["original"] = $upload->SourceName;
			$result["state"] = 'SUCCESS';
			
			
			exit_output();	
		

		
		
		}
	}
}

