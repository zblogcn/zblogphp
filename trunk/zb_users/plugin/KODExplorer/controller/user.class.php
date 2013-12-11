<?php
 /**
 * 登陆控制器
 * @author      warlee<kalcaddle@qq.com>
 */
class user extends Controller
{
	public $user;	//用户相关信息
    /**
     * 构造函数
     */
    function __construct()
	{
		session_start();
        parent::__construct();
		$this->tpl	= TEMPLATE  . 'user/';        
		$this->user = $_SESSION['user'];		
    }

	/**
     * 登陆状态检测
     */
	public function loginCheck()
    {
		return true;//
    	if ($this->user['login'] == 'ok'){
            return true;
		}else if($this->config['app_action'] == 'loginSubmit'){//登陆提交判断
            return ;
        }else{
			$this->login();
		}
    }

    /**
     * 登陆view
     */
    public function login()
    {
    	$this->display('login.html');
		exit;
    }
	/**
     * 登陆数据提交处理
     */
	public function loginSubmit()
    {
		$msg = '';
		if(!empty($this->in['name']) && !empty($this->in['password'])) {	
			if($this->in['name']==$this->config['user_name']
				&& md5($this->in['password'])==$this->config['user_password']){
				$_SESSION['user']=array(
					'login'	=> 'ok',
					'time'	=> time()   //登陆时间
				);
				header('location:./');
				return;
			}
			$msg = '密码错误!';
		}else{
			$msg = '用户名密码不能为空!';
		}
		$this->assign('msg',$msg);
		$this->display('login.html');
    }

	/**
     * 退出处理
     */
    public function logout()
    {
		session_destroy();
		header('location:./?user/login');
    }

	/**
     * 修改用户名
     */
    public function changeUserName()
    {
        $uname=$this->in['name'];
        if ($uname!=''){
            update_config($this->config['seting_file'],"config['user_name']",$uname);
            echo '{state:"succeed",msg:"配置修改成功!"}';
        }else {
            echo '{state:"warning",msg:"不能为空!"}';
        }
    }

	/**
     * 修改密码
     */
    public function changeUserPassword()
    {
        $upassword_now=$this->in['password_now'];
        $upassword_new=$this->in['password_new'];
        if ($upassword_now!='' && $upassword_new!='' ){
            $now_pass=get_config($this->config['seting_file'],"config['user_password']");
            if ($now_pass==md5($upassword_now)){
                update_config($this->config['seting_file'],"config['user_password']",
                    md5($upassword_new));
                echo '{state:"succeed",msg:"修改成功!"}';
            }else {
                echo '{state:"error",msg:"原密码错误!"}';
            }           
        }else {
            echo '{state:"warning",msg:"不能为空!"}';
        }  
    }
}