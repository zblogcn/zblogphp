<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 缩略图生成类.
 */
class Thumb
{
    /**
     * 是否需要裁剪.
     *
     * @var boolean
     */
    protected $shouldClip = false;

    /**
     * 图片句柄.
     *
     * @var 
     */
    protected $srcRes;

    /**
     * 临时图片句柄.
     *
     * @var resource|null
     */
    protected $tmpRes;

    /**
     * 原图片宽度.
     *
     * @var integer
     */
    protected $srcWidth;

    /**
     * 原图片高度.
     *
     * @var integer
     */
    protected $srcHeight;

    /**
     * 目标缩略图宽度.
     *
     * @var integer
     */
    protected $dstWidth = 200;

    /**
     * 目标缩略图高度.
     *
     * @var integer
     */
    protected $dstHeight = 200;

    /**
     * 目标缩略图路径.
     *
     * @var string
     */
    protected $dstImagePath;

    /**
     * 通过路径载入原图片.
     *
     * @param string $img_path
     * @return Thumb
     */
    public function loadSrcByPath($img_path)
    {
        if (! file_exists($img_path)) {
            throw new Exception('图片不存在');
        }

        return $this->loadSrcByString(file_get_contents($img_path));
    }

    /**
     * 从远程 Url 中载入图片.
     *
     * @param string $url
     * @return Thumb
     */
    public function loadSrcByExternalUrl($url)
    {
        global $bloghost;

        $http = Network::Create();
        $http->open('GET', $url);
        $http->setRequestHeader('Referer', $bloghost);
        $http->send();
        if ($http->status == 200 && ($r = $http->responseText)) {
            return $this->loadSrcByString($r);
        }

        throw new Exception('远程图片请求失败');
    }

    /**
     * 通过二进制内容载入原图片.
     *
     * @param string $img_string
     * @return Thumb
     */
    public function loadSrcByString($img_string)
    {
        $this->srcRes = imagecreatefromstring($img_string);
        
        if (! $this->srcRes) {
            throw new Exception('图片载入失败');
        }

        $this->loafSrcWidthAndHeight();

        if ($this->srcWidth == 0 || $this->srcHeight == 0) {
            throw new Exception('图片宽高不正常');
        }

        return $this;
    }

    /**
     * 载入原图宽高.
     */
    protected function loafSrcWidthAndHeight()
    {
        $this->srcWidth = imagesx($this->srcRes);
        $this->srcHeight = imagesy($this->srcRes);
    }

    /**
     * 设置是否需要裁剪.
     *
     * @param boolean $should
     * @return Thumb
     */
    public function shouldClip($should = true)
    {
        $this->shouldClip = $should;

        return $this;
    }

    /**
     * 设置目标文件宽度.
     *
     * @param int $width
     * @return Thumb
     */
    public function setWidth($width)
    {
        $this->dstWidth = $width;

        return $this;
    }

    /**
     * 设置目标文件高度.
     *
     * @param int $height
     * @return Thumb
     */
    public function setHeight($height)
    {
        $this->dstHeight = $height;

        return $this;
    }

    /**
     * 设置目标缩略图路径.
     *
     * @param string $path
     * @return Thumb
     */
    public function setDstImagePath($path)
    {
        $this->dstImagePath = $path;

        return $this;
    }

    /**
     * 将 tmp 同步到 src.
     */
    protected function syncSrcFromTmp()
    {
        if (! $this->tmpRes) {
            return;
        }
        imagedestroy($this->srcRes);
        $this->srcRes = $this->tmpRes;
        $this->tmpRes = null;
    }

