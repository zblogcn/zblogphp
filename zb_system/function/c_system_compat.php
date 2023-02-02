<?php

/**
 * < PHP 5.2.7.
 */
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ((int) $version[0] * 10000 + (int) $version[1] * 100 + (int) $version[2]));
    unset($version);
}

if (!function_exists('fnmatch')) {
    define('FNM_PATHNAME', 1);
    define('FNM_NOESCAPE', 2);
    define('FNM_PERIOD', 4);
    define('FNM_CASEFOLD', 16);

    function fnmatch($pattern, $string, $flags = 0)
    {
        return pcre_fnmatch($pattern, $string, $flags);
    }

    function pcre_fnmatch($pattern, $string, $flags = 0)
    {
        $modifiers = null;
        $transforms = array(
            '\*'      => '.*',
            '\?'      => '.',
            '\[\!'    => '[^',
            '\['      => '[',
            '\]'      => ']',
            '\.'      => '\.',
            '\\'      => '\\\\',
        );

        // Forward slash in string must be in pattern:
        if (($flags & FNM_PATHNAME)) {
            $transforms['\*'] = '[^/]*';
        }

        // Back slash should not be escaped:
        if (($flags & FNM_NOESCAPE)) {
            unset($transforms['\\']);
        }

        // Perform case insensitive match:
        if (($flags & FNM_CASEFOLD)) {
            $modifiers .= 'i';
        }

        // Period at start must be the same as pattern:
        if (($flags & FNM_PERIOD)) {
            if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) {
                return false;
            }
        }

        $pattern = '#^'
            . strtr(preg_quote($pattern, '#'), $transforms)
            . '$#'
            . $modifiers;

        return (bool) preg_match($pattern, $string);
    }

}

if (!function_exists('hex2bin')) {

    function hex2bin($str)
    {
        $sbin = "";
        $len = strlen($str);
        for ($i = 0; $i < $len; $i += 2) {
            $sbin .= pack("H*", substr($str, $i, 2));
        }

        return $sbin;
    }

}

if (!function_exists('rrmdir')) {

    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            if (function_exists('scandir')) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != '.' && $object != '..') {
                        if (filetype($dir . '/' . $object) == 'dir') {
                            rrmdir($dir . '/' . $object);
                        } else {
                            unlink($dir . '/' . $object);
                        }
                    }
                }
                reset($objects);
                rmdir($dir);
            } else {
                if ($handle = opendir($dir)) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            if (is_dir(rtrim(rtrim($dir, '/'), '\\') . '/' . $file)) {
                                rrmdir(rtrim(rtrim($dir, '/'), '\\') . '/' . $file);
                            } else {
                                unlink(rtrim(rtrim($dir, '/'), '\\') . '/' . $file);
                            }
                        }
                    }
                    closedir($handle);
                    rmdir($dir);
                }
            }
        }
    }

}

/*
 * URL constants as defined in the PHP Manual under "Constants usable with
 * http_build_url()".
 *
 * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
 * @see  https://github.com/jakeasmith/http_build_url/blob/master/src/http_build_url.php
 * @license  MIT
 */
if (!defined('HTTP_URL_REPLACE')) {
    define('HTTP_URL_REPLACE', 1);
}
if (!defined('HTTP_URL_JOIN_PATH')) {
    define('HTTP_URL_JOIN_PATH', 2);
}
if (!defined('HTTP_URL_JOIN_QUERY')) {
    define('HTTP_URL_JOIN_QUERY', 4);
}
if (!defined('HTTP_URL_STRIP_USER')) {
    define('HTTP_URL_STRIP_USER', 8);
}
if (!defined('HTTP_URL_STRIP_PASS')) {
    define('HTTP_URL_STRIP_PASS', 16);
}
if (!defined('HTTP_URL_STRIP_AUTH')) {
    define('HTTP_URL_STRIP_AUTH', 32);
}
if (!defined('HTTP_URL_STRIP_PORT')) {
    define('HTTP_URL_STRIP_PORT', 64);
}
if (!defined('HTTP_URL_STRIP_PATH')) {
    define('HTTP_URL_STRIP_PATH', 128);
}
if (!defined('HTTP_URL_STRIP_QUERY')) {
    define('HTTP_URL_STRIP_QUERY', 256);
}
if (!defined('HTTP_URL_STRIP_FRAGMENT')) {
    define('HTTP_URL_STRIP_FRAGMENT', 512);
}
if (!defined('HTTP_URL_STRIP_ALL')) {
    define('HTTP_URL_STRIP_ALL', 1024);
}

