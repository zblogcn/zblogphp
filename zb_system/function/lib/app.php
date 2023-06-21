<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * App 应用类.
 */
class App
{

    /**
     * @var string 应用类型，'plugin'表示插件，'theme'表示主题
     */
    public $type = '';

    /**
     * @var string 应用ID,必须以应用文件目录为ID
     */
    public $id;

    /**
     * @var string 应用名
     */
    public $name;

    /**
     * @var string 应用发布链接
     */
    public $url;

    /**
     * @var string 应用说明
     */
    public $note;

    /**
     * @var string 应用详细信息
     */
    public $description;

    /**
     * @var string 管理页面路径
     */
    public $path;

    /**
     * @var string include文件
     */
    public $include;

    /**
     * @var int 应用权限等级
     */
    public $level;

    /**
     * @var string 应用作者
     */
    public $author_name;

    /**
     * @var string 作者邮箱
     */
    public $author_email;

    /**
     * @var string 作者链接
     */
    public $author_url;

    /**
     * @var string 原作者名
     */
    public $source_name;

    /**
     * @var string 原作者邮箱
     */
    public $source_email;

    /**
     * @var string 原作者链接
     */
    public $source_url;

    /**
     * @var string 适用版本
     */
    public $adapted;

    /**
     * @var string 版本号
     */
    public $version;

    /**
     * @var string 发布时间
     */
    public $pubdate;

    /**
     * @var string 最后更新时间
     */
    public $modified;

    /**
     * @var string 应用价格
     */
    public $price;

    /**
     * @var string 高级选项：依赖插件列表（以|分隔）
     */
    public $advanced_dependency;

    /**
     * @var string 高级选项：重写函数列表（以|分隔）
     */
    public $advanced_rewritefunctions;

    /**
     * @var string 高级选项：必须函数列表（以|分隔）
     */
    public $advanced_existsfunctions;

    /**
     * @var string 高级选项：冲突插件列表（以|分隔）
     */
    public $advanced_conflict;

    /**
     * @var string 设置主题侧栏1
     */
    public $sidebars_sidebar1;

    /**
     * @var string 定义主题侧栏2
     */
    public $sidebars_sidebar2;

    /**
     * @var string 设置主题侧栏3
     */
    public $sidebars_sidebar3;

    /**
     * @var string 设置主题侧栏4
     */
    public $sidebars_sidebar4;

    /**
     * @var string 设置主题侧栏5
     */
    public $sidebars_sidebar5;

    /**
     * @var string 设置主题侧栏6
     */
    public $sidebars_sidebar6;

    /**
     * @var string 定义主题侧栏7
     */
    public $sidebars_sidebar7;

    /**
     * @var string 设置主题侧栏8
     */
    public $sidebars_sidebar8;

    /**
     * @var string 设置主题侧栏9
     */
    public $sidebars_sidebar9;

    /**
     * @var string PHP最低版本
     */
    public $phpver;

    /**
     * @var array 禁止打包文件glob
     */
    public $ignore_files = array('.gitignore', '.DS_Store', 'Thumbs.db', 'composer.lock', 'zbignore.txt', '*.code-workspace');

    /**
     * @var bool 加载xml成功否
     */
    public $isloaded = false;

    /**
     * @var string 当前样式表的crc32
     */
    public $css_crc32 = '';

    /**
     * @var string 静态访法返回unpack后的错误文件数
     */
    public static $check_error_count = 0;

    /**
     * @var string 静态访法返回unpack成功后的app
     */
    public static $unpack_app = null;

    public function __get($key)
    {
        global $zbp;
        if ($key === 'app_path') {
            $appDirectory = $zbp->usersdir . FormatString($this->type, '[filename]');
            $appDirectory .= '/' . FormatString($this->id, '[filename]') . '/';

            return $appDirectory;
        } elseif ($key === 'app_url') {
            return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/';
        }

        return '';
    }

    /**
     * 得到详细信息数组.
     *
     * @return array
     */
    public function GetInfoArray()
    {
        return get_object_vars($this);
    }

    /**
     * 是否可删除.
     *
     * @return bool
     */
    public function CanDel()
    {
        global $zbp;

        return !array_key_exists($this->id, $zbp->activedapps);
    }

