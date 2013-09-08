<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class Rss2 extends DOMDocument {

	private $channel;

	public function __construct($title,$link,$description){
		parent::__construct('1.0', 'utf-8');
		$this->formatOutput = true;

		$root = $this->appendChild($this->createElement('rss'));
		$root->setAttribute('version','2.0');
		
		$root->setAttribute('xmlns:dc','http://purl.org/dc/elements/1.1/');		

		$channel = $root->appendChild($this->createElement('channel'));

		$channel->appendChild($this->createElement('title',str_replace('&nbsp','&#160',$title)));
		$channel->appendChild($this->createElement('link',$link));
		$channel->appendChild($this->createElement('description',str_replace('&nbsp','&#160',$description)));

		$this->channel = $channel;
	}

	public function addItem($title,$link,$description,$date){
		$item = $this->createElement('item');
		$item->appendChild($this->createElement('title',str_replace('&nbsp','&#160',$title)));
		$item->appendChild($this->createElement('link',$link));
		$cdata=$this->createCDATASection($description);
		$d=$this->createElement('description');
		$d->appendChild($cdata);
		$item->appendChild($d);
		$item->appendChild($this->createElement('pubDate',date(DATE_RSS,$date)));

		$this->channel->appendChild($item);
	}

}


?>