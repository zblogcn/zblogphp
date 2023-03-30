<?php

/**
 * Created by ZBLOGPHP
 * User: zxasd
 * Date: 2022/02/18
 * Time: 17:00
 */

//ZbpEncrypt现在支持对称加密算法 AES-256-GCM, AES-256-CBC, SM4
//现在还支持非对称加密RSA算法

//使用方法
//$endata = ZbpEncrypt::encrypt('text', 'password', 'add');
//$dedata = ZbpEncrypt::decrypt($endata, 'password', 'add');

//$endata = ZbpEncrypt::aes256gcm_encrypt('text', 'password');
//$dedata = ZbpEncrypt::aes256gcm_decrypt($endata, 'password');

//$endata = ZbpEncrypt::aes256_encrypt('text', 'password', 'add', 'ofb');
//$dedata = ZbpEncrypt::aes256_decrypt($endata, 'password', 'add', 'ofb');

//$endata = ZbpEncrypt::sm4_encrypt('text', 'password', 'add', 'cbc');
//$dedata = ZbpEncrypt::sm4_decrypt($endata, 'password', 'add', 'cbc');

//$endata = ZbpEncrypt::rsa_public_encrypt('text', '公钥')
//$dedata = ZbpEncrypt::rsa_private_decrypt($endata, '私钥')

//$endata = ZbpEncrypt::rsa_private_encrypt('text', '私钥')
//$dedata = ZbpEncrypt::rsa_public_decrypt($endata, '公钥')

//可以和其它系统对接的aes256gcm,aes256,sm4加解密
//$endata = ZbpEncrypt::original_aes256gcm_encrypt('text', 'password', '附加认证字符', '初始向量');
//$dedata = ZbpEncrypt::original_aes256gcm_decrypt($endata, 'password', '附加认证字符', '初始向量');

//$endata = ZbpEncrypt::original_aes256_encrypt('text', 'password', '初始向量');
//$dedata = ZbpEncrypt::original_aes256_decrypt($endata, 'password', '初始向量');

//$endata = ZbpEncrypt::original_aes128_encrypt('text', 'password', '初始向量');
//$dedata = ZbpEncrypt::original_aes128_decrypt($endata, 'password', '初始向量');

//$endata = ZbpEncrypt::original_sm4_encrypt('text', 'password', '初始向量');
//$dedata = ZbpEncrypt::original_sm4_decrypt($endata, 'password', '初始向量');

class ZbpEncrypt
{

    //加密函数 (支持aes256cbc, aes256xts, aes256gcm，sm4cbc)
    public static function encrypt($data, $password, $additional = null, $type = 'aes256cbc')
    {
        return self::zbp_encrypt($data, $password, $additional, $type);
    }

    //解密函数 (支持aes256cbc, aes256xts, aes256gcm，sm4cbc)
    public static function decrypt($data, $password, $additional = null, $type = 'aes256cbc')
    {
        return self::zbp_decrypt($data, $password, $additional, $type);
    }

    //aes256gcm加密函数
    public static function aes256gcm_encrypt($data, $password, $additional = null)
    {
        return self::zbp_openssl_aes256gcm_encrypt($data, $password, $additional);
    }

    //aes256gcm解密函数
    public static function aes256gcm_decrypt($data, $password, $additional = null)
    {
        return self::zbp_openssl_aes256gcm_decrypt($data, $password, $additional);
    }

    //aes256加密函数 (mode = 'cbc', 'cfb', 'ctr', 'ecb', 'ofb', 'xts')
    public static function aes256_encrypt($data, $password, $additional = null, $mode = '')
    {
        return self::zbp_openssl_aes256_encrypt($data, $password, $additional, $mode);
    }

    //aes256cbc解密函数
    public static function aes256_decrypt($data, $password, $additional = null, $mode = '')
    {
        return self::zbp_openssl_aes256_decrypt($data, $password, $additional, $mode);
    }

    //sm4加密函数 (mode = 'cbc', 'cfb',' ctr', 'ecb', 'ofb')
    public static function sm4_encrypt($data, $password, $additional = null, $mode = '')
    {
        return self::zbp_openssl_sm4_encrypt($data, $password, $additional, $mode);
    }

    //sm4解密函数
    public static function sm4_decrypt($data, $password, $additional = null, $mode = '')
    {
        return self::zbp_openssl_sm4_decrypt($data, $password, $additional, $mode);
    }

    //chacha20加密函数
    public static function chacha20_encrypt($data, $password, $additional = null, $mode = '')
    {
        return self::zbp_chacha20_encrypt($data, $password, $additional, $mode);
    }

    //chacha20解密函数
    public static function chacha20_decrypt($data, $password, $additional = null, $mode = '')
    {
        return self::zbp_chacha20_decrypt($data, $password, $additional, $mode);
    }

    //rsa公钥加密函数
    public static function rsa_public_encrypt($data, $public_key_pem, $key_length = 2048)
    {
        return self::zbp_rsa_public_encrypt($data, $public_key_pem, $key_length);
    }

