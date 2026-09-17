<?php
/**
 * Z-BlogPHP ZBA 打包工具 (CLI & GitHub Action 版).
 *
 * 用法:
 *   php pack_zba.php <应用目录路径> [选项]
 *
 * 选项:
 *   -o, --output <路径>   指定输出 .zba 文件路径 (默认: <应用ID>_<版本号>_<修改时间>.zba)
 *   --no-gzip            不进行 gzip 压缩 (默认输出 gzip 压缩的 .zba)
 *   -v, --verbose        显示详细打包文件列表
 *   -h, --help           显示使用说明
 */
if ('cli' !== php_sapi_name() && empty($_SERVER['argv'])) {
    exit("此脚本仅能在 CLI 命令行环境下运行。\n");
}

class ZbaPacker
{
    private $targetDir;
    private $outputPath;
    private $gzip = true;
    private $verbose = false;

    private $appId = '';
    private $appType = '';
    private $appData = [];

    private $ignoreFiles = [
        '.git',
        '.github',
        '.history',
        '.idea',
        '.vscode',
        '.svn',
        '.hg',
        '.DS_Store',
        'Thumbs.db',
        'app_update.lock',
        '.gitignore',
        '.gitattributes',
        'zbignore.txt',
        '*.zba',
    ];

    private $dirs = [];
    private $files = [];

    public function __construct(array $argv)
    {
        $this->parseArgs($argv);
    }

