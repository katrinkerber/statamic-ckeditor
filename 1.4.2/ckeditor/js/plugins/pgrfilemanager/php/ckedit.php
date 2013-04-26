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

if (strlen(PGRFileManagerConfig::$ckEditorScriptPath) === 0) die();
if (strlen(PGRFileManagerConfig::$ckEditorExtensions) === 0) die();

//get dir from GET
if (isset($_POST['dir'])) {
    $directory = realpath(PGRFileManagerConfig::$rootDir . $_POST['dir']);
} else die();

//check if dir exist
if (!is_dir($directory)) die();

//check if dir is in rootdir
if (strpos($directory, realpath(PGRFileManagerConfig::$rootDir)) === false) die();

if (!isset($_POST['filename'])) die();

$filename = realpath($directory . '/' . $_POST['filename']);
//check if file is in dir
if(dirname($filename) !== $directory) die();

//check file extension
if (preg_match('/^.*\.(' . PGRFileManagerConfig::$ckEditorExtensions . ')$/', strtolower($filename)) === 0) die();

//check for extra function to do
if (isset($_POST['fun']) && PGRFileManagerConfig::$allowEdit) {
    $fun = $_POST['fun'];    
    if ($fun === 'getContent') {
        echo file_get_contents($filename);
    } else if (($fun === 'putContent') && (isset($_POST['content']))) {
        if (get_magic_quotes_gpc()) {
            $_POST['content'] = stripslashes($_POST['content']);
        }
        file_put_contents($filename, $_POST['content']);
    }
}
die();