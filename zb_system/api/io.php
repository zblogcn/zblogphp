<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
class API_IO
{
    const FORMAT_JSON = 0;
    /**
     * Instance.
     */
    private static $instance = null;
    /**
     * Saved object
     * for output.
     *
     * @var array
     */
    private static $savedObject = array();
    /**
     * GET object
     * for input.
     *
     * @var array
     */
    private static $getObject = array();
    /**
     * Input/Output format
     * for input/output.
     *
     * @var array
     */
    private static $ioFormat = self::FORMAT_JSON;

    /**
     * To avoid construct outside this class.
     *
     * @param string $formatString
     * @private
     */
    private function __construct($formatString)
    {
        if ($formatString === "") {
            self::$ioFormat = self::FORMAT_JSON;
        } elseif (0 > strpos($formatString, 'json')) {
            self::end(API_ERROR::NON_ACCEPT);
        }

        $uri = GetVars('REQUEST_URI', 'SERVER');
        $queryStringArray = explode('?', $uri);
        $queryString = end($queryStringArray);
        $query = parse_str($queryString, self::$getObject);
    }

    /**
     * To return instance.
     *
     * @param string $type
     *
     * @return API_Route
     */
    public static function getInstance($formatString)
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class($formatString);
        }

        return self::$instance;
    }

    /**
     * To avoid clone.
     */
    public function __clone()
    {
        throw new Exception("Singleton Class Can Not Be Cloned");
    }

    /**
     * Return outputData or HTTP_GET.
     *
     * @param  $name
     *
     * @return
     */
    public function __get($name)
    {
        if ($name === "output") {
            return $savedObject[$name];
        } elseif (isset(self::$getObject[$name])) {
            return self::$getObject[$name];
        } else {
            return "";
        }
    }

    /**
     * Set outputData.
     *
     * @param string $name
     * @param $value
     */
    public function __set($name, $value)
    {
        self::$savedObject[$name] = $value;
    }

    /**
     * Return POST Data.
     *
     * @param string $name
     */
    public static function post($name)
    {
        return GetVars($name, 'POST');
    }

    /**
     * Format array key.
     *
     * @param array &$object [description]
     */
    public static function formatObjectName(&$object)
    {
        foreach ($object as $oldKey => $value) {
            $newKey = str_replace('ID', 'Id', str_replace('iD', 'id', lcfirst($oldKey)));
            if ($newKey != $oldKey) {
                $object[$newKey] = $value;
                unset($object[$oldKey]);
            }
        }
    }

    /**
     * Format array key for save.
     *
     * @param array &$object [description]
     */
    public static function formatObjectNameForSave(&$object)
    {
        foreach ($object as $oldKey => $value) {
            $newKey = str_replace('id', 'ID', str_replace('Id', 'ID', ucfirst($oldKey)));
            if ($newKey != $oldKey) {
                $object[$newKey] = $value;
                unset($object[$oldKey]);
            }
        }
    }

    /**
     * Write data to page and exit.
     *
     * @param int    $errorCode
     * @param string $errorMessage
     */
    public static function end($errorCode = -1, $errorMessage = "")
    {
        global $zbp; // For language file
        $returnObject = array(
            'err' => $errorCode,
        );

        $err = $errorCode;
        if ($errorCode !== -1 && $errorMessage === "") {
            $returnObject['message'] = $zbp->lang['error'][$errorCode];
        } elseif ($errorCode !== -1 && $errorMessage !== "") {
            $returnObject['message'] = $errorMessage;
        }

        $returnObject['data'] = self::$savedObject;
        $returnObject['info'] = RunTime(); // A ZBP Function

        header('Content-Type: application/json');
        echo json_encode($returnObject);

        exit;

        return true;
    }
}