    //rsa公钥解密函数
    public static function rsa_public_decrypt($data, $public_key_pem, $key_length = 2048)
    {
        return self::zbp_rsa_public_decrypt($data, $public_key_pem, $key_length);
    }

    //rsa私钥加密函数
    public static function rsa_private_encrypt($data, $private_key_pem, $key_length = 2048)
    {
        return self::zbp_rsa_private_encrypt($data, $private_key_pem, $key_length);
    }

    //rsa私公钥解密函数
    public static function rsa_private_decrypt($data, $private_key_pem, $key_length = 2048)
    {
        return self::zbp_rsa_private_decrypt($data, $private_key_pem, $key_length);
    }

    //原味版本的aes256gcm加密（需要输入三种参数）
    public static function original_aes256gcm_encrypt($data, $password, $additional, $nonce)
    {
        return self::zbp_original_aes256gcm_encrypt($data, $password, $additional, $nonce);
    }

    //原味版本的aes256gcm解密（需要输入三种参数）
    public static function original_aes256gcm_decrypt($data, $password, $additional, $nonce)
    {
        return self::zbp_original_aes256gcm_decrypt($data, $password, $additional, $nonce);
    }

    //原味版本的aes256加密（需要输入二种参数）
    public static function original_aes256_encrypt($data, $password, $iv, $mode = 'cbc')
    {
        return self::zbp_original_aes256_encrypt($data, $password, $iv, $mode);
    }

    //原味版本的aes256解密（需要输入二种参数）
    public static function original_aes256_decrypt($data, $password, $iv, $mode = 'cbc')
    {
        return self::zbp_original_aes256_decrypt($data, $password, $iv, $mode);
    }

    //原味版本的aes128加密（需要输入二种参数）
    public static function original_aes128_encrypt($data, $password, $iv, $mode = 'cbc')
    {
        return self::zbp_original_aes128_encrypt($data, $password, $iv, $mode);
    }

    //原味版本的aes128解密（需要输入二种参数）
    public static function original_aes128_decrypt($data, $password, $iv, $mode = 'cbc')
    {
        return self::zbp_original_aes128_decrypt($data, $password, $iv, $mode);
    }

    //原味版本的sm4加密（需要输入二种参数）
    public static function original_sm4_encrypt($data, $password, $iv, $mode = 'cbc')
    {
        return self::zbp_original_sm4_encrypt($data, $password, $iv, $mode);
    }

    //原味版本的sm4解密（需要输入二种参数）
    public static function original_sm4_decrypt($data, $password, $iv, $mode = 'cbc')
    {
        return self::zbp_original_sm4_decrypt($data, $password, $iv, $mode);
    }

    /**
     * openssl的aes256gcm加密
     */
    private static function zbp_openssl_aes256gcm_encrypt($data, $password, $additional = null, $with_hash = false)
    {
        $mode = 'aes-256-gcm';
        $func_name = 'sodium_crypto_aead_aes256gcm_is_available';
        if (function_exists($func_name) && $func_name()) {
            $nonce_length = SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES;
            $nonce = random_bytes($nonce_length);
        } else {
            $option = OPENSSL_RAW_DATA;
            $nonce_length = openssl_cipher_iv_length($mode);
            $nonce = openssl_random_pseudo_bytes($nonce_length);
        }
        $additional_data = $additional;
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;
        if (function_exists($func_name) && $func_name()) {
            $endata = sodium_crypto_aead_aes256gcm_encrypt($data, $additional_data, $nonce, $keygen);
            $tag = substr($endata, -16);
            $endata = substr($endata, 0, -16);
        } else {
            $tag = '';
            $array = array($data, $mode, $keygen, $option, $nonce, &$tag, $additional_data, 16);
            $endata = call_user_func_array('openssl_encrypt', $array);
        }
        if ($with_hash == false) {
            return base64_encode($nonce . $tag . $endata);
        }
        $hmac = hash_hmac('sha256', $data, $keygen . $nonce, true);
        $json = $hmac . $nonce . $tag . $endata;
        return base64_encode($json);
    }

    /**
     * openssl的aes256gcm解密
     */
    private static function zbp_openssl_aes256gcm_decrypt($data, $password, $additional = null, $with_hash = false)
    {
        $mode = 'aes-256-gcm';
        $endata = base64_decode($data);
        $func_name = 'sodium_crypto_aead_aes256gcm_is_available';
        if (function_exists($func_name) && $func_name()) {
            $nonce_length = SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES;
        } else {
            $option = OPENSSL_RAW_DATA;
            $nonce_length = openssl_cipher_iv_length($mode);
        }
        if ($with_hash == false) {
            $nonce = substr($endata, 0, $nonce_length);
            $tag = substr($endata, (0 + $nonce_length), 16);
            $data = substr($endata, (0 + $nonce_length + 16));
        } else {
            $hmac = substr($endata, 0, 32);
            $nonce = substr($endata, 32, $nonce_length);
            $tag = substr($endata, (32 + $nonce_length), 16);
            $data = substr($endata, (32 + $nonce_length + 16));
        }
        $additional_data = $additional;
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;
        if (function_exists($func_name) && $func_name()) {
            $dedata = sodium_crypto_aead_aes256gcm_decrypt($data . $tag, $additional_data, $nonce, $keygen);
        } else {
            $dedata = call_user_func('openssl_decrypt', $data, $mode, $keygen, $option, $nonce, $tag, $additional_data);
        }
        if ($with_hash == false) {
            return $dedata;
        }
        if (hash_hmac('sha256', $dedata, $keygen . $nonce, true) == $hmac) {
            return $dedata;
        }
        return false;
    }

