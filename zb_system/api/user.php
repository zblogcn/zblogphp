<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
class API_USER
{
    /**
     * Instance.
     */
    private static $instance = null;
    /**
     * User object (from ZBP).
     */
    private $user = null;
    /**
     * Key cache.
     */
    private $key = "";
    /**
     * Secret cache.
     */
    private $secret = "";

    /**
     * To avoid construct outside this class.
     *
     * @private
     */
    private function __construct()
    {
    }

    /**
     * To return instance.
     *
     * @return API_Route
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Call some property.
     *
     * @param  $name
     *
     * @return
     */
    public function __get($name)
    {
        switch ($name) {
            case 'key':
                return $this->getKey();
                break;
            case 'secret':
                return $this->getSecret();
                break;
        }

        return false;
    }

    /**
     * To set some property.
     *
     * @param  $name
     *
     * @return
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'user':
                $this->user = &$value;
                $this->key = "";
                $this->secret = "";
                break;
        }
    }

    /**
     * To avoid clone.
     */
    public function __clone()
    {
        throw new Exception("Singleton Class Can Not Be Cloned");
    }

    /**
     * To encrypt user for Key.
     *
     * @return string $key [base64_encode($id . '+' . md5($guid))]
     */
    private function getKey()
    {
        if ($this->key != "") {
            return $this->key;
        }
        $id = $this->user->ID;
        $guid = $this->user->Guid;
        $this->key = base64_encode($id . '+' . md5($guid));

        return $this->key;
    }

    /**
     * To encrypt user for Secret.
     *
     * @return string $secret [base64_encode(aes_decode($key, $password))]
     */
    private function getSecret()
    {
        if ($this->secret != "") {
            return $this->secret;
        }
        if ($this->key == "") {
            $this->getKey();
        }

        $password = $this->user->Password;

        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $secret = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $password, $this->key, MCRYPT_MODE_ECB, $iv);
        $this->secret = base64_encode($secret);

        return $this->secret;
    }

    /**
     * To decrypt key to id.
     *
     * @param string $key [base64_encode(UserID . '+' . UserGuid)]
     */
    private function getUserFromKey($key)
    {
        $keyString = base64_decode($key);
        $keyArray = explode('+', $keyString);
        if (count($keyArray) !== 2) {
            return false;
        }

        return array($keyArray[0], $keyArray[1]);
    }

    /**
     * To decrypt secret to key.
     *
     * @param string $secret [base64_encode(ENCRYPTED_DATA)]
     *
     * @return string $key
     */
    private function getUserFromSecret($secret, $password)
    {
        $secret = base64_decode($secret);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);

        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, $secret, MCRYPT_MODE_ECB, $iv));
    }

    /**
     * Login.
     *
     * @param string $key
     * @param string $secret
     *
     * @return bool
     */
    public function login($key, $secret)
    {
        global $zbp;
        $userData = $this->getUserFromKey($key);
        if (!$userData) {
            return false;
        }
        $member = $zbp->GetMemberByID($userData[0]);
        if ($member->ID === 0 || md5($member->Guid) !== $userData[1]) {
            return false;
        }
        $password = $member->Password;
        $decryptedKey = $this->getUserFromSecret($secret, $password);
        if ($key !== $decryptedKey) {
            return false;
        }
        $zbp->user = $member;
        $this->user = $member;

        return true;
    }
}
