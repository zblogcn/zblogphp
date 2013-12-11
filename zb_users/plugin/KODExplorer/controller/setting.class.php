<?php 
class setting extends Controller{
    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct();	
    }

    /**
     * 用户首页展示
     */
    public function index() {
		$this->tpl = TEMPLATE.'setting/';
    	$this->display('index.php');
    }

    /**
     * 用户首页展示
     */
    public function slider() {
		$this->tpl = TEMPLATE . 'setting/slider/';
    	$this->display($this->in['slider'].'.php');
    }


    //图标显示
    public function setIcon() {
        update_config($this->config['seting_file'],"config['list']","icon");  
    }
    //列表显示
    public function setList() {
        update_config($this->config['seting_file'],"config['list']","list");  
    }
    //排序依靠字段，或升降方式
    public function setListSort() {
        update_config($this->config['seting_file'],
            "config['list_sort_field']",$this->in['field']);
        update_config($this->config['seting_file'],
            "config['list_sort_order']",$this->in['order']);
    }
    //播放器设置
    public function setPlayer() {
        $musictheme=$this->in['musictheme'];
        $movietheme=$this->in['movietheme'];
        if ($musictheme!='' && $movietheme!=''){
            update_config($this->config['seting_file'],"config['musictheme']",$musictheme);
            update_config($this->config['seting_file'],"config['movietheme']",$movietheme);
            echo '{state:"succeed",msg:"配置修改成功!"}';
        }else {
            echo '{state:"warning",msg:"不能为空!"}';
        }   
    }
    //设置壁纸
    public function setWall() {
        update_config($this->config['seting_file'],"config['wall']",$this->in['wall']);
        echo '{state:"warning",msg:"配置修改成功!"}';
    }
    //设置主题
    public function setTheme() {
        $theme = $this->in['theme'];
        if ($theme!=''){
            update_config($this->config['seting_file'],"config['theme']",$theme ); 
        }
    }
    //设置代码高亮
    public function setCodetheme() {
        $theme = $this->in['theme'];
        if ($theme!=''){
            update_config($this->config['seting_file'],"config['codetheme']",$theme); 
            echo '{state:"succeed",msg:"配置修改成功!"}';
        }else {
            echo '{state:"warning",msg:"不能为空!"}';
        }
    }
}
