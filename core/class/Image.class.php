<?php
!defined('IN_FW') && exit('Access Denied');
class image {
    /**
     * @var int
     */
    var $interlace = 0;

    /**
     * @param $img
     * @return array|bool
     */
    function info($img) {
        $imageinfo = getimagesize($img);
        if ($imageinfo === false) {
            return false;
        }
        $imagetype = strtolower(substr(image_type_to_extension($imageinfo[2]), 1));
        $imagesize = filesize($img);
        $info = array('width' => $imageinfo[0], 'height' => $imageinfo[1], 'type' => $imagetype, 'size' => $imagesize, 'mime' => $imageinfo['mime']);
        return $info;
    }

    /**
     * @param $image
     * @param string $filename
     * @param int $maxwidth
     * @param int $maxheight
     * @param string $suffix
     * @param int $ftp
     * @return string
     */
    function thumb($image, $filename = '', $maxwidth = 0, $maxheight = 0, $suffix = '', $ftp = 0) {
        if (!$this->check($image)) {
            return false;
        }
        $info = image::info($image);
        if ($info === false) {
            return false;
        }
        $srcwidth = $info['width'];
        $srcheight = $info['height'];
        $pathinfo = pathinfo($image);
        $type = $pathinfo['extension'];
        if (!$type) {
            $type = $info['type'];
        }
        $type = strtolower($type);
        unset($info);
        if ($maxwidth == 0 && $maxheight == 0) {
            return false;
        }
        $ratio_orig = $srcwidth / $srcheight;  //计算原始图片宽高比
        if ($maxwidth == 0 && $maxheight > 0) {
            $maxwidth = $maxheight * $ratio_orig;
        }
        if ($maxwidth > 0 && $maxheight == 0) {
            $maxheight = $maxwidth / $ratio_orig;
        }
        $psrc_x = $psrc_y = 0;
        $height = $maxheight;
        $width = $maxwidth;

        if ($maxwidth / $maxheight > $ratio_orig) {  //如果目标宽高比大于原始宽高比，原始图图片为窄图（相对于原图），以高度比缩小图片
            if ($maxheight < $srcheight) {  //如果目标高度小于原始高度进行缩小操作
                $width = $maxheight * $ratio_orig;
                $height = $maxheight;
                $psrc_x = abs(($width - $maxwidth) / 2);
            } else {  //如果目标高度大于原始高度按原始比例
                $width = $srcwidth;
                $height = $srcheight;
                $psrc_x = abs(($width - $maxwidth) / 2);
                $psrc_y = abs(($height - $maxheight) / 2);
            }
        } else {  //如果目标宽高比大于原始宽高比，原始图图片为宽图（相对于原图），以宽度比缩小图片
            if ($maxwidth < $srcwidth) {  //如果目标宽度小于原始宽度进行缩小操作
                $height = $maxwidth / $ratio_orig;
                $width = $maxwidth;
                $psrc_y = abs(($height - $maxheight) / 2);
            } else {    //如果目标宽度大于原始宽度按原始比例
                $width = $srcwidth;
                $height = $srcheight;
                $psrc_x = abs(($width - $maxwidth) / 2);
                $psrc_y = abs(($height - $maxheight) / 2);
            }
        }
        $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
        $srcimg = $createfun($image);
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $thumbimg = imagecreatetruecolor($maxwidth, $maxheight);
        } else {
            $thumbimg = imagecreate($maxwidth, $maxheight);
        }
        $background_color = imagecolorallocate($thumbimg, 255, 255, 255);
        if ($type == 'gif' || $type == 'png') {
            imagecolortransparent($thumbimg, $background_color);
        }
        imagefill($thumbimg, 0, 0, $background_color);


        if (function_exists('imagecopyresampled')) {
            imagecopyresampled($thumbimg, $srcimg, $psrc_x, $psrc_y, 0, 0, $width, $height, $srcwidth, $srcheight);
        } else {
            imagecopyresized($thumbimg, $srcimg, $psrc_x, $psrc_y, 0, 0, $width, $height, $srcwidth, $srcheight);
        }

        if ($type == 'jpg' || $type == 'jpeg') {
            imageinterlace($thumbimg, $this->interlace);
        }
        $imagefun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
        if (empty($filename)) {
            $filename = substr($image, 0, strrpos($image, '.')) . $suffix . '.' . $type;
        }
        $imagefun($thumbimg, $filename);
        imagedestroy($thumbimg);
        imagedestroy($srcimg);
        if ($ftp) {
            @unlink($image);
        }
        return $filename;
    }

    /**
     * @param $imgurl
     * @param $markurl
     * @return bool
     */
    function watermark($imgurl, $markurl) {
        if (!$this->check($imgurl)) {
            return false;
        }
        $info = image::info($imgurl);
        if ($info === false) {
            return false;
        }
        $srcwidth = $info['width'];
        $srcheight = $info['height'];
        $pathinfo = pathinfo($imgurl);
        $type = $pathinfo['extension'];
        if (!$type) {
            $type = $info['type'];
        }
        $type = strtolower($type);
        $type = $type == 'jpg' ? 'jpeg' : $type;
        unset($info);
        switch ($type) {
            case 'gif' :
                $source_img = imagecreatefromgif($imgurl);
                break;
            case 'jpeg' :
                $source_img = imagecreatefromjpeg($imgurl);
                break;
            case 'png' :
                $source_img = imagecreatefrompng($imgurl);
                break;
            default :
                return false;
        }
        if (!$this->check($markurl)) {
            return $markurl;
        }
        $info = image::info($markurl);
        if ($info === false) {
            return false;
        }
        $width = $info['width'];
        $height = $info['height'];
        $pathinfo = pathinfo($markurl);
        $w_type = $pathinfo['extension'];
        if (!$w_type) {
            $w_type = $info['type'];
        }
        $w_type = strtolower($w_type);
        $w_type = $w_type == 'jpg' ? 'jpeg' : $w_type;
        unset($info);
        switch ($w_type) {
            case 'gif' :
                $water_img = imagecreatefromgif($markurl);
                break;
            case 'jpeg' :
                $water_img = imagecreatefromjpeg($markurl);
                break;
            case 'png' :
                $water_img = imagecreatefrompng($markurl);
                break;
            default :
                return false;
        }
        $wx = $srcwidth - $width;
        $wy = $srcheight - $height;
        if ($w_type == 'png') {
            imagecopy($source_img, $water_img, $wx, $wy, 0, 0, $width, $height);
        } else {
            imagecopymerge($source_img, $water_img, $wx, $wy, 0, 0, $width, $height, 85);
        }
        switch ($type) {
            case 'gif' :
                imagegif($source_img, $imgurl);
                break;
            case 'jpeg' :
                imagejpeg($source_img, $imgurl, 80);
                break;
            case 'png' :
                imagepng($source_img, $imgurl);
                break;
            default :
                return false;
        }
        imagedestroy($water_img);
        imagedestroy($source_img);
        return true;
    }

    /**
     * @param $image
     * @return bool
     */
    function check($image) {
        return extension_loaded('gd') && preg_match("/\.(jpg|jpeg|gif|png)/i", $image, $m) && file_exists($image) && function_exists('imagecreatefrom' . ($m[1] == 'jpg' ? 'jpeg' : $m[1]));
    }

}

?>