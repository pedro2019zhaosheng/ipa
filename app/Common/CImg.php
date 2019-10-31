<?php
namespace App\Common;
/**
 *autor: cuity@20120906
 *func:  图片处理类
 *
*/
class CImg{

    /*
    * desc: 裁剪图片
    *
    *@fpimg   --- str 图片地址
    *@dWidth  --- int 要缩放的宽度
    *@dHeight --- int 要缩放的高度
    *
    *
    */
    static function cutImg($fpimg, $dWidth=250, $dHeight=280, $xArr=array())
    {
        if(!is_file($fpimg)) return false;
        $iArr = self::getImageInfo($fpimg);
        if(false === $iArr) return false;
        
        $skipsmall = isset($xArr['skipsmall'])?$xArr['skipsmall']:true;
        $scope     = isset($xArr['scope'])?$xArr['scope']:true;
        $suffix    = isset($xArr['suffix'])?$xArr['suffix']:$dWidth.$dHeight;
        $topng     = isset($xArr['topng'])?$xArr['topng']:false; //是否转换为png

        if($skipsmall && $dWidth >= $iArr['width'] && $dHeight >= $iArr['height']) return $fpimg;
        
        $graphOrigin = self::createImageFromAny($fpimg, $topng);
        if(!$graphOrigin)return false;
        $srcWidth  = imagesx($graphOrigin); //原始图宽度
        $srcHeight = imagesy($graphOrigin); //原始图高度

        if(isset($xArr['removeblank'])){ //移除空白
            $loops = 0;
            $rgbfirst  = self::_get_rgb($graphOrigin, 0, 0);
            
            if(isset($xArr['fast'])){
                $step_init = 20;
                //===================x1======================
                $x=0;
                $step = $step_init;
                $max  = $srcWidth;
                do{
                    for($y=0; $y<$srcHeight; $y++){
                        $loops++;
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        if($rgbx != $rgbfirst){
                            echo $x1   = $x; //logic
                            $max  = $x - 1;
                            $x   -= ($step-1);
                            $step = 1;
                            if(isset($into_limit) && $into_limit){
                                unset($into_limit);
                                break 2;
                            }else{
                                $into_limit = true;
                                break;
                            }
                        }
                    }
                    // sleep(1);
                    $x += $step;
                }while($x<$max && $x>0);
                //===================x1===================end
                
                //===================y1======================
                $y=0;
                $step = $step_init;
                $max  = $srcHeight;
                do{
                    for($x=0; $x<$srcWidth; $x++){
                        $loops++;
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        if($rgbx != $rgbfirst){
                            $y1   = $y; //logic
                            $max  = $y - 1;
                            $y   -= ($step-1);
                            $step = 1;
                            if(isset($into_limit) && $into_limit){
                                unset($into_limit);
                                break 2;
                            }else{
                                $into_limit = true;
                                break;
                            }
                        }
                    }
                    $y += $step;
                }while($y<$max && $y>0);
                //===================y1===================end
                
                //===================x2======================
                $x = $srcWidth-1;
                $step = $step_init;
                $min  = 0;
                do{
                    for($y=0; $y<$srcHeight; $y++){
                        $loops++;
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        if($rgbx != $rgbfirst){
                            $x2   = $x; //logic
                            $min  = $x + 1;
                            $x   += ($step-1);
                            $step = 1;
                            if(isset($into_limit) && $into_limit){
                                unset($into_limit);
                                break 2;
                            }else{
                                $into_limit = true;
                                break;
                            }
                        }
                    }
                    // sleep(1);
                    $x -= $step;
                }while($x>=$min && $x<$srcWidth);
                //===================x2===================end
                
                //===================y2======================
                $y = $srcHeight-1;
                $step = $step_init;
                $min  = 0;
                do{
                    for($x=0; $x<$srcWidth; $x++){
                        $loops++;
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        if($rgbx != $rgbfirst){
                            $y2   = $y; //logic
                            $min  = $y + 1;
                            $y   += ($step-1);
                            $step = 1;
                            if(isset($into_limit) && $into_limit){
                                unset($into_limit);
                                break 2;
                            }else{
                                $into_limit = true;
                                break;
                            }
                        }
                    }
                    $y -= $step;
                }while($y>=$min && $y<$srcHeight);
                //===================y2===================end
            }else{
                for($x=0; $x<$srcWidth; $x++){
                    for($y=0; $y<$srcHeight; $y++){
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        $loops++;
                        if($rgbx != $rgbfirst){
                            $x1 = $x;
                            break 2;
                        }
                    }
                }
            
                for($y=0; $y<$srcHeight; $y++){
                    for($x=0; $x<$srcWidth; $x++){
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        $loops++;
                        if($rgbx != $rgbfirst){
                            $y1 = $y;
                            break 2;
                        }
                    }
                }
            
                for($x=$srcWidth-1; $x>0; $x--){
                    for($y=0; $y<$srcHeight; $y++){
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        $loops++;
                        if($rgbx != $rgbfirst){
                            $x2 = $x;
                            break 2;
                        }
                    }
                }
            
                for($y=$srcHeight-1; $y>0; $y--){
                    for($x=0; $x<$srcWidth; $x++){
                        $rgbx = self::_get_rgb($graphOrigin, $x, $y);
                        $loops++;
                        if($rgbx != $rgbfirst){
                            $y2 = $y;
                            break 2;
                        }
                    }
                }
            }

            // echo "($x1,$y1),($x2,$y2)($loops)";
            // $rgbx = self::_get_rgb($graphOrigin, 430, 570);
            // echo "$rgbx";
            $dWidth  = $x2 - $x1 + 1;
            $dHeight = $y2 - $y1 + 1;
            $srcX1   = $x1;
            $srcY1   = $y1;
            $suffix  = 'removedblank';
            $explicitly = true;
        }elseif(isset($xArr['x1']) && isset($xArr['y1']) && isset($xArr['x2']) && isset($xArr['y2'])){
            //这意味着从原图中按照指定的坐标抠一块图像出来
            // echo 'aaaaaaaaaaaaaaaaa';
            $dWidth  = $xArr['x2'] - $xArr['x1'] + 1;
            $dHeight = $xArr['y2'] - $xArr['y1'] + 1;
            $srcX1   = $xArr['x1'];
            $srcY1   = $xArr['y1'];
            $suffix  = 'thumbled';
            $explicitly = true;
        }else{
            $srcX1   = 0;
            $srcY1   = 0;
            $explicitly = false;
        }

//        $graphThumbl = imagecreatetruecolor($dWidth, $dHeight);
//        if(!$graphThumbl) return false;
//        $rgbblack    = imagecolorallocate($graphThumbl, 0, 0, 0);
//        $rgbwhite    = imagecolorallocate($graphThumbl, 255, 255, 255);
//        imagecolortransparent($graphOrigin, $rgbblack);
//        imagecolortransparent($graphThumbl, $rgbblack);
//        
//        
//        
//        if(IMAGETYPE_PNG != $iArr['type']){
//            imagefilledrectangle($graphThumbl , 0,0 , $dWidth,$dHeight, $rgbwhite);
//        }else{
//            //分配颜色 + alpha，将颜色填充到新图上
//            $alpha = imagecolorallocatealpha($graphThumbl, 0, 0, 0, 127);
//            imagefill($graphThumbl, 0, 0, $alpha);
//        }


        if($explicitly){
            $graphThumbl = imagecreatetruecolor($dWidth, $dHeight);
            if(!$graphThumbl) return false;
            $rgbblack    = imagecolorallocate($graphThumbl, 0, 0, 0);
            $rgbwhite    = imagecolorallocate($graphThumbl, 255, 255, 255);
            imagecolortransparent($graphOrigin, $rgbblack);
            imagecolortransparent($graphThumbl, $rgbblack);



            if(IMAGETYPE_PNG != $iArr['type']){
                imagefilledrectangle($graphThumbl , 0,0 , $dWidth,$dHeight, $rgbwhite);
            }else{
                //分配颜色 + alpha，将颜色填充到新图上
                $alpha = imagecolorallocatealpha($graphThumbl, 0, 0, 0, 127);
                imagefill($graphThumbl, 0, 0, $alpha);
            }
            imagecopyresampled($graphThumbl, $graphOrigin, 0, 0, $srcX1, $srcY1, $dWidth, $dHeight, $dWidth, $dHeight);
        }else{
            if($srcWidth < $dWidth && $srcHeight < $dHeight) {
                //原图比将要截的缩略图小
                $dstWidth  = $srcWidth;
                $dstHeight = $srcHeight;
            }else {
                $divW = $srcWidth  / $dWidth;   //长和宽的比值
                $divH = $srcHeight / $dHeight;  //长和宽的比值
                // $scope = true; //标识了被截剪后的图片是否保持全景
                if($scope) {
                    if($divW >= $divH) { //表示为横图(在y方向需要被白),目标高度需要计算
                        $dstWidth  = $dWidth;
                        $dstHeight = ($srcHeight*$dWidth)/$srcWidth;
                    }else {//表示为竖图(在x方向需要被白)
                        $dstWidth  = ($srcWidth*$dHeight)/$srcHeight; //目标宽度需要计算
                        $dstHeight = $dHeight;
                    }
                }else { //生成的缩略图布满整个图片(这意味着原图可能被截剪)
                    if($divW >= $divH) { //表示为横图(在y方向需要被白)
                        $dstWidth  = ($srcWidth*$dHeight)/$srcHeight;
                        $dstHeight = $dHeight;
                    }else {//表示为竖图(在x方向需要被截)
                        $dstWidth  = $dWidth;
                        $dstHeight = ($srcHeight*$dWidth)/$srcWidth;
                    }
                }
            }
            $dstX = $dstY = 0;
            if($dstHeight < $dHeight) {
                $dstY = ($dHeight-$dstHeight)/2;
            }
            if($dstWidth < $dWidth) {
                $dstX = ($dWidth-$dstWidth)/2;
            }
            
            //创建新的图片对象
            $graphThumbl = imagecreatetruecolor($dstWidth, $dstHeight);
            if(!$graphThumbl) return false;
            $rgbblack    = imagecolorallocate($graphThumbl, 0, 0, 0);
            $rgbwhite    = imagecolorallocate($graphThumbl, 255, 255, 255);
            imagecolortransparent($graphOrigin, $rgbblack);
            imagecolortransparent($graphThumbl, $rgbblack);

            if(IMAGETYPE_PNG != $iArr['type']){
                imagefilledrectangle($graphThumbl , 0,0 , $dWidth,$dHeight, $rgbwhite);
            }else{
                //分配颜色 + alpha，将颜色填充到新图上
                $alpha = imagecolorallocatealpha($graphThumbl, 0, 0, 0, 127);
                imagefill($graphThumbl, 0, 0, $alpha);
            }
            
            if(function_exists('imagecopyresampled')){
//                 echo "$dstX, $dstY, $srcX1, $srcY1, $dstWidth, $dstHeight, $dWidth, $dHeight";die;
                imagecopyresampled($graphThumbl, $graphOrigin, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);
            }else{
                imagecopyresized($graphThumbl, $graphOrigin, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);
            }
        }
        
        $name = self::getName($fpimg) . '!' . $suffix . $iArr['ext'];
        if($topng){
            return imagepng($graphThumbl, $name.'.png');
        }
        switch($iArr['type'])
        {
            case IMAGETYPE_GIF     :
                $ok = imagegif($graphThumbl, $name); break;
            case IMAGETYPE_JPEG    :
                $ok = imagejpeg($graphThumbl, $name); break;
            case IMAGETYPE_PNG     :
                imagesavealpha($graphThumbl, true); //不失真
                $ok = imagepng($graphThumbl, $name); break;
            case IMAGETYPE_BMP     :
                $ok = imagewbmp($graphThumbl, $name); break;
            default:
                $ok = false;
        }
        if($ok){
            return $name;
        }
        return $ok;
    }
    
    
    static function removeBlank($fpimg)
    {
        return self::cutImg($fpimg, null,null,array('removeblank'=>1,'fast'=>1, 'topng'=>true));
    }
    static private function _get_rgb($graph, $x=0, $y=0)
    {
        $rgb = imagecolorat($graph, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return sprintf("0x%02x%02x%02x",$r,$g,$b);
    }
    //return resource
    static function createImageFromAny($fpimg, $topng=false)
    {
        if(!is_file($fpimg)) return false;
        $iArr = self::getImageInfo($fpimg);
        if(false === $iArr) return false;
        // print_r($iArr);exit;
        $type = $iArr['type'];
        switch($type) {
            case IMAGETYPE_GIF     :
                $graphOld = imagecreatefromgif($fpimg);
                break;
            case IMAGETYPE_JPEG    :
                $graphOld = imagecreatefromjpeg($fpimg);
                break;
            case IMAGETYPE_PNG     :
                $graphOld = imagecreatefrompng($fpimg);
                break;
            case IMAGETYPE_SWF     :
                break;
            case IMAGETYPE_PSD     :
                break;
            case IMAGETYPE_BMP     :
                $graphOld = imagecreatefromwbmp($fpimg);
                break;
            case IMAGETYPE_TIFF_II :
                break;
            case IMAGETYPE_TIFF_MM :
                break;
            case IMAGETYPE_JPC     :
                break;
            case IMAGETYPE_JP2     :
                break;
            case IMAGETYPE_JPX     :
                break;
            case IMAGETYPE_JB2     :
                break;
            case IMAGETYPE_SWC     :
                break;
            case IMAGETYPE_IFF     :
                break;
            case IMAGETYPE_WBMP    :
                break;
            case IMAGETYPE_XBM     :
                break;
        }
        if($topng){
            $graphNew = imagecreatetruecolor($iArr['width'], $iArr['height']);
            if(!$graphNew)return $graphOld;
            imagecopy($graphNew, $graphOld, 0,0,   0,0,$iArr['width'], $iArr['height']);
            return $graphNew;
        }
        return $graphOld;
    }
    
    /*
    * desc: img转换为png
    *
    *@$fpjpg  --- string(被转换图片的地址)
    *@fpout   --- png的输出地址(黑夜与fpjpg同目录)
    * return: true if success or else false
    */
    static function img2png($fpimg, $fpout=null)
    {
        if(!is_file($fpimg)) return false;
        $iArr = self::getImageInfo($fpimg);
        if(false === $iArr) return false;
        $type = $iArr['type'];
        $graphOld = self::createImageFromAny($fpimg);
        if(null === $fpout) {
            $name = self::getName($fpimg);
            $fpout = dirname($fpimg) . '/' . $name. '.png';
        }
        return imagepng($graphOld, $fpout);
    }
    
    //获取除后轰名的文件名
    static function getName($filename)
    {
        return substr($filename, 0, strrpos($filename,'.'));
        /*
        $pos = strrpos($filename, '.'); 
        if($pos === false){
            return basename($filename); // no extension 
        }else { 
            $basename = substr($filename, 0, $pos); 
            $extension = substr($filename, $pos+1); 
            return basename($basename); 
        }*/
    }
    static function getImageInfo($fpimg)
    {   
        if(!is_file($fpimg)) return false;
        $infoArr = array();
        $_t = $infoArr['type'] = exif_imagetype($fpimg);
        if(false === $_t) return false;
        
        if(IMAGETYPE_GIF == $_t || IMAGETYPE_JPEG == $_t) {
            $infoArr['head']   = @exif_read_data($fpimg); //只支持gif/jpg两种格式
            $infoArr['width']  = $infoArr['head']['COMPUTED']['Width'];
            $infoArr['height'] = $infoArr['head']['COMPUTED']['Height'];
        }else {
            $arr = getimagesize($fpimg);
            $infoArr['width']  = $arr[0];
            $infoArr['height'] = $arr[1];
        }
        if(empty($infoArr['width']) || empty($infoArr['height'])){
            $arr = getimagesize($fpimg);
            $infoArr['width']  = $arr[0];
            $infoArr['height'] = $arr[1];
        }

        $ext = substr($fpimg, strrpos($fpimg,'.'), 10);
        $infoArr['ext'] = $ext;
        // print_r($infoArr);
        return $infoArr;
        /*
        1  IMAGETYPE_GIF 
        2  IMAGETYPE_JPEG 
        3  IMAGETYPE_PNG 
        4  IMAGETYPE_SWF 
        5  IMAGETYPE_PSD 
        6  IMAGETYPE_BMP 
        7  IMAGETYPE_TIFF_II（Intel 字节顺序） 
        8  IMAGETYPE_TIFF_MM（Motorola 字节顺序）  
        9  IMAGETYPE_JPC 
        10 IMAGETYPE_JP2 
        11 IMAGETYPE_JPX 
        12 IMAGETYPE_JB2 
        13 IMAGETYPE_SWC 
        14 IMAGETYPE_IFF 
        15 IMAGETYPE_WBMP 
        16 IMAGETYPE_XBM 
        */
    }
    
    static function getImagesDir($dir)
    {
        if(!is_dir($dir)) return;
        $handler = opendir($dir);
        $extArr = array('jpg','jpeg','png','gif','bmp');
        $imgArr = array();
        while(false !== ($filename = readdir($handler)))
        {
            $ext = strtolower(substr($filename, strrpos($filename,'.')+1, 10));
            if($filename != '.' && $filename != '..') {
                $fullname = rtrim($dir,'/') .'/'. $filename;
                // echo $fullname . "($ext)\n";
                if(in_array($ext, $extArr)){
                    $imgArr[] = $fullname;
                }
            }
        }
        closedir($handler);
        return $imgArr;
    }

    /*
    *打水印
    */
    static function watermark($src_path,$dst_path){
        if(empty($dst_path) || empty($src_path)){
            return null;
        }
        //坐标是从左上角开始0,0
        //创建图片的实例
        $dst = imagecreatefromstring(file_get_contents($dst_path));
        $src = imagecreatefromstring(file_get_contents($src_path));
        //获取水印图片的宽高
        list($src_w, $src_h) = getimagesize($src_path);
        list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);
        //如果水印图片本身带透明色，则使用imagecopy方法
        imagecopy($dst, $src, $dst_w-$src_w-10, $dst_h-$src_h-10, 0, 0, $src_w, $src_h);
        //输出图片
        
        $ret = true;
        switch ($dst_type) {
            case 1:
                $ret = imagegif($dst,$dst_path);
                break;
            case 2:
                $ret = imagejpeg($dst,$dst_path);
                break;
            case 3:
                $ret = imagepng($dst,$dst_path);
                break;
            case 6:
                $ret = imagebmp($dst,$dst_path);
                break;
            case 17:
                $ret = imagejpeg($dst,$dst_path);
                break;
            default:
                $ret = imagejpeg($dst,$dst_path);
                break;
        }
        return $ret;
    }
};
ini_set("memory_limit","-1");

// CImg::cutImg("1.gif",256,256,array('scope'=>false, 'topng'=>true, 'x1'=>250,'y1'=>494,'x2'=>516,'y2'=>627));
/*
include 'CTool.php';
$t1 = CTool::getUTime();
CImg::removeBlank('3.png');
$t2 = CTool::getUTime();
printf("%.4f", $t2 - $t1);
*/
/*(276,494),(516,627)
$imgArr = CImg::getImagesDir('E:/pic/2222');
if($imgArr){
    foreach($imgArr as $img){
        echo "$img\n";
        CImg::cutImg($img,1920,1080,array('scope'=>false));
    }
}*/


    