<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

/**
* Pagebar
*/
class Pagebar
{
#内容总数
public $Count = 0;

#Pagebar长度数量
public $PageBarCount=0;

#每页数量
public $PageCount  = 0;

#总页数
public $PageAll  = 0;

#当前页
public $PageNow  = 0;

public $PageFirst  = 0;
public $PageLast  = 0;
public $PagePrevious  = 0;
public $PageNext  = 0;

public function make(){

	$this->PageAll = ceil($this->Count / $this->PageCount);
	$this->PageFirst = 1;
	$this->PageLast = $this->PageAll;

	$this->PagePrevious=$this->PageNow-1;
	if($this->PagePrevious<1){$this->PagePrevious=1;}

	$this->PageNext=$this->PageNow+1;
	if($this->PageNext>$this->PageAll){$this->PageNext=$this->PageAll;}

	$this->buttons['‹‹']=$this->PageFirst;
	if($this->PageNow <> $this->PageFirst){
		$this->buttons['‹'] = $this->PagePrevious;		
	}

	$j=$this->PageNow;
	if($j+$this->PageBarCount > $this->PageAll){
		$j=$this->PageAll-$this->PageBarCount+1;
	}
	if($j<1){$j=1;}

	for ($i=$j; $i < $j+$this->PageBarCount; $i++) { 
		if($i > $this->PageAll){break;}
		$this->buttons[$i]=$i;
	}
	if($this->PageNow <> $this->PageNext){
		$this->buttons['›']=$this->PageNext;
	}
	$this->buttons['››']=$this->PageLast;

}


public $buttons = null;

public $prevbutton = null;

public $nextbutton = null;

}

?>