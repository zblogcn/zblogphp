<?php
/**
 * Z-BlogPHP ZBA 工具包 (CLI).
 *
 * 功能:
 *   1. 解压 .zba 文件到指定目录
 *   2. 同步 .zba 到部署目录 (先删除同名应用, 再解压安装)
 *   3. 把多个 .zba 合并打包进一个 zip (用于主发布包)
 *
 * 用法:
 *   php zba_toolkit.php <命令> [选项]
 *
 * 命令:
 *   unpack <zba> <dest>          解压 .zba 到目标目录
 *   sync   <zba> <dest>          同步 .zba 到部署目录 (删除同名应用后安装)
 *   bundle <config.json|zba...>  合并多个 .zba 到一个 zip
 *
 * 选项:
 *   -c, --config <文件>          bundle 时指定 JSON 配置文件
 *   -o, --output <zip>           bundle 时指定输出 zip 路径
 *   --into <zip>                 bundle 时指定要追加到的现有 zip
 *   -f, --force                  强制覆盖/删除, 不提示
 *   -v, --verbose                显示详细过程
 *   -h, --help                   显示帮助
 */
if ('cli' !== php_sapi_name() && empty($_SERVER['argv'])) {
    exit("此脚本仅能在 CLI 命令行环境下运行。\n");
}

class ZbaToolkit
{
    private $command = '';
    private $args = [];
    private $configPath = '';
    private $outputPath = '';
    private $intoPath = '';
    private $force = false;
    private $verbose = false;

    public function __construct(array $argv)
    {
        $this->parseArgs($argv);
    }

    public function run()
    {
        switch ($this->command) {
            case 'unpack':
                $this->runUnpack();

                break;

            case 'sync':
                $this->runSync();

                break;

            case 'bundle':
                $this->runBundle();

                break;

            case 'help':
            default:
                $this->showHelp();

                break;
        }
    }

    private function parseArgs(array $argv)
    {
        array_shift($argv);

        if (empty($argv)) {
            $this->showHelp();

            exit(0);
        }

        $this->command = strtolower($argv[0]);
        if (in_array($this->command, ['-h', '--help', 'help'], true)) {
            $this->showHelp();

            exit(0);
        }

        $count = count($argv);
        for ($i = 1; $i < $count; ++$i) {
            $arg = $argv[$i];

            if (in_array($arg, ['-h', '--help'], true)) {
                $this->showHelp();

                exit(0);
            }
            if (in_array($arg, ['-f', '--force'], true)) {
                $this->force = true;
            } elseif (in_array($arg, ['-v', '--verbose'], true)) {
                $this->verbose = true;
            } elseif (in_array($arg, ['-c', '--config'], true)) {
                if (!isset($argv[$i + 1])) {
                    $this->error("参数 {$arg} 必须指定配置文件路径。");
                }
                $this->configPath = $argv[++$i];
            } elseif (in_array($arg, ['-o', '--output'], true)) {
                if (!isset($argv[$i + 1])) {
                    $this->error("参数 {$arg} 必须指定输出路径。");
                }
                $this->outputPath = $argv[++$i];
            } elseif ('--into' === $arg) {
                if (!isset($argv[$i + 1])) {
                    $this->error("参数 {$arg} 必须指定目标 zip 路径。");
                }
                $this->intoPath = $argv[++$i];
            } elseif (0 === strpos($arg, '-')) {
                $this->error("未知参数: {$arg}");
            } else {
                $this->args[] = $arg;
            }
        }
    }

    private function showHelp()
    {
        echo <<<'HELP'
Z-BlogPHP ZBA 工具包 (CLI)

用法:
  php zba_toolkit.php <命令> [选项]

命令:
  unpack <zba文件> <目标目录>     解压 .zba 到目标目录
  sync   <zba文件> <部署目录>     同步 .zba 到部署目录 (先删除同名应用)
  bundle <配置文件|zba文件...>    合并多个 .zba 到一个 zip

选项:
  -c, --config <文件>             bundle 时指定 JSON 配置文件
  -o, --output <zip>              bundle 时指定输出 zip 路径
  --into <zip>                    bundle 时指定要追加到的现有 zip
  -f, --force                     强制覆盖/删除, 不提示
  -v, --verbose                   显示详细过程
  -h, --help                      显示帮助信息

配置文件示例 (bundle):
  {
    "output": "zblogphp-with-apps.zip",
    "apps": [
      { "name": "AppCentre", "source": "download", "url": "https://app.zblogcn.com/?zba=231" },
      { "name": "UEditor",   "source": "download", "url": "https://github.com/.../UEditor_1.7.0_20220915.zba", "skip": true },
      { "name": "MyPlugin",  "source": "local",    "path": "/path/to/MyPlugin_1.0_20260920.zba" }
    ]
  }
  设置 "skip": true 可跳过某个应用不打包。

示例:
  php zba_toolkit.php unpack AppCentre.zba ./zb_users/plugin/
  php zba_toolkit.php sync   AppCentre.zba /var/www/zbp17
  php zba_toolkit.php bundle apps.json -o release.zip
  php zba_toolkit.php bundle AppCentre.zba UEditor.zba --into zblogphp.zip

HELP;
    }

