<?php
/**
 * Z-BlogPHP Clinic Check BOM
 * @package bom-check
 * @subpackage bom-check.php
 */

class bom_check extends clinic
{

    /**
     * Build queue
     * @return null
     */
    public function get_queue()
    {

        global $zbp;

        foreach (get_included_files() as $name => $value) {
            $this->set_queue('check_bom', $value);
        }

        $dir = $zbp->path . 'zb_system/defend/default/';
        $files = GetFilesInDir($dir, 'php');
        foreach ($files as $sortname => $fullname) {
            $this->set_queue('check_bom', $fullname);
        }

        $dir = $zbp->usersdir . 'theme/' . $zbp->theme . '/template/';
        $files = GetFilesInDir($dir, 'php');
        foreach ($files as $sortname => $fullname) {
            $this->set_queue('check_bom', $fullname);
        }
    }

    /**
     * Check BOM
     * @param string $param
     * @return null
     */
    public function check_bom($param)
    {

        $contents = file_get_contents($param);

        if (ord(substr($contents, 0, 1)) == 239 &&
            ord(substr($contents, 1, 1)) == 187 &&
            ord(substr($contents, 2, 1)) == 191
        ) {
            $this->output('error', $param . ' - 含BOM头');
            $this->set_queue('repair_bom', $param);
        } else {
            $this->output('success', $param . ' - 不含BOM头');
        }

        $array = explode('<?php', $contents);
        if ($array[0] != '' && count($array) > 1) {
            $this->output('error', $param . ' - 含空行 - ' . $array[0]);
            $this->set_queue('repair_empty', $param);
        } else {
            $this->output('success', $param . ' - 不含空行');
        }
    }

    /**
     * Repair BOM
     * @param string $param
     * @return null
     */
    public function repair_bom($param)
    {

        $data = substr(file_get_contents($param), 3);
        @file_put_contents($param, $data);
        $this->output('success', $param . ' - 修复完毕');
    }

    /**
     * Repair Empty Line
     * @param string $param
     * @return null
     */
    public function repair_empty($param)
    {

        $data = file_get_contents($param);
        $array = explode('<?php', $data);
        $array[0] = trim($array[0]);
        $data = implode('<?php', $array);
        @file_put_contents($param, $data);
        $this->output('success', $param . ' - 修复完毕');
    }
}
