<?php
/**
 * 验证码类.
 */
class RegPage_ValidateCode
{
    protected $charset = 'ABCDEFGHKMNPRSTUVWXYZ123456789';
    protected $code; //验证码
    protected $codelen = 5; //位数
    protected $width = 90; //宽度
    protected $height = 30; //高度
    protected $img; //图形
    protected $font; //字体
    protected $fontsize = 15; //字体大小
    protected $fontcolor; //字体颜色

    /**
     *构造方法初始化.
     */
    public function __construct()
    {
        global $zbp;
        $this->font = $zbp->path . (isset($zbp->option['ZC_VERIFYCODE_FONT']) ? $zbp->option['ZC_VERIFYCODE_FONT'] : 'zb_system/defend/arial.ttf');
        $this->charset = $zbp->option['ZC_VERIFYCODE_STRING'];
        $this->width = $zbp->option['ZC_VERIFYCODE_WIDTH'];
        $this->height = $zbp->option['ZC_VERIFYCODE_HEIGHT'];
    }

    /**
     *生成随机码
     */
    protected function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    /**
     *生成背景.
     */
    protected function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    /**
     *生成文字.
     */
    protected function createFont()
    {
        $_x = $this->width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }

    /**
     *生成线条、雪花.
     */
    protected function createLine()
    {
        for ($i = 50; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        for ($i = 3; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
    }

    /**
     *输出.
     */
    protected function outPut()
    {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     *对外生成.
     */
    public function GetImg()
    {
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }

    /**
     * 获取验证码
     *
     * @return string
     */
    public function GetCode()
    {
        return strtolower($this->code);
    }
}
