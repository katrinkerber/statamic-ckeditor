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
class PGRFileManagerUtils
{
    static private $imageType = array(
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
    
    static public function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
  
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
  
        $bytes /= pow(1024, $pow);
  
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    static public function getImageInfo($filename)
    {
        if ((list($width, $height, $type, $attr) = getimagesize($filename) ) !== false ) {
            if(($type == 4) || ($type == 13)) return false;
            return array(
                'type' => self::$imageType[$type],
                'width' => $width,
                'height' => $height
            );
        }
        return false;
    }    
    
    static public function getPhpThumb($params)
    {
        return PGRFileManagerConfig::$pgrThumbPath . '/pgrthumb.php?' . $params . '&hash=' . md5($params . PGRThumb_Config::$pass);
    }
            
    static public function deleteDirectory($dir) 
    {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!self::deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
        }
        return rmdir($dir);
    }
    
    static public function sendError($message)
    {
        echo json_encode(array(
            'res' => 'ERROR',
            'msg' => $message
        ));
        
        die();
    }
        
    static public function curPageURL() {
        $pageURL = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))?'https':'http'; 
        $pageURL .= '://' . $_SERVER['SERVER_NAME'];
        if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != '80')) {
            $pageURL .= ':' . $_SERVER['SERVER_PORT'];
        } 
        $pageURL .= $_SERVER['REQUEST_URI'];
        return $pageURL;
    }
}