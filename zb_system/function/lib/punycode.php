<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * Copyright (c) 2014 TrueServer B.V.
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:.
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * Punycode implementation as described in RFC 3492
 *
 * @link http://tools.ietf.org/html/rfc3492
 * @see https://github.com/true/php-punycode
 */
class Punycode
{
    /**
     * Bootstring parameter values.
     */
    const BASE = 36;
    const TMIN = 1;
    const TMAX = 26;
    const SKEW = 38;
    const DAMP = 700;
    const INITIAL_BIAS = 72;
    const INITIAL_N = 128;
    const PREFIX = 'xn--';
    const DELIMITER = '-';

    /**
     * Encode table.
     *
     * @param array
     */
    protected static $encodeTable = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    );

    /**
     * Decode table.
     *
     * @param array
     */
    protected static $decodeTable = array(
        'a' => 0, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 4, 'f' => 5,
        'g' => 6, 'h' => 7, 'i' => 8, 'j' => 9, 'k' => 10, 'l' => 11,
        'm' => 12, 'n' => 13, 'o' => 14, 'p' => 15, 'q' => 16, 'r' => 17,
        's' => 18, 't' => 19, 'u' => 20, 'v' => 21, 'w' => 22, 'x' => 23,
        'y' => 24, 'z' => 25, '0' => 26, '1' => 27, '2' => 28, '3' => 29,
        '4' => 30, '5' => 31, '6' => 32, '7' => 33, '8' => 34, '9' => 35,
    );

    /**
     * Character encoding.
     *
     * @param string
     */
    protected $encoding;

    /**
     * Constructor.
     *
     * @param string $encoding Character encoding
     */
    public function __construct($encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
    }

    /**
     * Encode a domain to its Punycode version.
     *
     * @param string $input Domain name in Unicode to be encoded
     *
     * @return string Punycode representation in ASCII
     */
    public function encode($input)
    {
        $input = mb_strtolower($input, $this->encoding);
        $url = parse_url($input);
        $host = $url['host'];
        $parts = explode('.', $host);
        foreach ($parts as &$part) {
            $length = strlen($part);
            if ($length < 1) {
                throw new Exception(sprintf('The length of any one label is limited to between 1 and 63 octets, but %s given.', $length));
            }
            $part = $this->encodePart($part);
        }
        $output = implode('.', $parts);
        $length = strlen($output);
        if ($length > 255) {
            throw new Exception(sprintf('A full domain name is limited to 255 octets (including the separators), %s given.', $length));
        }
        $url['host'] = $output;

        return http_build_url($url);
    }

    /**
     * Encode a part of a domain name, such as tld, to its Punycode version.
     *
     * @param string $input Part of a domain name
     *
     * @return string Punycode representation of a domain part
     */
    protected function encodePart($input)
    {
        $codePoints = $this->listCodePoints($input);
        $n = self::INITIAL_N;
        $bias = self::INITIAL_BIAS;
        $delta = 0;
        $h = $b = count($codePoints['basic']);
        $output = '';
        foreach ($codePoints['basic'] as $code) {
            $output .= $this->codePointToChar($code);
        }
        if ($input === $output) {
            return $output;
        }
        if ($b > 0) {
            $output .= self::DELIMITER;
        }
        $codePoints['nonBasic'] = array_unique($codePoints['nonBasic']);
        sort($codePoints['nonBasic']);
        $i = 0;
        $length = mb_strlen($input, $this->encoding);
        while ($h < $length) {
            $m = $codePoints['nonBasic'][$i++];
            $delta = ($delta + ($m - $n) * ($h + 1));
            $n = $m;
            foreach ($codePoints['all'] as $c) {
                if ($c < $n || $c < self::INITIAL_N) {
                    $delta++;
                }
                if ($c === $n) {
                    $q = $delta;
                    for ($k = self::BASE;; $k += self::BASE) {
                        $t = $this->calculateThreshold($k, $bias);
                        if ($q < $t) {
                            break;
                        }
                        $code = ($t + (($q - $t) % (self::BASE - $t)));
                        $output .= self::$encodeTable[$code];
                        $q = (($q - $t) / (self::BASE - $t));
                    }
                    $output .= self::$encodeTable[$q];
                    $bias = $this->adapt($delta, ($h + 1), ($h === $b));
                    $delta = 0;
                    $h++;
                }
            }
            $delta++;
            $n++;
        }
        $out = self::PREFIX . $output;
        $length = strlen($out);
        if ($length > 63 || $length < 1) {
            throw new Exception(sprintf('The length of any one label is limited to between 1 and 63 octets, but %s given.', $length));
        }

        return $out;
    }

    /**
     * Decode a Punycode domain name to its Unicode counterpart.
     *
     * @param string $input Domain name in Punycode
     *
     * @return string Unicode domain name
     */
    public function decode($input)
    {
        $input = strtolower($input);
        $url = parse_url($input);
        $host = $url['host'];
        $parts = explode('.', $host);
        foreach ($parts as &$part) {
            $length = strlen($part);
            if ($length > 63 || $length < 1) {
                throw new Exception(sprintf('The length of any one label is limited to between 1 and 63 octets, but %s given.', $length));
            }
            if (strpos($part, self::PREFIX) !== 0) {
                continue;
            }
            $part = substr($part, strlen(self::PREFIX));
            $part = $this->decodePart($part);
        }
        $output = implode('.', $parts);
        $length = strlen($output);
        if ($length > 255) {
            throw new Exception(sprintf('A full domain name is limited to 255 octets (including the separators), %s given.', $length));
        }

        $url['host'] = $output;

        return http_build_url($url);
    }

    /**
     * Decode a part of domain name, such as tld.
     *
     * @param string $input Part of a domain name
     *
     * @return string Unicode domain part
     */
    protected function decodePart($input)
    {
        $n = self::INITIAL_N;
        $i = 0;
        $bias = self::INITIAL_BIAS;
        $output = '';
        $pos = strrpos($input, self::DELIMITER);
        if ($pos !== false) {
            $output = substr($input, 0, $pos++);
        } else {
            $pos = 0;
        }
        $outputLength = strlen($output);
        $inputLength = strlen($input);
        while ($pos < $inputLength) {
            $oldi = $i;
            $w = 1;
            for ($k = self::BASE;; $k += self::BASE) {
                $digit = self::$decodeTable[$input[$pos++]];
                $i = ($i + ($digit * $w));
                $t = $this->calculateThreshold($k, $bias);
                if ($digit < $t) {
                    break;
                }
                $w = ($w * (self::BASE - $t));
            }
            $bias = $this->adapt(($i - $oldi), ++$outputLength, ($oldi === 0));
            $n = ($n + (int) ($i / $outputLength));
            $i = ($i % ($outputLength));
            $output = mb_substr($output, 0, $i, $this->encoding) . $this->codePointToChar($n) . mb_substr($output, $i, ($outputLength - 1), $this->encoding);
            $i++;
        }

        return $output;
    }

    /**
     * Calculate the bias threshold to fall between TMIN and TMAX.
     *
     * @param int $k
     * @param int $bias
     *
     * @return int
     */
    protected function calculateThreshold($k, $bias)
    {
        if ($k <= ($bias + self::TMIN)) {
            return self::TMIN;
        } elseif ($k >= ($bias + self::TMAX)) {
            return self::TMAX;
        }

        return ($k - $bias);
    }

    /**
     * Bias adaptation.
     *
     * @param int  $delta
     * @param int  $numPoints
     * @param bool $firstTime
     *
     * @return int
     */
    protected function adapt($delta, $numPoints, $firstTime)
    {
        $delta = (int) (
            ($firstTime) ? $delta / self::DAMP : $delta / 2
        );
        $delta += (int) ($delta / $numPoints);
        $k = 0;
        while ($delta > (((self::BASE - self::TMIN) * self::TMAX) / 2)) {
            $delta = (int) ($delta / (self::BASE - self::TMIN));
            $k = ($k + self::BASE);
        }
        $k = ($k + (int) (((self::BASE - self::TMIN + 1) * $delta) / ($delta + self::SKEW)));

        return $k;
    }

    /**
     * List code points for a given input.
     *
     * @param string $input
     *
     * @return array Multi-dimension array with basic, non-basic and aggregated code points
     */
    protected function listCodePoints($input)
    {
        $codePoints = array(
            'all'      => array(),
            'basic'    => array(),
            'nonBasic' => array(),
        );
        $length = mb_strlen($input, $this->encoding);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($input, $i, 1, $this->encoding);
            $code = $this->charToCodePoint($char);
            if ($code < 128) {
                $codePoints['all'][] = $codePoints['basic'][] = $code;
            } else {
                $codePoints['all'][] = $codePoints['nonBasic'][] = $code;
            }
        }

        return $codePoints;
    }

    /**
     * Convert a single or multi-byte character to its code point.
     *
     * @param string $char
     *
     * @return int
     */
    protected function charToCodePoint($char)
    {
        $code = ord($char[0]);
        if ($code < 128) {
            return $code;
        } elseif ($code < 224) {
            return ((($code - 192) * 64) + (ord($char[1]) - 128));
        } elseif ($code < 240) {
            return ((($code - 224) * 4096) + ((ord($char[1]) - 128) * 64) + (ord($char[2]) - 128));
        } else {
            return ((($code - 240) * 262144) + ((ord($char[1]) - 128) * 4096) + ((ord($char[2]) - 128) * 64) + (ord($char[3]) - 128));
        }
    }

    /**
     * Convert a code point to its single or multi-byte character.
     *
     * @param int $code
     *
     * @return string
     */
    protected function codePointToChar($code)
    {
        if ($code <= 0x7F) {
            return chr($code);
        } elseif ($code <= 0x7FF) {
            return chr(($code >> 6) + 192) . chr(($code & 63) + 128);
        } elseif ($code <= 0xFFFF) {
            return chr(($code >> 12) + 224) . chr((($code >> 6) & 63) + 128) . chr(($code & 63) + 128);
        } else {
            return chr(($code >> 18) + 240) . chr((($code >> 12) & 63) + 128) . chr((($code >> 6) & 63) + 128) . chr(($code & 63) + 128);
        }
    }

}
