<?php
ini_set('max_execution_time', '0');

require_once CHANGYAN_PLUGIN_PATH . '/Synchronizer.php';
class Changyan_Handler
{
    const version = '1.0';
    public $value;
    //changyan URL
    public $PluginURL = 'xcv';
    //the singleton instance of this class
    private static $instance = null;
    private $changyanSynchronizer = 'xcv';

    private function __construct()
    {
        $this->PluginURL = plugin_dir_url(__FILE__);
        $this->changyanSynchronizer = Changyan_Synchronizer::getInstance();
    }

    private function __clone()
    {
        //Prevent from being cloned
    }

    //return the single instance of this class
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    //rt
    public function doPluginActionLinks($linkes, $file)
    {
        array_unshift($links, '<a href="' . admin_url('admin.php?page=changyan_settings') . '">' . __('Settings') . '</a>');
    }

    //do nothing
    public function filterActions($actions)
    {
        return $actions;
    }

    public function getOption($option)
    {
		global $zbp;
		return $zbp->Config('changyan')->$option;
        //return get_option($option);
    }

    public function setOption($option, $value)
    {
		global $zbp;
		$zbp->Config('changyan')->$option=$value;
		$zbp->SaveConfig('changyan');
		return true;
        //return update_option($option, $value);
    }

    public function delOption($option)
    {
        return delete_option($option);
    }

    public function showCommentsNotice()
    {
        echo '<div class="updated">'
            . '请访问<a color = red href="http://changyan.sohu.com/manage" target="blank"><font color="red">畅言站长管理后台</font></a>进行评论管理，当前页面的管理操作不能被同步到畅言管理服务器。</p>'
            . '</div>';
    }

    //return a template to be used for comment
    public function getCommentsTemplate($default_template)
    {
        global $wpdb, $post;

        if (!(is_singular() && (have_comments() || 'open' == $post->comment_status))) {
            return $default_template;
        }

        return dirname(__FILE__) . '/comments_sohu.php';
    }

    public function setup()
    {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        //tips
        include dirname(__FILE__) . '/setup.php';
    }

    public function configure()
    {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        //tips
        include dirname(__FILE__) . '/configure.php';
    }

    public function analysis()
    {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        //tips
        include dirname(__FILE__) . '/analysis.php';
    }

    public function settings()
    {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include dirname(__FILE__) . '/settings.php';

        //synchronization
        //$this->sync2Wordpress();
        //$this->sync2Changyan();
    }
    //deprecated
    public function account()
    {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include dirname(__FILE__) . '/account.php';
    }

    public function sync2Wordpress()
    {
        $this->changyanSynchronizer->sync2Wordpress();
    }

    public function sync2Changyan()
    {
        $this->changyanSynchronizer->sync2Changyan();
    }
    //deprecated
    public function saveScript()
    {
        //get script
        $aScript = $_POST['script'];
        $aScript = trim($aScript);
        $aScript = stripslashes($aScript);
        $this->setOption('changyan_script', $aScript);

        die($aScript);
    }

    public function saveAppID()
    {
        //set auto cron
        $this->setOption('changyan_isCron', true);
        //get appID from POST[]
        $appID = $_POST['appID'];
        $appID = trim($appID);
        $appIDArray = array(
            'app_id' => $appID
        );
        //get conf using appID through http://changyan.sohu.com/getConf?app_id=cyqqryvMq
        $aUrl = $this->changyanSynchronizer->buildURL($appIDArray, "http://changyan.sohu.com/getConf");
        $conf = $this->changyanSynchronizer->getContents_curl($aUrl);
        //build script
        $scriptPart0 = "<div id=\"SOHUCS\"></div><script>(function(){var appid = '";
        $scriptPart1 = "',conf = '";
        $scriptPart2 = "';
var doc = document,
s = doc.createElement('script'),
h = doc.getElementsByTagName('head')[0] || doc.head || doc.documentElement;
s.type = 'text/javascript';
s.charset = 'utf-8';
s.src =  'http://assets.changyan.sohu.com/upload/changyan.js?conf='+ conf +'&appid=' + appid;
h.insertBefore(s,h.firstChild);
})()</script>";
        $script = $scriptPart0 . $appID . $scriptPart1 . $conf . $scriptPart2;
        $this->setOption('changyan_script', $script);
        die($appID);
    }

    public function saveAppKey()
    {
        //get appKey
        $appKey = $_POST['appKey'];
        $appKey = trim($appKey);
        //save
        $this->setOption('changyan_appKey', $appKey);

        die($appKey);
    }

    public function setCron()
    {
        $isChecked = $_POST['isChecked'];
        $isChecked = trim($isChecked);
        $flag = 0;

        if ('true' == $isChecked) {
            $flag = $this->setOption('changyan_isCron', true);
        } else {
            $flag = $this->setOption('changyan_isCron', false);
        }

        if (!empty($flag) || $flag != false) {
            die("TRUE");
        } else {
            die("FALSE");
        }
    }

    //run synchronization to wordpress
    public function cronSync()
    {
        $this->sync2Wordpress();
    }
}

?>
