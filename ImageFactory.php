<?php
namespace imageFactory;
/**
 * Created by PhpStorm.
 * User: YCJ
 * Date: 2017/6/27
 * Time: 10:08
 */
class ImageFactory
{
    public function mergeImage($background, $params)
    {

    }

    public function mergeText($background, $size, $angle, $x, $y, $text)
    {
        // Create the image
        $im = imagecreatetruecolor(100, 30);
        // Create some colors
        $text_color = imagecolorallocate($im, 0, 0, 0);
        // Replace path by your own font path
        $font = './public/FZHTJW.TTF';;

        imagettftext($background, $size, $angle, $x, $y, $text_color, $font, $text);
        return $background;
    }


    /**
     * @param $filepath
     * @return bool|resource
     */
    public function imageCreateFromAny($filepath)
    {
        $type = getimagesize($filepath); // [] if you don't have exif you could use getImageSize()
        $allowedTypes = array(
            IMAGETYPE_GIF,
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_BMP,
        );
        if (!in_array($type[2], $allowedTypes)) {
            return false;
        }
        switch ($type[2]) {
            case IMAGETYPE_GIF :
                $im = imagecreatefromgif($filepath);
                break;
            case IMAGETYPE_JPEG :
                $im = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG :
                $im = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_BMP :
                $im = imagecreatefromwbmp($filepath);
                break;
        }
        return $im;
    }

    /**
     * 缩小图片
     * @param $imgfile string 原始图片
     * @param $minx int 缩放后的宽
     * @param $miny int 缩放后的高
     * @return resource 缩放后的图片
     */

    function ImageShrink($imgfile, $minx, $miny)
    {
        if (strpos($imgfile, 'http')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imgfile);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
            $data = curl_exec($ch);
            curl_close($ch);

            $maxim = imagecreatefromstring($data);
            $imgarr = getimagesizefromstring($data);
            $maxx = $imgarr[0];//宽
            $maxy = $imgarr[1];//长
            $maxt = $imgarr[2];//图片格式
            $maxm = $imgarr['mime'];//mime类型
        } else {
            //获取大图信息
            $imgarr = getimagesize($imgfile);
            $maxx = $imgarr[0];//宽
            $maxy = $imgarr[1];//长
            $maxt = $imgarr[2];//图片格式
            $maxm = $imgarr['mime'];//mime类型

            //大图资源
            $maxim = $this->imageCreateFromAny($imgfile);
        }

        //缩放判断
        if (($minx / $maxx) > ($miny / $maxy)) {
            $scale = $miny / $maxy;
        } else {
            $scale = $minx / $maxx;
        }

        //对所求值进行取整
        $minx = floor($maxx * $scale);
        $miny = floor($maxy * $scale);
        $this->width = $minx;
        $this->height = $miny;
        //添加小图
        $minim = imagecreatetruecolor($minx, $miny);

        //缩放函数
        imagecopyresampled($minim, $maxim, 0, 0, 0, 0, $minx, $miny, $maxx, $maxy);

        //释放资源
//        imagedestroy($maxim);
//        imagedestroy($minim);
        return $minim;
    }
}