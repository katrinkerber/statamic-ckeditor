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

//get dir from post
$directory = realpath(PGRFileManagerConfig::$rootDir);
$relativePath = ''; 

//check if dir exist
if (!is_dir($directory)) PGRFileManagerUtils::sendError("Can't find root directory");

//check for extra function to do
if (isset($_POST['fun']) && PGRFileManagerConfig::$allowEdit) {
    $fun = $_POST['fun'];
    
    if (($fun === 'deleteDir') && isset($_POST['dirname'])) {
        $dirname = $_POST['dirname'];
        
        $dir = realpath($directory . $dirname);
        
        //check if dir is not a rootdir        
        if ($dir === $directory) die();
        //check if dir is in rootdir
        if (strpos($dir, $directory) !== 0) die();        
        
        if(is_dir($dir)) PGRFileManagerUtils::deleteDirectory($dir);
        
        echo json_encode(array(
    		'res'     => 'OK',
        ));

        exit(0);        
    } else if (($fun === 'addDir') && isset($_POST['dirname']) && isset($_POST['newDirname'])) {
        $dirname = $_POST['dirname'];
        $newDirname = $_POST['newDirname'];
        
        //allowed chars
        if(preg_match("/^[.A-Z0-9_ !@#$%^&()+={}\\[\\]\\',~`-]+$/i", $newDirname) === 0) die();
        
        $dirnameLength = strlen($newDirname);
        if($dirnameLength === 0) die();
        if($dirnameLength > 200) die();
                
        $dir = realpath($directory . $dirname);
        
        //check if dir is in rootdir
        if (strpos($dir, $directory) !== 0) die();        
        
        if(is_dir($dir . '/' . basename($newDirname))) die();
        if(is_dir($dir)) mkdir($dir . '/' . basename($newDirname));
    } else if (($fun === 'renameDir') && (isset($_POST['dirname'])) && (isset($_POST['newDirname']))) {
        
        $dirname = $_POST['dirname'];
        $newDirname = basename($_POST['newDirname']);
        
        //allowed chars
        if(preg_match("/^[.A-Z0-9_ !@#$%^&()+={}\\[\\]\\',~`-]+$/i", $newDirname) === 0) die();
        
        $dirnameLength = strlen($newDirname);
        if($dirnameLength === 0) die();
        if($dirnameLength > 200) die();
        
        $dir = realpath($directory . $dirname);
        
        //check if dir is not a rootdir        
        if ($dir === $directory) die();
        //check if dir is in rootdir
        if (strpos($dir, $directory) !== 0) die();
        
        if(is_dir($dir . '/../' . $newDirname)) die();
        
        if(is_dir($dir)) rename($dir, $dir . '/../' . $newDirname);
    } else if (($fun === 'moveDir') && (isset($_POST['dir'])) && (isset($_POST['dirname'])) && (isset($_POST['toDir']))) {
        $dir = realpath(PGRFileManagerConfig::$rootDir . $_POST['dir']);
        $targetDir = realpath(PGRFileManagerConfig::$rootDir . $_POST['toDir']);
        $dirname = basename($_POST['dirname']);
        //check if dir is in rootdir
        if(strpos($dir, $directory) !== 0) die();
        if(strpos($targetDir, $directory) !== 0) die();
        if($dir === $targetDir) die();
        if(strpos($targetDir . '/', $dir . '/') === 0) die();
        
        if(is_dir($targetDir . '/' . $dirname)) die();
                
        if(is_dir($dir)) rename($dir, $targetDir . '/' . $dirname);
    }   
}

if (isset($_POST['fetchDir']) && ($_POST['fetchDir'])) {
    $dirname = $_POST['fetchDir'];
        
    $dir = realpath($directory . $dirname);
        
    //check if dir is not a rootdir        
    if ($dir === $directory) die();
    //check if dir is in rootdir
    if (strpos($dir, $directory) !== 0) die();        
        
    $directory = $dir;
    $relativePath = $dirname;
}

$folders = array();
$depth = 0;
//group folders
function getFolders($dir, $relativePath)
{
    global $folders;
    global $depth;   
    
    foreach (scandir($dir) as $elem) {
        if (($elem === '.') || ($elem === '..')) continue;
        $dirpath = $dir . '/' . $elem;
        if (is_dir($dirpath)) {
            $folder = array();
            $folder['dirname'] = $elem;
            $folder['shortname'] = (strlen($elem) > 17) ? substr($elem, 0, 17) . '...' : $elem;
            $folder['relativePath'] = $relativePath . '/' . $elem;
            $folder['depth'] = $depth; 
            $folders[] = $folder; 
            
            if ($depth < 1) {
                $depth++;
                getFolders($dirpath, $folder['relativePath']);
                $depth--;
            } else break;
        }
    } 
}

getFolders($directory, $relativePath);

echo json_encode(array(
    'res'     => 'OK',
    'folders' => $folders
));

exit(0);