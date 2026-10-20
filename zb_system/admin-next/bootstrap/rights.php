<?php

/**
 * 计算当前用户对各 action 的权限，供前端做菜单与按钮级判断.
 *
 * 注意：前端判断只用于呈现，服务端每次请求仍会独立校验。
 *
 * @return array<string, bool>
 */
function ZbpAdminNext_Rights(): array
{
    global $zbp, $actions;

    $rights = [];
    foreach (array_keys((array) $actions) as $action) {
        $rights[$action] = $zbp->CheckRights($action);
    }

    return $rights;
}
