<?php

/**
 * 读取 Vite 产出的 manifest.json，返回可直接输出的资源地址.
 *
 * @return array{css: string[], js: string[], missing: bool}
 */
function ZbpAdminNext_Assets(): array
{
    global $zbp;

    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $manifestFile = ZBP_PATH . 'zb_system/admin-next/assets/manifest.json';
    $baseUrl = $zbp->host . 'zb_system/admin-next/assets/';

    if (!is_readable($manifestFile)) {
        $cached = ['css' => [], 'js' => [], 'missing' => true];
        return $cached;
    }

    $manifest = json_decode((string) file_get_contents($manifestFile), true);
    $entry = $manifest['index.html'] ?? null;

    if (!is_array($entry) || empty($entry['file'])) {
        $cached = ['css' => [], 'js' => [], 'missing' => true];
        return $cached;
    }

    $cached = [
        'css' => array_map(
            static fn (string $file): string => $baseUrl . $file,
            $entry['css'] ?? []
        ),
        'js' => [$baseUrl . $entry['file']],
        'missing' => false,
    ];

    return $cached;
}
