<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

date_default_timezone_set('UTC');

require dirname(__DIR__) . '/zb_system/function/c_system_base.php';
require dirname(__DIR__) . '/zb_system/function/c_system_admin.php';
require __DIR__ . '/install.php';

$zbp->LoadLanguage('system', '', 'zh-cn');
$zbp->LoadLanguage('zb_install', 'zb_install', 'zh-cn');

$options = getopt('', [
    'help',
    'db-type:',
    'db-server::',
    'db-port::',
    'db-name:',
    'db-user::',
    'db-password::',
    'db-prefix::',
    'db-engine::',
    'site-name:',
    'admin-user:',
    'admin-password::',
    'theme::',
]);

if (isset($options['help'])) {
    ZbpInstallerCli::PrintHelp();
    exit(0);
}

try {
    $options = ZbpInstaller::Normalize([
        'db_type' => $options['db-type'] ?? '',
        'db_server' => $options['db-server'] ?? '',
        'db_port' => (int) ($options['db-port'] ?? 0),
        'db_name' => $options['db-name'] ?? '',
        'db_user' => $options['db-user'] ?? '',
        'db_password' => $options['db-password'] ?? '',
        'db_prefix' => $options['db-prefix'] ?? 'zbp_',
        'db_engine' => $options['db-engine'] ?? 'MyISAM',
        'site_name' => $options['site-name'] ?? '',
        'admin_user' => $options['admin-user'] ?? '',
        'admin_password' => $options['admin-password'] ?? '',
        'theme' => $options['theme'] ?? 'default|default',
    ]);
    $options['db_password'] = $options['db_password'] ?: getenv('ZBP_DB_PASSWORD');
    $options['admin_password'] = $options['admin_password'] ?: getenv('ZBP_ADMIN_PASSWORD');
    if (!$options['admin_password']) {
        $options['admin_password'] = ZbpInstallerCli::ReadSecret('管理员密码: ');
    }

    $result = ZbpInstaller::Install($options);
    foreach ($result['messages'] as $message) {
        fwrite(STDOUT, $message . PHP_EOL);
    }
    if (!$result['success']) {
        fwrite(STDERR, '安装失败: ' . $result['error'] . PHP_EOL);
        exit(1);
    }
    fwrite(STDOUT, '安装成功。' . PHP_EOL);
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, '参数错误: ' . $exception->getMessage() . PHP_EOL);
    ZbpInstallerCli::PrintHelp(STDERR);
    exit(2);
}

class ZbpInstallerCli
{
    public static function PrintHelp($stream = STDOUT)
    {
        fwrite($stream, <<<HELP
Z-BlogPHP CLI 安装器

用法:
  php zb_install/cli.php --db-type=sqlite3 --db-name=zb_users/data/database.db \\
    --site-name="我的站点" --admin-user=admin

参数:
  --db-type        sqlite3、pdo_sqlite、mysqli、pdo_mysql、pgsql 或 pdo_postgresql
  --db-name        SQLite 文件路径或 MySQL/PostgreSQL 数据库名
  --db-server      数据库地址（SQLite 不需要）
  --db-port        数据库端口
  --db-user        数据库用户名
  --db-password    数据库密码，也可使用 ZBP_DB_PASSWORD
  --db-prefix      数据表前缀，默认 zbp_
  --db-engine      MySQL 存储引擎，默认 MyISAM
  --site-name      网站名称
  --admin-user     管理员账号
  --admin-password 管理员密码，也可使用 ZBP_ADMIN_PASSWORD
  --theme          主题和样式，例如 default|default
  --help           显示帮助

HELP);
    }

    public static function ReadSecret($prompt)
    {
        fwrite(STDOUT, $prompt);
        $command = 'stty -g';
        $state = @shell_exec($command);
        if ($state) {
            shell_exec('stty -echo');
            $password = fgets(STDIN);
            shell_exec('stty ' . trim($state));
            fwrite(STDOUT, PHP_EOL);

            return trim($password);
        }

        return trim((string) fgets(STDIN));
    }
}
