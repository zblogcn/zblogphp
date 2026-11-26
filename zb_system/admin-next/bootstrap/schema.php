<?php

declare(strict_types=1);

/**
 * admin-next 数据库结构预检.
 *
 * 只检查当前 datainfo 中 Post/Category/Tag/Member 的必需列，不修改数据库。
 * 缺列时由入口阻断 SPA，并由内部 API 拒绝写请求。
 */
if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 当前新版后台依赖的字段清单.
 *
 * 键使用“实体.物理列名”，便于管理员和日志直接定位缺列。
 *
 * @return array<string, array{entity: string, field: string, table: string, column: string}>
 */
function ZbpAdminNext_SchemaRequirements(): array
{
    global $zbp;

    $requirements = [];

    foreach (['Post', 'Category', 'Tag', 'Member'] as $entity) {
        $table = (string) ($zbp->table[$entity] ?? '');

        foreach ((array) ($zbp->datainfo[$entity] ?? []) as $field => $definition) {
            if (!is_array($definition) || !isset($definition[0])) {
                continue;
            }

            $column = (string) $definition[0];
            $requirements[$entity . '.' . $column] = [
                'entity' => $entity,
                'field'  => (string) $field,
                'table'  => $table,
                'column' => $column,
            ];
        }
    }

    return $requirements;
}

/**
 * 返回缺失的物理列名列表；结构完整时返回空数组.
 *
 * @return string[]
 */
function ZbpAdminNext_MissingSchemaColumns(): array
{
    global $zbp;

    $missing = [];

    foreach (ZbpAdminNext_SchemaRequirements() as $label => $requirement) {
        if (!$zbp->db->ExistColumn($requirement['table'], $requirement['column'])) {
            $missing[] = $label;
        }
    }

    return $missing;
}

/**
 * 当前请求是否为写请求.
 */
function ZbpAdminNext_IsWriteRequest(): bool
{
    return !in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')), ['GET', 'HEAD', 'OPTIONS'], true);
}

/**
 * 写请求的结构冲突信息；无需拦截时返回 null.
 */
function ZbpAdminNext_WriteSchemaError(): ?string
{
    if (!ZbpAdminNext_IsWriteRequest()) {
        return null;
    }

    $missing = ZbpAdminNext_MissingSchemaColumns();
    if ([] === $missing) {
        return null;
    }

    return '数据库结构不完整，缺少字段：' . implode('、', $missing) . '。请先完成数据库升级。';
}

/**
 * 输出缺列阻断页；root 显示直达迁移脚本的修复链接.
 *
 * @param string[] $missing
 */
function ZbpAdminNext_RenderSchemaBlock(array $missing): void
{
    global $zbp;

    if (!headers_sent()) {
        http_response_code(409);
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store');
    }

    $title = htmlspecialchars((string) ($zbp->name ?: 'Z-BlogPHP'), ENT_QUOTES, 'UTF-8');
    $items = '';

    foreach ($missing as $column) {
        $items .= '<li><code>' . htmlspecialchars((string) $column, ENT_QUOTES, 'UTF-8') . '</code></li>';
    }

    if ($zbp->CheckRights('root')) {
        $repairUrl = htmlspecialchars($zbp->host . 'zb_system/admin/updatedb.php?updatedb=1', ENT_QUOTES, 'UTF-8');
        $action = '<p><a class="repair" href="' . $repairUrl . '">运行数据库升级修复</a></p>'
            . '<p class="note">请先备份数据库。修复完成后刷新本页；该操作不会改动插件文件。</p>';
    } else {
        $action = '<p>当前数据库结构不完整，请联系管理员运行数据库升级后再继续。</p>';
    }

    echo '<!doctype html>' . "\n";
    echo '<html lang="zh-CN"><head><meta charset="utf-8">' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    echo '<meta name="robots" content="none">' . "\n";
    echo '<title>数据库结构需要升级 - ' . $title . '</title>' . "\n";
    echo '<style>body{font:14px/1.7 system-ui,sans-serif;padding:48px;max-width:760px;margin:0 auto;color:#1f2937}'
        . 'h1{font-size:20px;margin:0 0 16px}code{font-family:ui-monospace,monospace;background:#f3f4f6;padding:1px 4px}'
        . 'li{margin:4px 0}.repair{display:inline-block;padding:8px 14px;background:#2563eb;color:#fff;'
        . 'text-decoration:none;border-radius:4px}.note{color:#6b7280;font-size:13px}</style>' . "\n";
    echo '</head><body>' . "\n";
    echo '<h1>无法加载新版后台：数据库结构需要升级</h1>' . "\n";
    echo '<p>检测到以下必需字段缺失，已阻止页面和写操作继续，以避免数据损坏。</p>' . "\n";
    echo '<ul>' . $items . '</ul>' . "\n";
    echo $action . "\n";
    echo '</body></html>' . "\n";
}
