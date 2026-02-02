#!/usr/bin/env php
<?php
// 获取 Z-BlogPHP 版本（用于命令行）
// 使用示例：php get_version.php --help

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script is intended to be run from the command line.\n");
    exit(1);
}

require_once __DIR__ . '/../zb_system/function/c_system_base.php';

$opts = getopt('hvs', ['help', 'version', 'short', 'full', 'display', 'commit', 'json']);

function usage()
{
    $u = <<<USAGE
Usage: php get_version.php [options]

Options:
  -h, --help       Show this help message
  -v, --version    Print the default version (same as --display)
      --short      Print short version (ZC_VERSION: numeric commit string)
      --display    Print display version (ZC_BLOG_VERSION)
      --full       Print full version (ZC_VERSION_FULL)
      --commit     Print commit number (ZC_VERSION_COMMIT)
      --json       Output all fields in JSON format

If no option is provided, the script prints the display version (--display).
USAGE;
    echo $u . "\n";
}

if (isset($opts['h']) || isset($opts['help'])) {
    usage();
    exit(0);
}

// Default behavior
if (isset($opts['json'])) {
    $out = [
        'ZC_VERSION_MAJOR' => defined('ZC_VERSION_MAJOR') ? ZC_VERSION_MAJOR : null,
        'ZC_VERSION_MINOR' => defined('ZC_VERSION_MINOR') ? ZC_VERSION_MINOR : null,
        'ZC_VERSION_BUILD' => defined('ZC_VERSION_BUILD') ? ZC_VERSION_BUILD : null,
        'ZC_VERSION_COMMIT' => defined('ZC_VERSION_COMMIT') ? ZC_VERSION_COMMIT : null,
        'ZC_VERSION' => defined('ZC_VERSION') ? ZC_VERSION : null,
        'ZC_VERSION_DISPLAY' => defined('ZC_VERSION_DISPLAY') ? ZC_VERSION_DISPLAY : null,
        'ZC_VERSION_FULL' => defined('ZC_VERSION_FULL') ? ZC_VERSION_FULL : null,
        'ZC_BLOG_VERSION' => defined('ZC_BLOG_VERSION') ? ZC_BLOG_VERSION : null,
    ];
    echo json_encode($out, (JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) . "\n";
    exit(0);
}

if (isset($opts['short'])) {
    echo (defined('ZC_VERSION') ? ZC_VERSION : '') . "\n";
    exit(0);
}

if (isset($opts['full'])) {
    echo (defined('ZC_VERSION_FULL') ? ZC_VERSION_FULL : '') . "\n";
    exit(0);
}

if (isset($opts['display']) || isset($opts['version']) || isset($opts['v'])) {
    echo (defined('ZC_BLOG_VERSION') ? ZC_BLOG_VERSION : (defined('ZC_VERSION_DISPLAY') ? ZC_VERSION_DISPLAY : '')) . "\n";
    exit(0);
}

if (isset($opts['commit'])) {
    echo (defined('ZC_VERSION_COMMIT') ? ZC_VERSION_COMMIT : '') . "\n";
    exit(0);
}

// default
echo (defined('ZC_BLOG_VERSION') ? ZC_BLOG_VERSION : (defined('ZC_VERSION_DISPLAY') ? ZC_VERSION_DISPLAY : '')) . "\n";
exit(0);