    /**
     * 裁剪.
     *
     * @param int $clipx             被裁切图片的X坐标
     * @param int $clipy             被裁切图片的Y坐标
     * @param int $clip_width        被裁区域的宽度
     * @param int $clip_height       被裁区域的高度
     */
    protected function clip($clipx, $clipy, $clip_width, $clip_height)
    {
        $this->syncSrcFromTmp();

        $this->tmpRes = imagecreatetruecolor($clip_width, $clip_height);
        imagefill($this->tmpRes, 0, 0, 0xffffff);
        imagecopyresampled($this->tmpRes, $this->srcRes, 0, 0, $clipx, $clipy, $this->srcWidth, $this->srcHeight, $this->srcWidth, $this->srcHeight);
    }

    /**
     * 缩略.
     *
     * @param integer $forced_width  生成的宽度
     * @param integer $forced_height 生成的高度
     * @return void
     */
    public function zoom($forced_width, $forced_height = 0)
    {
        $this->syncSrcFromTmp();

        $this->loafSrcWidthAndHeight();

        // 按规定比例缩略
        $src_scale = $this->srcWidth / $this->srcHeight;

        // 如果裁剪的高为未定义，那么等比例缩小，高自适应
        if (! $forced_height) {
            $dst_width = $forced_width;
            $dst_height = $forced_width / $src_scale; 
        } else {
            $dst_scale = $forced_width / $forced_height;
            if ($this->srcWidth <= $forced_width && $this->srcHeight <= $forced_height) {
                $dst_width = $this->srcWidth;
                $dst_height = $this->srcHeight;
            } elseif ($src_scale >= $dst_scale) {
                $dst_width = $this->srcWidth >= $forced_width ? $forced_width : $this->srcWidth;
                $dst_height = $dst_width / $src_scale;
                $dst_height = $dst_height >= $forced_height ? $forced_height : $dst_height;
            } else {
                $dst_height = $this->srcHeight >= $forced_height ? $forced_height : $this->srcHeight;
                $dst_width = $dst_height * $src_scale;
                $dst_width = $dst_width >= $forced_width ? $forced_width : $dst_width;
            }
        }

        $this->tmpRes = imagecreatetruecolor($dst_width, $dst_height);
        imagefill($this->tmpRes, 0, 0, 0xffffff);
        imagecopyresampled($this->tmpRes, $this->srcRes, 0, 0, 0, 0, $dst_width, $dst_height, $this->srcWidth, $this->srcHeight);
    }

    /**
     * 保存缩略图.
     */
    protected function save()
    {        
        $this->syncSrcFromTmp();

        $ext = GetFileExt($this->dstImagePath);

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->srcRes, $this->dstImagePath, 90);
                break;
            case 'gif':
                imagegif($this->srcRes, $this->dstImagePath);
                break;
            case 'png':
                imagepng($this->srcRes, $this->dstImagePath);
                break;
        }
    }

    /**
     * 生成.
     */
    public function handle()
    {
        if ($this->shouldClip) {
            $src_scale = $this->srcWidth / $this->srcHeight;
            $dst_scale = $this->dstWidth / $this->dstHeight;
            $h_scale = $this->srcHeight / $this->dstHeight;
            $w_scale = $this->srcWidth / $this->dstWidth;
            $w_des = $this->dstWidth * $h_scale;
            $h_des = $this->dstHeight * $w_scale;
            if ($this->srcWidth <= $this->dstWidth && $this->srcHeight <= $this->dstHeight) {
                $dst_width = $this->srcWidth;
                $dst_height = $this->srcHeight;
            }

            // 原图为横着的矩形
            if ($src_scale >= $dst_scale) {
                // 以原图的高度作为标准，进行缩略
                $dst_widthx = ($this->srcWidth - $w_des) / 2;
                $this->clip($dst_widthx, 0, $w_des, $this->srcHeight);
                $this->zoom($this->dstWidth, $this->dstHeight);
            } else {
                $dst_heighty = ($this->srcHeight - $h_des) / 2;
                $this->clip(0, $dst_heighty, $this->srcWidth, $h_des);
                $this->zoom($this->dstWidth, $this->dstHeight);
            }
        } else {
            $this->zoom($this->dstWidth);
        }

        $this->save();

        imagedestroy($this->srcRes);
    }
}
