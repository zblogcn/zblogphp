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
     * @var string PHP最低版本
     */
    public $phpver;

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

        return !isset($zbp->activedapps[$this->id]);
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
        global $zbp;

        return $zbp->path . 'zb_users/' . $this->type . '/' . $this->id . '/';
    }

    /**
     * 获取应用Logo图片地址
     *
     * @return string
     */
    public function GetLogo()
    {
        global $zbp;
        if ($this->type == 'plugin') {
            return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/logo.png';
        } else {
            return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/screenshot.png';
        }
    }

    /**
     * 获取应用截图地址
     *
     * @return string
     */
    public function GetScreenshot()
    {
        global $zbp;

        return $zbp->host . 'zb_users/' . $this->type . '/' . $this->id . '/screenshot.png';
    }

    /**
     * 获取应用（主题）样式文件列表.
     *
     * @return array
     */
    public function GetCssFiles()
    {
        global $zbp;
        $dir = $zbp->usersdir . 'theme/' . $this->id . '/style/';

        return GetFilesInDir($dir, 'css');
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
        $path = $zbp->usersdir . TransferHTML($type, '[filename]');
        $path .= '/' . TransferHTML($id, '[filename]') . '/' . TransferHTML($type, '[filename]') . '.xml';

        if (!is_readable($path)) {
            return false;
        }

        $content = file_get_contents($path);
        $xml = @simplexml_load_string($content);
        if (!$xml) {
            return false;
        }

        $appver = $xml->attributes();
        if ($appver != 'php') {
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
        $s .= '</sidebars>' . "\r\n";

        $s .= '</' . $this->type . '>';

        $path = $zbp->usersdir . $this->type . '/' . $this->id . '/' . $this->type . '.xml';

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
        if (function_exists('scandir')) {
            foreach (scandir($dir) as $d) {
                if (is_dir($dir . $d)) {
                    if ((substr($d, 0, 1) != '.') &&
                        !($d == 'compile' && $this->type == 'theme')) {
                        $this->GetAllFileDir($dir . $d . '/');
                        $this->dirs[] = $dir . $d . '/';
                    }
                } else {
                    $this->files[] = $dir . $d;
                }
            }
        } else {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if (is_dir($dir . $file)) {
                        if ((substr($file, 0, 1) != '.') &&
                            !($file == 'compile' && $this->type == 'theme')) {
                            $this->dirs[] = $dir . $file . '/';
                            $this->GetAllFileDir($dir . $file . '/');
                        }
                    } else {
                        $this->files[] = $dir . $file;
                    }
                }
                closedir($handle);
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

        $dir = $this->GetDir();
        $this->GetAllFileDir($dir);

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
        $s .= '</sidebars>';

        foreach ($this->dirs as $key => $value) {
            $value = str_replace($dir, '', $value);
            $value = preg_replace('/[^(\x20-\x7F)]*/', '', $value);
            $d = $this->id . '/' . $value;
            $s .= '<folder><path>' . htmlspecialchars($d) . '</path></folder>';
        }
        foreach ($this->files as $key => $value) {
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
        }

        $s .= '<verify>' . base64_encode($zbp->host . "\n" . $zbp->path) . '</verify>';

        $s .= '</app>';

        return $s;
    }

    public function PackGZip()
    {
        return gzencode($this->Pack(), 9, FORCE_GZIP);
    }

    /**
     * 解开应用包.
     *
     * @param $xml
     *
     * @return bool
     */
    public static function UnPack($xml)
    {
        global $zbp;
        $charset = array();
        $charset[1] = substr($xml, 0, 1);
        $charset[2] = substr($xml, 1, 1);
        if (ord($charset[1]) == 31 && ord($charset[2]) == 139) {
            $xml = gzdecode($xml);
        }

        $xml = simplexml_load_string($xml);
        if (!$xml) {
            return false;
        }

        if ($xml['version'] != 'php') {
            return false;
        }

        $type = $xml['type'];
        $id = $xml->id;
        $dir = $zbp->path . 'zb_users/' . $type . '/';

        ZBlogException::SuspendErrorHook();

        if (!file_exists($dir . $id . '/')) {
            @mkdir($dir . $id . '/', 0755, true);
        }

        foreach ($xml->folder as $folder) {
            $f = $dir . $folder->path;
            if (!file_exists($f)) {
                @mkdir($f, 0755, true);
            }
        }

        foreach ($xml->file as $file) {
            $s = base64_decode($file->stream);
            $f = $dir . $file->path;
            @file_put_contents($f, $s);
            if (function_exists('chmod')) {
                @chmod($f, 0755);
            }
        }

        ZBlogException::ResumeErrorHook();

        return true;
    }

    /**
     * @throws Exception
     */
    public function CheckCompatibility()
    {
        global $zbp;

        if ((int) $this->adapted > (int) $zbp->version) {
            $zbp->ShowError(str_replace('%s', $this->adapted, $zbp->lang['error'][78]), __FILE__, __LINE__);
        }

        if (trim($this->phpver) == '') {
            $this->phpver = '5.2';
        }
        if (version_compare($this->phpver, GetPHPVersion()) > 0) {
            $zbp->ShowError(str_replace('%s', $this->phpver, $zbp->lang['error'][91]), __FILE__, __LINE__);
        }

        $ae = explode('|', $this->advanced_existsfunctions);
        foreach ($ae as $e) {
            $e = trim($e);
            if (!$e) {
                continue;
            }

            if (function_exists($e) == false) {
                $zbp->ShowError(str_replace('%s', $e, $zbp->lang['error'][92]), __FILE__, __LINE__);
            }
        }

        $ad = explode('|', $this->advanced_dependency);
        foreach ($ad as $d) {
            if (!$d) {
                continue;
            }

            if (!in_array($d, $zbp->activedapps)) {
                $d = '<a href="' . $zbp->host . 'zb_users/plugin/AppCentre/main.php?alias=' . $d . '">' . $d . '</a>';
                $zbp->ShowError(str_replace('%s', $d, $zbp->lang['error'][83]), __FILE__, __LINE__);
            }
        }

        $ac = explode('|', $this->advanced_conflict);
        foreach ($ac as $c) {
            if (!$c) {
                continue;
            }

            if (in_array($c, $zbp->activedapps)) {
                $zbp->ShowError(str_replace('%s', $c, $zbp->lang['error'][85]), __FILE__, __LINE__);
            }
        }
    }

    /**
     * Delete app.
     */
    public function Del()
    {
        global $zbp;
        rrmdir($zbp->usersdir . $this->type . '/' . $this->id);
        $this->DelCompiled();
    }

    /**
     * Delete Compiled theme.
     */
    public function DelCompiled()
    {
        global $zbp;
        rrmdir($zbp->usersdir . 'cache/compiled/' . $this->id);
    }
}
