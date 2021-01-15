<?php

$GLOBALS['zbpdk']->add_extension(array(
    'url'         => 'main.php',
    'description' => '用于查看Post类型的详细信息',
    'id'          => 'PostType',
));

$GLOBALS['zbpdk']->submenu->add(array(
    'url'   => 'PostType/main.php',
    'float' => 'left',
    'id'    => 'PostType',
    'title' => 'PostType',
));

