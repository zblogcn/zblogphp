<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 模块类.
 *
 * @property string FileName
 * @property int|string ID
 * @property string Source 模块来源
 * @property string Content
 * @property string Type 模块显示类型（div / ul）
 * @property bool NoRefresh 拒绝系统刷新该模块
 */
class Module extends Base
{
    protected $_isincludefile = false;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['Module'], $zbp->datainfo['Module'], __CLASS__);
    }

    /**
     * 设置参数值
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        global $zbp;
        if ($name == 'SourceType') {
            return;
        }
        if ($name == 'NoRefresh') {
            if ((bool) $value) {
                $this->Metas->norefresh = (bool) $value;
            } else {
                $this->Metas->Del('norefresh');
            }

            return;
        }
        if ($name == 'IsIncludeFile') {
            $this->_isincludefile = (bool) $value;

            return;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Set'] as $fpname => &$fpsignal) {
            $fpname($this, $name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * 获取参数值
     *
     * @param $name
     *
     * @return bool|mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'SourceType') {
            if ($this->Source == 'system') {
                return 'system';
            } elseif ($this->Source == 'user') {
                return 'user';
            } elseif ($this->Source == 'theme') {
                return 'theme';
            } elseif (stripos($this->Source, 'theme_') === 0) {
                return 'theme';
            } elseif (stripos($this->Source, 'plugin_') === 0) {
                //如果是plugin_主题名，还是判断为theme，修正历史遗留问题
                $ts = $zbp->LoadThemes();
                foreach ($ts as $t) {
                    if ($this->Source == 'plugin_' . $t->id) {
                        return 'theme';
                    }
                }

                return 'plugin';
            } else {
                return 'plugin';
            }
        }
        if ($name == 'NoRefresh') {
            return (bool) $this->Metas->norefresh;
        }
        if ($name == 'IsIncludeFile') {
            return $this->_isincludefile;
        }
        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Get'] as $fpname => &$fpsignal) {
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
    public function Save()
    {
        global $zbp;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Save'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        if ($this->IsIncludeFile) {
            if (empty($this->FileName)) {
                return true;
            }

            $c = RemovePHPCode($this->Content);
            $d = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/';
            $f = $d . $this->FileName . '.php';
            if (!file_exists($d)) {
                @mkdir($d, 0755);
            }
            @file_put_contents($f, $c);

            return true;
        }

        //防Module重复保存的机制
        $m = $zbp->GetListType(
            'Module',
            $zbp->db->sql->get()->select($zbp->table['Module'])
                    ->where(array('=', $zbp->datainfo['Module']['FileName'][0], $this->FileName))
                    ->sql
        );
        if (count($m) >= 1 && $this->ID == 0) {//如果已有同名，且新ID为0就不存
            return false;
        }

        return parent::Save();
    }

    /**
     * @return bool
     */
    public function Del()
    {
        global $zbp;
        foreach ($zbp->modules as $key => $m) {
            if ($this->ID > 0 && $m->ID == $this->ID) {
                unset($zbp->modules[$key]);
            }
            if ($this->IsIncludeFile) {
                if ($this->FileName != '' && $m->FileName == $this->FileName) {
                    unset($zbp->modules[$key]);
                }
            }
        }
        foreach ($zbp->modulesbyfilename as $key => $m) {
            if ($this->FileName != '' && $m->FileName == $this->FileName) {
                unset($zbp->modulesbyfilename[$this->FileName]);
            }
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Del'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($this);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }
        if ($this->IsIncludeFile) {
            if (empty($this->FileName)) {
                return true;
            }

            $f = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $this->FileName . '.php';
            if (file_exists($f)) {
                @unlink($f);
            }

            return true;
        }

        return parent::Del();
    }

    public function Build()
    {
        if ($this->NoRefresh == true) {
            return;
        }

        if (isset(ModuleBuilder::$List[$this->FileName])) {
            if (isset(ModuleBuilder::$List[$this->FileName]['function'])) {
                $f = str_replace(' ', '', ModuleBuilder::$List[$this->FileName]['function']);
                $p = ModuleBuilder::$List[$this->FileName]['parameters'];
                $p = is_array($p) ? $p : array();

                if (function_exists($f)) {
                    $this->Content = call_user_func_array($f, $p);
                } elseif (strpos($f, '::') !== false) {
                    $a = explode('::', $f);
                    if (method_exists($a[0], $a[1])) {
                        $this->Content = call_user_func_array($f, $p);
                    }
                } elseif (strpos($f, '->') !== false) {
                    $f = str_replace(array('$', '{', '}'), '', $f);
                    $a = explode('->', $f);
                    if (is_callable(array($GLOBALS[$a[0]], $a[1]))) {
                        $this->Content = call_user_func_array(array($GLOBALS[$a[0]], $a[1]), $p);
                    }
                }
            }
        }

        return true;
    }
}
