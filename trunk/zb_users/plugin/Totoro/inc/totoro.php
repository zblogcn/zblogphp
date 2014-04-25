<?php

class Totoro_Class
{
	public $config_array = array();
	public $sv = 0;
	
	function __construct()
	{
		$this->config_array = include(TOTORO_INCPATH . 'totoro_config.php');
		$this->init_config();
	}
	
	
	function init_config()
	{
		global $zbp;
		$config_save = FALSE;
		foreach($this->config_array as $type_name => &$type_value)
		{
			foreach($type_value as $name => &$value)
			{
				$config_name = $type_name . '_' . $name;
				$config_value = $zbp->Config('Totoro')->$config_name;
				if(!isset($config_value))
				{
					$zbp->Config('Totoro')->$config_name = $value['DEFAULT'];
					$config_save = TRUE;
				}
				$value['VALUE'] = $zbp->Config('Totoro')->$config_name;
			}
		}
		
		if($config_save) $zbp->SaveConfig('Totoro');		
		return true;
	}
	
	
	function output_config($type, $name, $convert = TRUE)
	{
		global $zbp;
		$content = $this->config_array[$type][$name]['VALUE'];
		return $convert ? TransferHTML($content, '[html-format]') : $content;
	}
	
	
	
	function build_content(&$comment)
	{
		$content = '';
		//$content .= $comment->Name . ' ';
		//$content .= $comment->Email . ' ';
		$content .= $comment->Content . ' ';
		
		foreach($this->config_array['BUILD_CONFIG'] as $name => $value)
		{
			if ($value['VALUE'])
			{
				$low_name = strtolower($name);
				$file = TOTORO_INCPATH . 'build_' . $low_name . '.php';
				if (file_exists($file))
				{
					$func = include($file);
					$func($content);
				}
			}
		}
		
		return array(
			'author' => array(
				'id' => $comment->AuthorID,
				'name' => $comment->Name,
				'ip' => $comment->IP,
				'email' => $comment->Email,
				'url' => $comment->HomePage
			),
			'content' => $content
		);
		
	}
	
	function get_score(&$comment)
	{
		$build = $this->build_content($comment);
		
		foreach($this->config_array['SV_RULE'] as $name => $value)
		{
			$low_name = strtolower($name);
			$file = TOTORO_INCPATH . 'rule_' . $low_name . '.php';
			if (file_exists($file))
			{
				$func = include($file);
				$func($build['author'], $build['content'], $this->sv, $value['VALUE']);
			}
		}
		
		return $this->sv;
	}
	
	function edit_comment(&$comment)
	{
		
	}
}
