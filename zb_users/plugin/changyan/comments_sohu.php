<?php
require_once CHANGYAN_PLUGIN_PATH . '/Handler.php';
$changyanPlugin = Changyan_Handler::getInstance();

//Get comment template from option
$changyan_script = $changyanPlugin->getOption('changyan_script');
//display the comment template
echo $changyan_script;
?>
