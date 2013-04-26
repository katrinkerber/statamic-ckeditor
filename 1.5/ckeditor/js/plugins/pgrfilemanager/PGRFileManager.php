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

    include_once dirname(__FILE__) . '/php/init.php';
    $PGRUploaderExtension = "";
    if (PGRFileManagerConfig::$allowedExtensions == "") $PGRUploaderExtension = "*.*";
    else
    foreach(explode("|", PGRFileManagerConfig::$allowedExtensions) as $key => $extension) { 
        if ($key > 0) $PGRUploaderExtension .= ";";
        $PGRUploaderExtension .= "*." . $extension;   
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Choose or upload file</title>
  <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.1.custom.css" />
  <link rel="stylesheet" type="text/css" href="css/jquery.treeview.css" />
  <link rel="stylesheet" type="text/css" href="css/fancybox/jquery.fancybox.css" />
  <link rel="stylesheet" type="text/css" href="css/PGRFileManager.css" />
  <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
  <script type="text/javascript" src="js/jquery.treeview.js"></script>
  <script type="text/javascript" src="SWFUpload v2.2.0.1 Core/swfupload.js"></script>
  <script type="text/javascript" src="js/jquery.i18n.js"></script>
  <script type="text/javascript" src="js/jquery.fancybox-1.2.1.pack.js"></script>
  <?php if (PGRFileManagerConfig::$ckEditorScriptPath):?>
  <script type="text/javascript" src="<?php echo PGRFileManagerConfig::$ckEditorScriptPath ?>"></script>
  <?php endif;?>
  <script type="text/javascript" src="lang/lang.js"></script>
  <script type="text/javascript" src="js/PGRUploader.js"></script>
  <script type="text/javascript" src="js/PGRContextMenu.js"></script>
  <script type="text/javascript" src="js/PGRSelectable.js"></script>
  <script type="text/javascript" src="js/PGRFileManager.js"></script>
  <script type="text/javascript" src="js/PGRFileManagerContent.js"></script>
  
  </head>
  <body>   
    <div id="container" class="ui-widget ui-widget-content">
      <div id="buttons">
      	<?php if (isset($_SESSION['PGRFileManagerAuthorized'])):?>
      	<form method="post">
      		<div>
        	<button id="btnLogoff" name="logoff" type="submit" class="ui-state-default ui-corner-all" style="float:left">sign out</button>
        	</div>
        </form>
        <?php endif;?>
        <button id="btnRefresh" class="ui-state-default ui-corner-all">refresh</button>
        <button id="btnSelectAllFiles" class="ui-state-default ui-corner-all">select all files</button>
        <button id="btnUnselectAllFiles" class="ui-state-default ui-corner-all">unselect all files</button>
        <select id="fileListType" class="ui-state-default ui-corner-all">
            <option value="icons">icons</option>
            <option value="list">list</option>
        </select>
      </div>
      <div id="content" class="ui-dialog-content ui-widget-content">
        <div id="leftColumn">
          <div id="folderList">
          </div>
          <?php if (PGRFileManagerConfig::$allowEdit):?>
          <div id="uploadPanel" class="ui-widget-content" >
            <strong>Upload files</strong>
            <input type="file" name="fileInput" id="fileInput"  />
            <img id="uploadFiles" src="img/blank.gif" />
            <img id="removeFiles" src="img/blank.gif" />
            <div id="fsUploadProgress"></div>
          </div>
          <?php endif;?>
        </div>
        <div id="rightColumn">
          <div id="fileList" class="unselectable">                      
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
        function _(str)
        {
            return $.i18n._(str);
        }
        $(function() {        	            
            var filemanager = new PGRFileManager({
            	sId : "<?php echo session_id()?>", 
                rootDir : "<?php echo PGRFileManagerConfig::$urlPath?>", 
                allowedExtension : "<?php echo $PGRUploaderExtension?>", 
                fileDescription : "<?php echo $PGRUploaderDescription?>", 
                filesType : "<?php echo $PGRUploaderType?>",
                fileMaxSize : "<?php echo PGRFileManagerConfig::$fileMaxSize?> B",
                lang: "<?php echo $PGRLang?>",
                ckEditorFuncNum: "<?php echo $ckEditorFuncNum?>",
                allowEdit: <?php echo PGRFileManagerConfig::$allowEdit?'true':'false'?>
            });
            filemanager.init();
        });    
    </script>  
  </body>
</html>