    /**
     * openssl的aes256的6种模式加密
     */
    private static function zbp_openssl_aes256_encrypt($data, $password, $additional = null, $mode = '', $with_hash = false)
    {
        $mode = str_replace(array('aes', '256', '-', '_'), '', $mode);
        if (!in_array($mode, array('xts', 'cbc', 'cfb', 'ctr', 'ecb', 'ofb'))) {
            $mode = 'cbc';
        }
        if (function_exists('openssl_encrypt')) {
            $mode = strtolower('aes-256-' . $mode);
            $nonce_length = openssl_cipher_iv_length($mode);
            if ($nonce_length > 0) {
                $nonce = openssl_random_pseudo_bytes($nonce_length);
            } else {
                $nonce = null;
            }
        } elseif (function_exists('mcrypt_encrypt')) {
            if (!$mode == 'ctr') {
                $mode = constant(strtoupper('MCRYPT_MODE_' . $mode));
            }
            $nonce_length = call_user_func('mcrypt_get_iv_size', constant('MCRYPT_RIJNDAEL_128'), $mode);
            $nonce = call_user_func('mcrypt_create_iv', $nonce_length, constant('MCRYPT_RAND'));
            if ($mode == constant('MCRYPT_MODE_ECB')) {
                $nonce_length = 0;
                $nonce = null;
            }
        }
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;
        if (function_exists('openssl_encrypt')) {
            $option = (PHP_VERSION_ID < 50400) ? 0 : OPENSSL_RAW_DATA;
            $endata = call_user_func('openssl_encrypt', $data, $mode, $keygen, $option, $nonce);
            $endata = (PHP_VERSION_ID < 50400) ? base64_decode($endata) : $endata;
        } elseif (function_exists('mcrypt_encrypt')) {
            //pkcs7 pad
            $pkcs7_data = $data;
            if (function_exists('mb_strlen')) {
                $len = mb_strlen($pkcs7_data, '8bit');
            } else {
                $len = strlen($pkcs7_data);
            }
            $c = (16 - ($len % 16));
            $pkcs7_data .= str_repeat(chr($c), $c);
            $endata = call_user_func('mcrypt_encrypt', constant('MCRYPT_RIJNDAEL_128'), $keygen, $pkcs7_data, $mode, $nonce);
        }
        if ($with_hash == false) {
            return base64_encode($nonce . $endata);
        }
        $hmac = hash_hmac('sha256', $data, $keygen . $nonce, true);
        $json = $hmac . $nonce . $endata;
        return base64_encode($json);
    }

    /**
     * openssl的aes256的6种模式解密
     */
    private static function zbp_openssl_aes256_decrypt($data, $password, $additional = null, $mode = '', $with_hash = false)
    {
        $mode = str_replace(array('aes', '256', '-', '_'), '', $mode);
        if (!in_array($mode, array('xts', 'cbc', 'cfb', 'ctr', 'ecb', 'ofb'))) {
            $mode = 'cbc';
        }
        if (function_exists('openssl_decrypt')) {
            $mode = strtolower('aes-256-' . $mode);
            $nonce_length = openssl_cipher_iv_length($mode);
        } elseif (function_exists('mcrypt_decrypt')) {
            if (!$mode == 'ctr') {
                $mode = constant(strtoupper('MCRYPT_MODE_' . $mode));
            }
            $nonce_length = call_user_func('mcrypt_get_iv_size', constant('MCRYPT_RIJNDAEL_128'), $mode);
            if ($mode == constant('MCRYPT_MODE_ECB')) {
                $nonce_length = 0;
            }
        }
        $endata = base64_decode($data);
        if ($with_hash == false) {
            $nonce = substr($endata, 0, $nonce_length);
            $data = substr($endata, (0 + $nonce_length));
        } else {
            $hmac = substr($endata, 0, 32);
            $nonce = substr($endata, 32, $nonce_length);
            $data = substr($endata, (32 + $nonce_length));
        }
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;
        if (function_exists('openssl_encrypt')) {
            $option = (PHP_VERSION_ID < 50400) ? 0 : OPENSSL_RAW_DATA;
            $data = (PHP_VERSION_ID < 50400) ? base64_encode($data) : $data;
            $dedata = call_user_func('openssl_decrypt', $data, $mode, $keygen, $option, $nonce);
        } elseif (function_exists('mcrypt_decrypt')) {
            $data = call_user_func('mcrypt_decrypt', constant('MCRYPT_RIJNDAEL_128'), $keygen, $data, $mode, $nonce);
            //pkcs7 unpad
            $text = $data;
            $pad = ord($text[(strlen($text) - 1)]);
            if ($pad > strlen($text)) {
                $data = $text;
            } else {
                if (strspn($text, chr($pad), (strlen($text) - $pad)) != $pad) {
                    $data = $text;
                } else {
                    $data = substr($text, 0, (-1 * $pad));
                }
            }
            $dedata = $data;
            //$dedata = rtrim($data, "\0");
        }
        if ($with_hash == false) {
            return $dedata;
        }
        if (hash_hmac('sha256', $dedata, $keygen . $nonce, true) == $hmac) {
            return $dedata;
        }
        return false;
    }

