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

include_once dirname(__FILE__) . '/config.php';
include_once dirname(__FILE__) . '/../myconfig.php';
PGRFileManagerConfig::$rootDir = PGRFileManagerConfig::$rootPath;

//Lang
if (isset($_GET['langCode'])) {
    $PGRLang = $_GET['langCode'];
} else {
    $PGRLang = 'en';
}

include_once dirname(__FILE__) . '/auth.php';

$PGRUploaderDescription = 'all files';
//For fckeditor
if (isset($_GET['type'])) {
    $type = $_GET['type'];
    if ($type === 'Image') {
        PGRFileManagerConfig::$allowedExtensions = PGRFileManagerConfig::$imagesExtensions;
        $PGRUploaderDescription = 'images';
    } else if ($type === 'Flash') {
        PGRFileManagerConfig::$allowedExtensions = 'swf|flv';   
        $PGRUploaderDescription = 'flash';
    } else {
        $PGRUploaderDescription = 'all files';        
    }
    
    $PGRUploaderType = $_GET['type']; 
} else {
    $PGRUploaderType = 'all files';
}

//for ckeditor
if (isset($_GET['CKEditorFuncNum'])) {
    $ckEditorFuncNum = $_GET['CKEditorFuncNum'];
} else {
    $ckEditorFuncNum = '1';
}

//for PHP <= 5.2.0 json
include_once dirname(__FILE__) . '/json.php';