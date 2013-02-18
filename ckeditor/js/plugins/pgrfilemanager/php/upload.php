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
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}

include_once dirname(__FILE__) . '/init.php';
include_once dirname(__FILE__) . '/utils.php';

//check if upload is allowed
if (!PGRFileManagerConfig::$allowEdit) die("Not allowed!");

//get dir from GET
if (isset($_POST['dir'])) {
    $directory = realpath(PGRFileManagerConfig::$rootDir . $_POST['dir']);
} else {
    $directory = realpath(PGRFileManagerConfig::$rootDir);
}   

//check if dir exist
if (!is_dir($directory)) die();

//check if dir is in rootdir
if (strpos($directory, realpath(PGRFileManagerConfig::$rootDir)) === false) die();

if (!empty($_FILES)) {
    $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetFile =  $directory . '/' . $_FILES['Filedata']['name'];
            
    
    // Validate the file size (Warning: the largest files supported by this code is 2GB)
    $file_size = filesize($tempFile);
    if (!$file_size || $file_size > PGRFileManagerConfig::$fileMaxSize) exit(0);
        
    //check file ext
    if (PGRFileManagerConfig::$allowedExtensions != "") {
        if(preg_match('/^.*\.(' . PGRFileManagerConfig::$allowedExtensions . ')$/', strtolower($_FILES['Filedata']['name'])) === 0) {
            exit(0);            
        }
    }         
    
    move_uploaded_file($tempFile,$targetFile);
    
    //if image check size, and rescale if necessary    
    try{
        if (preg_match('/^.*\.(jpg|gif|jpeg|png|bmp)$/', strtolower($_FILES['Filedata']['name'])) > 0) {
            $targetFile = realpath($targetFile);
            $imageInfo = PGRFileManagerUtils::getImageInfo($targetFile);
            if (($imageInfo !== false) && 
               (($imageInfo['height'] > PGRFileManagerConfig::$imageMaxHeight) || 
                ($imageInfo['width'] > PGRFileManagerConfig::$imageMaxWidth))) {                
                    require_once(realpath(dirname(__FILE__) . '/../PGRThumb/php/Image.php'));
                    $image = PGRThumb_Image::factory($targetFile);
                    $image->maxSize(PGRFileManagerConfig::$imageMaxWidth, PGRFileManagerConfig::$imageMaxHeight);
                    $image->saveImage($targetFile, 80);
            }
        }
    } catch(Exception $e) {
        //todo    
    }    
}

exit(0);