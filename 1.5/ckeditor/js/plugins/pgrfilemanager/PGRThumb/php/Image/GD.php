<?php
/*
Copyright (c) 2009 Grzegorz Żydek

This file is part of PGRFileManager v2.1.0

Permission is hereby granted, free of charge, to any person obtaining a copy
of PGRFileManager and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

PGRFileManager IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

class PGRThumb_Image_GD extends PGRThumb_Image
{
    /**
     * GD version
     * 
     * @var string
     */
    static private $_gdVer = null;
    
    /**
     * @param string $file
     * @return PGRThumb_Image|false
     */
    static public function create($file)
    {
        if (self::getGDVer() >= 2) return new PGRThumb_Image_GD($file);
        else return false;
    }
    
    /**
     * Get GD version
     * 
     * @return string|false
     */
    static public function getGDVer()
    {
        if (self::$_gdVer != null) return self::$_gdVer; 
        
        $res = false;
        if (!extension_loaded('gd')) {
            if (dl('gd.so')) {
                $res = true;
            }
        } else $res = true;
        
        if ($res) {
            if (function_exists('gd_info')) {
                $gdInfo = gd_info();
                preg_match('/\d/', $gdInfo['GD Version'], $match);
                self::$_gdVer = $match[0];
                if (self::$_gdVer >= 2) $res = self::$_gdVer;
            } else {
                $res = false;
            }
        }
    
        return $res;
    } 
    
    public function __construct($file)
    {
        parent::__construct($file);
        $this->_processedImage = $this->_imageCreate();
    }
    
    public function destroy()
    {
        if ($this->_processedImage) imagedestroy($this->_processedImage);
    }
        
    public function resize($newWidth, $newHeight, $aspectRatio = true)
    {
        if (!$this->_processedImage) return false;
        
        //Save proportions        
        if ($aspectRatio) {
            $scale = 0;
            if ($newWidth >= $newHeight) $scale = $newWidth / $this->_width;
            else $scale = $newHeight / $this->_height;
            
            $newWidth = $this->_width * $scale;
            $newHeight = $this->_height * $scale;            
        }
        
        // Load
        $source = $this->_processedImage; 
        $this->_processedImage = imagecreatetruecolor($newWidth, $newHeight);

        //Resize
        $res = imagecopyresampled($this->_processedImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $this->_width, $this->_height);
        
        $this->_scaleX = $newWidth/$this->_width;
        $this->_scaleY = $newHeight/$this->_height;
        
        $this->_width = $newWidth;
        $this->_height = $newHeight;
        
        imagedestroy($source);
        
        return $res;        
    }

    public function crop($x1, $y1, $x2, $y2)
    {
        if (!$this->_processedImage) return false;
                
        // Load
        $source = $this->_processedImage; 
        
        $x = min($x1, $x2);
        $y = min($y1, $y2);
        $width = abs($x2 - $x1);
        $height = abs($y2 - $y1);
        
        if(($width <= 0) || ($height == 0)) return false;
        
        $this->_processedImage = imagecreatetruecolor($width, $height);

        //Resize
        $res = imagecopy($this->_processedImage, $source, 0, 0, $x, $y, $width, $height);
        
        $this->_width = $width;
        $this->_height = $height;
        
        imagedestroy($source);
        
        return $res;                
    }
    
    private function _imageCreate()
    {
        switch(self::$_imageType[$this->_type]) {
            case 'GIF':
                return imagecreatefromgif($this->_file);
            case 'JPEG':
                return imagecreatefromjpeg($this->_file);
            case 'PNG':
                return imagecreatefrompng($this->_file);
            default:
                return false;
        }
    }

    public function saveImage($file, $quality = 100, $type = null)
    {
        if (!$type) $type = self::$_imageType[$this->_type];
        
        switch($type) {
            case 'GIF':
                return imagegif($this->_processedImage, $file);
            case 'JPEG':
                return imagejpeg($this->_processedImage, $file, $quality);
            case 'PNG':
                return imagepng($this->_processedImage, $file, $quality/100);
            default:
                return false;
        }        
    }
    
    public function filterGray()
    {
        if (!$this->_processedImage) return false;
        if(!imagefilter($this->_processedImage, IMG_FILTER_GRAYSCALE)) return false;
        
        return true;
    }

    public function filterSepia()
    {
        if (!$this->_processedImage) return false;
        if(!imagefilter($this->_processedImage, IMG_FILTER_GRAYSCALE)) return false;
        if(!imagefilter($this->_processedImage, IMG_FILTER_COLORIZE, 100, 50, 0)) return false;
        
        return true;
    }
    
    public function border($size, $color)
    {
        if (!$this->_processedImage) return false;
        
        if (is_array($color)) {
            list($red, $green, $blue) = $color;
        } else list($red, $green, $blue) = PGRThumb_Utils::html2rgb($color);
        $width = $this->_width + 2 * $size;
        $height = $this->_height + 2 * $size;
        
        $newimage = imagecreatetruecolor($width, $height);
        $border_color = imagecolorallocate($newimage, $red, $green, $blue);
        imagefilledrectangle($newimage, 0, 0, $width, $height, $border_color);
        $res = imageCopyResized($newimage, $this->_processedImage, $size, $size, 0, 0, $this->_width, $this->_height, $this->_width, $this->_height);

        if ($res) $this->_processedImage = $newimage;
        
        $this->_width = $width;
        $this->_height = $height;
                
        return $res;
    }
    
    public function watermark($text, $font, $size, $color, $transparency, $place)
    {
        if(strlen($text) == 0) return;
        
        if (is_array($color)) {
            list($red, $green, $blue) = $color;
        } else list($red, $green, $blue) = PGRThumb_Utils::html2rgb($color);
        
        $x = 0;
        $y = 0;
        
        $textcord = imagettfbbox($size, 0, $font, $text);
        $textWidth = $textcord[4] - $textcord[6];
        $textHeight = $textcord[1] - $textcord[7];
        $textCenterX =  round($this->_width/2 - $textWidth/2);
        $textCenterY =  round($this->_height/2 + $textHeight/3);
        
        $padding = 2;
        
        switch ($place) {
            case 'LT':
                $y = $padding + $textHeight;
                $x = $padding;
                break;
            case 'CT':
                $y = $padding + $textHeight;
                $x = $textCenterX;
                break;
            case 'RT':
                $y = $padding + $textHeight;
                $x = $this->_width - $textWidth - $padding;
                break;
            case 'LC':
                $y = $textCenterY;
                $x = $padding;
                break;
            case 'CC':
                $y = $textCenterY;
                $x = $textCenterX;
                break;
            case 'RC':
                $y = $textCenterY;
                $x = $this->_width - $textWidth - $padding;
                break;
            case 'LB':
                $y = $this->_height - $padding;
                $x = $padding;
                break;
            case 'CB':
                $y = $this->_height - $padding;
                $x = $textCenterX;
                break;
            case 'RB':
                $y = $this->_height - $padding;
                $x = $this->_width - $textWidth - $padding;
                break;              
        }
        
        $color = imagecolorallocatealpha($this->_processedImage, $red, $green, $blue, $transparency);
        imagettftext($this->_processedImage, $size, 0, $x, $y, $color, $font, $text);
    }

    public function rotate($angle)
    {
        if (!$this->_processedImage) return false;
        
        $this->_processedImage = imagerotate($this->_processedImage, $angle, 0);
    }
    
}