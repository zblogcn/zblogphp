<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 验证码类.
 */
/**
 * Builds a new captcha image
 * Uses the fingerprint parameter, if one is passed, to generate the same image.
 *
 * @author Gregwar <g.passault@gmail.com>
 * @author Jeremy Livingston <jeremy.j.livingston@gmail.com>
 * @author zsx <zsx@zsxsoft.com>
 *
 * @see https://github.com/Gregwar/Captcha
 *
 * @license MIT
 */
class ValidateCode
{

    /**
     * @var array
     */
    protected $fingerprint = array();

    /**
     * @var bool
     */
    protected $useFingerprint = false;

    /**
     * @var array
     */
    protected $textColor = null;

    /**
     * @var array
     */
    protected $backgroundColor = null;

    /**
     * @var resource
     */
    protected $contents = null;

    /**
     * @var string
     */
    protected $phrase = null;

    /**
     * @var bool
     */
    protected $distortion = true;

    /**
     * The maximum number of lines to draw in front of
     * the image. null - use default algorithm.
     */
    protected $maxFrontLines = null;

    /**
     * The maximum number of lines to draw behind
     * the image. null - use default algorithm.
     */
    protected $maxBehindLines = null;

    /**
     * The maximum angle of char.
     */
    protected $maxAngle = 8;

    /**
     * The maximum offset of char.
     */
    protected $maxOffset = 5;

    /**
     * Is the interpolation enabled ?
     *
     * @var bool
     */
    protected $interpolation = true;

    /**
     * Ignore all effects.
     *
     * @var bool
     */
    protected $ignoreAllEffects = false;

    protected $charset = 'ABCDEFGHKMNPRSTUVWXYZ123456789';

    protected $codelen = 5; //位数

    protected $width = 90; //宽度

    protected $height = 30; //高度

    protected $font; //字体

    protected $fontsize = 15; //字体大小

    /**
     * The image contents.
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Enable/Disables the interpolation.
     *
     * @param $interpolate bool  True to enable, false to disable
     *
     * @return CaptchaBuilder
     */
    public function setInterpolation($interpolate = true)
    {
        $this->interpolation = $interpolate;

        return $this;
    }

    public function __construct()
    {
        global $zbp;
        $this->font = $zbp->path . (isset($zbp->option['ZC_VERIFYCODE_FONT']) ? $zbp->option['ZC_VERIFYCODE_FONT'] : 'zb_system/defend/arial.ttf');
        $this->charset = $zbp->option['ZC_VERIFYCODE_STRING'];
        if ($this->charset == null) {
            $this->charset = 'ABCDEFGHKMNPRSTUVWXYZ123456789';
        }
        $this->width = $zbp->option['ZC_VERIFYCODE_WIDTH'];
        if ($this->width == 0) {
            $this->width = 90;
        }
        $this->height = $zbp->option['ZC_VERIFYCODE_HEIGHT'];
        if ($this->height == 0) {
            $this->height = 30;
        }
    }

    /**
     * 生成随机码
     */
    protected function createCode($n = null)
    {
        if ($n !== null) {
            $this->phrase = (string) $n;

            return;
        }
        $_len = (strlen($this->charset) - 1);
        for ($i = 0; $i < $this->codelen; $i++) {
            if (function_exists('mt_rand')) {
                $this->phrase .= $this->charset[mt_rand(0, $_len)];
            } else {
                $this->phrase .= $this->charset[rand(0, $_len)];
            }
        }
    }

    /**
     * Setting the phrase.
     */
    public function setPhrase($phrase)
    {
        $this->phrase = (string) $phrase;
    }

    /**
     * Enables/disable distortion.
     */
    public function setDistortion($distortion)
    {
        $this->distortion = (bool) $distortion;

        return $this;
    }

    public function setMaxBehindLines($maxBehindLines)
    {
        $this->maxBehindLines = $maxBehindLines;

        return $this;
    }

