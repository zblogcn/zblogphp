<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 分页条码
 */
class PageBar
{
    /**
     * @var int|null 内容总数
     */
    public $Count = null;
    /**
     * @var int Pagebar长度数量
     */
    public $PageBarCount = 0;
    /**
     * @var int 每页数量
     */
    public $PageCount = 0;
    /**
     * @var int 总页数
     */
    public $PageAll = 0;
    /**
     * @var int 当前页
     */
    public $PageNow = 0;
    /**
     * @var int 起始页
     */
    public $PageFirst = 0;
    /**
     * @var int 最后页
     */
    public $PageLast = 0;
    /**
     * @var int 上一页
     */
    public $PagePrevious = 0;
    /**
     * @var int 下一页
     */
    public $PageNext = 0;
    /**
     * @var null|UrlRule
     */
    public $UrlRule = null;
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
    /**
     * @var array
     */
    public $Buttons = array();
    /**
     * @var null
     */
    public $PrevButton = null;
    /**
     * @var null
     */
    public $NextButton = null;

    /**
     * @param $url
     * @param bool $makeReplace
     * @param bool $isIndex
     */
    public function __construct($url, $makeReplace = true, $isIndex = false)
    {
        $this->UrlRule = new UrlRule($url);
        $this->UrlRule->MakeReplace = $makeReplace;
        $this->UrlRule->IsIndex = $isIndex;
        $this->Buttons = &$this->buttons;
        $this->PrevButton = &$this->prevbutton;
        $this->NextButton = &$this->nextbutton;
    }

    /**
     * 构造分页条
     */
    public function Make()
    {
        global $zbp;
        if ($this->PageCount == 0) {
            return;
        }

        $this->PageAll = ceil($this->Count / $this->PageCount);
        $this->PageFirst = 1;
        $this->PageLast = $this->PageAll;

        $this->PagePrevious = $this->PageNow - 1;
        if ($this->PagePrevious < 1) {
            $this->PagePrevious = 1;
        }

        $this->PageNext = $this->PageNow + 1;
        if ($this->PageNext > $this->PageAll) {
            $this->PageNext = $this->PageAll;
        }

        $this->UrlRule->Rules['{%page%}'] = $this->PageFirst;
        $this->buttons[$zbp->langs->msg->first_button] = $this->UrlRule->Make();

        if ($this->PageNow != $this->PageFirst) {
            $this->UrlRule->Rules['{%page%}'] = $this->PagePrevious;
            $this->buttons[$zbp->langs->msg->prev_button] = $this->UrlRule->Make();
            $this->prevbutton = $this->buttons[$zbp->langs->msg->prev_button];
        }

        $pageAll = $this->PageAll + 1;
        $middle = ceil($this->PageBarCount / 2);
        $start = 1;
        if ($this->PageNow > $middle) {
            $start = $this->PageNow - $middle + 1;
        }
        if ($pageAll > $this->PageBarCount && ($pageAll - $start) < $this->PageBarCount) {
            $start = $pageAll - $this->PageBarCount;
        }
        $end = $start + $this->PageBarCount;
        if ($end > $pageAll) {
            $end = $pageAll;
        }

        $j = trim($zbp->langs->msg->numeral_button);
        $j = ($j == '') ? '%num%' : $j;
        for ($i = $start; $i < $end; $i++) {
            $this->UrlRule->Rules['{%page%}'] = $i;
            $this->buttons[str_ireplace('%num%', $i, $j)] = $this->UrlRule->Make();
        }

        if ($this->PageNow != $this->PageNext) {
            $this->UrlRule->Rules['{%page%}'] = $this->PageNext;
            $this->buttons[$zbp->langs->msg->next_button] = $this->UrlRule->Make();
            $this->nextbutton = $this->buttons[$zbp->langs->msg->next_button];
        }

        $this->UrlRule->Rules['{%page%}'] = $this->PageLast;
        $this->buttons[$zbp->langs->msg->last_button] = $this->UrlRule->Make();
    }
}