    /**
     * 是否带管理页面.
     *
     * @return bool
     */
    public function CanManage()
    {
        if ($this->path) {
            return true;
        }

        return false;
    }

    /**
     * 是否正在使用.
     *
     * @return bool
     */
    public function IsUsed()
    {
        global $zbp;

        return $zbp->CheckPlugin($this->id);
    }

    /**
     * 是否附带主题插件（针对主题应用）.
     *
     * @return bool
     */
    public function HasPlugin()
    {
        if ($this->path || $this->include) {
            return true;
        }

        return false;
    }

    /**
     * 获取应用ID的crc32Hash值
     *
     * @return string
     */
    public function GetHash()
    {
        global $zbp;

        return crc32($this->id);
    }

    /**
     * 获取应用管理页面链接.
     *
     * @return string
     */
    public function GetManageUrl()
    {
        global $zbp;

        return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/' . $this->path;
    }

    /**
     * 获取应用目录地址
     *
     * @return string
     */
    public function GetDir()
    {
        return $this->app_path;
    }

    /**
     * 获取应用Logo图片地址(优先Logo)
     *
     * @return string
     */
    public function GetLogo()
    {
        if ($this->type == 'plugin') {
            return $this->app_url . 'logo.png';
        } elseif (is_readable($this->app_path . 'logo.png')) {
            return $this->app_url . 'logo.png';
        } else {
            return $this->app_url . 'screenshot.png';
        }
    }

    /**
     * 获取应用截图地址
     *
     * @return string
     */
    public function GetScreenshot()
    {
        return $this->app_url . 'screenshot.png';
    }

    /**
     * 获取应用（主题）样式文件列表.
     *
     * @return array
     */
    public function GetCssFiles()
    {
        $dir = $this->app_path . 'style/';

        $array = GetFilesInDir($dir, 'css');
        if (isset($array['default'])) {
            $a = array('default' => $array['default']);
            unset($array['default']);
            $array = array_merge($a, $array);
        }
        if (isset($array['style'])) {
            $a = array('style' => $array['style']);
            unset($array['style']);
            $array = array_merge($a, $array);
        }

        return $array;
    }

    /**
     * 载入应用xml中的信息.
     *
     * @param string $type 应用类型
     * @param string $id   应用ID
     *
     * @return bool
     */
    public function LoadInfoByXml($type, $id)
    {
        global $zbp;

        $this->id = $id;
        $this->type = $type;
        $xmlPath = $this->app_path . FormatString($type, '[filename]') . '.xml';
        $this->isloaded = false;

        if (!is_readable($xmlPath)) {
            return false;
        }

        $content = file_get_contents($xmlPath);
        $xml = @simplexml_load_string($content);
        if (!$xml) {
            return false;
        }

        $appver = $xml->attributes();
        if ((string) $appver->version !== 'php') {
            return false;
        }

        $this->type = $type;

        $this->id = (string) $xml->id;
        $this->name = (string) $xml->name;

        $this->url = (string) $xml->url;
        $this->note = (string) $xml->note;

        $this->path = (string) $xml->path;
        $this->include = (string) $xml->include;
        $this->level = (string) $xml->level;

        $this->author_name = (string) $xml->author->name;
        $this->author_email = (string) $xml->author->email;
        $this->author_url = (string) $xml->author->url;

        $this->source_name = (string) $xml->source->name;
        $this->source_email = (string) $xml->source->email;
        $this->source_url = (string) $xml->source->url;

        $this->adapted = (string) $xml->adapted;
        $this->version = (string) $xml->version;
        $this->pubdate = (string) $xml->pubdate;
        $this->modified = (string) $xml->modified;
        $this->description = (string) $xml->description;
        $this->price = (string) $xml->price;
        if (empty($xml->phpver)) {
            $this->phpver = '5.2';
        } else {
            $this->phpver = (string) $xml->phpver;
        }
        $this->advanced_dependency = (string) $xml->advanced->dependency;
        $this->advanced_rewritefunctions = (string) $xml->advanced->rewritefunctions;
        $this->advanced_existsfunctions = (string) $xml->advanced->existsfunctions;
        $this->advanced_conflict = (string) $xml->advanced->conflict;

        $this->sidebars_sidebar1 = (string) $xml->sidebars->sidebar1;
        $this->sidebars_sidebar2 = (string) $xml->sidebars->sidebar2;
        $this->sidebars_sidebar3 = (string) $xml->sidebars->sidebar3;
        $this->sidebars_sidebar4 = (string) $xml->sidebars->sidebar4;
        $this->sidebars_sidebar5 = (string) $xml->sidebars->sidebar5;
        $this->sidebars_sidebar6 = (string) $xml->sidebars->sidebar6;
        $this->sidebars_sidebar7 = (string) $xml->sidebars->sidebar7;
        $this->sidebars_sidebar8 = (string) $xml->sidebars->sidebar8;
        $this->sidebars_sidebar9 = (string) $xml->sidebars->sidebar9;

        $appIgnorePath = $this->app_path . 'zbignore.txt';
        $appIgnores = array();
        if (is_readable($appIgnorePath)) {
            $appIgnores = explode("\n", str_replace("\r", "\n", trim(file_get_contents($appIgnorePath))));
        }
        foreach ($appIgnores as $key => $value) {
            if (!empty($value)) {
                $this->ignore_files[] = $value;
            }
        }
        $this->ignore_files = array_unique($this->ignore_files);


        $stylecss_file = $this->app_path . 'style/' . $zbp->style . '.css';
        if (is_readable($stylecss_file)) {
            $this->css_crc32 = crc32(file_get_contents($stylecss_file));
        }

        $this->isloaded = true;

        return true;
    }

