<?php
/**
 * PHP 富文本XSS过滤类
 *
 * @package XssHtml
 * @version 1.0.0
 * @link http://phith0n.github.io/XssHtml
 * @since 20140621
 * @copyright (c) Phithon All Rights Reserved
 *
 */

#
# Written by Phithon <root@leavesongs.com> in 2014 and placed in
# the public domain.
#
# phithon <root@leavesongs.com> 编写于20140621
# From: XDSEC <www.xdsec.org> & 离别歌 <www.leavesongs.com>
# Usage:
# <?php
# require('xsshtml.class.php');
# $html = '<html code>';
# $xss = new XssHtml($html);
# $html = $xss->getHtml();
# ?\>
#
# 需求：
# PHP Version > 5.0
# 浏览器版本：IE7+ 或其他浏览器，无法防御IE6及以下版本浏览器中的XSS
# 更多使用选项见 http://phith0n.github.io/XssHtml

// zsx一手贱就修改了一下~

class XssHtml {
	private $m_dom;
	private $m_xss;
	private $m_ok;
	private $m_AllowAttr = array('title', 'src', 'href', 'style', 'width', 'height', 'alt', 'target');
	private $m_AllowTag = array('a', 'img', 'br', 'strong', 'code', 'pre', 'p', 'div', 'em', 'span');
	private $m_host = '';
	private $m_defaultimg = '';

	/**
	 * 构造函数
	 *
	 * @param string $html 待过滤的文本
	 * @param string $charset 文本编码，默认utf-8
	 * @param array $AllowTag 允许的标签，如果不清楚请保持默认，默认已涵盖大部分功能，不要增加危险标签
	 */
	public function __construct($html, $charset = 'utf-8', $AllowTag = array()) {
		$this->m_AllowTag = empty($AllowTag) ? $this->m_AllowTag : $AllowTag;
		$this->m_xss = strip_tags($html, '<' . implode('><', $this->m_AllowTag) . '>');
		if (empty($this->m_xss)) {
			$this->m_ok = FALSE;
			return;
		}
		$this->m_xss = "<meta http-equiv=\"Content-Type\" content=\"text/html;charset={$charset}\">" . $this->m_xss;
		$this->m_dom = new DOMDocument();
		$this->m_dom->strictErrorChecking = FALSE;
		$this->m_ok = @$this->m_dom->loadHTML($this->m_xss);
	}

	/**
	 * 获得过滤后的内容
	 */
	public function getHtml() {
		if (!$this->m_ok) {
			return '';
		}
		$nodeList = $this->m_dom->getElementsByTagName('*');
		for ($i = 0; $i < $nodeList->length; $i++) {
			$node = $nodeList->item($i);
			if (in_array($node->nodeName, $this->m_AllowTag)) {
				if (method_exists($this, "__node_{$node->nodeName}")) {
					call_user_func(array($this, "__node_{$node->nodeName}"), $node);
				} else {
					call_user_func(array($this, '__node_default'), $node);
				}
			}
		}
		return strip_tags($this->m_dom->saveHTML(), '<' . implode('><', $this->m_AllowTag) . '>');
	}

	/**
	 * 设置域名不存在返回的地址
	 */
	public function setHost($url) {
		$this->m_host = $url;
	}

	/**
	 * 设置图片为非本地图片时自动替换的地址
	 */
	public function setDefaultImg($url) {
		$this->m_defaultimg = $url;
	}

	private function __true_url($url) {
		if (preg_match('#^(https?|ftp|ed2k)://.+#is', $url)) {
			return $url;
		} else {
			return $this->m_host . $url;
		}
	}

	private function __true_src($url) {
		$url = $this->__true_url($url);
		if (stripos('a' . $url, $this->m_host)) {
			return $url;
		} else {
			return $this->m_defaultimg;
		}
	}

	private function __get_style($node) {
		if ($node->attributes->getNamedItem('style')) {
			$style = $node->attributes->getNamedItem('style')->nodeValue;
			$style = str_replace('\\', ' ', $style);
			$style = str_replace(array('&#', '/*', '*/'), ' ', $style);
			$style = preg_replace('#e.*x.*p.*r.*e.*s.*s.*i.*o.*n#Uis', ' ', $style);
			$style = str_replace('-', '_', $style);
			$style = preg_replace('/\b((?!color|decoration)[a-zA-Z0-9\_])+:(.+?)(;|$)/si', '', $style);
			$style = str_replace('_', '-', $style);
			$style = trim($style);
			return $style;
		} else {
			return '';
		}
	}

	private function __get_link($node, $att) {
		$link = $node->attributes->getNamedItem($att);
		if ($link) {
			return $this->__true_url($link->nodeValue);
		} else {
			return '';
		}
	}

	private function __get_src($node, $att) {
		$link = $node->attributes->getNamedItem($att);
		if ($link) {
			return $this->__true_src($link->nodeValue);
		} else {
			return '';
		}
	}

	private function __setAttr($dom, $attr, $val) {
		if (!empty($val)) {
			$dom->setAttribute($attr, $val);
		} else {
			$dom->removeAttribute($attr);
		}
	}

	private function __set_default_attr($node, $attr, $default = '') {
		$o = $node->attributes->getNamedItem($attr);
		if ($o) {
			$this->__setAttr($node, $attr, $o->nodeValue);
		} else {
			$this->__setAttr($node, $attr, $default);
		}
	}

	private function __common_attr($node) {
		$list = array();
		foreach ($node->attributes as $attr) {
			if (!in_array($attr->nodeName,
				$this->m_AllowAttr)) {
				$list[] = $attr->nodeName;
			}
		}
		foreach ($list as $attr) {
			$node->removeAttribute($attr);
		}

		$style = $this->__get_style($node);
		$this->__setAttr($node, 'style', $style);
		$this->__set_default_attr($node, 'title');
		$this->__set_default_attr($node, 'id');
		$this->__set_default_attr($node, 'class');
	}

	private function __node_img($node) {
		$this->__common_attr($node);

		$src = $this->__get_src($node, 'src');
		$this->__setAttr($node, 'src', $src);
		$this->__set_default_attr($node, 'width');
		$this->__set_default_attr($node, 'height');
		$this->__set_default_attr($node, 'alt');
		$this->__set_default_attr($node, 'align');

	}

	private function __node_a($node) {
		$this->__common_attr($node);
		$href = $this->__get_link($node, 'href');
		$this->__setAttr($node, 'href', $href);
		$this->__set_default_attr($node, 'target', '_blank');
		$this->__set_default_attr($node, 'rel', 'nofollow');
	}

	private function __node_default($node) {
		$this->__common_attr($node);
	}
}

?>