    /**
     * openssl的sm4的5种模式加密
     */
    private static function zbp_openssl_sm4_encrypt($data, $password, $additional = null, $mode = '', $with_hash = false)
    {
        $mode = str_replace(array('sm4', '-', '_'), '', $mode);
        $sm4_array = array('cbc', 'cfb', 'ctr', 'ecb', 'ofb');
        if (!in_array($mode, $sm4_array)) {
            $mode = "cbc";
        }
        $mode = "sm4-" . $mode;
        $nonce_length = openssl_cipher_iv_length($mode);
        if ($nonce_length == 0) {
            $nonce = null;
        } else {
            $nonce = openssl_random_pseudo_bytes($nonce_length);
        }
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;
        $array = array($data, $mode, $keygen, OPENSSL_RAW_DATA, $nonce);
        $endata = call_user_func_array('openssl_encrypt', $array);
        if ($with_hash == false) {
            return base64_encode($nonce . $endata);
        }
        $hmac = hash_hmac('sha256', $data, $keygen . $nonce, true);
        $json = $hmac . $nonce . $endata;
        return base64_encode($json);
    }

    /**
     * openssl的sm4的5种模式解密
     */
    private static function zbp_openssl_sm4_decrypt($data, $password, $additional = null, $mode = '', $with_hash = false)
    {
        $mode = str_replace(array('sm4', '-', '_'), '', $mode);
        $sm4_array = array('cbc', 'cfb', 'ctr', 'ecb', 'ofb');
        if (!in_array($mode, $sm4_array)) {
            $mode = "cbc";
        }
        $mode = "sm4-" . $mode;
        $endata = base64_decode($data);
        $nonce_length = openssl_cipher_iv_length($mode);
        if ($with_hash == false) {
            $hmac = '';
            $nonce = substr($endata, 0, $nonce_length);
            $data = substr($endata, $nonce_length);
        } else {
            $hmac = substr($endata, 0, 32);
            $nonce = substr($endata, 32, $nonce_length);
            $data = substr($endata, (32 + $nonce_length));
        }
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;
        $dedata = call_user_func('openssl_decrypt', $data, $mode, $keygen, OPENSSL_RAW_DATA, $nonce);
        if ($with_hash == false) {
            return $dedata;
        }
        if (hash_hmac('sha256', $dedata, $keygen . $nonce, true) == $hmac) {
            return $dedata;
        }
        return false;
    }

    /**
     * zbp内置对称加密函数
     *
     * @param string $data 待加密数据string
     * @param string $password 密码明文
     * @param string $additional 附加认证数据
     * @param string $type 可以指定类型为 aes256gcm, aes256cbc, sm4cbc
     */
    private static function zbp_encrypt($data, $password, $additional = null, $type = 'aes256cbc')
    {
        $type = trim(strtolower($type));
        $type = str_replace('-', '', $type);
        if ($type == 'aes256gcm') {
            if (PHP_VERSION_ID < 70100 && !function_exists('sodium_crypto_aead_aes256gcm_encrypt')) {
                return false;
            }
            return self::zbp_openssl_aes256gcm_encrypt($data, $password, $additional);
        } elseif (stripos($type, 'aes256') === 0) {
            return self::zbp_openssl_aes256_encrypt($data, $password, $additional, $type);
        } elseif (stripos($type, 'sm4') === 0) {
            if (PHP_VERSION_ID < 70200) {
                return false;
            }
            return self::zbp_openssl_sm4_encrypt($data, $password, $additional, $type);
        }
    }

    /**
     * zbp内置对称解密函数
     *
     * @param string $data 待解密数据string
     * @param string $password 密码明文
     * @param string $additional 附加认证数据
     * @param string $type 可以指定类型为 aes256gcm, aes256cbc, sm4cbc
     */
    private static function zbp_decrypt($data, $password, $additional = null, $type = 'aes256cbc')
    {
        $type = trim(strtolower($type));
        if ($type == 'aes256gcm') {
            if (PHP_VERSION_ID < 70100 && !function_exists('sodium_crypto_aead_aes256gcm_encrypt')) {
                return false;
            }
            return self::zbp_openssl_aes256gcm_decrypt($data, $password, $additional);
        } elseif (stripos($type, 'aes256') === 0) {
            return self::zbp_openssl_aes256_decrypt($data, $password, $additional, $type);
        } elseif (stripos($type, 'sm4') === 0) {
            if (PHP_VERSION_ID < 70200) {
                return false;
            }
            return self::zbp_openssl_sm4_decrypt($data, $password, $additional, $type);
        }
    }