    /**
     * 保存应用信息到xml文件.
     *
     * @return bool
     */
    public function SaveInfoByXml()
    {
        global $zbp;
        $s = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
        $s .= '<' . $this->type . ' version="php">' . "\r\n";

        $s .= '<id>' . htmlspecialchars($this->id) . '</id>' . "\r\n";
        $s .= '<name>' . htmlspecialchars($this->name) . '</name>' . "\r\n";
        $s .= '<url>' . htmlspecialchars($this->url) . '</url>' . "\r\n";
        $s .= '<note>' . htmlspecialchars($this->note) . '</note>' . "\r\n";
        $s .= '<description>' . htmlspecialchars($this->description) . '</description>' . "\r\n";

        $s .= '<path>' . htmlspecialchars($this->path) . '</path>' . "\r\n";
        $s .= '<include>' . htmlspecialchars($this->include) . '</include>' . "\r\n";
        $s .= '<level>' . htmlspecialchars($this->level) . '</level>' . "\r\n";

        $s .= '<author>' . "\r\n";
        $s .= '  <name>' . htmlspecialchars($this->author_name) . '</name>' . "\r\n";
        $s .= '  <email>' . htmlspecialchars($this->author_email) . '</email>' . "\r\n";
        $s .= '  <url>' . htmlspecialchars($this->author_url) . '</url>' . "\r\n";
        $s .= '</author>' . "\r\n";

        $s .= '<source>' . "\r\n";
        $s .= '  <name>' . htmlspecialchars($this->source_name) . '</name>' . "\r\n";
        $s .= '  <email>' . htmlspecialchars($this->source_email) . '</email>' . "\r\n";
        $s .= '  <url>' . htmlspecialchars($this->source_url) . '</url>' . "\r\n";
        $s .= '</source>' . "\r\n";

        $s .= '<adapted>' . htmlspecialchars($this->adapted) . '</adapted>' . "\r\n";
        $s .= '<version>' . htmlspecialchars($this->version) . '</version>' . "\r\n";
        $s .= '<pubdate>' . htmlspecialchars($this->pubdate) . '</pubdate>' . "\r\n";
        $s .= '<modified>' . htmlspecialchars($this->modified) . '</modified>' . "\r\n";
        $s .= '<price>' . htmlspecialchars($this->price) . '</price>' . "\r\n";
        $s .= '<phpver>' . htmlspecialchars($this->phpver) . '</phpver>' . "\r\n";

        $s .= '<advanced>' . "\r\n";
        $s .= '  <dependency>' . htmlspecialchars($this->advanced_dependency) . '</dependency>' . "\r\n";
        $s .= '  <rewritefunctions>' . htmlspecialchars($this->advanced_rewritefunctions) . '</rewritefunctions>' . "\r\n";
        $s .= '  <existsfunctions>' . htmlspecialchars($this->advanced_existsfunctions) . '</existsfunctions>' . "\r\n";
        $s .= '  <conflict>' . htmlspecialchars($this->advanced_conflict) . '</conflict>' . "\r\n";
        $s .= '</advanced>' . "\r\n";

        $s .= '<sidebars>' . "\r\n";
        $s .= '  <sidebar1>' . htmlspecialchars($this->sidebars_sidebar1) . '</sidebar1>' . "\r\n";
        $s .= '  <sidebar2>' . htmlspecialchars($this->sidebars_sidebar2) . '</sidebar2>' . "\r\n";
        $s .= '  <sidebar3>' . htmlspecialchars($this->sidebars_sidebar3) . '</sidebar3>' . "\r\n";
        $s .= '  <sidebar4>' . htmlspecialchars($this->sidebars_sidebar4) . '</sidebar4>' . "\r\n";
        $s .= '  <sidebar5>' . htmlspecialchars($this->sidebars_sidebar5) . '</sidebar5>' . "\r\n";
        $s .= '  <sidebar6>' . htmlspecialchars($this->sidebars_sidebar6) . '</sidebar6>' . "\r\n";
        $s .= '  <sidebar7>' . htmlspecialchars($this->sidebars_sidebar7) . '</sidebar7>' . "\r\n";
        $s .= '  <sidebar8>' . htmlspecialchars($this->sidebars_sidebar8) . '</sidebar8>' . "\r\n";
        $s .= '  <sidebar9>' . htmlspecialchars($this->sidebars_sidebar9) . '</sidebar9>' . "\r\n";
        $s .= '</sidebars>' . "\r\n";

        $s .= '</' . $this->type . '>';

        $path = $this->app_path . $this->type . '.xml';

        @file_put_contents($path, $s);

        return true;
    }

