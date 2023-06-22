<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 上传类.
 *
 * @property string Name
 * @property string FullFile
 * @property string Size
 * @property string Dir
 * @property int PostTime
 * @property int|string AuthorID
 * @property string SourceName
 * @property string MimeType
 * @property Member Author
 */
abstract class Base__Upload extends Base
{

    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Upload'], $zbp->datainfo['Upload'], __CLASS__);

        $this->PostTime = time();
    }

    /**
     * @param string $extList
     *
     * @return bool
     */
    public function CheckExtName($extList = '')
    {
        global $zbp;
        $fn = $this->Name;
        if (stripos($fn, '.htaccess') !== false) {
            return false;
        }
        if (stripos($fn, 'web.config') !== false) {
            return false;
        }
        $e = GetFileExt($this->Name);
        $extList = strtolower($extList);
        // 无论如何，禁止.php、.php5之类的文件的上传
        if (preg_match('/php/i', $e)) {
            return false;
        }
        if (preg_match('/pht(ml)?(\d*)|phar/i', $e)) {
            return false;
        }
        if (trim($extList) == '') {
            $extList = $zbp->option['ZC_UPLOAD_FILETYPE'];
        }

        return HasNameInString($extList, $e);
    }

    /**
     * @return bool
     */
    public function CheckSize()
    {
        global $zbp;
        $n = (1024 * 1024 * (int) $zbp->option['ZC_UPLOAD_FILESIZE']);

        return $n >= $this->Size;
    }

    /**
     * @return bool
     */
    public function DelFile()
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_DelFile'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        if (file_exists($this->FullFile)) {
            @unlink($this->FullFile);
        }

        return true;
    }

    /**
     * @param $tmp
     *
     * @return bool
     */
    public function SaveFile($tmp)
    {
        global $zbp;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_SaveFile'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($tmp, $this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        if (!file_exists($zbp->usersdir . $this->Dir)) {
            @mkdir($zbp->usersdir . $this->Dir, 0755, true);
        }
        if (PHP_SYSTEM === SYSTEM_WINDOWS) {
            $fn = iconv("UTF-8", $zbp->lang['windows_character_set'] . "//IGNORE", $this->Name);
        } else {
            $fn = $this->Name;
        }
        if ($this->CheckExtName()) {
            @move_uploaded_file($tmp, $zbp->usersdir . $this->Dir . $fn);
            return true;
        }

        return true;
    }

    /**
     * @param $str64
     *
     * @return bool
     */
    public function SaveBase64File($str64)
    {
        global $zbp;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_SaveBase64File'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($str64, $this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        if (!file_exists($zbp->usersdir . $this->Dir)) {
            @mkdir($zbp->usersdir . $this->Dir, 0755, true);
        }
        $s = base64_decode($str64);
        $this->Size = strlen($s);
        if (PHP_SYSTEM === SYSTEM_WINDOWS) {
            $fn = iconv("UTF-8", "GBK//IGNORE", $this->Name);
        } else {
            $fn = $this->Name;
        }
        if ($this->CheckExtName()) {
            file_put_contents($zbp->usersdir . $this->Dir . $fn, $s);
            return true;
        }
    }

    /**
     * @param string $s
     *
     * @return bool|string
     */
    public function Time($s = 'Y-m-d H:i:s')
    {
        return date($s, $this->PostTime);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (in_array($name, array('Url', 'Dir', 'FullFile', 'Author'))) {
            return;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Set'] as $fpname => &$fpsignal) {
            $fpname($this, $name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     *
     * @return Member|mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        //注：这里原先设计失误，没有判断接口函数返回值就直接return，造成了插件必须自己拼接所有的url，不管接管还是不接管
        //1.7.3后修改为先判断null，如果返回null就交给下一棒直到返回系统默认值
        //Filter_Plugin_Upload_Url和Filter_Plugin_Upload_Dir都做了同样的修复
        if ($name == 'Url') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Url'] as $fpname => &$fpsignal) {
                $furl = $fpname($this);
                if (!empty($furl)) {
                    return $furl;
                }
            }

            return $zbp->host . 'zb_users/' . $this->Dir . rawurlencode($this->Name);
        }
        if ($name == 'Dir') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Dir'] as $fpname => &$fpsignal) {
                $fdir = $fpname($this);
                if (!empty($fdir)) {
                    return $fdir;
                }
            }
            $dir = 'upload/' . date('Y', $this->PostTime) . '/' . date('m', $this->PostTime) . '/';
            if ($zbp->option['ZC_UPLOAD_DIR_YEARMONTHDAY']) {
                $dir .= date('d', $this->PostTime) . '/';
            }
            return $dir;
        }
        if ($name == 'FullFile') {
            return $zbp->usersdir . $this->Dir . $this->Name;
        }
        if ($name == 'Author') {
            return $zbp->GetMemberByID($this->AuthorID);
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Get'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this, $name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::__get($name);
    }

    /**
     * @return bool
     */
    public function Del()
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Del'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        return parent::Del();
    }

    /**
     * 获取该附件图片缩略图.
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $clip
     * @return string|null
     */
    public function Thumb($width = 200, $height = 150, $clip = true)
    {
        if (strpos($this->MimeType, 'image') === false) {
            return null;
        }

        $thumbs = Thumb::Thumbs(array($this->Url), $width, $height, 1, $clip);
        return isset($thumbs[0]) ? $thumbs[0] : null;
    }

}
