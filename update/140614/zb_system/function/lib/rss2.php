<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class Rss2 {

	private $channel;
	private $xml='';

	public function __construct($title,$link,$description){

		$this->xml .= '<rss xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">';
		$this->xml .= '<channel>';
		
		$this->xml .= $this->createElement('title',$title);
		$this->xml .= $this->createElement('link',$link);
		$this->xml .= $this->createElement('description',$description);

		$this->xml .= '</channel>';
	}
	
	public function createElement($name,$value){
		return '<' . $name . '>' . htmlspecialchars($value) . '</' . $name . '>';
	}

	public function addItem($title,$link,$description,$date){
		if(substr($this->xml,-6)=='</rss>')return ;
		$this->xml .= '<item>';
		$this->xml .= $this->createElement('title',$title);
		$this->xml .= $this->createElement('link',$link);
		$this->xml .= $this->createElement('description',$description);
		$this->xml .= $this->createElement('pubDate',date(DATE_RSS,$date));
		$this->xml .= '</item>';
	}
	
	public function saveXML(){
		if(substr($this->xml,-6)!=='</rss>')$this->xml .= '</rss>';
		return $this->xml;
	}

}