<?php
#注册插件
RegisterPlugin("os2020", "ActivePlugin_os2020");

function ActivePlugin_os2020()
{
    Add_Filter_Plugin('Filter_Plugin_Post_Url', 'os2020_Object_Url', PLUGIN_EXITSIGNAL_RETURN);
    Add_Filter_Plugin('Filter_Plugin_Category_Url', 'os2020_Object_Url', PLUGIN_EXITSIGNAL_RETURN);
    Add_Filter_Plugin('Filter_Plugin_Member_Url', 'os2020_Object_Url', PLUGIN_EXITSIGNAL_RETURN);
    Add_Filter_Plugin('Filter_Plugin_Tag_Url', 'os2020_Object_Url', PLUGIN_EXITSIGNAL_RETURN);
}

function os2020_Object_Url($object)
{
    global $zbp;

    $plugin = null;

    switch ($type = get_class($object)) {
        case 'Post':
            $key = 'id';
            $type = $object->TypeName;
            $plugin = &$GLOBALS['hooks']['Filter_Plugin_Post_Url'];
            break;
        case 'Category':
            $key = 'cate';
            $plugin = &$GLOBALS['hooks']['Filter_Plugin_Category_Url'];
            break;
        case 'Member':
            $key = 'auth';
            $plugin = &$GLOBALS['hooks']['Filter_Plugin_Member_Url'];
            break;
        case 'Tag':
            $key = 'tags';
            $plugin = &$GLOBALS['hooks']['Filter_Plugin_Tag_Url'];
            break;
    }
    //给每一次的调用都设置退出信号
    $plugin['os2020_Object_Url'] = PLUGIN_EXITSIGNAL_RETURN;
    return $zbp->host . '?' . http_build_query(
        array(
            'type' => $type,
            $key => $object->ID,
        )
    );
}

function InstallPlugin_os2020()
{
}

function UninstallPlugin_os2020()
{
}
