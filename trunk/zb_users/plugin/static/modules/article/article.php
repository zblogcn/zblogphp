<?php
/**
 * Z-BlogPHP Clinic Check BOM
 * @package article
 * @subpackage article.php
 */
class article extends clinic {
	public function get_queue() {
		global $zbp;
		$posts = $zbp->GetPostList();
		foreach ($posts as $key => $value) {
			$this->set_queue('static_post_build', $value->ID);
		}
	}

	public function static_post_build($postid){
		global $zbp;
		$post = $zbp->GetPostByID($postid);
		$str = null;
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
		$data = json_encode(array($post->ID, $save_dir));
		$this->set_queue('static_file_put_contents', $data);

	}

	public function static_file_put_contents($data){
		$data = json_decode($data,true);
		ob_start();
		ViewPost($data[0], null, false);
		$article_Content = ob_get_contents();
		ob_end_clean();
		file_put_contents($data[1], $article_Content);
		$this->output('success', '文章ID【'.$data[0].'】重建成功！');
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