    public function setMaxFrontLines($maxFrontLines)
    {
        $this->maxFrontLines = $maxFrontLines;

        return $this;
    }

    public function setMaxAngle($maxAngle)
    {
        $this->maxAngle = $maxAngle;

        return $this;
    }

    public function setMaxOffset($maxOffset)
    {
        $this->maxOffset = $maxOffset;

        return $this;
    }

    /**
     * Gets the captcha phrase.
     */
    public function getPhrase()
    {
        return strtolower($this->phrase);
    }

    /**
     * Instantiation.
     */
    public static function create($phrase = null)
    {
        return new self($phrase);
    }

    /**
     * Sets the text color to use.
     */
    public function setTextColor($r, $g, $b)
    {
        $this->textColor = array($r, $g, $b);

        return $this;
    }

    /**
     * Sets the background color to use.
     */
    public function setBackgroundColor($r, $g, $b)
    {
        $this->backgroundColor = array($r, $g, $b);

        return $this;
    }

    /**
     * Sets the ignoreAllEffects value.
     *
     * @param bool $ignoreAllEffects
     *
     * @return CaptchaBuilder
     */
    public function setIgnoreAllEffects($ignoreAllEffects)
    {
        $this->ignoreAllEffects = $ignoreAllEffects;

        return $this;
    }

    /**
     * Sets the list of background images to use (one image is randomly selected).
     */
    public function setBackgroundImages(array $backgroundImages)
    {
        $this->backgroundImages = $backgroundImages;

        return $this;
    }

    /**
     * Draw lines over the image.
     */
    protected function drawLine($image, $width, $height, $tcol = null)
    {
        return ;
        if ($tcol === null) {
            $tcol = imagecolorallocate($image, $this->rand(100, 255), $this->rand(100, 255), $this->rand(100, 255));
        }

        if ($this->rand(0, 1)) { // Horizontal
            $Xa = $this->rand(0, ($width / 2));
            $Ya = $this->rand(0, $height);
            $Xb = $this->rand(($width / 2), $width);
            $Yb = $this->rand(0, $height);
        } else { // Vertical
            $Xa = $this->rand(0, $width);
            $Ya = $this->rand(0, ($height / 2));
            $Xb = $this->rand(0, $width);
            $Yb = $this->rand(($height / 2), $height);
        }
        imagesetthickness($image, $this->rand(1, 3));
        imageline($image, $Xa, $Ya, $Xb, $Yb, $tcol);
    }

    /**
     * Apply some post effects.
     */
    protected function postEffect($image)
    {
        if (!function_exists('imagefilter')) {
            return;
        }

        if ($this->backgroundColor != null || $this->textColor != null) {
            return;
        }

        // Negate ?
        if ($this->rand(0, 1) == 0) {
            imagefilter($image, IMG_FILTER_NEGATE);
        }

        // Edge ?
        if ($this->rand(0, 10) == 0) {
            imagefilter($image, IMG_FILTER_EDGEDETECT);
        }

        // Contrast
        imagefilter($image, IMG_FILTER_CONTRAST, $this->rand(-50, 10));

        // Colorize
        if ($this->rand(0, 5) == 0) {
            imagefilter($image, IMG_FILTER_COLORIZE, $this->rand(-80, 50), $this->rand(-80, 50), $this->rand(-80, 50));
        }
    }

