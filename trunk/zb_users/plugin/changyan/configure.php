<?php
require_once CHANGYAN_PLUGIN_PATH . '/Handler.php';
$changyanPlugin = Changyan_Handler::getInstance();

include_once dirname(__FILE__) . '/header.html';
?>

<div id="divMain" class="margin" style="width: 839px">
    <iframe id="rightBar_1" 
            name="rightBar_1" marginwidth="0" allowtransparency="true"
            src=<?php $script = $changyanPlugin->getOption('changyan_script');
                      $appID = explode("'", $script);
                      $appID = $appID[1];
                      echo "http://changyan.sohu.com/login?type=audit&from=wpplugin&appid=".$appID; ?> frameborder="0"
            scrolling="yes"></iframe>
</div>

<?php
include_once dirname(__FILE__) . '/scripts.html';
?>