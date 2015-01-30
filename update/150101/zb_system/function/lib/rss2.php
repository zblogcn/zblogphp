<?php
/**
 * RSS2类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib 类库
 */
class Rss2 {

	private $channel;
	/**
	 * @var string $xml xml文档
	 */
	private $xml='<?xml version="1.0" encoding="utf-8"?>';

	/**
	 * 构造函数，初始化RSS文档开头部分
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 */
	public function __construct($title,$link,$description){

		$this->xml .= '<rss xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">';
		$this->xml .= '<channel>';

		$this->xml .= $this->createElement('title',$title);
		$this->xml .= $this->createElement('link',$link);
		$this->xml .= $this->createElement('description',$description);

		#$this->xml .= '</channel>';
	}

	/**
	 * 构造元素节点
	 * @param $name
	 * @param $value
	 * @return string
	 */
	public function createElement($name,$value){
		return '<' . $name . '>' . htmlspecialchars($value) . '</' . $name . '>';
	}

	/**
	 * 添加文章节点
	 * @param $title
	 * @param $link
	 * @param $description
	 * @param $date
	 */
	public function addItem($title,$link,$description,$date){
		if(substr($this->xml,-6)=='</rss>')return ;
		$this->xml .= '<item>';
		$this->xml .= $this->createElement('title',$title);
		$this->xml .= $this->createElement('link',$link);
		$this->xml .= $this->createElement('description',$description);
		$this->xml .= $this->createElement('pubDate',date(DATE_RSS,$date));
		$this->xml .= '</item>';
	}

	/**
	 * 返回xml格式的RSS文档
	 * @return string
	 */
	public function saveXML(){
		if(substr($this->xml,-6)!=='</rss>')$this->xml .= '</channel></rss>';
		return $this->xml;
	}

}