    /**
     * zbp内置非对称RSA公钥加密函数
     *
     * @param string $data 待加密数据string
     * @param string $public_key_pem 公钥pem字符串
     * @param string $key_length 密钥长度默认2048
     */
    private static function zbp_rsa_public_encrypt($data, $public_key_pem, $key_length = 2048, $with_hash = false)
    {
        $length = (($key_length / 8) - 11);
        $dataarray = str_split($data, $length);
        $endata = null;
        foreach ($dataarray as $single) {
            $endata_single = null;
            openssl_public_encrypt($single, $endata_single, $public_key_pem);
            $endata .= $endata_single;
        }
        if ($with_hash == false) {
            return base64_encode($endata);
        }
        $hmac = hash_hmac('sha256', $data, md5($endata), true);
        return base64_encode($hmac . $endata);
    }

    /**
     * zbp内置非对称RSA公钥解密函数
     *
     * @param string $data 待解密数据string
     * @param string $public_key_pem 公钥pem字符串
     * @param string $key_length 密钥长度默认2048
     */
    private static function zbp_rsa_public_decrypt($data, $public_key_pem, $key_length = 2048, $with_hash = false)
    {
        $length = ($key_length / 8);
        $data = base64_decode($data);
        if ($with_hash == true) {
            $hmac = substr($data, 0, 32);
            $data = substr($data, 32);
            $md5_endata = md5($data);
        }
        $dataarray = str_split($data, $length);
        $dedata = null;
        foreach ($dataarray as $single) {
            $dedata_single = null;
            openssl_public_decrypt($single, $dedata_single, $public_key_pem);
            $dedata .= $dedata_single;
        }
        if ($with_hash == false) {
            return $dedata;
        }
        if (hash_hmac('sha256', $dedata, $md5_endata, true) == $hmac) {
            return $dedata;
        }
        return false;
    }

    /**
     * zbp内置非对称RSA私钥加密函数
     *
     * @param string $data 待加密数据string
     * @param string $private_key_pem 私钥pem字符串
     * @param string $key_length 密钥长度默认2048
     */
    private static function zbp_rsa_private_encrypt($data, $private_key_pem, $key_length = 2048, $with_hash = false)
    {
        $length = (($key_length / 8) - 11);
        $dataarray = str_split($data, $length);
        $endata = null;
        foreach ($dataarray as $single) {
            $endata_single = null;
            openssl_private_encrypt($single, $endata_single, $private_key_pem);
            $endata .= $endata_single;
        }
        if ($with_hash == false) {
            return base64_encode($endata);
        }
        $hmac = hash_hmac('sha256', $data, md5($endata), true);
        return base64_encode($hmac . $endata);
    }

    /**
     * zbp内置非对称RSA私钥解密函数
     *
     * @param string $data 待解密数据string
     * @param string $private_key_pem 私钥pem字符串
     * @param string $key_length 密钥长度默认2048
     */
    private static function zbp_rsa_private_decrypt($data, $private_key_pem, $key_length = 2048, $with_hash = false)
    {
        $length = ($key_length / 8);
        $data = base64_decode($data);
        if ($with_hash == true) {
            $hmac = substr($data, 0, 32);
            $data = substr($data, 32);
            $md5_endata = md5($data);
        }
        $dataarray = str_split($data, $length);
        $dedata = null;
        foreach ($dataarray as $single) {
            $dedata_single = null;
            openssl_private_decrypt($single, $dedata_single, $private_key_pem);
            $dedata .= $dedata_single;
        }
        if ($with_hash == false) {
            return $dedata;
        }
        if (hash_hmac('sha256', $dedata, $md5_endata, true) == $hmac) {
            return $dedata;
        }
        return false;
    }

    /**
     * 原始版本的输入密码，附加认证字符串，初始向量的aes256gcm加密函数
     */
    private static function zbp_original_aes256gcm_encrypt($data, $password, $additional, $nonce)
    {
        $password = substr(str_pad($password, 32, '0'), 0, 32);
        $nonce = substr(str_pad($nonce, 12, '0'), 0, 12);
        $func_name = 'sodium_crypto_aead_aes256gcm_is_available';
        if (function_exists($func_name) && $func_name()) {
            $endata = sodium_crypto_aead_aes256gcm_encrypt($data, $additional, $nonce, $password);
        } else {
            $func_name = 'openssl_encrypt';
            $tag = null;
            $endata = $func_name($data, 'aes-256-gcm', $password, OPENSSL_RAW_DATA, $nonce, $tag, $additional, 16);
            $endata .= $tag;
        }
        return base64_encode($endata);
    }