    /**
     * @var array 所有目录列表
     * @private
     */
    private $dirs = array();

    /**
     * @var array 所有文件列表
     * @private
     */
    private $files = array();

    /**
     * @param string $dir 获取所有目录及文件列表
     * @private
     */
    private function GetAllFileDir($dir)
    {
        foreach (scandir($dir) as $d) {
            if (is_dir($dir . $d)) {
                if ((substr($d, 0, 1) != '.')
                    && !($d == 'compile' && $this->type == 'theme')
                ) {
                    $this->GetAllFileDir($dir . $d . '/');
                    $this->dirs[] = $dir . $d . '/';
                }
            } else {
                $this->files[] = $dir . $d;
            }
        }
    }

    /**
     * 应用打包.
     *
     * @return string
     */
    public function Pack()
    {
        global $zbp;
        $this->dirs = array();
        $this->files = array();

        $dir = $this->app_path;
        $this->GetAllFileDir($dir);
        foreach ($this->dirs as $key => $value) {
            $this->dirs[$key] = str_ireplace('\\', '/', $this->dirs[$key]);
        }
        foreach ($this->files as $key => $value) {
            $this->files[$key] = str_ireplace('\\', '/', $this->files[$key]);
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_App_Pack'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $this->dirs, $this->files);
        }

        $s = '<?xml version="1.0" encoding="utf-8"?>';
        $s .= '<app version="php" type="' . $this->type . '">';

        $s .= '<id>' . htmlspecialchars($this->id) . '</id>';
        $s .= '<name>' . htmlspecialchars($this->name) . '</name>';
        $s .= '<url>' . htmlspecialchars($this->url) . '</url>';
        $s .= '<note>' . htmlspecialchars($this->note) . '</note>';
        $s .= '<description>' . htmlspecialchars($this->description) . '</description>';

        $s .= '<path>' . htmlspecialchars($this->path) . '</path>';
        $s .= '<include>' . htmlspecialchars($this->include) . '</include>';
        $s .= '<level>' . htmlspecialchars($this->level) . '</level>';

        $s .= '<author>';
        $s .= '<name>' . htmlspecialchars($this->author_name) . '</name>';
        $s .= '<email>' . htmlspecialchars($this->author_email) . '</email>';
        $s .= '<url>' . htmlspecialchars($this->author_url) . '</url>';
        $s .= '</author>';

        $s .= '<source>';
        $s .= '<name>' . htmlspecialchars($this->source_name) . '</name>';
        $s .= '<email>' . htmlspecialchars($this->source_email) . '</email>';
        $s .= '<url>' . htmlspecialchars($this->source_url) . '</url>';
        $s .= '</source>';

        $s .= '<adapted>' . htmlspecialchars($this->adapted) . '</adapted>';
        $s .= '<version>' . htmlspecialchars($this->version) . '</version>';
        $s .= '<pubdate>' . htmlspecialchars($this->pubdate) . '</pubdate>';
        $s .= '<modified>' . htmlspecialchars($this->modified) . '</modified>';
        $s .= '<price>' . htmlspecialchars($this->price) . '</price>';
        $s .= '<phpver>' . htmlspecialchars($this->phpver) . '</phpver>';

        $s .= '<advanced>';
        $s .= '<dependency>' . htmlspecialchars($this->advanced_dependency) . '</dependency>';
        $s .= '<rewritefunctions>' . htmlspecialchars($this->advanced_rewritefunctions) . '</rewritefunctions>';
        $s .= '<existsfunctions>' . htmlspecialchars($this->advanced_existsfunctions) . '</existsfunctions>' . "\r\n";
        $s .= '<conflict>' . htmlspecialchars($this->advanced_conflict) . '</conflict>';
        $s .= '</advanced>';

        $s .= '<sidebars>';
        $s .= '<sidebar1>' . htmlspecialchars($this->sidebars_sidebar1) . '</sidebar1>';
        $s .= '<sidebar2>' . htmlspecialchars($this->sidebars_sidebar2) . '</sidebar2>';
        $s .= '<sidebar3>' . htmlspecialchars($this->sidebars_sidebar3) . '</sidebar3>';
        $s .= '<sidebar4>' . htmlspecialchars($this->sidebars_sidebar4) . '</sidebar4>';
        $s .= '<sidebar5>' . htmlspecialchars($this->sidebars_sidebar5) . '</sidebar5>';
        $s .= '<sidebar6>' . htmlspecialchars($this->sidebars_sidebar6) . '</sidebar6>';
        $s .= '<sidebar7>' . htmlspecialchars($this->sidebars_sidebar7) . '</sidebar7>';
        $s .= '<sidebar8>' . htmlspecialchars($this->sidebars_sidebar8) . '</sidebar8>';
        $s .= '<sidebar9>' . htmlspecialchars($this->sidebars_sidebar9) . '</sidebar9>';
        $s .= '</sidebars>';
        $s .= "\n";

        foreach ($this->ignore_files as $glob) {
            if (is_dir($d = $this->app_path . $glob)) {
                $this->ignored_dirs[crc32($d)] = rtrim($d, '/') . '/';
            }
        }
        foreach ($this->dirs as $key => $value) {
            if ($this->IsPathIgnored($value)) {
                continue;
            }
            $value = str_replace($dir, '', $value);
            $value = preg_replace('/[^(\x20-\x7F)]*/', '', $value);
            $d = $this->id . '/' . $value;
            $s .= '<folder><path>' . htmlspecialchars($d) . '</path></folder>';
            $s .= "\n";
        }
        foreach ($this->files as $key => $value) {
            if ($this->IsPathIgnored($value)) {
                continue;
            }
            $d = $this->id . '/' . str_replace($dir, '', $value);
            $ext = pathinfo($value, PATHINFO_EXTENSION);
            if ($ext == 'php' || $ext == 'inc') {
                $c = base64_encode(RemoveBOM(file_get_contents($value)));
            } else {
                $c = base64_encode(file_get_contents($value));
            }
            if (IS_WINDOWS) {
                $d = iconv($zbp->lang['windows_character_set'], 'UTF-8//IGNORE', $d);
            }

            $s .= '<file><path>' . htmlspecialchars($d) . '</path><stream>' . $c . '</stream></file>';
            $s .= "\n";
        }

        $s .= '<verify>' . base64_encode($zbp->host . "\n" . $zbp->path) . '</verify>';

        $s .= '</app>';

        return $s;
    }

