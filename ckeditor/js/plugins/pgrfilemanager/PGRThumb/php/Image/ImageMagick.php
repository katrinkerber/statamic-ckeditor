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

class PGRThumb_Image_ImageMagick extends PGRThumb_Image
{
    /**
     * ImageMagick Version
     * 
     * @var string
     */
    static private $_imVer = null;
        
    static private $_exec = 'exec';
    
    private $_commands = array();
    
    /**
     * @param string $file
     * @return PGRThumb_Image|false
     */
    static public function create($file)
    {
        if (self::getIMVer() >= '6.0.0') return new PGRThumb_Image_ImageMagick($file);
        else return false;
    }
    
    static private function _exec($command)
    {
        $fun = self::$_exec;
        $fun(PGRThumb_Config::$imageMagickPath . $command, $output);
        return implode(' ', $output);
    }
    
    public function __construct($file)
    {
        parent::__construct($file);
        //$this->_processedImage = $this->_imageCreate();
    }
    
    /**
     * Get GD version
     * 
     * @return string|false
     */
    static public function getIMVer()
    {
        if (self::$_imVer != null) return self::$_imVer; 
                
        $output = self::_exec('convert --version');
        
        preg_match('/ImageMagick\s+([\d.]+)/', $output, $match);
        
        if (count($match) > 0) {
            self::$_imVer = $match[1];
            return self::$_imVer;
        }
        
        return false;
    } 
    
    public function destroy() 
    {
    }

    public function resize($newWidth, $newHeight, $aspectRatio = true) 
    {
        //Save proportions        
        if ($aspectRatio) {
            $scale = 0;
            if ($newWidth >= $newHeight) $scale = $newWidth / $this->_width;
            else $scale = $newHeight / $this->_height;
            
            $newWidth = $this->_width * $scale;
            $newHeight = $this->_height * $scale;            
        }
        
        //Resize
        $this->_commands[] = '-resize ' . $newWidth . 'x' . $newHeight . '!'; 
        
        $this->_scaleX = $newWidth/$this->_width;
        $this->_scaleY = $newHeight/$this->_height;
        
        $this->_width = $newWidth;
        $this->_height = $newHeight;
                
        return true;        
    }
    
    public function crop($x1, $y1, $x2, $y2)
    {
        $x = min($x1, $x2);
        $y = min($y1, $y2);
        $width = abs($x2 - $x1);
        $height = abs($y2 - $y1);
        
        if(($width <= 0) || ($height == 0)) return false;
        
        //Crop
        $this->_commands[] = '-crop ' . $newWidth . 'x' . $newHeight . '!+' . $x . '+' . $y; 
        
        $this->_width = $width;
        $this->_height = $height;
                
        return true;                
    }
    
    public function filterGray()
    {
        $this->_commands[] = '-colorspace Gray';
        
        return true;
    }
    
    public function filterSepia()
    {
        $this->_commands[] = '-sepia-tone 80%';
        
        return true;
    }
    
    public function border($size, $color)
    {
        if (is_array($color)) {
            list($red, $green, $blue) = $color;
        } else list($red, $green, $blue) = PGRThumb_Utils::html2rgb($color);
        
        $this->_commands[] = '-border-color "rgb(' . $red . ',' . $green . ',' . $blue . ')" -borderwidth ' . $size;
        
        return true;
    }
    
    public function watermark($text, $font, $size, $color, $transparency, $place)
    {
        if (is_array($color)) {
            list($red, $green, $blue) = $color;
        } else list($red, $green, $blue) = PGRThumb_Utils::html2rgb($color);
        
        switch ($place) {
            case 'LT':
                $place = "NorthWest";
                break;
            case 'CT':
                $place = "North";
                break;
            case 'RT':
                $place = "NorthEast";
                break;
            case 'LC':
                $place = "West";
                break;
            case 'CC':
                $place = "Center";
                break;
            case 'RC':
                $place = "East";
                break;
            case 'LB':
                $place = "SouthWest";
                break;
            case 'CB':
                $place = "South";
                break;
            case 'RB':
                $place = "SouthEast";
                break;              
        }
        
        $this->_commands[] = '-font "' . $font . '"';
        $this->_commands[] = '-draw \'text gravity ' . $place . ' color "rgb(' . $red . ',' . $green . ',' . $blue . ')" "'. $text . '"\'';
        
        
    }
    
    public function saveImage($file, $quality = 100, $type = null)
    {   
        if (file_exists($file)) unlink($file);
        $command = 'convert "' . $this->_file . '" ' . implode(' ', $this->_commands) . ' "' . $file . '"';
        self::_exec($command);
        
        if (file_exists($file)) return true;
        else return false;
    }    

    public function rotate($angle)
    {
        //TODO
    }
    
}