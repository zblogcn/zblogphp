<?php
/**
 * Z-BlogPHP Clinic recount-category
 * @package recount-category
 * @subpackage recount-category.php
 */

class recount_category extends clinic
{

    /**
     * Build queue
     * @return null
     */
    public function get_queue()
    {
        global $zbp;

        if ((int) $zbp->version < 140624) {
            $this->set_queue('output_message', json_encode(array('error', '版本没到Z-Blog 1.4，无法使用本组件')));
            return;
        }

        $sql = $zbp->db->sql->Count($zbp->table['Category'], array(array('MAX', 'cate_ID', 'num')), null, null, null, null);
        $max_id = $zbp->db->Query($sql);

        if (count($max_id > 0)) {
            $max = $max_id[0]['num'];
            $this->set_queue('output_message', json_encode(array('success', '初始化成功')));
            for ($i = 1; $i <= $max; $i++) {
                $this->set_queue('category_recount', $i);
            }
        } else {
            $this->set_queue('output_message', json_encode(array('error', '初始化失败')));
        }
    }

    /**
     * Recount category
     * @return null
     */
    public function category_recount($id)
    {

        CountCategoryArray(array($id));
        $this->output('success', '分类' . '（ID = ' . $id . '）重建成功');
    }

    /**
     * output_message
     * @return null
     */
    public function output_message($str)
    {
        $str = json_decode($str);
        $this->output($str[0], $str[1]);
    }
}
