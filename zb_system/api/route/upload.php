<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
/**
 * Format single upload object.
 *
 * @param object $upload
 *
 * @return array
 */
function return_upload($id)
{
    global $zbp;

    $upload = $zbp->GetUploadByID($id);
    $ret = $upload->GetData();
    API::$IO->formatObjectName($ret);

    return $ret;
}

/**
 * Get upload.
 */
function api_upload_get_function()
{
    $id = (int) API::$IO->id;
    if ($id === 0) {
        API::$IO->end(3);
    }
    //
    $ret = return_upload($id);

    API::$IO->upload = $ret;
}
API::$Route->get('/upload/', 'api_upload_get_function');

/**
 * Get attachments list.
 */
function api_attachments_get_function()
{
}
API::$Route->get('/attachments/', 'api_attachments_get_function');

/**
 * Create upload.
 */
function api_upload_create_function()
{
}
API::$Route->post('/upload/create/', 'api_upload_create_function');

/**
 * Update upload.
 */
function api_upload_update_function()
{
}
API::$Route->post('/upload/update/', 'api_upload_update_function');

/**
 * Update upload.
 */
function api_upload_delete_function()
{
}
API::$Route->post('/upload/delete/', 'api_upload_delete_function');