    public function run()
    {
        $this->loadAppXml();
        $this->loadIgnoreRules();
        $this->scanDirectory($this->targetDir);
        $xmlContent = $this->buildXml();

        if ($this->gzip && function_exists('gzencode')) {
            $packData = gzencode($xmlContent, 9, FORCE_GZIP);
        } else {
            $packData = $xmlContent;
        }

        if (empty($this->outputPath)) {
            $version = (string) ($this->appData['version'] ?? '1.0');
            $modified = (string) ($this->appData['modified'] ?? date('Ymd'));
            $filename = "{$this->appId}_{$version}_{$modified}.zba";
            $this->outputPath = getcwd() . DIRECTORY_SEPARATOR . $filename;
        }

        $outDir = dirname($this->outputPath);
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0755, true);
        }

        if (false === file_put_contents($this->outputPath, $packData)) {
            $this->error("错误: 无法写入目标文件 {$this->outputPath}");
        }

        $size = filesize($this->outputPath);
        $sizeStr = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : ($size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B');

        $this->exportGithubOutputs();

        echo "========================================\n";
        echo "打包成功!\n";
        echo "应用 ID   : {$this->appId}\n";
        echo "应用类型 : {$this->appType}\n";
        echo '应用名称 : ' . ($this->appData['name'] ?? '') . "\n";
        echo '版本号   : ' . ($this->appData['version'] ?? '') . "\n";
        echo '包含目录 : ' . count($this->dirs) . " 个\n";
        echo '包含文件 : ' . count($this->files) . " 个\n";
        echo '压缩模式 : ' . ($this->gzip ? 'Gzip 压缩' : '未压缩 (XML)') . "\n";
        echo "输出文件 : {$this->outputPath} ({$sizeStr})\n";
        echo "========================================\n";
    }

    private function exportGithubOutputs()
    {
        $githubOutput = getenv('GITHUB_OUTPUT');
        if ($githubOutput && is_file($githubOutput)) {
            $outputs = [
                'zba-path=' . $this->outputPath,
                'zba-name=' . basename($this->outputPath),
                'app-id=' . $this->appId,
                'app-name=' . ($this->appData['name'] ?? ''),
                'app-version=' . ($this->appData['version'] ?? ''),
                'app-type=' . $this->appType,
                'app-modified=' . ($this->appData['modified'] ?? ''),
            ];
            file_put_contents($githubOutput, implode("\n", $outputs) . "\n", FILE_APPEND);
        }
    }

    private function showHelp()
    {
        echo <<<'HELP'
Z-BlogPHP ZBA 打包工具 (CLI)

使用方法:
  php pack_zba.php <应用目录路径> [选项]

选项:
  -o, --output <文件路径>  指定输出 .zba 目标路径
  --no-gzip               不使用 gzip 压缩 (输出纯 XML)
  -v, --verbose           显示打包过程文件明细
  -h, --help              显示帮助信息

示例:
  php pack_zba.php /path/to/plugin_dir
  php pack_zba.php /path/to/plugin_dir -o /tmp/my_plugin.zba
  php pack_zba.php /path/to/theme_dir --no-gzip

HELP;
    }

    private function parseArgs(array $argv)
    {
        array_shift($argv); // 移除脚本名称

        if (empty($argv)) {
            $this->showHelp();

            exit(0);
        }

        $positional = [];
        $count = count($argv);
        for ($i = 0; $i < $count; ++$i) {
            $arg = $argv[$i];

            if ('-h' === $arg || '--help' === $arg) {
                $this->showHelp();

                exit(0);
            }
            if ('--no-gzip' === $arg) {
                $this->gzip = false;
            } elseif ('-v' === $arg || '--verbose' === $arg) {
                $this->verbose = true;
            } elseif ('-o' === $arg || '--output' === $arg) {
                if (!isset($argv[$i + 1])) {
                    $this->error("错误: 参数 {$arg} 必须指定输出路径。");
                }
                $this->outputPath = $argv[++$i];
            } else {
                if (0 === strpos($arg, '-')) {
                    $this->error("未知参数: {$arg}");
                }
                $positional[] = $arg;
            }
        }

        if (empty($positional)) {
            $this->error('错误: 请指定需要打包的应用文件夹路径。');
        }

        $this->targetDir = rtrim(realpath($positional[0]), '/\\');
        if (!$this->targetDir || !is_dir($this->targetDir)) {
            $this->error("错误: 目标路径不存在或非有效的文件夹: {$positional[0]}");
        }
    }

    private function loadAppXml()
    {
        $pluginXml = $this->targetDir . DIRECTORY_SEPARATOR . 'plugin.xml';
        $themeXml = $this->targetDir . DIRECTORY_SEPARATOR . 'theme.xml';

        if (is_file($pluginXml)) {
            $xmlPath = $pluginXml;
            $this->appType = 'plugin';
        } elseif (is_file($themeXml)) {
            $xmlPath = $themeXml;
            $this->appType = 'theme';
        } else {
            $this->error("错误: 未在 '{$this->targetDir}' 目录找到 plugin.xml 或 theme.xml");
        }

        $content = file_get_contents($xmlPath);
        $this->appData = $this->parseXmlString($content);
        $this->appId = trim($this->appData['id'] ?? '');

        if (empty($this->appId)) {
            $this->error('错误: XML 文件中未找到有效的 <id> 节点。');
        }
    }

    private function parseXmlString($content)
    {
        if (function_exists('simplexml_load_string')) {
            $xml = @simplexml_load_string($content);
            if ($xml) {
                return json_decode(json_encode($xml), true);
            }
        }

        $data = [];
        $tags = [
            'id', 'name', 'url', 'note', 'description',
            'path', 'include', 'level', 'adapted', 'version',
            'pubdate', 'modified', 'price', 'phpver',
        ];

        foreach ($tags as $tag) {
            if (preg_match("#<{$tag}>(.*?)</{$tag}>#is", $content, $m)) {
                $data[$tag] = htmlspecialchars_decode(trim($m[1]));
            } else {
                $data[$tag] = '';
            }
        }

        if (preg_match('#<author>(.*?)</author>#is', $content, $mAuthor)) {
            foreach (['name', 'email', 'url'] as $sub) {
                if (preg_match("#<{$sub}>(.*?)</{$sub}>#is", $mAuthor[1], $mSub)) {
                    $data['author'][$sub] = htmlspecialchars_decode(trim($mSub[1]));
                }
            }
        }

        if (preg_match('#<source>(.*?)</source>#is', $content, $mSource)) {
            foreach (['name', 'email', 'url'] as $sub) {
                if (preg_match("#<{$sub}>(.*?)</{$sub}>#is", $mSource[1], $mSub)) {
                    $data['source'][$sub] = htmlspecialchars_decode(trim($mSub[1]));
                }
            }
        }

        if (preg_match('#<advanced>(.*?)</advanced>#is', $content, $mAdv)) {
            foreach (['dependency', 'rewritefunctions', 'existsfunctions', 'conflict'] as $sub) {
                if (preg_match("#<{$sub}>(.*?)</{$sub}>#is", $mAdv[1], $mSub)) {
                    $data['advanced'][$sub] = htmlspecialchars_decode(trim($mSub[1]));
                }
            }
        }

        if (preg_match('#<sidebars>(.*?)</sidebars>#is', $content, $mSide)) {
            for ($i = 1; $i <= 9; ++$i) {
                $sub = "sidebar{$i}";
                if (preg_match("#<{$sub}>(.*?)</{$sub}>#is", $mSide[1], $mSub)) {
                    $data['sidebars'][$sub] = htmlspecialchars_decode(trim($mSub[1]));
                }
            }
        }

        return $data;
    }

    private function loadIgnoreRules()
    {
        $ignoreFile = $this->targetDir . DIRECTORY_SEPARATOR . 'zbignore.txt';
        if (is_file($ignoreFile)) {
            $lines = explode("\n", str_replace("\r", "\n", file_get_contents($ignoreFile)));
            foreach ($lines as $line) {
                $line = trim($line);
                if ('' !== $line && 0 !== strpos($line, '#')) {
                    $this->ignoreFiles[] = $line;
                }
            }
        }
        $this->ignoreFiles = array_unique($this->ignoreFiles);
    }

    private function scanDirectory($dir)
    {
        $items = @scandir($dir);
        if (false === $items) {
            return;
        }

        foreach ($items as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
            $relativePath = ltrim(str_replace('\\', '/', substr($fullPath, strlen($this->targetDir))), '/');

            if ($this->isIgnored($relativePath, $item)) {
                continue;
            }

            if (is_dir($fullPath)) {
                $this->dirs[] = $relativePath . '/';
                $this->scanDirectory($fullPath);
            } else {
                $this->files[] = $relativePath;
            }
        }
    }

    private function isIgnored($relativePath, $fileName)
    {
        foreach ($this->ignoreFiles as $pattern) {
            if (empty($pattern)) {
                continue;
            }
            if (fnmatch($pattern, $fileName) || fnmatch($pattern, $relativePath) || fnmatch("*/{$pattern}", $relativePath)) {
                return true;
            }
            if (0 === strpos($relativePath, trim($pattern, '/'))) {
                return true;
            }
        }

        return false;
    }

    private function removeBom($str)
    {
        if ("\xEF\xBB\xBF" === substr($str, 0, 3)) {
            return substr($str, 3);
        }

        return $str;
    }

    private function getValue($key, $default = '')
    {
        $val = $this->appData[$key] ?? $default;
        if (is_array($val)) {
            return '';
        }

        return (string) $val;
    }

    private function getNestedValue(array $arr, $key, $default = '')
    {
        $val = $arr[$key] ?? $default;
        if (is_array($val)) {
            return '';
        }

        return (string) $val;
    }

    private function buildXml()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<app version="php" type="' . htmlspecialchars($this->appType) . '">';

        $fields = [
            'id', 'name', 'url', 'note', 'description',
            'path', 'include', 'level',
        ];
        foreach ($fields as $field) {
            $val = $this->getValue($field);
            $xml .= "<{$field}>" . htmlspecialchars($val) . "</{$field}>";
        }

        // author
        $author = is_array($this->appData['author'] ?? null) ? $this->appData['author'] : [];
        $xml .= '<author>';
        $xml .= '<name>' . htmlspecialchars($this->getNestedValue($author, 'name')) . '</name>';
        $xml .= '<email>' . htmlspecialchars($this->getNestedValue($author, 'email')) . '</email>';
        $xml .= '<url>' . htmlspecialchars($this->getNestedValue($author, 'url')) . '</url>';
        $xml .= '</author>';

        // source
        $source = is_array($this->appData['source'] ?? null) ? $this->appData['source'] : [];
        $xml .= '<source>';
        $xml .= '<name>' . htmlspecialchars($this->getNestedValue($source, 'name')) . '</name>';
        $xml .= '<email>' . htmlspecialchars($this->getNestedValue($source, 'email')) . '</email>';
        $xml .= '<url>' . htmlspecialchars($this->getNestedValue($source, 'url')) . '</url>';
        $xml .= '</source>';

        $metaFields = ['adapted', 'version', 'pubdate', 'modified', 'price'];
        foreach ($metaFields as $field) {
            $val = $this->getValue($field);
            $xml .= "<{$field}>" . htmlspecialchars($val) . "</{$field}>";
        }

        $phpver = $this->getValue('phpver', '5.2');
        $xml .= '<phpver>' . htmlspecialchars('' !== $phpver ? $phpver : '5.2') . '</phpver>';

        // advanced
        $advanced = is_array($this->appData['advanced'] ?? null) ? $this->appData['advanced'] : [];
        $xml .= '<advanced>';
        $xml .= '<dependency>' . htmlspecialchars($this->getNestedValue($advanced, 'dependency')) . '</dependency>';
        $xml .= '<rewritefunctions>' . htmlspecialchars($this->getNestedValue($advanced, 'rewritefunctions')) . '</rewritefunctions>';
        $xml .= '<existsfunctions>' . htmlspecialchars($this->getNestedValue($advanced, 'existsfunctions')) . '</existsfunctions>' . "\r\n";
        $xml .= '<conflict>' . htmlspecialchars($this->getNestedValue($advanced, 'conflict')) . '</conflict>';
        $xml .= '</advanced>';

        // sidebars
        $sidebars = is_array($this->appData['sidebars'] ?? null) ? $this->appData['sidebars'] : [];
        $xml .= '<sidebars>';
        for ($i = 1; $i <= 9; ++$i) {
            $key = "sidebar{$i}";
            $val = $this->getNestedValue($sidebars, $key);
            $xml .= "<{$key}>" . htmlspecialchars($val) . "</{$key}>";
        }
        $xml .= '</sidebars>' . "\n";

        // folders
        foreach ($this->dirs as $dirPath) {
            $d = $this->appId . '/' . $dirPath;
            $xml .= '<folder><path>' . htmlspecialchars($d) . '</path></folder>' . "\n";
            if ($this->verbose) {
                echo "[目录] {$dirPath}\n";
            }
        }

        // files
        foreach ($this->files as $filePath) {
            $fullPath = $this->targetDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            $content = file_get_contents($fullPath);
            if ('php' === $ext || 'inc' === $ext) {
                $content = $this->removeBom($content);
            }

            $encoded = base64_encode($content);
            $d = $this->appId . '/' . $filePath;

            $xml .= '<file><path>' . htmlspecialchars($d) . '</path><stream>' . $encoded . '</stream></file>' . "\n";
            if ($this->verbose) {
                echo "[文件] {$filePath}\n";
            }
        }

        $xml .= '<verify>' . base64_encode('') . '</verify>';
        $xml .= '</app>';

        return $xml;
    }

    private function error($msg)
    {
        fwrite(STDERR, $msg . "\n");

        exit(1);
    }
}

// 运行打包器
$packer = new ZbaPacker($argv);
$packer->run();