if (!function_exists('http_build_url')) {

    /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to
     * the flags argument.
     *
     * @param mixed $url     (part(s) of) an URL in form of a string or
     *                       associative array like parse_url() returns
     * @param mixed $parts   same as the first argument
     * @param int   $flags   a bitmask of binary or'ed HTTP_URL constants;
     *                       HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the
     *                       composed url like parse_url() would return
     *
     * @return string
     */
    function http_build_url($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = array())
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);
        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;
        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');
        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if (($flags & HTTP_URL_STRIP_ALL)) {
            $flags |= (HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
        } elseif (($flags & HTTP_URL_STRIP_AUTH)) {
            $flags |= (HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS);
        }
        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }
        if (($flags & HTTP_URL_REPLACE)) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    // Workaround for trailing slashes
                    $url['path'] .= 'a';
                    $url['path'] = rtrim(
                        str_replace(basename($url['path']), '', $url['path']),
                        '/'
                    ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);
                    $url['query'] = http_build_query(
                        array_replace_recursive(
                            $url_query,
                            $parts_query
                        )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }
        }
        if (isset($url['path']) && $url['path'] !== '' && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }
        foreach ($keys as $key) {
            $strip = 'HTTP_URL_STRIP_' . strtoupper($key);
            if (($flags & constant($strip))) {
                unset($url[$key]);
            }
        }
        $parsed_string = '';
        if (!empty($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }
        if (!empty($url['user'])) {
            $parsed_string .= $url['user'];
            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }
            $parsed_string .= '@';
        }
        if (!empty($url['host'])) {
            $parsed_string .= $url['host'];
        }
        if (!empty($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }
        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        }
        if (!empty($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }
        if (!empty($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }
        $new_url = $url;

        return $parsed_string;
    }

}

if (!function_exists('gzdecode')) {

    function gzdecode($data)
    {
        $len = strlen($data);
        if ($len < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
            return;  // Not GZIP format (See RFC 1952)
        }
        $method = ord(substr($data, 2, 1));  // Compression method
         $flags = ord(substr($data, 3, 1));  // Flags
        if (($flags & 31) != $flags) {
            // Reserved bits are set -- NOT ALLOWED by RFC 1952
            return;
        }
        // NOTE: $mtime may be negative (PHP integer limitations)
        $mtime = unpack("V", substr($data, 4, 4));
        $mtime = $mtime[1];
        $xfl = substr($data, 8, 1);
        $os = substr($data, 8, 1);
        $headerlen = 10;
        $extralen = 0;
        $extra = "";
        if (($flags & 4)) {
            // 2-byte length prefixed EXTRA data in header
            if (($len - $headerlen - 2) < 8) {
                return false;    // Invalid format
            }
            $extralen = unpack("v", substr($data, 8, 2));
            $extralen = $extralen[1];
            if (($len - $headerlen - 2 - $extralen) < 8) {
                return false;    // Invalid format
            }
            $extra = substr($data, 10, $extralen);
            $headerlen += (2 + $extralen);
        }

        $filenamelen = 0;
        $filename = "";
        if (($flags & 8)) {
            // C-style string file NAME data in header
            if (($len - $headerlen - 1) < 8) {
                return false;    // Invalid format
            }
            $filenamelen = strpos(substr($data, (8 + $extralen)), chr(0));
            if ($filenamelen === false || ($len - $headerlen - $filenamelen - 1) < 8) {
                return false;    // Invalid format
            }
            $filename = substr($data, $headerlen, $filenamelen);
            $headerlen += ($filenamelen + 1);
        }

        $commentlen = 0;
        $comment = "";
        if (($flags & 16)) {
            // C-style string COMMENT data in header
            if (($len - $headerlen - 1) < 8) {
                return false;    // Invalid format
            }
            $commentlen = strpos(substr($data, (8 + $extralen + $filenamelen)), chr(0));
            if ($commentlen === false || ($len - $headerlen - $commentlen - 1) < 8) {
                return false;    // Invalid header format
            }
            $comment = substr($data, $headerlen, $commentlen);
            $headerlen += ($commentlen + 1);
        }

        $headercrc = "";
        if (($flags & 2)) {
            // 2-bytes (lowest order) of CRC32 on header present
            if (($len - $headerlen - 2) < 8) {
                return false;    // Invalid format
            }
            $calccrc = (crc32(substr($data, 0, $headerlen)) & 0xffff);
            $headercrc = unpack("v", substr($data, $headerlen, 2));
            $headercrc = $headercrc[1];
            if ($headercrc != $calccrc) {
                return false;    // Bad header CRC
            }
            $headerlen += 2;
        }

        // GZIP FOOTER - These be negative due to PHP's limitations
        $datacrc = unpack("V", substr($data, -8, 4));
        $datacrc = $datacrc[1];
        $isize = unpack("V", substr($data, -4));
        $isize = $isize[1];

        // Perform the decompression:
        $bodylen = ($len - $headerlen - 8);
        if ($bodylen < 1) {
            // This should never happen - IMPLEMENTATION BUG!
            return;
        }
        $body = substr($data, $headerlen, $bodylen);
        $data = "";
        if ($bodylen > 0) {
            switch ($method) {
                case 8:
                    // Currently the only supported compression method:
                    $data = gzinflate($body);
                    break;
                default:
                    // Unknown compression method
                    return false;
            }
        } else {
            // I'm not sure if zero-byte body content is allowed.
            // Allow it for now...  Do nothing...
        }

        // Verifiy decompressed size and CRC32:
        // NOTE: This may fail with large data sizes depending on how
        //       PHP's integer limitations affect strlen() since $isize
        //       may be negative for large sizes.
        if ($isize != strlen($data) || crc32($data) != $datacrc) {
            // Bad format!  Length or CRC doesn't match!
            return false;
        }

        return $data;
    }

}

