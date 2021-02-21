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

    switch ($type = get_class($object)) {
        case 'Post':
            $key = 'id';
            $type = $object->Type == 1 ? 'Page' : 'Article';
            break;
        case 'Category':
            $key = 'cate';
            break;
        case 'Member':
            $key = 'auth';
            break;
        case 'Tag':
            $key = 'tags';
            break;
    }

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
