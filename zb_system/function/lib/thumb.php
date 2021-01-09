<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 缩略图类.
 */
class Thumb
{
    protected $sourceFile;

    protected $upload;

    protected $imageInfo = array();

    protected $image;

    protected $newImage = null;

    public function __construct($sourceFile, $upload = null)
    {
        $this->sourceFile = $sourceFile;
        $this->upload = $upload ? : new Upload;
    }

    public function loadImage()
    {   
        $this->imageInfo = getimagesize($this->sourceFile);
        $src_width = $this->imageInfo[0];
        $src_height = $this->imageInfo[1];

        if ($src_width == 0 || $src_height == 0) {
            return $this;
        }
        if (! function_exists('imagecreatefromjpeg')) {
            return $this;
        }

        switch ($this->imageInfo['mime']) {
            case 'image/jpeg':
                $this->image = imagecreatefromjpeg($this->sourcefile);
                !$this->image && ($this->image = imagecreatefromgif($this->sourcefile));
                break;
            case 'image/gif':
                $this->image = imagecreatefromgif($this->sourcefile);
                !$this->image && ($this->image = imagecreatefromjpeg($this->sourcefile));
                break;
            case 'image/png':
                $this->image = imagecreatefrompng($this->sourcefile);
                break;
            case 'image/wbmp':
                $this->image = imagecreatefromwbmp($this->sourcefile);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                	$this->image = call_user_func('imagecreatefromwebp', $this->sourcefile);
                }
                break;
        }

        return $this;        
    }

    public function clip($clipx, $clipy, $clipwidth, $clipheight)
    {
        if ($this->newImage !== null) {
            $this->image = $this->newImage;
            $this->newImage = null;
        }

        $src_width = $this->imageInfo[0];
        $src_height = $this->imageInfo[1];

        $this->newImage = imagecreatetruecolor($clipwidth, $clipheight);
        imagefill($this->newImage, 0, 0, 0xffffff);
        imagecopyresampled($this->newImage, $this->image, 0, 0, $clipx, $clipy, $src_width, $src_height, $src_width, $src_height);

        return $this;
    }

    public function scale($forced_width = 300, $forced_height = 300)
    {
        if ($this->newImage !== null) {
            $this->image = $this->newImage;
            $this->newImage = null;
        }

        $src_width = $this->imageInfo[0];
        $src_height = $this->imageInfo[1];

        // 按规定比例缩略
        $src_scale = $src_width / $src_height;
        //如果裁剪的高为未定义，那么等比例缩小，高自适应
        if(!$forced_height){
            $des_width = $forced_width;
            $des_height = $forced_width / $src_scale; 
        }else{
            $des_scale = $forced_width / $forced_height;
            if ($src_width <= $forced_width && $src_height <= $forced_height) {
                $des_width = $src_width;
                $des_height = $src_height;
            } elseif ($src_scale >= $des_scale) {
                $des_width = $src_width >= $forced_width ? $forced_width : $src_width;
                $des_height = $des_width / $src_scale;
                $des_height = $des_height >= $forced_height ? $forced_height : $des_height;
            } else {
                $des_height = $src_height >= $forced_height ? $forced_height : $src_height;
                $des_width = $des_height * $src_scale;
                $des_width = $des_width >= $forced_width ? $forced_width : $des_width;
            }
        }

        $this->newImage = imagecreatetruecolor($des_width, $des_height);
        imagefill($this->newImage, 0, 0, 0xffffff);
        imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $des_width, $des_height, $src_width, $src_height);

        return $this;
    }

    public function save()
    {
        global $zbp;

        $tmppath = ini_get('upload_tmp_dir') . '/';
        $tmppath == '/' and $tmppath = ZBP_PATH . 'zb_users/cache';
        //如果设置的目录不存在，则创建
        if (!file_exists($tmppath)) {
            @mkdir($tmppath, 0777, true);
        }
        $tmpfile = $tmppath . md5($this->sourceFile . '_thumb') . '.tmp';

        switch ($destext) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($this->image, $tmpfile, 90);
                break;
            case 'gif':
                imagegif($this->image, $tmpfile);
                break;
            case 'png':
                imagepng($this->image, $tmpfile);
                break;
        }

        // $this->upload->Name = $_FILES[$key]['name'];
        // $this->upload->SourceName = $_FILES[$key]['name'];
        // $this->upload->MimeType = $_FILES[$key]['type'];
        // $this->upload->Size = $_FILES[$key]['size'];
        $this->upload->AuthorID = $zbp->user->ID;
        $this->upload->SaveFile($tmpfile);
        $this->upload->Save();
        $zbp->AddCache($this->upload);
    }
}
