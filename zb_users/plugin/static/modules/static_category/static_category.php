<?php
/**
 * Z-BlogPHP Clinic Check BOM
 * @package category
 * @subpackage category.php
 */
class static_category extends Static_Class {

	public function get_queue() {
		global $zbp;

		//首页
		$sql = $zbp->db->sql->Select($zbp->table['Post'],'COUNT(*) as allpost_num',array(array('=', 'log_Type', '0'),array('=', 'log_IsTop', '0')),'','','');
		$post_count = $zbp->db->Query($sql);
		$page_num = ceil ($post_count[0]['allpost_num']/$zbp->option['ZC_DISPLAY_COUNT'] );
		for ($i=1; $i <= $page_num; $i++) {
			$data = array(null,null,null,null,null);
			if( $i >1 ) {
				$save_path = str_replace('{%host%}', '', $zbp->option['ZC_INDEX_REGEX']);
				$save_path = str_replace('{%page%}', $i, $save_path);
				array_unshift($data, $save_path);
				$data[1] = $i;
			}else{
				array_unshift($data, 'index.html');
			}
			$this->set_queue('static_index_build', json_encode($data));
		}

		//分类
		$cate_list = $zbp->GetCategoryList();
		foreach ($cate_list as $key => $cate) {

			$page_num = ceil ( $cate->Count / $zbp->option['ZC_DISPLAY_COUNT'] );
			for ($i=1; $i <= $page_num; $i++) {
				$data = array(null,null,null,null,null);
				if( $i >1 ) {
					$alisa = ($cate->Alias == '' ? $cate->Name : $cate->Alias);
					$save_path = str_replace('{%host%}', '', $zbp->option['ZC_CATEGORY_REGEX']);
					$save_path = str_replace('{%id%}', $cate->ID, $save_path);
					$save_path = str_replace('{%alias%}', $alisa, $save_path);
					$save_path = str_replace('{%page%}', $i, $save_path);
					$save_path = urlencode($save_path);
					array_unshift($data, $save_path);
					$data[1] = $i;
				}else{
					array_unshift($data, str_replace($zbp->host, '', $cate->Url));
				}
				$data[2] = $cate->ID;
				$this->set_queue('static_index_build', json_encode($data));
			}
		}

		//用户
		$mem_list = $zbp->GetMemberList();
		foreach ($mem_list as $key => $mem) {
			$page_num = ceil ( $mem->Articles / $zbp->option['ZC_DISPLAY_COUNT'] );
			for ($i=1; $i <= $page_num; $i++) {
				$data = array(null,null,null,null,null);
				if( $i >1 ) {
					$save_path = str_replace('{%host%}', '', $zbp->option['ZC_AUTHOR_REGEX']);
					$save_path = str_replace('{%id%}', $mem->ID, $save_path);
					$save_path = str_replace('{%alias%}', $mem->Alias, $save_path);
					$save_path = str_replace('{%page%}', $i, $save_path);
					array_unshift($data, $save_path);
					$data[1] = $i;
				}else{
					array_unshift($data, str_replace($zbp->host, '', $mem->Url));
				}
				$data[3] = $mem->ID;
				$this->set_queue('static_index_build', json_encode($data));
			}
		}
	}

	public function static_index_build($data){
		global $zbp;
		$data = json_decode($data, true);
		if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
			$data[0] = iconv("utf-8", "gbk",$data[0]);
		}
		$data[0] = $data[6] = urldecode($data[0]);
		$save_dir = $zbp->path . $data[0];
		$url = explode('/', $data[0]);
		if(count($url) > 1){
			$exists_url = $zbp->path;
			for ($i=0; $i < (count($url)-1); $i++) {
				$exists_url .= ($url[$i].'/');
				if (!file_exists($exists_url)) {
					@mkdir($exists_url);
				}
			}
		}
		$data[0] = $save_dir;
		$data = json_encode($data);
		$this->set_queue('static_file_put_contents', $data);
	}

	public function static_file_put_contents($data){
		global $zbp;
		//Logs($data);
		$data = json_decode($data,true);
		ob_start();
		// ViewList($page, $cate, $auth, $date, $tags, $isrewrite = false)
		ViewList($data[1], $data[2], $data[3], $data[4], $data[5], false);
		$content = ob_get_contents();
		ob_end_clean();
		if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
			$save_url = iconv("utf-8", "gbk",$data[0]);
		}else{
			$save_url = $data[1];
		}
		file_put_contents($save_url, $content);
		$this->output('success', '【'.$data[6].'】重建成功！');
	}

}