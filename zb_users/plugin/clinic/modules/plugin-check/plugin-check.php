<?php
/**
 * Z-BlogPHP Clinic check plugin
 * @package plugin-check
 * @subpackage plugin-check.php
 */

class plugin_check extends clinic
{
    /**
     * Build queue
     * @return null
     */
    public function get_queue()
    {

        $this->set_queue('repair_plugin', '');
    }
    /**
     * Repair plugin
     */
    public function repair_plugin($param)
    {
        global $zbp;
        global $blogpath;
        $pluginList = explode("|", $zbp->option['ZC_USING_PLUGIN_LIST']);
        $newPluginList = array();
        foreach ($pluginList as $index => $plugin) {
            if (file_exists($blogpath . 'zb_users/plugin/' . $plugin . '/plugin.xml')) {
                array_push($newPluginList, $plugin);
            } else {
                $this->output('success', '插件' . $plugin . ' 不存在，已修复。');
            }
        }
        $theme = $zbp->option['ZC_BLOG_THEME'];
        if (!file_exists($blogpath . 'zb_users/theme/' . $theme . '/theme.xml')) {
            $this->output('success', '主题不存在，已替换为default主题。');
            $zbp->option['ZC_BLOG_THEME'] = 'default';
        }
        $zbp->option['ZC_USING_PLUGIN_LIST'] = trim(implode($newPluginList, '|'));
        $zbp->SaveOption();
        $this->output('success', '修复完成');
    }
}
