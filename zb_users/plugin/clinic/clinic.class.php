<?php
/**
 * Z-BlogPHP Clinic.
 */

/**
 * Clinic main class.
 */
class Clinic
{
    public $module_path = '';
    public $include_path = '';
    public $modules = array();
    public $categories = array();
    public $output_json = array();

    public function __construct()
    {
        $this->module_path = dirname(__FILE__) . '/modules/';
        $this->include_path = dirname(__FILE__) . '/include/';
        $this->categories = array_merge(
            json_decode(file_get_contents('./include/category.json'), true),
            $GLOBALS['clinic_register_cate']
        );
        $this->scan_dir();
    }

    /**
     * Scan modules folder.
     *
     * @return array modules
     */
    public function scan_dir()
    {
        //For third-party developers
        $this->modules = $GLOBALS['clinic_register_array'];
        $dir = scandir($this->module_path);
        foreach ($dir as $name) {
            // Directory name must be English
            if ($name != '.' && $name != '..' && is_dir($this->module_path . $name)) {
                if (is_file($this->module_path . $name . '/' . $name . '.json')) {
                    // Load JSON Data
                    $this->modules[$name] = json_decode(file_get_contents($this->module_path . $name . '/' . $name . '.json'), true);
                    $this->modules[$name]['path'] = $this->module_path . $name . '/' . $name . '.php';
                    $category_name = $this->modules[$name]['category'];
                    $this->categories[$category_name]['modules'][] = $name;
                }
            }
        }

        return $this->modules;
    }

    /**
     * Load module.
     *
     * @param string $module_name
     *
     * @return object
     */
    public function load_module($module_name)
    {
        if (isset($this->modules[$module_name])) {
            include_once $this->modules[$module_name]['path'];
            // Class name cannot include '-'
            $class_name = str_replace('-', '_', $module_name);
            if (class_exists($class_name)) {
                $class = new $class_name();

                return $class;
            }
        }

        return false;
    }

    /**
     * Output.
     *
     * @param string $status
     * @param string $text
     *
     * @return string
     */
    public function output($status, $text)
    {
        $string = '<span style="color:';
        $string .= ($status === 'success' ? 'green">√' : 'red">×') . ' ';
        $string .= $text . '</span>';
        $this->output_json[] = json_encode(array('type' => 'msg', 'msg' => $string, 'error' => $status));

        return $string;
    }

    /**
     * Load output.
     *
     * @param string $function
     * @param string $param
     *
     * @return string
     */
    public function set_queue($function, $param)
    {
        $this->output_json[] = json_encode(array('type' => 'queue', 'function' => $function, 'param' => $param, 'error' => 0));

        return true;
    }
}