    /**
     * 原始版本的输入密码，附加认证字符串，初始向量的aes256gcm解密函数
     */
    private static function zbp_original_aes256gcm_decrypt($data, $password, $additional, $nonce)
    {
        $password = substr(str_pad($password, 32, '0'), 0, 32);
        $nonce = substr(str_pad($nonce, 12, '0'), 0, 12);
        $data = base64_decode($data);
        $func_name = 'sodium_crypto_aead_aes256gcm_is_available';
        if (function_exists($func_name) && $func_name()) {
            $dedata = sodium_crypto_aead_aes256gcm_decrypt($data, $additional, $nonce, $password);
        } else {
            $tag = substr($data, -16);
            $data = substr($data, 0, -16);
            $func_name = 'openssl_decrypt';
            $dedata = $func_name($data, 'aes-256-gcm', $password, OPENSSL_RAW_DATA, $nonce, $tag, $additional);
        }
        return $dedata;
    }

    /**
     * 原始版本的输入密码，初始向量，mode的aes256加密函数
     */
    private static function zbp_original_aes256_encrypt($data, $password, $iv, $mode = 'cbc')
    {
        $password = substr(str_pad($password, 32, '0'), 0, 32);
        //初始向量长度在ecb下为0，其它为16
        $nonce = substr(str_pad($iv, 16, '0'), 0, 16);
        $mode = str_replace(array('aes', '256', '128', '-', '_'), '', $mode);
        if (!in_array($mode, array('xts', 'cbc', 'cfb', 'ctr', 'ecb', 'ofb'))) {
            $mode = 'cbc';
        }
        if (function_exists('openssl_encrypt')) {
            $mode = strtolower('aes-256-' . $mode);
            $nonce_length = 16;
            if ($mode == 'aes-256-ecb') {
                $nonce_length = 0;
                $nonce = null;
            }
        } elseif (function_exists('mcrypt_encrypt')) {
            if (!$mode == 'ctr') {
                $mode = constant(strtoupper('MCRYPT_MODE_' . $mode));
            }
            $nonce_length = 16;
            if ($mode == constant('MCRYPT_MODE_ECB')) {
                $nonce_length = 0;
                $nonce = null;
            }
        }
        if (function_exists('openssl_encrypt')) {
            $option = (PHP_VERSION_ID < 50400) ? 0 : OPENSSL_RAW_DATA;
            $endata = call_user_func('openssl_encrypt', $data, $mode, $password, $option, $nonce);
            $endata = (PHP_VERSION_ID < 50400) ? base64_decode($endata) : $endata;
        } elseif (function_exists('mcrypt_encrypt')) {
            //pkcs7 pad
            $pkcs7_data = $data;
            if (function_exists('mb_strlen')) {
                $len = mb_strlen($pkcs7_data, '8bit');
            } else {
                $len = strlen($pkcs7_data);
            }
            $c = (16 - ($len % 16));
            $pkcs7_data .= str_repeat(chr($c), $c);
            $endata = call_user_func('mcrypt_encrypt', constant('MCRYPT_RIJNDAEL_128'), $password, $pkcs7_data, $mode, $nonce);
        }
        return base64_encode($endata);
    }

    /**
     * 原始版本的输入密码，初始向量，mode的aes256解密函数
     */
    private static function zbp_original_aes256_decrypt($data, $password, $iv, $mode = 'cbc')
    {
        $password = substr(str_pad($password, 32, '0'), 0, 32);
        $nonce = substr(str_pad($iv, 16, '0'), 0, 16);
        $mode = str_replace(array('aes', '256', '128', '-', '_'), '', $mode);
        if (!in_array($mode, array('xts', 'cbc', 'cfb', 'ctr', 'ecb', 'ofb'))) {
            $mode = 'cbc';
        }
        if (function_exists('openssl_decrypt')) {
            $mode = strtolower('aes-256-' . $mode);
            $nonce_length = 16;
            if ($mode == 'aes-256-ecb') {
                $nonce_length = 0;
                $nonce = null;
            }
        } elseif (function_exists('mcrypt_decrypt')) {
            if (!$mode == 'ctr') {
                $mode = constant(strtoupper('MCRYPT_MODE_' . $mode));
            }
            $nonce_length = 16;
            if ($mode == constant('MCRYPT_MODE_ECB')) {
                $nonce_length = 0;
                $nonce = null;
            }
        }
        $data = base64_decode($data);
        if (function_exists('openssl_encrypt')) {
            $option = (PHP_VERSION_ID < 50400) ? 0 : OPENSSL_RAW_DATA;
            $data = (PHP_VERSION_ID < 50400) ? base64_encode($data) : $data;
            $dedata = call_user_func('openssl_decrypt', $data, $mode, $password, $option, $nonce);
        } elseif (function_exists('mcrypt_decrypt')) {
            $data = call_user_func('mcrypt_decrypt', constant('MCRYPT_RIJNDAEL_128'), $password, $data, $mode, $nonce);
            //pkcs7 unpad
            $text = $data;
            $pad = ord($text[(strlen($text) - 1)]);
            if ($pad > strlen($text)) {
                $data = $text;
            } else {
                if (strspn($text, chr($pad), (strlen($text) - $pad)) != $pad) {
                    $data = $text;
                } else {
                    $data = substr($text, 0, (-1 * $pad));
                }
            }
            $dedata = $data;
        }
        return $dedata;
    }

