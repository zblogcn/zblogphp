<?php
/**
 * Z-BlogPHP Clinic recount-member
 * @package recount-member
 * @subpackage recount-member.php
 */

class recount_member extends clinic
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

        $sql = $zbp->db->sql->Count($zbp->table['Member'], array(array('MAX', 'mem_ID', 'num')), null, null, null, null);
        $max_id = $zbp->db->Query($sql);

        if (count($max_id > 0)) {
            $max = $max_id[0]['num'];
            $this->set_queue('output_message', json_encode(array('success', '初始化成功')));
            for ($i = 1; $i <= $max; $i++) {
                $this->set_queue('member_recount', $i);
            }
        } else {
            $this->set_queue('output_message', json_encode(array('error', '初始化失败')));
        }
    }

    /**
     * Recount member
     * @return null
     */
    public function member_recount($id)
    {

        //$param = unserialize($param);
        //$id = $param[0];
        //$name = $param[1];

        CountMemberArray(array($id));
        $this->output('success', '用户' . '（ID = ' . $id . '）重建成功');
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
