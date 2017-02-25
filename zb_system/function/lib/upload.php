<?php
/**
 * 上传类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Upload 类库
 */
class Upload extends Base
{

    /**
     *
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Upload'], $zbp->datainfo['Upload'], __CLASS__);

        $this->PostTime = time();
    }

    /**
     * @param string $extlist
     * @return bool
     */
    public function CheckExtName($extlist = '')
    {
        global $zbp;
        $e = GetFileExt($this->Name);
        $extlist = strtolower($extlist);
        if (trim($extlist) == '') {
            $extlist = $zbp->option['ZC_UPLOAD_FILETYPE'];
        }

        return HasNameInString($extlist, $e);
    }

    /**
     * @return bool
     */
    public function CheckSize()
    {
        global $zbp;
        $n = 1024 * 1024 * (int) $zbp->option['ZC_UPLOAD_FILESIZE'];

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
        @move_uploaded_file($tmp, $zbp->usersdir . $this->Dir . $fn);

        return true;
    }

    /**
     * @param $str64
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
        file_put_contents($zbp->usersdir . $this->Dir . $fn, $s);

        return true;
    }

    /**
     * @param string $s
     * @return bool|string
     */
    public function Time($s = 'Y-m-d H:i:s')
    {
        return date($s, $this->PostTime);
    }

    /**
     * @param $name
     * @param $value
     * @return null
     */
    public function __set($name, $value)
    {
        global $zbp;
        if ($name == 'Url') {
            return null;
        }
        if ($name == 'Dir') {
            return null;
        }
        if ($name == 'FullFile') {
            return null;
        }
        if ($name == 'Author') {
            return null;
        }
        parent::__set($name, $value);
    }

    /**
     * @param $name
     * @return Member|mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'Url') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Url'] as $fpname => &$fpsignal) {
                return $fpname($this);
            }

            return $zbp->host . 'zb_users/' . $this->Dir . rawurlencode($this->Name);
        }
        if ($name == 'Dir') {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Upload_Dir'] as $fpname => &$fpsignal) {
                return $fpname($this);
            }

            return 'upload/' . date('Y', $this->PostTime) . '/' . date('m', $this->PostTime) . '/';
        }
        if ($name == 'FullFile') {
            return $zbp->usersdir . $this->Dir . $this->Name;
        }
        if ($name == 'Author') {
            return $zbp->GetMemberByID($this->AuthorID);
        }

        return parent::__get($name);
    }
}