    private function runUnpack()
    {
        if (count($this->args) < 2) {
            $this->error('unpack 命令需要指定 .zba 文件和目标目录。');
        }

        $zbaPath = $this->resolvePath($this->args[0]);
        $destDir = $this->resolvePath($this->args[1]);

        if (!is_file($zbaPath)) {
            $this->error("文件不存在: {$zbaPath}");
        }

        $this->unpackZba($zbaPath, $destDir);

        echo "解压完成: {$zbaPath} -> {$destDir}\n";
    }

    private function runSync()
    {
        if (count($this->args) < 2) {
            $this->error('sync 命令需要指定 .zba 文件和部署目录。');
        }

        $zbaPath = $this->resolvePath($this->args[0]);
        $deployDir = $this->resolvePath($this->args[1]);

        if (!is_file($zbaPath)) {
            $this->error("文件不存在: {$zbaPath}");
        }

        $appInfo = $this->inspectZba($zbaPath);
        $appDir = $deployDir . '/zb_users/' . $appInfo['type'] . '/' . $appInfo['id'];

        if (is_dir($appDir)) {
            if (!$this->force) {
                $this->promptConfirm("目标目录已存在, 将删除: {$appDir}");
            }
            $this->removeDirectory($appDir);
            if ($this->verbose) {
                echo "已删除旧版应用: {$appDir}\n";
            }
        }

        $this->unpackZba($zbaPath, $deployDir . '/zb_users/' . $appInfo['type']);

        echo "同步完成: {$appInfo['name']} ({$appInfo['id']}) -> {$appDir}\n";
    }

    private function runBundle()
    {
        $apps = [];

        if ($this->configPath) {
            $config = $this->loadConfig($this->configPath);
            if (isset($config['apps']) && is_array($config['apps'])) {
                $apps = $config['apps'];
            }
            if (empty($this->outputPath) && !empty($config['output'])) {
                $this->outputPath = $config['output'];
            }
        }

        foreach ($this->args as $arg) {
            if (is_file($arg)) {
                $apps[] = ['name' => basename($arg), 'source' => 'local', 'path' => $arg];
            } elseif (is_file($this->resolvePath($arg))) {
                $realPath = $this->resolvePath($arg);
                $apps[] = ['name' => basename($realPath), 'source' => 'local', 'path' => $realPath];
            }
        }

        if (empty($apps)) {
            $this->error('bundle 命令需要至少一个 .zba 文件或配置文件。');
        }

        if (empty($this->outputPath) && empty($this->intoPath)) {
            $this->error('bundle 命令需要指定 -o/--output 或 --into 参数。');
        }

        $workDir = sys_get_temp_dir() . '/zba_bundle_' . uniqid();
        mkdir($workDir, 0755, true);

        try {
            foreach ($apps as $app) {
                if (!empty($app['skip'])) {
                    if ($this->verbose) {
                        echo "已跳过: {$app['name']}\n";
                    }

                    continue;
                }
                $zbaPath = $this->resolveAppSource($app);
                if (!is_file($zbaPath)) {
                    throw new Exception("无法获取应用文件: {$zbaPath}");
                }
                $appInfo = $this->inspectZba($zbaPath);
                // 对于 bundle 操作，将应用解压到正确的目录结构中
                $appDestDir = $workDir . '/zb_users/' . $appInfo['type'] . '/';
                $this->unpackZba($zbaPath, $appDestDir);
                if ($this->verbose) {
                    echo "已准备应用: {$appInfo['name']} ({$appInfo['id']})\n";
                }
            }

            $this->createBundleZip($workDir);
        } finally {
            $this->removeDirectory($workDir);
        }
    }

    private function loadConfig($path)
    {
        $realPath = $this->resolvePath($path);
        if (!is_file($realPath)) {
            $this->error("配置文件不存在: {$realPath}");
        }

        $content = file_get_contents($realPath);
        $config = json_decode($content, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->error('配置文件 JSON 解析失败: ' . json_last_error_msg());
        }

        return $config;
    }

    private function resolveAppSource(array $app)
    {
        $source = $app['source'] ?? 'local';

        if ('download' === $source || 'url' === $source) {
            $url = $app['url'] ?? '';
            if (empty($url)) {
                $this->error('下载来源必须提供 url 字段。');
            }

            return $this->downloadFile($url);
        }

        $path = $app['path'] ?? '';
        if (empty($path)) {
            $this->error('本地来源必须提供 path 字段。');
        }

        return $this->resolvePath($path);
    }

