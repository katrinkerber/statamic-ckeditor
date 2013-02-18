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

class PGRThumb_Cache
{
    /**
     * File mode
     * 
     * @var string
     */
    static public $dirMode = 0755;
    /**
     * Dir depth structure
     * 
     * @var int
     */
    static public $dirDepth = 2;
    /**
     * File mode
     * 
     * @var string
     */
    static public $fileMode = 0600;
    
    /**
     * Generate cached filename
     * 
     * @param string $file
     * @return string
     */
    static public function generateFilename($file, $params)
    {
        $dir = dirname($file);
        $filename = basename($file);

        $hashedFile = '';
        foreach (str_split($dir, strlen($dir) / self::$dirDepth) as $chunk) {
            $hashedFile .= '/' . md5($chunk);
        }
        
        $hashedFile .= '/' . md5($filename . $params) . '.jpg';
        
        return $hashedFile;
    }
    
    /**
     * Save image to cache file
     * 
     * @param PGRThumb_Image $image
     * @return bool
     */
    static public function saveImage($cachedFile, PGRThumb_Image $image)
    {
        $res = true;
        $cachedDir = dirname($cachedFile);
        if (!file_exists($cachedDir)) {
            $res = mkdir($cachedDir, PGRThumb_Cache::$dirMode, true);
        }          
        if ($res) {
            return $image->saveImage($cachedFile, 75, 'JPEG'); //jpeg
        }          

        return false;        
    }
    
    static public function outputCache($file)
    {        
        if (headers_sent()) {
			PGRThumb_UrlThumb::error('headers already sent');
		}        
	    				
        $modifiedDate  = filemtime($file);
        
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
		    ($modifiedDate == strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])) && 
		    $_SERVER['SERVER_PROTOCOL']) {
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			exit(0);
		} 

		return false;
    }
    
    static public function outputFile($file)
    {
    	if (headers_sent()) {
			PGRThumb_UrlThumb::error('headers already sent');
		}        
	    				
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', @filemtime($file)).' GMT');
		//header('Location: ' . $file . '?new');
		header("Content-Type: image/jpg");
        readfile($file);
        exit;
    }
}