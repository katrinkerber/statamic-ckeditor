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
include_once dirname(__FILE__) . '/init.php';
include_once dirname(__FILE__) . '/utils.php';

require_once(realpath(dirname(__FILE__) . '/../PGRThumb/myconfig.php'));
PGRFileManagerConfig::$pgrThumbPath = 'http://' . $_SERVER['SERVER_NAME'] . substr(dirname($_SERVER['PHP_SELF']), 0, strlen(dirname($_SERVER['PHP_SELF'])) - 3) . 'PGRThumb';

//get dir from post
if (isset($_POST['dir'])) {
    $directory = realpath(PGRFileManagerConfig::$rootDir . $_POST['dir']);
} else {
    $directory = realpath(PGRFileManagerConfig::$rootDir);    
}

//check if dir exist
if (!is_dir($directory)) die();

//check if dir is in rootdir
if(strpos($directory, realpath(PGRFileManagerConfig::$rootDir)) !== 0) die();

//check for extra function to do
if (isset($_POST['fun']) && PGRFileManagerConfig::$allowEdit) {
    $fun = $_POST['fun'];
    
    if (($fun === 'deleteFiles') && (isset($_POST['files']))) {
        $files = str_replace("\\", "", $_POST['files']);
        $files = json_decode($files, true);
        
        foreach ($files as $filename) {
            $file = realpath($directory . '/' . $filename);
            //check if file is in dir
            if(dirname($file) !== $directory) continue;
            if(file_exists($file)) unlink($file);
        }
    } else if (($fun === 'moveFiles') && (isset($_POST['toDir'])) && (isset($_POST['files']))) {
        $targetDirectory = realpath(PGRFileManagerConfig::$rootDir . $_POST['toDir']);
        //check if dir is in rootdir
        if(strpos($targetDirectory, realpath(PGRFileManagerConfig::$rootDir)) !== 0) die();
        if($directory === $targetDirectory) die();
        
        $files = str_replace("\\", "", $_POST['files']);
        $files = json_decode($files, true);
        
        foreach ($files as $filename) {
            $filename = basename($filename);
            $file = realpath($directory . '/' . $filename);
            $newFile = $targetDirectory . '/' . $filename;
            //check if file is in dir
            if(dirname($file) !== $directory) continue;
            if(file_exists($file) && !file_exists($newFile)) {
                rename($file, $newFile);
            }
        }        
    } else if (($fun === 'copyFiles') && (isset($_POST['toDir'])) && (isset($_POST['files']))) {
        $targetDirectory = realpath(PGRFileManagerConfig::$rootDir . $_POST['toDir']);
        //check if dir is in rootdir
        if(strpos($targetDirectory, realpath(PGRFileManagerConfig::$rootDir)) !== 0) die();
        if($directory === $targetDirectory) die();
        
        $files = str_replace("\\", "", $_POST['files']);
        $files = json_decode($files, true);
        
        foreach ($files as $filename) {
            $filename = basename($filename);
            $file = realpath($directory . '/' . $filename);
            $newFile = $targetDirectory . '/' . $filename;
            //check if file is in dir
            if(dirname($file) !== $directory) continue;
            if(file_exists($file)) {
                copy($file, $newFile);
            }
        }
        die();
    } else if (($fun === 'renameFile') && (isset($_POST['filename'])) && (isset($_POST['newFilename']))) {
        
        $filename = basename($_POST['filename']);
        $newFilename = basename($_POST['newFilename']);
        
        //allowed chars
        if(preg_match("/^[.A-Z0-9_ !@#$%^&()+={}\\[\\]\\',~`-]+$/i", $newFilename) === 0) die();
        
        $fileLength = strlen($newFilename);
        if($fileLength === 0) die();
        if($fileLength > 200) die();
                
        $file = realpath($directory . '/' . $filename);
        $newFile = $directory . '/' . $newFilename;
        //check if file is in dir
        if(dirname($file) !== $directory) die();
        if(file_exists($file) && !file_exists($newFile)) {
            rename($file, $newFile);
        }
    } else if (($fun === 'createThumb') && (isset($_POST['filename'])) && (isset($_POST['thumbWidth'])) && (isset($_POST['thumbHeight']))) {
        $thumbWidth = intval($_POST['thumbWidth']);
        $thumbHeight = intval($_POST['thumbHeight']);
        if (($thumbWidth >= 10) && ($thumbHeight >= 10)) {
            require_once(realpath(dirname(__FILE__) . '/../PGRThumb/php/Image.php'));
            $filename = basename($_POST['filename']);
            $file = realpath($directory . '/' . $filename);
            $fileInfo = pathinfo($file);
            $image = PGRThumb_Image::factory($file);
            $image->maxSize($thumbWidth, $thumbHeight);
            $image->saveImage($fileInfo['dirname'] . '/' . $fileInfo['filename']  . $thumbWidth . 'x' . $thumbHeight . '.' . $fileInfo['extension']);
        }    
    } else if (($fun === 'rotateImage90Clockwise') && (isset($_POST['filename']))) {
        require_once(realpath(dirname(__FILE__) . '/../PGRThumb/php/Image.php'));
        $filename = basename($_POST['filename']);
        $file = realpath($directory . '/' . $filename);
        $image = PGRThumb_Image::factory($file);
        $image->rotate(-90);
        $image->saveImage($file);        
    } else if (($fun === 'rotateImage90CounterClockwise') && (isset($_POST['filename']))) {
        require_once(realpath(dirname(__FILE__) . '/../PGRThumb/php/Image.php'));
        $filename = basename($_POST['filename']);
        $file = realpath($directory . '/' . $filename);
        $image = PGRThumb_Image::factory($file);
        $image->rotate(90);
        $image->saveImage($file);        
    }
}

$files = array();
//group files
foreach (scandir($directory) as $elem) {
    if (($elem === '.') || ($elem === '..')) continue;
    //check file ext
    if (PGRFileManagerConfig::$allowedExtensions != "") {
        if(preg_match('/^.*\.(' . PGRFileManagerConfig::$allowedExtensions . ')$/', strtolower($elem)) === 0) {
            continue;            
        }
    } 
   
    $filepath = $directory . '/' . $elem;
    if (is_file($filepath)) {              
        $file = array();
        $file['filename'] = $elem;
        $file['shortname'] = (strlen($elem) > 17) ? substr($elem, 0, 17) . '...' : $elem;
        $file['size'] = PGRFileManagerUtils::formatBytes(filesize($filepath));
        $file['md5'] = md5(filemtime($filepath));
        if (PGRFileManagerConfig::$ckEditorExtensions != "") $file['ckEdit'] = (preg_match('/^.*\.(' . PGRFileManagerConfig::$ckEditorExtensions . ')$/', strtolower($elem)) > 0);
        else $file['ckEdit'] = false;
        $file['date'] = date('Y-m-d H:i:s', filemtime($filepath));
        $file['imageInfo'] = PGRFileManagerUtils::getImageInfo($filepath);
        if ($file['imageInfo'] != false) {
            $file['thumb'] = PGRFileManagerUtils::getPhpThumb("src=" . urlencode(PGRFileManagerConfig::$rootPath . $_POST['dir'] . '/' .$elem) . "&w=64&h=64&md5=" . $file['md5']);
        } else $file['thumb'] = false;
        $files[] = $file; 
    } 
}

echo json_encode(array(
    'res'     => 'OK',
    'files' => $files
));

exit(0);