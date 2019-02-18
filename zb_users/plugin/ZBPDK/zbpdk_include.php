<?php

$blogtitle = 'Z-Blog PHP Development Kit';

class zbpdk_t
{
    public $submenu = null;
    public $objects = array();

    public function __construct()
    {
        $this->submenu = new zbpdk_submenu();
        //$this->scan_extensions();
        //var_dump($this->objects);
    }

    public function scan_extensions()
    {
        global $blogpath;
        $current_path = $blogpath . '/zb_users/plugin/ZBPDK/extensions/';
        if ($handle = opendir($current_path)) {
            while (false !== ($filename = readdir($handle))) {
                if ($filename[0] == '.') {
                    continue;
                }

                $file = $current_path . $filename;
                if (is_dir($file)) {
                    if (is_file($current_path . $filename . '/include.php')) {
                        require $current_path . $filename . '/include.php';
                    }
                }
            }
            closedir($handle);
        }
    }

    public function add_extension(array $ary)
    {
        if (!isset($ary['url'])) {
            throw new Exception('Param \'url\' is empty!');
        }

        if (!isset($ary['description'])) {
            throw new Exception('Param \'description\' is empty!');
        }

        if (!isset($ary['id'])) {
            throw new Exception('Param \'id\' is empty!');
        }

        $this->objects[] = new zbpdk_extension($ary);
        $this->actions[] = $ary;
    }
}

class zbpdk_extension
{
    public $id = '';
    public $description = '';
    public $url = '';

    public function __construct($ary)
    {
        $this->id = $ary['id'];
        $this->url = $ary['url'];
        $this->description = $ary['description'];
    }

    public function load($id)
    {
    }
}

class zbpdk_submenu
{
    public static $html = '';
    private $actions = array();
    private $template = '';

    public function __construct()
    {
        global $zbp;
        $this->template = '<a href="' . $zbp->host . 'zb_users/plugin/ZBPDK/extensions/$url"><span class="m-$float$light">$title</span></a>';
        $this->add(array(
            'url'   => '../main.php',
            'float' => 'left',
            'title' => '首页',
            'id'    => 'main',
        ));
    }

    public function add(array $ary)
    {
        if (!isset($ary['url'])) {
            throw new Exception('Param \'url\' is empty!');
        }

        if (!isset($ary['float'])) {
            throw new Exception('Param \'float\' is empty!');
        }

        if (!isset($ary['title'])) {
            throw new Exception('Param \'title\' is empty!');
        }

        if (!isset($ary['id'])) {
            throw new Exception('Param \'id\' is empty!');
        }

        $this->actions[] = $ary;
    }

    public function export($id)
    {
        $html = '';
        $temp = '';
        for ($i = 0; $i < count($this->actions); $i++) {
            $temp = $this->template;
            $temp = str_replace('$url', $this->actions[$i]['url'], $temp);
            $temp = str_replace('$float', $this->actions[$i]['float'], $temp);
            $temp = str_replace('$title', $this->actions[$i]['title'], $temp);
            $temp = str_replace('$id', $this->actions[$i]['id'], $temp);
            $temp = str_replace('$light', ($this->actions[$i]['id'] == $id ? ' m-now' : ''), $temp);
            $html .= $temp;
        }

        return $html;
    }
}
