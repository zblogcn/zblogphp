<?php
/**
 * Z-BlogPHP Clinic Check BOM
 * @package category
 * @subpackage category.php
 */
class static_category extends Static_Class {

	public function get_queue() {
		global $zbp;
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
		//$this->set_queue('static_post_build_complete', count($posts));
	}

	public function static_index_build($data){
		global $zbp;

		$data = json_decode($data, true);

		if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
			$data[0] = iconv("utf-8", "gbk",$data[0]);
		}
		$url = explode('/', $data[0]);
		if(count($url) == 1){
			$save_dir = $zbp->path.$url[0];
		}else{
			$exists_url = $zbp->path;
			for ($i=0; $i < (count($url)-1); $i++) {
				$exists_url .= ($url[$i].'/');
				if (!file_exists($exists_url)) {
					@mkdir($exists_url);
				}
			}
			$save_dir = $exists_url.end($url);
		}
		$data[0] = $save_dir;
		$data = json_encode($data);
		$this->set_queue('static_file_put_contents', $data);
	}

	public function static_file_put_contents($data){
		global $zbp;
		$data = json_decode($data,true);
		$zbp->user->ID = 0;
		ob_start();
		ViewList($data[1], $data[2], $data[3], $data[4], $data[5], false);
		$content = ob_get_contents();
		ob_end_clean();
		file_put_contents($data[0], $content);
		$this->output('success', '分类【'.$data[0].'】重建成功！');
	}

	// public function static_post_build_complete($posts){
	// 	$this->output('success', '所有文章静态页重建完成，共生成'.$posts.'篇文章！');
	// }

}