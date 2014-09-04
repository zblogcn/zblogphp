<?php
/**
 * Z-BlogPHP static Check BOM
 * @package article
 * @subpackage article.php
 */
class article extends Static_Class {
	public function get_queue() {
		global $zbp;
		$posts = $zbp->GetPostList();
		foreach ($posts as $key => $value) {
			$this->set_queue('static_post_build', serialize(array($value->ID, count($posts), end($posts)->ID)));
		}
	}

	public function static_post_build($param){
		global $zbp;
		$param = unserialize($param);
		$postid = $param[0];
		$post = $zbp->GetPostByID($postid);
		$post_url = $post->Url;
		if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
			$post_url = iconv("utf-8", "gbk",$post_url);
		}
		$url = str_replace($zbp->host, '', $post_url);
		$url = explode('/', $url);
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
		$data = json_encode(array($post->ID, $save_dir, $param[1],$param[2]));
		$this->output('success', '文章ID【'.$param[0].'】准备完成！');
		$this->set_queue('static_file_put_contents', $data);
	}

	public function static_file_put_contents($data){
		global $zbp;
		$data = json_decode($data,true);
		$zbp->user->ID = 0;
		ob_start();
		ViewPost($data[0], null, false);
		$article_Content = ob_get_contents();
		ob_end_clean();
		file_put_contents($data[1], $article_Content);
		$this->output('success', '文章ID【'.$data[0].'】重建成功！');
		if ($data[0] == $data[3]) {
			$this->static_post_build_complete($data[2]);
		}
	}

	public function static_post_build_complete($posts){
		$this->output('success', '所有文章静态页重建完成，共生成'.$posts.'篇文章！');
	}

}



/*		for($i = 0; $i <= 10000; $i += 5)
$this->set_queue('build_article', serialize(array($i, $i + 4)));
public function build_article($param) {
$config = unserialize($param);
for($i = $config[0]; $i <= $config[1]; $i++ ) {
$this->output('success', $i . '生成成功');
}
}

*/