    /**
     * 原始版本的输入密码，初始向量，mode的aes128加密函数
     */
    private static function zbp_original_aes128_encrypt($data, $password, $iv, $mode = 'cbc')
    {
        //aes128的密码长度16
        $password = substr(str_pad($password, 16, '0'), 0, 16);
        //初始向量长度在ecb下为0，其它为16
        $nonce = substr(str_pad($iv, 16, '0'), 0, 16);
        $mode = str_replace(array('aes', '256', '128', '-', '_'), '', $mode);
        if (!in_array($mode, array('xts', 'cbc', 'cfb', 'ctr', 'ecb', 'ofb'))) {
            $mode = 'cbc';
        }
        if (function_exists('openssl_encrypt')) {
            $mode = strtolower('aes-128-' . $mode);
            $nonce_length = 16;
            if ($mode == 'aes-128-ecb') {
                $nonce_length = 0;
                $nonce = null;
            }
        } elseif (function_exists('mcrypt_encrypt')) {
            if (!$mode == 'ctr') {
                $mode = constant(strtoupper('MCRYPT_MODE_' . $mode));
            }
            $nonce_length = 16;
            if ($mode == constant('MCRYPT_MODE_ECB')) {
                $nonce_length = 0;
                $nonce = null;
            }
        }
        if (function_exists('openssl_encrypt')) {
            $option = (PHP_VERSION_ID < 50400) ? 0 : OPENSSL_RAW_DATA;
            $endata = call_user_func('openssl_encrypt', $data, $mode, $password, $option, $nonce);
            $endata = (PHP_VERSION_ID < 50400) ? base64_decode($endata) : $endata;
        } elseif (function_exists('mcrypt_encrypt')) {
            //pkcs7 pad
            $pkcs7_data = $data;
            if (function_exists('mb_strlen')) {
                $len = mb_strlen($pkcs7_data, '8bit');
            } else {
                $len = strlen($pkcs7_data);
            }
            $c = (16 - ($len % 16));
            $pkcs7_data .= str_repeat(chr($c), $c);
            $endata = call_user_func('mcrypt_encrypt', constant('MCRYPT_RIJNDAEL_128'), $password, $pkcs7_data, $mode, $nonce);
        }
        return base64_encode($endata);
    }

    /**
     * 原始版本的输入密码，初始向量，mode的aes128解密函数
     */
    private static function zbp_original_aes128_decrypt($data, $password, $iv, $mode = 'cbc')
    {
        $password = substr(str_pad($password, 16, '0'), 0, 16);
        $nonce = substr(str_pad($iv, 16, '0'), 0, 16);
        $mode = str_replace(array('aes', '256', '128', '-', '_'), '', $mode);
        if (!in_array($mode, array('xts', 'cbc', 'cfb', 'ctr', 'ecb', 'ofb'))) {
            $mode = 'cbc';
        }
        if (function_exists('openssl_decrypt')) {
            $mode = strtolower('aes-128-' . $mode);
            $nonce_length = 16;
            if ($mode == 'aes-128-ecb') {
                $nonce_length = 0;
                $nonce = null;
            }
        } elseif (function_exists('mcrypt_decrypt')) {
            if (!$mode == 'ctr') {
                $mode = constant(strtoupper('MCRYPT_MODE_' . $mode));
            }
            $nonce_length = 16;
            if ($mode == constant('MCRYPT_MODE_ECB')) {
                $nonce_length = 0;
                $nonce = null;
            }
        }
        $data = base64_decode($data);
        if (function_exists('openssl_encrypt')) {
            $option = (PHP_VERSION_ID < 50400) ? 0 : OPENSSL_RAW_DATA;
            $data = (PHP_VERSION_ID < 50400) ? base64_encode($data) : $data;
            $dedata = call_user_func('openssl_decrypt', $data, $mode, $password, $option, $nonce);
        } elseif (function_exists('mcrypt_decrypt')) {
            $data = call_user_func('mcrypt_decrypt', constant('MCRYPT_RIJNDAEL_128'), $password, $data, $mode, $nonce);
            //pkcs7 unpad
            $text = $data;
            $pad = ord($text[(strlen($text) - 1)]);
            if ($pad > strlen($text)) {
                $data = $text;
            } else {
                if (strspn($text, chr($pad), (strlen($text) - $pad)) != $pad) {
                    $data = $text;
                } else {
                    $data = substr($text, 0, (-1 * $pad));
                }
            }
            $dedata = $data;
        }
        return $dedata;
    }