    public function PackGZip()
    {
        return gzencode($this->Pack(), 9, FORCE_GZIP);
    }

    private $ignored_dirs = array();

    private function IsPathIgnored($path)
    {
        $path = str_ireplace('\\', '/', $path);
        $appPath = str_ireplace('\\', '/', $this->app_path);
        $fileName = str_ireplace($appPath, '', $path);
        foreach ($this->ignore_files as $glob) {
            if (fnmatch($glob, $fileName)) {
                return true;
            }
            if (is_file($path)) {
                foreach ($this->ignored_dirs as $key => $value) {
                    if (stripos($path, $value) !== false) {
                        return true;
                    }
                }
            }
            if (is_dir($path) && is_dir($d = $appPath . $glob)) {
                $d = rtrim($d, '/') . '/';
                if (stripos($path, $d) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 解开应用包.
     *
     * @param $s
     *
     * @return bool
     */
    public static function UnPack($s)
    {
        global $zbp;
        $charset = array();
        $charset[1] = substr($s, 0, 1);
        $charset[2] = substr($s, 1, 1);
        if (ord($charset[1]) == 31 && ord($charset[2]) == 139) {
            $s = gzdecode($s);
        }

        $xml = @simplexml_load_string($s, 'SimpleXMLElement', (LIBXML_COMPACT | LIBXML_PARSEHUGE));
        if (!$xml) {
            return false;
        }

        if ($xml['version'] != 'php') {
            return false;
        }

        $type = $xml['type'];
        $id = $xml->id;
        $dir = $zbp->path . 'zb_users/' . $type . '/';

        ZbpErrorControl::SuspendErrorHook();

        self::$unpack_app = null;

        if (!file_exists($dir . $id . '/')) {
            @mkdir($dir . $id . '/', 0755, true);
        }

        foreach ($xml->folder as $folder) {
            $f = $dir . $folder->path;
            if (!file_exists($f)) {
                @mkdir($f, 0755, true);
            }
        }

        self::$check_error_count = 0;
        foreach ($xml->file as $file) {
            $s = base64_decode($file->stream);
            $f = $dir . $file->path;
            $f = str_replace('./', '', pathinfo($f, PATHINFO_DIRNAME)) . '/' . pathinfo($f, PATHINFO_BASENAME);
            @file_put_contents($f, $s);
            @chmod($f, 0755);

            $s2 = file_get_contents($f);
            if (md5($s) != md5($s2)) {
                self::$check_error_count = (self::$check_error_count + 1);
            }
        }

        self::$unpack_app = $zbp->LoadApp($type, $id);

        ZbpErrorControl::ResumeErrorHook();

        return true;
    }

    /**
     * @return true | Exception
     */
    public function CheckCompatibility()
    {
        global $zbp;

        if ((int) $this->adapted > (int) $zbp->version) {
            //$zbp->ShowError(str_replace('%s', $this->adapted, $zbp->lang['error'][78]), __FILE__, __LINE__);
            return new Exception(str_replace('%s', $this->adapted, $zbp->lang['error'][78]));
        }

        if (trim($this->phpver) == '') {
            $this->phpver = '5.2';
        }
        if (version_compare($this->phpver, GetPHPVersion()) > 0) {
            $zbp->ShowError(str_replace('%s', $this->phpver, $zbp->lang['error'][91]), __FILE__, __LINE__);
            return new Exception(str_replace('%s', $this->phpver, $zbp->lang['error'][91]));
        }

        $ae = explode('|', $this->advanced_existsfunctions);
        foreach ($ae as $e) {
            $e = trim($e);
            if (!$e) {
                continue;
            }

            if (!function_exists($e)) {
                //$zbp->ShowError(str_replace('%s', $e, $zbp->lang['error'][92]), __FILE__, __LINE__);
                return new Exception(str_replace('%s', $e, $zbp->lang['error'][92]));
            }
        }

        $ad = explode('|', $this->advanced_dependency);
        foreach ($ad as $d) {
            if (!$d) {
                continue;
            }

            if (!in_array($d, $zbp->activedapps)) {
                $d = '<a href="' . $zbp->host . 'zb_users/plugin/AppCentre/main.php?alias=' . $d . '">' . $d . '</a>';
                //$zbp->ShowError(str_replace('%s', $d, $zbp->lang['error'][83]), __FILE__, __LINE__);
                return new Exception(str_replace('%s', $d, $zbp->lang['error'][83]));
            }
        }

        $ac = explode('|', $this->advanced_conflict);
        foreach ($ac as $c) {
            if (!$c) {
                continue;
            }

            if (in_array($c, $zbp->activedapps)) {
                //$zbp->ShowError(str_replace('%s', $c, $zbp->lang['error'][85]), __FILE__, __LINE__);
                return new Exception(str_replace('%s', $c, $zbp->lang['error'][85]));
            }
        }

        return true;
    }

    /**
     * 从全局检查 依赖(关闭时) or 拒绝(开启时)
     * @param $action string (Enable|Disable)
     * @return true | Exception
     */
    public function CheckCompatibility_Global($action)
    {
        global $zbp;
        if ($action == 'Enable') {
            $apps = $zbp->LoadPlugins();
            $apps[] = $zbp->LoadApp('theme', $zbp->theme);
            foreach ($apps as $app) {
                if (!$zbp->CheckApp($app->id)) {
                    continue;
                }
                $conflictList = explode('|', $app->advanced_conflict);
                foreach ($conflictList as $conflict) {
                    if ($conflict == $this->id) {
                        //$zbp->ShowError(str_replace('%s', ' <b>' . $app->name . '</b> ', $zbp->lang['error'][85]), __FILE__, __LINE__);
                        return new Exception(str_replace('%s', ' <b>' . $app->name . '</b> ', $zbp->lang['error'][85]));
                    }
                }
            }
        }
        if ($action == 'Disable') {
            $apps = $zbp->LoadPlugins();
            $apps[] = $zbp->LoadApp('theme', $zbp->theme);
            foreach ($apps as $app) {
                if (!$zbp->CheckApp($app->id)) {
                    continue;
                }
                $dependList = explode('|', $app->advanced_dependency);
                foreach ($dependList as $depend) {
                    if ($depend == $this->id) {
                        //$zbp->ShowError(str_replace('%s', ' <b>' . $app->name . '</b> ', $zbp->lang['error'][84]), __FILE__, __LINE__);
                        return new Exception(str_replace('%s', ' <b>' . $app->name . '</b> ', $zbp->lang['error'][84]));
                    }
                }
            }
        }

        return true;
    }

    /**
     * Delete app.
     */
    public function Del()
    {
        rrmdir($this->app_path);
        $this->DelCompiled();
    }

    /**
     * Delete Compiled theme.
     */
    public function DelCompiled()
    {
        global $zbp;
        rrmdir($zbp->cachedir . 'compiled/' . $this->id);
    }

    /**
     * LoadSideBars 从xml和cache里.
     */
    public function LoadSideBars()
    {
        global $zbp;

        if (is_null($zbp->cache)) {
            $zbp->cache = new Config('cache');
        }
        $s = $zbp->cache->{'sidebars_' . $this->id};
        $a = (empty($s)) ? null : json_decode($s, true);
        if (is_array($a)) {
            foreach ($a as $key => $value) {
                $zbp->option['ZC_SIDEBAR' . (($key > 1) ? $key : '') . '_ORDER'] = $value;
            }

            return true;
        }

        $s = '';
        for ($i = 1; $i < 10; $i++) {
            $s .= $this->{'sidebars_sidebar' . $i};
        }
        if (!empty($s)) {
            for ($i = 1; $i < 10; $i++) {
                $zbp->option['ZC_SIDEBAR' . (($i > 1) ? $i : '') . '_ORDER'] = $this->{'sidebars_sidebar' . $i};
            }
        }

        return true;
    }

    /**
     * SaveSideBars 保存到cache.
     */
    public function SaveSideBars()
    {
        global $zbp;

        for ($i = 1; $i < 10; $i++) {
            $a[$i] = $zbp->option['ZC_SIDEBAR' . (($i > 1) ? $i : '') . '_ORDER'];
        }
        $zbp->cache->{'sidebars_' . $this->id} = json_encode($a);
        $zbp->SaveCache();
    }

}
