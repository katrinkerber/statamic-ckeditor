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

abstract class PGRThumb_Image
{
    static protected $_imageType = array(
        1  => 'GIF',
        2  => 'JPEG',
        3  => 'PNG',
        4  => 'SWF',
        5  => 'PSD',
        6  => 'BMP',
        7  => 'TIFF',
        8  => 'TIFF',
        9  => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF',
        15 => 'WBMP',
        16 => 'XBM'
    );
    
    protected $_file;
    protected $_width;
    protected $_height;
    protected $_type;
    
    protected $_scaleX;
    protected $_scaleY;
    
    protected $_processedImage;
    
    /**
     * @param string $file
     * @return PGRThumb_Image|false
     */
    static public function factory($file)
    {
        $image = false;
        
        /*if (!$image) {
            //check if ImageMagick exist;
            include_once dirname(__FILE__) . '/Image/ImageMagick.php';
            $image = PGRThumb_Image_ImageMagick::create($file);
        }*/       
        if (!$image) {
            //check if GD exist;
            include_once dirname(__FILE__) . '/Image/GD.php';
            $image = PGRThumb_Image_GD::create($file);
        }
        
        $type = $image->getType();
        if ((self::$_imageType[$type] == 'GIF') || 
            (self::$_imageType[$type] == 'JPEG') ||
            (self::$_imageType[$type] == 'PNG')) {
            return $image;
        }
        
        return false;
    }
    
    public function __construct($file)
    {
        $this->_file = $file;    
        list($this->_width, $this->_height, $this->_type) = getimagesize($this->_file);
    }
    
    public abstract function destroy();
    
    public abstract function resize($newWidth, $newHeight, $aspectRatio = true);
    
    public abstract function crop($x1, $y1, $x2, $y2);
    
    public function maxSize($maxWidth, $maxHeight)
    {
        if ($maxWidth > $this->_width) $maxWidth = $this->_width;
        if ($maxHeight > $this->_height) $maxHeight = $this->_height;
        
        $ratio = $this->_width / $this->_height;
        $maxRatio = $maxWidth / $maxHeight; 
        if($maxRatio <= $ratio) {
            $maxHeight = round($maxWidth / $ratio);
        } else {
            $maxWidth = round($maxHeight * $ratio);
        }
        return $this->resize($maxWidth, $maxHeight, true);
    }
    
    public abstract function rotate($angle);
    
    public function getWidth()
    {
        return $this->_width;
    }
    
    public function getHeight()
    {
        return $this->_height;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function getScaleX()
    {
        return $this->_scaleX;
    }
    
    public function getScaleY()
    {
        return $this->_scaleY;
    }
    
    public abstract function filterGray();
    
    public abstract function filterSepia();
    
    public abstract function border($size, $color);
    
    public abstract function watermark($text, $font, $size, $color, $transparency, $place);
    
    public abstract function saveImage($file, $quality = 100, $type = null);    
}