    /**
     * Writes the phrase on the image.
     */
    protected function writePhrase($image, $phrase, $font, $width, $height)
    {
        $length = strlen($phrase);
        if ($length === 0) {
            return imagecolorallocate($image, 0, 0, 0);
        }

        // Gets the text size and start position
        $size = $this->fontsize;
        $box = imagettfbbox($size, 0, $font, $phrase);
        $textWidth = ($box[2] - $box[0]);
        $textHeight = ($box[1] - $box[7]);
        $x = (($width - $textWidth) / 2);
        $y = (($height - $textHeight) / 2 + $size);

        //if (isset($this->textCount) && is_array($this->textCount) && !count($this->textColor)) {
        $textColor = array($this->rand(0, 150), $this->rand(0, 150), $this->rand(0, 150));
        //} else {
        //    $textColor = $this->textColor;
        //}

        @$col = imagecolorallocate($image, $textColor[0], $textColor[1], $textColor[2]);

        // Write the letters one by one, with random angle
        for ($i = 0; $i < $length; $i++) {
            $box = imagettfbbox($size, 0, $font, $phrase[$i]);
            $w = ($box[2] - $box[0]);
            $angle = $this->rand(-$this->maxAngle, $this->maxAngle);
            $offset = $this->rand(-$this->maxOffset, $this->maxOffset);
            imagettftext($image, $size, $angle, $x, ($y + $offset), $col, $font, $phrase[$i]);
            $x += $w;
        }

        return $col;
    }

    /**
     * Generate the image.
     */
    public function build($fingerprint = null)
    {
        $width = $this->width;
        $height = $this->height;
        if (null !== $fingerprint) {
            $this->fingerprint = $fingerprint;
            $this->useFingerprint = true;
        } else {
            $this->fingerprint = array();
            $this->useFingerprint = false;
        }

        // if background images list is not set, use a color fill as a background
        $image = imagecreatetruecolor($width, $height);
        if ($this->backgroundColor == null) {
            $bg = imagecolorallocate($image, $this->rand(200, 255), $this->rand(200, 255), $this->rand(200, 255));
        } else {
            $color = $this->backgroundColor;
            $bg = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        }
        $this->background = $bg;
        imagefill($image, 0, 0, $bg);

        // Apply effects
        if (!$this->ignoreAllEffects) {
            $square = ($width * $height);
            $effects = $this->rand(($square / 3000), ($square / 2000));

            // set the maximum number of lines to draw in front of the text
            if ($this->maxBehindLines != null && $this->maxBehindLines > 0) {
                $effects = min($this->maxBehindLines, $effects);
            }

            if ($this->maxBehindLines !== 0) {
                for ($e = 0; $e < $effects; $e++) {
                    $this->drawLine($image, $width, $height);
                }
            }
        }

        // Write CAPTCHA text
        $color = $this->writePhrase($image, $this->phrase, $this->font, $width, $height);

        // Apply effects
        if (!$this->ignoreAllEffects) {
            $square = ($width * $height);
            $effects = $this->rand(($square / 2000), ($square / 2000));

            // set the maximum number of lines to draw in front of the text
            if ($this->maxFrontLines != null && $this->maxFrontLines > 0) {
                $effects = min($this->maxFrontLines, $effects);
            }

            if ($this->maxFrontLines !== 0) {
                for ($e = 0; $e < $effects; $e++) {
                    $this->drawLine($image, $width, $height, $color);
                }
            }
        }

        // Distort the image
        if ($this->distortion && !$this->ignoreAllEffects) {
            $image = $this->distort($image, $width, $height, $bg);
        }

        // Post effects
        if (!$this->ignoreAllEffects) {
            $this->postEffect($image);
        }

        $this->contents = $image;

        return $this;
    }

