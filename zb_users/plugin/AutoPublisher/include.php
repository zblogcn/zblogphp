<?php

//注册插件
RegisterPlugin("AutoPublisher", "ActivePlugin_AutoPublisher");

function ActivePlugin_AutoPublisher()
{
    Add_Filter_Plugin('Filter_Plugin_Zbp_Load', 'AutoPublisher_Begin');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response3', 'AutoPublisher_Response3');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'AutoPublisher_PostArticle_Succeed');
}

function AutoPublisher_Begin()
{
    global $zbp;
    $s = $zbp->Config('AutoPublisher')->idstring;
    if (!$s) {
        return;
    }
    $s2 = $s;
    $array = explode('|', $s);
    foreach ($array as $aid) {
        $a = $zbp->GetPostByID($aid);
        if ($a->ID > 0) {
            if ($a->PostTime <= time() && $a->Status == ZC_POST_STATUS_DRAFT) {
                $a->Status = ZC_POST_STATUS_PUBLIC;
                $a->Save();
                $s = DelNameInString($s, (string) $a->ID);
            }
        }
    }
    if ($s != $s2) {
        $zbp->Config('AutoPublisher')->idstring = $s;
        $zbp->SaveConfig('AutoPublisher');
    }
}

function AutoPublisher_PostArticle_Succeed(&$article)
{
    global $zbp;

    if (isset($_POST['AutoPublisher'])) {
        if ($_POST['AutoPublisher']) {
            $s = $zbp->Config('AutoPublisher')->idstring;
            $s = AddNameInString($s, (string) $article->ID);
            $zbp->Config('AutoPublisher')->idstring = $s;
            $zbp->SaveConfig('AutoPublisher');
        } else {
            $s = $zbp->Config('AutoPublisher')->idstring;
            $s = DelNameInString($s, (string) $article->ID);
            $zbp->Config('AutoPublisher')->idstring = $s;
            $zbp->SaveConfig('AutoPublisher');
        }
    }
}

function AutoPublisher_Response3()
{
    global $zbp;
    global $article;

    $s = $zbp->Config('AutoPublisher')->idstring;

    echo '<dl style="padding-left:10px;">';
    echo '<dt></dt>';
    echo '<dd><b>定时发布</b>&nbsp;<input type="text" name="AutoPublisher" id="edtAutoPublisher" value="' . (HasNameInString($s, (string) $article->ID) ? '1' : '0') . '" class="checkbox" />';
    echo '<br/>(如定时请设置为“on”，并保存为“草稿”，且将发布时间设为未来时间。如不定时请设置为“off”。)</dd>';
    echo '</dl>';
}
