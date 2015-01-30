<?php
/**
 * 审计类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Article 类库
 */
class Counter extends Base {

	/**
	* 构造函数
	*/
	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Counter'],$zbp->datainfo['Counter']);
	}


}