    /**
     * 原始版本的输入密码，初始向量，mode的sm4的5种模式加密
     */
    private static function zbp_original_sm4_encrypt($data, $password, $iv, $mode = 'cbc')
    {
        $password = substr(str_pad($password, 32, '0'), 0, 32);
        $nonce = substr(str_pad($iv, 16, '0'), 0, 16);
        $mode = str_replace(array('sm4', '-', '_'), '', $mode);
        $sm4_array = array('cbc', 'cfb', 'ctr', 'ecb', 'ofb');
        if (!in_array($mode, $sm4_array)) {
            $mode = "cbc";
        }
        $mode = "sm4-" . $mode;
        if ($mode == 'sm4-ecb') {
            $nonce = null;
        }
        $array = array($data, $mode, $password, OPENSSL_RAW_DATA, $nonce);
        $endata = call_user_func_array('openssl_encrypt', $array);
        return base64_encode($endata);
    }

    /**
     * 原始版本的输入密码，初始向量，mode的sm4的5种模式解密
     */
    private static function zbp_original_sm4_decrypt($data, $password, $iv, $mode = 'cbc')
    {
        $password = substr(str_pad($password, 32, '0'), 0, 32);
        $nonce = substr(str_pad($iv, 16, '0'), 0, 16);
        $mode = str_replace(array('sm4', '-', '_'), '', $mode);
        $sm4_array = array('cbc', 'cfb', 'ctr', 'ecb', 'ofb');
        if (!in_array($mode, $sm4_array)) {
            $mode = "cbc";
        }
        $mode = "sm4-" . $mode;
        $endata = base64_decode($data);
        if ($mode == 'sm4-ecb') {
            $nonce = null;
        }
        $dedata = call_user_func('openssl_decrypt', $endata, $mode, $password, OPENSSL_RAW_DATA, $nonce);
        return $dedata;
    }

    /**
     * chacha20加密
     */
    private static function zbp_chacha20_encrypt($data, $password, $additional = null, $mode = null, $with_hash = false)
    {
        if ($mode == 'chacha20') {//php73
            $nonce_length = 16;
        } elseif ($mode == 'chacha20-poly1305') {
            $nonce_length = 8;
        } elseif ($mode == 'chacha20-poly1305-ieft') {
            $nonce_length = 12;
        } elseif ($mode == 'xchacha20-poly1305-ieft') {
            $nonce_length = 24;
        } else {
            $nonce_length = 12;
            $mode = 'chacha20-poly1305-ieft';
        }
        $nonce = random_bytes($nonce_length);
        $additional_data = $additional;
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;

        if ($mode == 'chacha20') {
            $tag = null;
            $endata = openssl_encrypt($data, 'chacha20', $keygen, 1, $nonce, $tag, $additional_data);
        }
        if ($mode == 'chacha20-poly1305') {
            $endata = sodium_crypto_aead_chacha20poly1305_encrypt($data, $additional_data, $nonce, $keygen);
        }
        if ($mode == 'chacha20-poly1305-ieft') {
            $endata = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($data, $additional_data, $nonce, $keygen);
        }
        if ($mode == 'xchacha20-poly1305-ieft') {
            $endata = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($data, $additional_data, $nonce, $keygen);
        }

        if ($with_hash == false) {
            return base64_encode($nonce . $endata);
        }
        $hmac = hash_hmac('sha256', $data, $keygen . $nonce, true);
        $json = $hmac . $nonce . $endata;
        return base64_encode($json);
    }

    /**
     * chacha20解密
     */
    private static function zbp_chacha20_decrypt($data, $password, $additional = null, $mode = null, $with_hash = false)
    {
        $endata = base64_decode($data);
        if ($mode == 'chacha20') {
            $nonce_length = 16;
        } elseif ($mode == 'chacha20-poly1305') {
            $nonce_length = 8;
        } elseif ($mode == 'chacha20-poly1305-ieft') {
            $nonce_length = 12;
        } elseif ($mode == 'xchacha20-poly1305-ieft') {
            $nonce_length = 24;
        } else {
            $nonce_length = 12;
            $mode = 'chacha20-poly1305-ieft';
        }
        $nonce = random_bytes($nonce_length);
        $additional_data = $additional;
        $md5password = md5(hash('sha256', $password) . $additional);
        $keygen = $md5password;

        if ($with_hash == false) {
            $nonce = substr($endata, 0, $nonce_length);
            $data = substr($endata, (0 + $nonce_length));
        } else {
            $hmac = substr($endata, 0, 32);
            $nonce = substr($endata, 32, $nonce_length);
            $data = substr($endata, (32 + $nonce_length));
        }

        if ($mode == 'chacha20') {
            $tag = null;
            $dedata = openssl_decrypt($data, 'chacha20', $keygen, 1, $nonce, $tag, $additional_data);
        }
        if ($mode == 'chacha20-poly1305') {
            $dedata = sodium_crypto_aead_chacha20poly1305_decrypt($data, $additional_data, $nonce, $keygen);
        }
        if ($mode == 'chacha20-poly1305-ieft') {
            $dedata = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($data, $additional_data, $nonce, $keygen);
        }
        if ($mode == 'xchacha20-poly1305-ieft') {
            $dedata = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt($data, $additional_data, $nonce, $keygen);
        }

        if ($with_hash == false) {
            return $dedata;
        }
        if (hash_hmac('sha256', $dedata, $keygen . $nonce, true) == $hmac) {
            return $dedata;
        }
        return false;
    }

}
