<?php

$GLOBALS['zbpdk']->add_extension(array(
    'url'         => 'main.php',
    'description' => '用于查看当前系统的路由表',
    'id'          => 'Route',
));

$GLOBALS['zbpdk']->submenu->add(array(
    'url'   => 'Route/main.php',
    'float' => 'left',
    'id'    => 'Route',
    'title' => 'Route',
));

