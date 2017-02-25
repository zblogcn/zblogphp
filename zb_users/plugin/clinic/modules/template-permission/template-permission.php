<?php
/**
 * Z-BlogPHP Clinic check template permission
 * @package template-permission
 * @subpackage template-permission.php
 */

class template_permission extends clinic
{

    private $_dirs = array(
        'zb_users/cache' => '缓存文件夹',
        'zb_users/theme/{%template%}/template' => '主题模板文件夹',
        'zb_users/theme/{%template%}/compile' => '主题编译缓存文件夹',
    );

    /**
     * Build queue
     * @return null
     */
    public function get_queue()
    {
        foreach ($this->_dirs as $name => $value) {
            $this->set_queue('check_right', $name);
        }
        $this->set_queue('build_template', '');
    }

    /**
     * Build Template
     * @return null
     */
    public function build_template($param)
    {
        global $zbp;
        $zbp->BuildTemplate();
        $this->output('success', '模板重编译完成');
        $this->output('success', '请自行清理浏览器缓存后重试');
    }

    /**
     * Check folder right
     * @param string $param
     * @return null
     */
    public function check_right($param)
    {

        global $zbp;
        global $blogtheme;

        $s = '';
        $path = $zbp->path . str_replace('{%template%}', $blogtheme, $param);
        $return = false;
        if (isset($this->_dirs[$param])) {
            if (!is_dir($path)) {
                $s = '文件夹不存在 - 待修复';
            } else {
                /*$s = GetFilePerms($path);

                if (substr($s, 0, 1) == '-') {
                $return = (substr($s, 1, 1) == 'r' && substr($s, 2, 1) == 'w' && substr($s, 4, 1) == 'r' && substr($s, 7, 1) == 'r');
                }
                else {
                $return = (substr($s, 1, 1) == 'r' && substr($s, 2, 1) == 'w' && substr($s, 3, 1) == 'x' && substr($s, 4, 1) == 'r' && substr($s, 7, 1) == 'r' && substr($s, 6, 1) == 'x' && substr($s, 9, 1) == 'x');
                }*/

                if (@file_put_contents($path . '/clinic_check.php', '<?php echo "test";')) {
                    if (@unlink($path . '/clinic_check.php')) {
                        $s = '读写权限正常';
                        $return = true;
                    } else {
                        $s = '无法删除文件';
                    }
                } else {
                    $s = '无法写入文件';
                }
            }
        }

        if ($return) {
            $this->output('success', $path . '-' . $this->_dirs[$param] . ' - ' . $s);
        } else {
            $this->output('error', $path . '-' . $this->_dirs[$param] . ' - ' . $s);
            $this->set_queue('repair_right', $param);
        }
    }

    /**
     * Repair folder right
     * @param string $param
     * @return null
     */
    public function repair_right($param)
    {

        global $zbp;
        global $blogtheme;

        $return = false;
        $s = '';
        $path = $zbp->path . str_replace('{%template%}', $blogtheme, $param);
        if (isset($this->_dirs[$param])) {
            if (!is_dir($path)) {
                if (!mkdir($path, 0777)) {
                    $s = '文件夹创建失败';
                } else {
                    @$return = chmod($path, 0777);
                }
            } else {
                @$return = chmod($path, 0777);
            }
        }

        if (!$return && $s == '') {
            $s = '请联系服务器商给该文件夹提供0777权限。';
        }

        if ($return) {
            $this->output('success', $path . '-' . $this->_dirs[$param] . ' - 文件夹权限修复成功');
        } else {
            $this->output('error', $path . '-' . $this->_dirs[$param] . ' - ' . $s);
        }
    }
}