    /**
     * Distorts the image.
     */
    public function distort($image, $width, $height, $bg)
    {
        $contents = imagecreatetruecolor($width, $height);
        $X = $this->rand(0, $width);
        $Y = $this->rand(0, $height);
        $phase = $this->rand(0, 10);
        $scale = (1.1 + $this->rand(0, 10000) / 30000);
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $Vx = ($x - $X);
                $Vy = ($y - $Y);
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if ($Vn != 0) {
                    $Vn2 = ($Vn + 4 * sin($Vn / 30));
                    $nX = ($X + ($Vx * $Vn2 / $Vn));
                    $nY = ($Y + ($Vy * $Vn2 / $Vn));
                } else {
                    $nX = $X;
                    $nY = $Y;
                }
                $nY = ($nY + $scale * sin($phase + $nX * 0.2));

                if ($this->interpolation) {
                    $p = $this->interpolate(
                        ($nX - floor($nX)),
                        ($nY - floor($nY)),
                        $this->getCol($image, floor($nX), floor($nY), $bg),
                        $this->getCol($image, ceil($nX), floor($nY), $bg),
                        $this->getCol($image, floor($nX), ceil($nY), $bg),
                        $this->getCol($image, ceil($nX), ceil($nY), $bg)
                    );
                } else {
                    $p = $this->getCol($image, round($nX), round($nY), $bg);
                }

                if ($p == 0) {
                    $p = $bg;
                }

                imagesetpixel($contents, $x, $y, $p);
            }
        }

        return $contents;
    }

    /**
     * Gets the image GD.
     */
    public function getGd()
    {
        return $this->contents;
    }

    /**
     * Gets the image contents.
     */
    public function get($quality = 90)
    {
        ob_start();
        $this->directOutput($quality);

        return ob_get_clean();
    }

    /**
     * Gets the HTML inline base64.
     */
    public function inline($quality = 90)
    {
        return 'data:image/jpeg;base64,' . base64_encode($this->get($quality));
    }

    /**
     * Outputs the image.
     */
    public function directOutput($quality = 90)
    {
        imagejpeg($this->contents, null, $quality);
    }

    /**
     * Outputs the image.
     */
    public function output($n = null)
    {
        header('Content-type: image/jpeg');
        $this->createCode($n);
        $this->build();
        $this->directOutput();
    }

    /**
     * 对外生成.
     */
    public function GetImg($n = null)
    {
        $this->outPut($n);
    }

    /**
     * @return array
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * Returns a random number or the next number in the
     * fingerprint.
     */
    protected function rand($min, $max)
    {
        if (!is_array($this->fingerprint)) {
            $this->fingerprint = array();
        }

        if ($this->useFingerprint) {
            $value = current($this->fingerprint);
            next($this->fingerprint);
        } else {
            $value = mt_rand($min, $max);
            $this->fingerprint[] = $value;
        }

        return $value;
    }

    /**
     * @param $x
     * @param $y
     * @param $nw
     * @param $ne
     * @param $sw
     * @param $se
     *
     * @return int
     */
    protected function interpolate($x, $y, $nw, $ne, $sw, $se)
    {
        list($r0, $g0, $b0) = $this->getRGB($nw);
        list($r1, $g1, $b1) = $this->getRGB($ne);
        list($r2, $g2, $b2) = $this->getRGB($sw);
        list($r3, $g3, $b3) = $this->getRGB($se);

        $cx = (1.0 - $x);
        $cy = (1.0 - $y);

        $m0 = ($cx * $r0 + $x * $r1);
        $m1 = ($cx * $r2 + $x * $r3);
        $r = (int) ($cy * $m0 + $y * $m1);

        $m0 = ($cx * $g0 + $x * $g1);
        $m1 = ($cx * $g2 + $x * $g3);
        $g = (int) ($cy * $m0 + $y * $m1);

        $m0 = ($cx * $b0 + $x * $b1);
        $m1 = ($cx * $b2 + $x * $b3);
        $b = (int) ($cy * $m0 + $y * $m1);

        return (($r << 16) | ($g << 8) | $b);
    }

    /**
     * @param $image
     * @param $x
     * @param $y
     *
     * @return int
     */
    protected function getCol($image, $x, $y, $background)
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x < 0 || $x >= $L || $y < 0 || $y >= $H) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }

    /**
     * @param $col
     *
     * @return array
     */
    protected function getRGB($col)
    {
        return array(
            (int) ($col >> 16) & 0xff,
            (int) ($col >> 8) & 0xff,
            (int) ($col) & 0xff,
        );
    }

    /**
     * 获取验证码
     *
     * @return string
     */
    public function GetCode()
    {
        return $this->getPhrase();
    }

}
