<?php
/**
 * Z-BlogPHP Clinic recount-category
 * @package recount-category
 * @subpackage recount-category.php
 */
// 这个必须重写
// 现在每扫描一篇文章就要扫描一次全表
// 必须重写
// 自己手动扫描一遍全部的评论
// 然后维护一个文章数组，初始值为0，遍历一遍全部评论，给文章数组一个一个++
// 最后遍历文章数组，写入数据库
// 数据保存可能要Redis/memcached了，或者文件
class recount_comment extends clinic
{

    private $perRequestBuildArticle = 100;
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

        $sql = $zbp->db->sql->Count($zbp->table['Post'], array(array('MAX', 'log_ID', 'num')), null, null, null, null);
        $max_id = $zbp->db->Query($sql);

        if (count($max_id > 0)) {
            $max = $max_id[0]['num'];
            $this->perRequestBuildArticle = (int) ($max / 100);
            $this->set_queue('output_message', json_encode(array('success', '初始化成功')));
            for ($i = 1; $i <= $max; $i += $this->perRequestBuildArticle) {
            //for($i = 1; $i <= $max; $i += 1000)
            //for($i = $max; $i >= 1; $i -= 1000)
                $this->set_queue('category_recount', json_encode(array($i, $this->perRequestBuildArticle)));
            }
        } else {
            $this->set_queue('output_message', json_encode(array('error', '初始化失败')));
        }
    }

    /**
     * Recount category
     * @return null
     */
    public function category_recount($param)
    {

        //$array = array();
        //for($i = 0; $i <= 9999; $i++) {
        //	$array[] = $id + $i;
        //}
        $array = array();
        $param = json_decode($param);
        $id = $param[0];
        $perRequestBuildArticle = $param[1];
        for ($i = $id; $i < $id + $perRequestBuildArticle; $i++) {
            array_push($array, $i);
        }
        CountPostArray($array);
        $this->output('success', '文章' . '（ID = ' . $id . '）重建成功');
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
