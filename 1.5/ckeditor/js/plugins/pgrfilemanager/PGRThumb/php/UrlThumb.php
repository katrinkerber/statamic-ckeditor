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

include_once dirname(__FILE__) . '/Cache.php';

class PGRThumb_UrlThumb
{
    /**
     * Full path to file
     * 
     * @var string
     */
    static private $_file = null;
    /**
     * Width
     * 
     * @var int
     */
    static private $_width = null;
    /**
     * Height
     * 
     * @var int
     */
    static private $_height = null;
    /**
     * Fixed aspect ratio
     * 
     * @var bool
     */
    static private $_aspectRatio = true;
    
    static private $_watermarkText = null;
    
    static private function _getParamsFromUrl()
    {
        if (isset($_GET['src'])) {
            $relPath = strip_tags(htmlspecialchars($_GET['src']));
            self::$_file = realpath($relPath); 
        }
        if (isset($_GET['w']) && is_numeric($_GET['w'])) {
            self::$_width = strip_tags(htmlspecialchars($_GET['w'])); 
        }
        if (isset($_GET['h']) && is_numeric($_GET['h'])) {
            self::$_height = strip_tags(htmlspecialchars($_GET['h'])); 
        }
        if (isset($_GET['iar'])) {
            self::$_aspectRatio =  !(strtolower($_GET['iar']) == 'true'); 
        }
        if (isset($_GET['iar'])) {
            self::$_aspectRatio =  !(strtolower($_GET['iar']) == 'true'); 
        }
        if (isset($_GET['wt'])) {
            self::$_watermarkText =  strip_tags(htmlspecialchars($_GET['wt'])); 
        }
    }
    
    static public function error($msg)
    {
        header("HTTP/1.0 404 Not Found");
            echo '<h1>Not Found</h1>';
            echo '<p>The image you requested could not be found.</p>';
            echo "<p>An error was triggered: <b>$msg</b></p>";
        exit();        
    }
    
    /**
     * Generate thumb
     * 
     * @return false|image
     */
    static public function generateThumb()
    {
        //check pass        
        if (!isset($_GET['hash']) || 
            ($_GET['hash'] != md5(str_replace('&hash='.$_GET['hash'], '', $_SERVER['QUERY_STRING']) . PGRThumb_Config::$pass))) {
            self::error("can't do this");            
        }
        
        self::_getParamsFromUrl();
        
        //check if file is set
        if (!self::$_file) return false;
        
        //generate cached filename
        $cachedFile = realpath(dirname(__FILE__) . '/../cache') . PGRThumb_Cache::generateFilename(self::$_file, $_SERVER['QUERY_STRING']);
        
        //check if cached file exist
        if (file_exists($cachedFile)) {
            PGRThumb_Cache::outputCache($cachedFile);
        }
        
        //file is not cached, so I will generate thumb
        
        //check if file exist
        if (!file_exists(self::$_file)) {
            self::error("can't find file");
        }
        
        //Get image library 
        include_once 'Image.php';
        $image = PGRThumb_Image::factory(self::$_file);
        
        if (!$image) {
            self::error("image library doesn't exist");
        }
        
        //watermark
        if (self::$_watermarkText) {
            $image->resize(355, 0, true);                
            $image->watermark(
                self::$_watermarkText,
                realpath(dirname(__FILE__) . '/ttf/Parkvane.ttf'), 
                28,
                array(210,210,210),
                80,
                'CB'
            );
        }
        
        //resize image
        $res = null;
        if ((self::$_width > 0) && (self::$_height > 0)) {
            if (self::$_aspectRatio) {
                $res = $image->maxSize(self::$_width, self::$_height);
            } else {
                $res = $image->resize(self::$_width, self::$_height, false);                
            }
        }
                
        //save thumb 
        if ($res) $res = PGRThumb_Cache::saveImage($cachedFile, $image);
        
        if ($res) PGRThumb_Cache::outputFile($cachedFile);

        if (!$res) {
            self::error("Can't perform thumb");
        }        
    }
}