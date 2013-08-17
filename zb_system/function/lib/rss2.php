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

		$channel = $root->appendChild($this->createElement('channel'));

		$channel->appendChild($this->createElement('title',$title));
		$channel->appendChild($this->createElement('link',$link));
		$channel->appendChild($this->createElement('description',$description));

		$this->channel = $channel;
	}

	public function addItem($title,$link,$description,$date){
		$item = $this->createElement('item');
		$item->appendChild($this->createElement('title',$title));
		$item->appendChild($this->createElement('link',$link));
		$item->appendChild($this->createElement('description',$description));
		$item->appendChild($this->createElement('pubDate',date(DATE_RSS,$date)));

		$this->channel->appendChild($item);
	}

}


?>