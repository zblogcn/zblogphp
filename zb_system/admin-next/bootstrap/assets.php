<?php

/**
 * 读取 Vite 产出的 manifest.json，返回可直接输出的资源地址.
 *
 * @return array{css: string[], js: string[], runtime: string, styles: string[], missing: bool}
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

    $empty = [
        'css'     => [],
        'js'      => [],
        'runtime' => '',
        'styles'  => [],
        'missing' => true,
    ];

    if (!is_readable($manifestFile)) {
        $cached = $empty;
        return $cached;
    }

    $manifest = json_decode((string) file_get_contents($manifestFile), true);
    $entry = $manifest['index.html'] ?? null;

    if (!is_array($entry) || empty($entry['file'])) {
        $cached = $empty;
        return $cached;
    }

    $runtimeEntry = $manifest['src/plugin-host/runtime.ts'] ?? null;

    // 插件样式：{appId => url}
    $styles = [];
    foreach (ZbpAdminNext_AppManifests() as $manifestApp) {
        if ($manifestApp['style'] === '') {
            continue;
        }
        $styles[$manifestApp['appId']] = $manifestApp['baseUrl'] . $manifestApp['style'];
    }

    $cached = [
        'css' => array_map(
            static fn (string $file): string => $baseUrl . $file,
            $entry['css'] ?? []
        ),
        'js' => [$baseUrl . $entry['file']],
        'runtime' => is_array($runtimeEntry) && !empty($runtimeEntry['file'])
            ? $baseUrl . $runtimeEntry['file']
            : '',
        'styles'  => $styles,
        'missing' => false,
    ];

    return $cached;
}