if (!function_exists('session_status')) {

    function session_status()
    {
        if (!extension_loaded('session')) {
            return 0;
        } elseif (!session_id()) {
            return 1;
        } else {
            return 2;
        }
    }

}

if (!function_exists('array_replace_recursive')) {

    function _compat_recurse($array, $array1)
    {
        foreach ($array1 as $key => $value) {
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                $array[$key] = array();
            }

            // overwrite the value in the base array
            if (is_array($value)) {
                $value = _compat_recurse($array[$key], $value);
            }
            $array[$key] = $value;
        }

        return $array;
    }

    function array_replace_recursive($array, $array1)
    {
        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];
        if (!is_array($array)) {
            return $array;
        }
        for ($i = 1; $i < count($args); $i++) {
            if (is_array($args[$i])) {
                $array = _compat_recurse($array, $args[$i]);
            }
        }
        return $array;
    }

}


if (!function_exists('hash_equals')) {

    /**
     * Timing attack safe string comparison
     *
     * Compares two strings using the same time whether they're equal or not.
     * This function should be used to mitigate timing attacks; for instance, when testing crypt() password hashes.
     *
     * @param string $known_string The string of known length to compare against
     * @param string $user_string The user-supplied string
     * @return boolean Returns TRUE when the two strings are equal, FALSE otherwise.
     */
    function hash_equals($known_string, $user_string)
    {
        if (func_num_args() !== 2) {
            // handle wrong parameter count as the native implentation
            trigger_error('hash_equals() expects exactly 2 parameters, ' . func_num_args() . ' given', E_USER_WARNING);
            return null;
        }
        if (is_string($known_string) !== true) {
            trigger_error('hash_equals(): Expected known_string to be a string, ' . gettype($known_string) . ' given', E_USER_WARNING);
            return false;
        }
        $known_string_len = strlen($known_string);
        $user_string_type_error = 'hash_equals(): Expected user_string to be a string, ' . gettype($user_string) . ' given'; // prepare wrong type error message now to reduce the impact of string concatenation and the gettype call
        if (is_string($user_string) !== true) {
            trigger_error($user_string_type_error, E_USER_WARNING);
            // prevention of timing attacks might be still possible if we handle $user_string as a string of diffent length (the trigger_error() call increases the execution time a bit)
            $user_string_len = strlen($user_string);
            $user_string_len = $known_string_len + 1;
        } else {
            $user_string_len = $known_string_len + 1;
            $user_string_len = strlen($user_string);
        }
        if ($known_string_len !== $user_string_len) {
            $res = $known_string ^ $known_string; // use $known_string instead of $user_string to handle strings of diffrent length.
            $ret = 1; // set $ret to 1 to make sure false is returned
        } else {
            $res = $known_string ^ $user_string;
            $ret = 0;
        }
        for ($i = strlen($res) - 1; $i >= 0; $i--) {
            $ret |= ord($res[$i]);
        }
        return $ret === 0;
    }

}


if (!function_exists('emptyFunction')) {
    function emptyFunction() {}
}

