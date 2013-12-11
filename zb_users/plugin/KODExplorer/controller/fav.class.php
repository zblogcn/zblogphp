<?php 
class fav extends Controller{
    /**
     * 构造函数
     */
    function __construct()    {
        parent::__construct();
        load_class('arraysql');
    }

    /**
     * 获取收藏夹json
     */
    public function get() {
        echo json_encode(unserialize(file_get_contents($this->config['fav_path'])));
    }

    /**
     * 添加
     */
    public function add() {
        $fav=unserialize(file_get_contents($this->config['fav_path']));
        $sql=new arraysql($fav);
        $res=$sql->insert(array('name'=>$this->in['name'],'path'=>$this->in['path']));
        $sql_arr=serialize($sql->getarray());
        file_put_contents($this->config['fav_path'],$sql_arr);
        
        if ($res)echo '1';
        else echo '0';
    }

    /**
     * 编辑
     */
    public function edit() {
        $to_array=array('name'=>$this->in['name_to'],'path'=>$this->in['path_to']);
        //查找到一条记录，修改为该数组
        $fav=unserialize(file_get_contents($this->config['fav_path']));
        $sql=new arraysql($fav);        
        $res=$sql->update('name',$this->in['name'],$to_array);
        $sql_arr=serialize($sql->getarray());
        file_put_contents($this->config['fav_path'],$sql_arr);
        
        if ($res)echo '1';
        else echo '0';
    }

    /**
     * 删除
     */
    public function del() {
        $fav=unserialize(file_get_contents($this->config['fav_path']));
        $sql=new arraysql($fav);
        $res=$sql->del('name',$this->in['name']);
        $sql_arr=serialize($sql->getarray());
        file_put_contents($this->config['fav_path'],$sql_arr);

        if ($res)echo '1';
        else echo '0';
    }
}