    private function downloadFile($url)
    {
        $tmpFile = sys_get_temp_dir() . '/zba_download_' . md5($url . time()) . '.zba';

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code < 200 || $code >= 300 || false === $data) {
                $this->error("下载失败 HTTP {$code}: {$url}");
            }
            file_put_contents($tmpFile, $data);
        } elseif (ini_get('allow_url_fopen')) {
            $data = @file_get_contents($url);
            if (false === $data) {
                $this->error("下载失败: {$url}");
            }
            file_put_contents($tmpFile, $data);
        } else {
            $this->error('当前环境不支持 curl 或 allow_url_fopen, 无法下载文件。');
        }

        return $tmpFile;
    }

    private function inspectZba($zbaPath)
    {
        $xml = $this->loadZbaXml($zbaPath);
        if (!$xml) {
            $this->error("无法解析 .zba 文件: {$zbaPath}");
        }

        return [
            'id' => (string) $xml->id,
            'name' => (string) $xml->name,
            'type' => (string) ($xml['type'] ?? 'plugin'),
            'version' => (string) $xml->version,
        ];
    }

    private function unpackZba($zbaPath, $destDir)
    {
        $xml = $this->loadZbaXml($zbaPath);
        if (!$xml) {
            $this->error("无法解析 .zba 文件: {$zbaPath}");
        }

        if ('php' !== (string) $xml['version']) {
            $this->error("不支持的 .zba 版本: {$xml['version']}");
        }

        $type = (string) $xml['type'];
        $id = (string) $xml->id;

        if (empty($id)) {
            $this->error('.zba 文件中未找到应用 ID。');
        }

        $baseDir = rtrim($destDir, '/\\') . '/' . $id . '/';
        if (!is_dir($baseDir)) {
            @mkdir($baseDir, 0755, true);
        }

        foreach ($xml->folder as $folder) {
            $path = (string) $folder->path;
            $path = $this->normalizePath($path);
            $relativePath = $this->getRelativePath($path, $id);
            if (false === $relativePath) {
                continue;
            }
            $dir = $baseDir . $relativePath;
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }

        foreach ($xml->file as $file) {
            $path = (string) $file->path;
            $stream = (string) $file->stream;
            $path = $this->normalizePath($path);
            $relativePath = $this->getRelativePath($path, $id);
            if (false === $relativePath) {
                continue;
            }
            $filePath = $baseDir . $relativePath;
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $content = base64_decode($stream);
            file_put_contents($filePath, $content);
            @chmod($filePath, 0755);

            if ($this->verbose) {
                echo "  [写入] {$relativePath}\n";
            }
        }
    }

    private function loadZbaXml($zbaPath)
    {
        $data = file_get_contents($zbaPath);
        if (false === $data) {
            return false;
        }

        $c1 = substr($data, 0, 1);
        $c2 = substr($data, 1, 1);
        if (31 === ord($c1) && 139 === ord($c2)) {
            $data = @gzdecode($data);
            if (false === $data) {
                return false;
            }
        }

        $xml = @simplexml_load_string($data, 'SimpleXMLElement', (LIBXML_COMPACT | LIBXML_PARSEHUGE));

        return $xml ?: false;
    }

    private function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = str_replace('./', '', $path);

        return ltrim($path, '/');
    }

    private function getRelativePath($path, $appId)
    {
        $prefix = $appId . '/';
        if (0 === strpos($path, $prefix)) {
            return substr($path, strlen($prefix));
        }

        return false;
    }

    private function createBundleZip($sourceDir)
    {
        if (!class_exists('ZipArchive')) {
            $this->error('当前 PHP 未启用 ZipArchive 扩展, 无法创建 zip。');
        }

        $outputPath = $this->outputPath ? $this->resolvePath($this->outputPath) : '';
        $intoPath = $this->intoPath ? $this->resolvePath($this->intoPath) : '';

        if ($intoPath) {
            if (!is_file($intoPath)) {
                $this->error("要追加的 zip 文件不存在: {$intoPath}");
            }
            $zipPath = $intoPath;
            $mode = ZIPARCHIVE::CREATE;
        } else {
            $zipPath = $outputPath;
            $mode = ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE;
            $outDir = dirname($zipPath);
            if (!is_dir($outDir)) {
                @mkdir($outDir, 0755, true);
            }
        }

        $zip = new ZipArchive();
        if (true !== $zip->open($zipPath, $mode)) {
            $this->error("无法打开 zip 文件: {$zipPath}");
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $file) {
            $fullPath = $file->getPathname();
            $relativePath = str_replace('\\', '/', substr($fullPath, strlen($sourceDir) + 1));
            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($fullPath, $relativePath);
            }
        }

        $zip->close();

        $size = filesize($zipPath);
        $sizeStr = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : ($size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B');

        echo "打包完成: {$zipPath} ({$sizeStr})\n";
    }

    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($dir);
    }

    private function resolvePath($path)
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('/', '\\', $path);
        }

        $realPath = realpath($path);
        if (false !== $realPath) {
            return $realPath;
        }

        return $path;
    }

    private function promptConfirm($message)
    {
        echo "{$message}\n确认继续? 输入 yes: ";
        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        fclose($handle);
        if ('yes' !== trim($line)) {
            echo "已取消。\n";

            exit(0);
        }
    }

    private function error($msg)
    {
        fwrite(STDERR, "错误: {$msg}\n");

        exit(1);
    }
}

$toolkit = new ZbaToolkit($argv);
$toolkit->run();
