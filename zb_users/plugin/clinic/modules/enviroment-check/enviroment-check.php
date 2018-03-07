<?php
/**
 * Z-BlogPHP Clinic check enviroment
 * @package enviroment-check
 * @subpackage enviroment-check.php
 */

class enviroment_check extends clinic
{

    private $_functions = array(
        'gd_info' => 'GD库 - 用于生成验证码等',
        'curl_init' => 'curl（非必须） - 用于更高效地访问网络',
    );

    /**
     * Build queue
     * @return null
     */
    public function get_queue()
    {
        foreach ($this->_functions as $name => $value) {
            $this->set_queue('check_function', $name);
        }
    }

    /**
     * Check function exists
     * @param string $param
     * @return null
     */
    public function check_function($param)
    {

        if (function_exists($param)) {
            $output = '';
            switch ($param) {
                case 'gd_info':
                    $info = $param();
                    $output = $info['GD Version'];
                    break;
                case 'curl_init':
                    $output = '';
                    break;
            }

            $this->output('success', $this->_functions[$param] . ' - ' . $output);
        } else {
            $this->output('error', $this->_functions[$param] . ' 未安装，请联系空间商');
        }
    }
}
