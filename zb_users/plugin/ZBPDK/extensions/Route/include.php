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

$GLOBALS['zbdk_interface_defined_plugins'] = array(
    "action"   => array(),
    "filter"   => array(),
    "response" => array(),
);

function Route_getall()
{
    foreach ($GLOBALS as $temp_name => $temp_value) {
        if (preg_match("/^(Action|Filter|Response)_/i", $temp_name, $matches)) {
            switch (strtolower($matches[1])) {
                case 'filter':
                    Route_formatfilter($temp_name, true);
            }
        }
    }
}

function Route_filterexit($filter)
{
    switch ($filter) {
        case '':
            return 'PLUGIN_EXITSIGNAL_NONE';
        case 'break':
            return 'PLUGIN_EXITSIGNAL_BREAK';
        case 'return':
            return 'PLUGIN_EXITSIGNAL_RETURN';
    }
}

function Route_outputfunc($interface_name, $closure)
{
    $str = '';

    try {
        $func = new ReflectionFunction($closure);
    } catch (ReflectionException $e) {
        echo $e->getMessage();

        return;
    }
    $start = $func->getStartLine() - 1;
    $end = $func->getEndLine() - 1;
    $filename = $func->getFileName();
    $str .= 'Interface: ' . $interface_name . "\n";
    $str .= 'FilePath: ' . $filename . "\n";
    $str .= 'StartLine: ' . $start . "\n";
    $str .= 'EndLine: ' . $end . "\n";
    $str .= implode("", array_slice(file($filename), $start, $end - $start + 1));

    return $str;
}

function Route_formatfilter($interface_name, $show_interface_name = false)
{
    foreach ($GLOBALS[$interface_name] as $temp => $temp2) {
        $w = array(
            "orig"           => $temp,
            "output"         => "",
            "interface_name" => $interface_name,
        );

        if (!function_exists($temp)) {
            $temp = '(undefined) ' . $temp;
        }

        if ($show_interface_name) {
            $temp = $interface_name . ' => ' . $temp;
        }

        $temp .= '  (exitsignal = ' . Route_filterexit($temp2) . ')';

        $w["output"] = $temp;

        array_push($GLOBALS['zbdk_interface_defined_plugins']['filter'], $w);
    }
}
