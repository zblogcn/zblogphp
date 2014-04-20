<?php

class Totoro_Class
{
	public $config_array = array();
	public $sv = 0;
	
	function __construct()
	{
		$this->config_array = include(TOTORO_PATH . '/inc/config.php');
		$this->init_config();
	}
	
	function init_config()
	{
		return true;
	}
	
	function output_config($name)
	{
		global $zbp;
		return TransferHTML($zbp->Config('Totoro')->$name, '[html-format]');
	}
	
	function build_content(&$comment)
	{
		$content = '';
		//$content .= $comment->Name . ' ';
		//$content .= $comment->Email . ' ';
		$content .= $comment->Content . ' ';
		
		foreach($this->config_array['BUILD_CONFIG'] as $name => $value)
		{
			$low_name = strtolower($name);
			if($value['VALUE'])
			{
				$func = include(TOTORO_PATH . '/inc/config_' . $low_name . '.php');
				$func($content);
			}
		}
		
		return array(
			'name' => $comment->Name,
			'ip' => $comment->IP,
			'email' => $comment->Email,
			'url' => $comment->HomePage,
			'content' => $content
		);
		
	}
	
	function get_score(&$comment)
	{
		$build = $this->build_content($comment);
		
		foreach($this->config_array['SV_RULE'] as $name => $value)
		{
			$low_name = strtolower($name);
			$func = include(TOTORO_PATH . '/inc/rule_' . $low_name . '.php');
			$func($build['name'], $build['ip'], $build['email'], $build['url'], $build['content'], $this->sv, $value['VALUE']);
		}
		
		return $this->sv;
	}
	
	function edit_comment(&$comment)
	{
		
	}
}
