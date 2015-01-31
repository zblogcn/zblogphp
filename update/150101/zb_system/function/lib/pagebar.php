<?php
/**
* 分页条码
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Article 类库
*/
class PageBar{

	 /**
	 * @var int|null 内容总数
	 */
	public $Count = null;
	/**
	 * @var int Pagebar长度数量
	 */
	public $PageBarCount=0;
	/**
	 * @var int 每页数量
	 */
	public $PageCount  = 0;
	/**
	 * @var int 总页数
	 */
	public $PageAll  = 0;
	/**
	 * @var int 当前页
	 */
	public $PageNow  = 0;
	/**
	 * @var int 起始页
	 */
	public $PageFirst  = 0;
	/**
	 * @var int 最后页
	 */
	public $PageLast  = 0;
	/**
	 * @var int 上一页
	 */
	public $PagePrevious  = 0;
	/**
	 * @var int 下一页
	 */
	public $PageNext  = 0;
	/**
	 * @var null|UrlRule
	 */
	public $UrlRule  = null;
	
	public function __set($name, $value){
		if($name=='count')
			$this->Count=$value;
		if($name=='pagebarcount')
			$this->PageBarCount=$value;
		if($name=='pagecount')
			$this->PageCount=$value;
		if($name=='pageall')
			$this->PageAll=$value;
		if($name=='pagenow')
			$this->PageNow=$value;
		if($name=='pagefirst')
			$this->PageFirst=$value;
		if($name=='pagelast')
			$this->PageLast=$value;
		if($name=='pageprevious')
			$this->PagePrevious=$value;
		if($name=='pagenext')
			$this->PageNext=$value;
		if($name=='urlrule')
			$this->UrlRule=$value;
	}

	/**
	* @param $name
	* @return mixed
	*/
	public function __get($name){
		if($name=='count')
			return $this->Count;
		if($name=='pagebarcount')
			return $this->PageBarCount;
		if($name=='pagecount')
			return $this->PageCount;
		if($name=='pageall')
			return $this->PageAll;
		if($name=='pagenow')
			return $this->PageNow;
		if($name=='pagefirst')
			return $this->PageFirst;
		if($name=='pagelast')
			return $this->PageLast;
		if($name=='pageprevious')
			return $this->PagePrevious;
		if($name=='pagenext')
			return $this->PageNext;
		if($name=='urlrule')
			return $this->UrlRule;
	}

	/**
	 * @param $url
	 * @param bool $makereplace
	 */
	public function __construct($url,$makereplace=true){
		$this->UrlRule=new UrlRule($url);
		$this->UrlRule->MakeReplace=$makereplace;
	}

	/**
	 * 构造分页条
	 * @return null
	 */
	public function Make(){
		global $zbp;
		if($this->PageCount==0)return null;

		$this->PageAll = ceil($this->Count / $this->PageCount);
		$this->PageFirst = 1;
		$this->PageLast = $this->PageAll;

		$this->PagePrevious=$this->PageNow-1;
		if($this->PagePrevious<1){$this->PagePrevious=1;}

		$this->PageNext=$this->PageNow+1;
		if($this->PageNext>$this->PageAll){$this->PageNext=$this->PageAll;}

		$this->UrlRule->Rules['{%page%}']=$this->PageFirst;
		$this->buttons['‹‹']=$this->UrlRule->Make();

		if($this->PageNow <> $this->PageFirst){
			$this->UrlRule->Rules['{%page%}']=$this->PagePrevious;
			$this->buttons['‹'] = $this->UrlRule->Make();
			$this->prevbutton=$this->buttons['‹'];
		}

		$j=$this->PageNow;
		if($j+$this->PageBarCount > $this->PageAll){
			$j=$this->PageAll-$this->PageBarCount+1;
		}
		if($j<1){$j=1;}

		for ($i=$j; $i < $j+$this->PageBarCount; $i++) {
			if($i > $this->PageAll){break;}
			$this->UrlRule->Rules['{%page%}']=$i;
			$this->buttons[$i]=$this->UrlRule->Make();
		}
		if($this->PageNow <> $this->PageNext){
			$this->UrlRule->Rules['{%page%}']=$this->PageNext;
			$this->buttons['›']=$this->UrlRule->Make();
			$this->nextbutton=$this->buttons['›'];
		}

		$this->UrlRule->Rules['{%page%}']=$this->PageLast;
		$this->buttons['››']=$this->UrlRule->Make();

	}

	/**
	 * @var array
	 */
	public $buttons = array();
	/**
	 * @var null
	 */
	public $prevbutton = null;
	/**
	 * @var null
	 */
	public $nextbutton = null;

}
