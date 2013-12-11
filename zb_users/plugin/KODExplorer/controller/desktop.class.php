<?php 
class desktop extends Controller{
    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct();
        $this->tpl = TEMPLATE.'desktop/';	
    }

    /**
     * 首页
     */
    public function index() {
        $this->display('index.php');
    }
}
