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
function PGRFileManager(options)
{
	var self = this;

	var filesSelectable = null;
	var currentDir = "";
	var $dropDir = null;
	
	var folderMenu;
	var fileMenu;
	var filesMenu;
	var copymoveMenu;
	
    var folderLoading = new loadingPanel($("#folderList"));	
    var fileLoading = new loadingPanel($("#fileList"));
    
    var uploaderInit = false;
    var uploader = null;
    
    var fileListType = "icons";
        
    var downloadFrame = $('<iframe style="display:none"></iframe>');
    $("body").append(downloadFrame);	    
	
	this.init = function()
	{		
    	$.i18n.setDictionary(PGRFileManagerDict[options.lang]);
		
		fileListType = $("#fileListType").val();
		
		fileInfoPanel();
		
		$("#fileListType").change(function(){
			fileListType = $("#fileListType").val();
			readFolder();
		});
		
		if (options.allowEdit) {
			folderMenu = new PGRContextMenu("folderMenu", "contextMenu ui-widget-content");
			folderMenu.addItem(_("new"), addDir, "icon-add");
			folderMenu.addItem(_("rename"), renameDir, "icon-rename");
			folderMenu.addSeparator();
			folderMenu.addItem(_("delete"), deleteDir, "icon-delete");
		}
		
		var fileImageMenuPos = 2;
		fileMenu = new PGRContextMenu("fileMenu", "contextMenu ui-widget-content");
		if (window.opener && (window.opener.SetUrl || window.opener.CKEDITOR)) {
			fileMenu.addItem(
				_("choose"), 
				function() {$("#files .ui-selected").dblclick();},
				"icon-choose");
			fileMenu.addSeparator();
			fileImageMenuPos = 4;
		}
		fileMenu.addItem(_("download"), downloadFiles, "icon-download");
		fileMenu.addItem(_("preview"), preview, "icon-preview");
		if (options.allowEdit) {
			fileMenu.addItem(_("rename"), renameFile, "icon-rename");
			fileMenu.addSeparator();
			fileMenu.addItem(_("delete"), deleteFiles, "icon-delete");
		}

		fileImageMenu = new PGRContextMenu("fileImageMenu", fileMenu);
		if (options.allowEdit) {
			fileImageMenu.addItemAt(_("rotate -90"), rotateImage90CounterClockwise, "", fileImageMenuPos);
			fileImageMenu.addItemAt(_("rotate +90"), rotateImage90Clockwise, "", fileImageMenuPos);
			fileImageMenu.addItemAt(_("create a thumb"), createThumb, "", fileImageMenuPos);
			fileImageMenu.addSeparatorAt(fileImageMenuPos);
		}
		
		fileCKEditorMenu = new PGRContextMenu("fileCKEditMenu", fileMenu);
		if (options.allowEdit) {
			fileCKEditorMenu.addItemAt(_("edit with CKEditor"), editInCKEditor, "", fileImageMenuPos);
			fileCKEditorMenu.addSeparatorAt(fileImageMenuPos);
		}
		
		filesMenu = new PGRContextMenu("filesMenu", "contextMenu ui-widget-content");
		filesMenu.addItem(_("download"), downloadFiles, "icon-download");
		if (options.allowEdit) {
			filesMenu.addSeparator();
			filesMenu.addItem(_("delete"), deleteFiles, "icon-delete");
		}
		
		if (options.allowEdit) {
			copymoveMenu = new PGRContextMenu("copymoveMenu", "contextMenu ui-widget-content");
			copymoveMenu.addItem(_("copy files"), copyFiles, "icon-copy");
			copymoveMenu.addSeparator();
			copymoveMenu.addItem(_("move files"), moveFiles, "icon-move");	
		}	
		
		$("#btnRefresh").click(function(){
			getFolderTree();
		});
		$("#btnSelectAllFiles").click(function(){
			filesSelectable.selectAll();
		});
		$("#btnUnselectAllFiles").click(function(){
			filesSelectable.unselectAll();
		});
		
		if (options.allowEdit) {
			uploader = $("#fileInput").PGRUploader({
				flash_url : "SWFUpload v2.2.0.1 Core/Flash/swfupload.swf",
				upload_url: "php/upload.php?type=" + options.filesType,
				post_params: {"PHPSESSID" : options.sId},
				file_size_limit : options.fileMaxSize,
				file_types : options.allowedExtension,
				file_types_description : _(options.fileDescription),
				file_upload_limit : 100,
				file_queue_limit : 0,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "removeFiles"
				},
				debug: false,

				// Button settings
				button_image_url: "img/uploadButton.gif",
				button_width: "110",
				button_height: "30",
				button_placeholder_id: "fileInput",
				button_text: '<span class="theFont">' + _("FILES") + '</span>',
				button_text_style: ".theFont { font-family: arial; font-size: 16; font-weight:bold; color: #FFFFFF; text-align:center}",
				button_text_top_padding: 3,				
				// The event handler functions
				file_queue_error_handler :  function(file_object, error_code, message) {alert(file_object.name + " - " + message)},
				upload_init_handler : function() {uploader.addPostParam("dir", currentDir)},
				upload_all_complete_handler: readFolder
			});
		}
		
		
		$("#uploadFiles").click(function(){
			uploader.startUpload();
		});
		
		getFolderTree();	
		
		$("#leftColumn").resizable({
			handles: "e",
			stop: function() {
				if(filesSelectable) filesSelectable.setMultiselectData();
			}
		});  
			    
		function resize()
		{
			$("#content").height($("body").outerHeight() - $("#content").position().top - 10);
			$("#fileListTable").height($("#fileList").outerHeight() - $("#fileListTableHeader").outerHeight() -50);
			$("#folderList").height($("#leftColumn").outerHeight() - $("#uploadPanel").outerHeight() - 3);
		}
							   
		resize();
		//for IE6
		if($.browser.msie) $("#folderList").height($("#leftColumn").outerHeight() - $("#uploadPanel").outerHeight() - 3);
		//for IE6
			      
		var doResize = false;
		$(window).resize(function(){ 
			if (doResize !== false) clearTimeout(doResize); 
			doResize = setTimeout(function(){
				resize();
			    if(filesSelectable) filesSelectable.setMultiselectData();
			}, 200);
		}); 
			  
		//lang
		$("#btnLogoff").text(_("sign out"));
		$("#btnRefresh").text(_("refresh"));
		$("#btnSelectAllFiles").text(_("select all files"));
		$("#btnUnselectAllFiles").text(_("unselect all files"));
		$("#fileListType").children("[value='icons']").text(_("icons"));
		$("#fileListType").children("[value='list']").text(_("list"));
		//lang
	}
	
	this.initFiles = function()
	{
		filesSelectable = $("#files").PGRSelectable({
			accept: ".selectee",
			start: function() {
				if (options.allowEdit) $("#files .ui-selected").draggable('destroy');
			},
			stop: function() {
				if (options.allowEdit) $("#files .ui-selected").draggable({
					appendTo: $("body"),
					cursor: "default",
			        cursorAt: {left:-5, bottom:5},
			        helper: function(){    
			        	var container = $('<div id="filesDrop">');
			        	container.css({
			        		"height" : "48px",
			        		"width" : "48px"
			        	});
			        	container.addClass("icon-drag");    
			        	return container;   
			        }
			    });      
			},
			ondblclick: sendUrl
		});
		
		$("#fileList").PGRContextMenu({
			accept: ".selectee",
			idMenu: function(){
				$file = $("#files .ui-selected");
				if ($file.length > 1) return "filesMenu";
				else if ($file.data("fileData").imageInfo) return "fileImageMenu";
				else if (window['CKEDITOR'] && $file.data("fileData").ckEdit) return "fileCKEditMenu";
				else return "fileMenu";
			}
		});
		
		if (fileListType == "list") {
			$("#NameTableColumnHeader").resizable({
				alsoResize: "#NameTableColumn",
				handles: "e",
				minWidth: 150,
				maxWidth: 1000
			});  

			$("#SizeTableColumnHeader").resizable({
				alsoResize: "#SizeTableColumn",
				handles: "e",
				minWidth: 70,
				maxWidth: 150
			});  				
		}

	}
	
	this.initFolders = function($folders)
	{	
		if (!$folders) $folders = $("#folders");
		
		$folders.treeview({
			collapsed: true,
			prerender: true,
			onExpand: function(obj) {
				var $folder = $(obj).parents("li").eq(0); 
				if (!$folder.hasClass("fetched")) {
					getSubfolderTree($folder);					
				}
			}
		});		

		$folders.PGRSelectable({
			accept: ".selectee",
			multiselect: false,
			start: function() {
				if (options.allowEdit) $folders.find(".ui-selected").draggable('destroy');
			},
			stop: function() {
				if (options.allowEdit) $folders.find(".ui-selected").draggable({
					appendTo: $("body"),      
			        cursor: "default",
			        cursorAt: {left:-5, bottom:5},
			        helper: function(){    
			        	var container = $('<div id="folderDrop">');
			        	container.css({
			        		"height" : "48px",
			        		"width" : "48px"
			        	});
			        	container.addClass("icon-folderdrag");    
			        	return container;   
			        },
			        start: function() {$dropDir = null;}
				});
							      
			},
			onselect: function(){
				readFolder();
			}
		}); 
		
		if (options.allowEdit) {
			$folders.find("li span span").droppable({
				greedy : true,
				hoverClass : "ui-selecting",
				tolerance : "pointer",
				drop : function(e, ui){
					if($(this).is(".ui-selected")) return;
					data = $.data(ui.helper.get(0), "ui-droppedElements");
					$dropDir = $(this);
					if(ui.helper.attr("id") == "filesDrop") PGRContextMenu.showMenu(copymoveMenu.menu, e);
					else if(ui.helper.attr("id") == "folderDrop") moveDir();
				}
			});   
		}
		
		if (options.allowEdit) {
			$folders.find("li").PGRContextMenu({
				accept: ".selectee",
				idMenu: folderMenu
			});
		}
	}
		
	function getFolderTree()
	{
	    folderLoading.setLoadingPanel();
	    folderLoading.bindLoadingPanel();
		
		$.post("php/folders.php", {fun:""}, function(res){
			if (res.res == "OK") {
				$("#folderList").html(prepareFoldersContent(res.folders));
				
				self.initFolders();

				var dir = $("#folderList .selectee[directory='" + currentDir + "']");
				openDir(dir);
				dir.mousedown();
				dir.mouseup();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't get folder list"));				
			}
		}, "json");		
	}
	function getSubfolderTree($folder, onFetchCompleted)
	{		
		var dirname = $folder.find("span.selectee").attr("directory");
			
	    folderLoading.setLoadingPanel();
	    folderLoading.bindLoadingPanel();
			    
		$.post("php/folders.php", {fetchDir:dirname}, function(res){
			if (res.res == "OK") {
				initSubFolders($folder, res.folders);
				self.initFolders($folder.find("ul"));
				if (onFetchCompleted) onFetchCompleted();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't get folder list"));				
			}
		}, "json");		
	}
	function initSubFolders($folder, subfolders)
	{
		var $subfolders = $(prepareSubfoldersContent(subfolders));
		if ($folder.find("ul").length > 0) {
			$folder.find("ul").eq(0).replaceWith($subfolders);
		}
		else {
			$folder.append($subfolders);
		}
		$folder.addClass("fetched");
	}
	function resetFolderTree($folder)
	{
		var $parent = $folder.parents("ul").eq(0);
		if (!$parent) $parent = $("#folders");
		$parent.find("div").remove();
		$parent.find("li").each(function(){
			if ($(this).hasClass("fetched")) {
				$(this).removeClass().addClass("fetched");
			} else {
				$(this).removeClass()
			}
		});
		self.initFolders($parent);
	}
	
	function readFolder()
	{
		currentDir = $("#folders .ui-selected").attr("directory");
		
		delete filesSelectable;
		
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();
	    
		$.post("php/files.php?type=" + options.filesType, {dir:currentDir}, function(res){
			if (res.res == "OK") {			
				$("#fileList").html(prepareFilesContent(res.files, fileListType));			
				self.initFiles();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't read file(s)"));				
			}
		}, "json");	
		
	}
		
	function downloadFiles(file)
	{
	    if(!file) file = $("#files .ui-selected");		

	    $(".dowloadFrame").remove();
	    
	    file.each(function(){
		    var iframe = $('<iframe class="dowloadFrame" style="display:none">');
		    iframe.attr("src", "php/download.php?dir=" + currentDir + "&filename=" + $(this).attr("filename"));
		    $("body").append(iframe);
	    });
	}
	
	function moveFiles()
	{
		if ($dropDir == null) return;
		
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();		
	    
	    var $file = $("#files .ui-selected");
	    
		var i = 0;
		
		var files = "{";		
		$file.each(function(){
			if (i > 0) files += ',';
			files += '"' + i + '":"' + $(this).attr("filename") + '"';
			i++;
		})
		files += "}";
			
		$.post("php/files.php?type=" + options.filesType, {fun:"moveFiles", dir:currentDir,toDir:$dropDir.attr("directory"),files:files}, function(res){
			if (res.res == "OK") {
				$("#fileList").html(prepareFilesContent(res.files, fileListType));
			
				self.initFiles();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't move files"));				
			}
		}, "json");				
	}

	function copyFiles()
	{
		if ($dropDir == null) return;
		
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();		
	    
	    var $file = $("#files .ui-selected");
	    
		var i = 0;
		
		var files = "{";		
		$file.each(function(){
			if (i > 0) files += ',';
			files += '"' + i + '":"' + $(this).attr("filename") + '"';
			i++;
		})
		files += "}";
			
		$.post("php/files.php?type=" + options.filesType, {fun:"copyFiles", dir:currentDir,toDir:$dropDir.attr("directory"),files:files}, function(res){
			if (!res) return;
			if (res.res == "OK") {
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't delete dir"));				
			}
		}, "json");				
	}
	    		
	function renameFile()
	{
	    var filename = $("#files .ui-selected").attr("filename");	
	    
	    var buttons = new Object();
	    buttons[_('Ok')] = function() {
			var newFilename = $(this).children("input").val();
			
			if (newFilename != filename) {
				
				if(!checkFileName(newFilename)) {
					$(this).find(".ui-error-message").html("<strong>" + _("Alert") + "!</strong><br />" + _("Use only allowed chars") + ":<br />.A-Z0-9_ !@#$%^&()+={}[]',~`-");
					$(this).children("div").show();
					return;
				}
				
			    fileLoading.setLoadingPanel();
			    fileLoading.bindLoadingPanel();
				
				$.post("php/files.php?type=" + options.filesType, {fun:"renameFile", dir:currentDir,filename:filename,newFilename:newFilename}, function(res){
					if (res.res == "OK") {
						$("#fileList").html(prepareFilesContent(res.files, fileListType));
					
						self.initFiles();
					} else if (res.res == "ERROR") {
						alert(_(res.msg));
					} else {
						alert(_("Can't rename file(s)"));				
					}
				}, "json");										
			}
			$(this).dialog('close');
		};
	    buttons[_('Cancel')] = function() {
			$(this).dialog('close');
		};
	    
	    $('<div title="' + _("Enter a new file name") + '"><div class="ui-state-error ui-corner-all" style="display:none"><span class="ui-icon ui-icon-alert" style="float:left"></span><span class="ui-error-message"></span></div><input type="text" value="' + filename + '" style="border:solid 1px black;width:330px" maxlength="200" /></div>').dialog({
	    	buttons: buttons,
	    	close: function(){$(this).remove();$(this).dialog("destroy");},
	    	open: function(){
	    		var $input = $(this).children("input");
	    		setTimeout(function(){$input.focus();}, 100);
	    	},
	    	minHeight: 50,
			modal: true,
			resizable: false,
			width: 360
	    });
	}
	
	function deleteFiles()
	{
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();

	    var $file = $("#files .ui-selected");		
		
		var i = 0;
		
		var files = "{";		
		$file.each(function(){
			if (i > 0) files += ',';
			files += '"' + i + '":"' + $(this).attr("filename") + '"';
			i++;
		})
		files += "}";
								
		$.post("php/files.php?type=" + options.filesType, {fun:"deleteFiles", dir:currentDir,files:files}, function(res){
			if (res.res == "OK") {
				$("#fileList").html(prepareFilesContent(res.files, fileListType));
			
				self.initFiles();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't delete file(s)"));				
			}
		}, "json");		
	}

	var thumbWidth = 64;
	var thumbHeight = 64;
	
	function createThumb()
	{
	    var filename = $("#files .ui-selected").attr("filename");	
	    
	    var buttons = new Object();
	    buttons[_('Ok')] = function() {
			thumbWidth = parseInt($(this).children("input[name='width']").val(), 10);
			thumbHeight = parseInt($(this).children("input[name='height']").val(), 10);
			
			if (isNaN(thumbWidth)) thumbWidth = 10; 
			if (isNaN(thumbHeight)) thumbHeight = 10; 
			
			if (true) {								
			    fileLoading.setLoadingPanel();
			    fileLoading.bindLoadingPanel();
				
				$.post("php/files.php?type=" + options.filesType, {fun:"createThumb", dir:currentDir,filename:filename,thumbWidth:thumbWidth,thumbHeight:thumbHeight}, function(res){
					if (res.res == "OK") {
						$("#fileList").html(prepareFilesContent(res.files, fileListType));
					
						self.initFiles();
					} else if (res.res == "ERROR") {
						alert(_(res.msg));
					} else {
						alert(_("Can't create a thumb"));				
					}
				}, "json");										
			}
			$(this).dialog('close');
		};
	    buttons[_('Cancel')] = function() {
			$(this).dialog('close');
		};
	    
	    $('<div title="' + _("Create a thumb") + '"><div class="ui-state-error ui-corner-all" style="display:none"><span class="ui-icon ui-icon-alert" style="float:left"></span><span class="ui-error-message"></span></div><p>' + _("Set thumb size") + ':</p>' + _("max width") + ':<br /><input type="text" name="width" value="' + thumbWidth + '" style="border:solid 1px black;width:100px" maxlength="3" />px<br/>' + _("max height") + ':<br/><input type="text" name="height" value="' + thumbHeight + '" style="border:solid 1px black;width:100px" maxlength="3" />px</div>').dialog({
	    	buttons: buttons,
	    	close: function(){$(this).remove();$(this).dialog("destroy");},
	    	open: function(){
	    		var $input = $(this).children("input");
	    		$input.bind('keypress', function(e) {
	    			return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
	    		});
	    		setTimeout(function(){$input.eq(0).focus();}, 100);
	    	},
	    	minHeight: 50,
			modal: true,
			resizable: false,
			width: 200
	    });	
	}
	
	function rotateImage90Clockwise()
	{
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();

	    var filename = $("#files .ui-selected").attr("filename");	
										
		$.post("php/files.php?type=" + options.filesType, {fun:"rotateImage90Clockwise", dir:currentDir,filename:filename}, function(res){
			if (res.res == "OK") {
				$("#fileList").html(prepareFilesContent(res.files, fileListType));
			
				self.initFiles();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't rotate image"));				
			}
		}, "json");			
	}

	function rotateImage90CounterClockwise()
	{
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();

	    var filename = $("#files .ui-selected").attr("filename");	
										
		$.post("php/files.php?type=" + options.filesType, {fun:"rotateImage90CounterClockwise", dir:currentDir,filename:filename}, function(res){
			if (res.res == "OK") {
				$("#fileList").html(prepareFilesContent(res.files, fileListType));
			
				self.initFiles();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't rotate image"));				
			}
		}, "json");			
	}
	
	function editInCKEditor()
	{
		if (!window['CKEDITOR']) return;
		
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();

	    var filename = $("#files .ui-selected").attr("filename");	

	    $.post("php/ckedit.php", {fun:"getContent", dir:currentDir, filename:filename}, function(res){
			var $ckPanel = $('<div title="PGRFileManager">');
			var $textarea = $('<textarea id="ckEditContent" name="content" style="width:100%;height:220px">');
		    var buttons = new Object();
		    buttons[_('Ok')] = function() {
		    	var content = CKEDITOR.instances.ckEditContent.getData();
		    	$.post("php/ckedit.php", {fun:"putContent", dir:currentDir, filename:filename, content:content}, function(res) {
		    		$ckPanel.dialog('close');		    	
		    	}, 'text');
		    };
		    buttons[_('Cancel')] = function() {
		    	$(this).dialog('close');
		    };
			$textarea.text(res);
			$ckPanel.append($textarea);
			$ckPanel.dialog({
				resizable: false,
				width: 750,
				modal: true,
				buttons: buttons,
				close: function() {
					if (CKEDITOR.instances.ckEditContent) {
						CKEDITOR.remove(CKEDITOR.instances.ckEditContent);
						delete CKEDITOR.instances.ckEditContent;
					}
					$textarea.remove();
					$ckPanel.remove();
					delete $textarea; 
					delete $ckPanel; 				
				}
			});
			CKEDITOR.replace("ckEditContent", {
				defaultLanguage : options.lang,
				enterMode 		: CKEDITOR.ENTER_BR,
				width			: "100%",
				resize_minWidth : "100%"
			});			
		}, "text");					
	}
	
	function uploadFiles()
	{
		readFolder();		
	}
	
	function openDir(dir)
	{
		dir.parents("li").eq(0).parents("li.expandable:not(.open)").children("span").click();
	}
	
	function addDir()
	{
		var dirname = $("#folders .ui-selected").attr("directory");
		var $folder = $("#folders .ui-selected").parents("li").eq(0);
	    
	    var buttons = new Object();
	    buttons[_('Ok')] = function() {
			var newDirname = $(this).children("input").val();
			
			if(!checkFileName(newDirname)) {
				$(this).find(".ui-error-message").html("<strong>" + _("Alert") + "!</strong><br />" + _("Use only allowed chars") + ":<br />.A-Z0-9_ !@#$%^&()+={}[]',~`-");
				$(this).children("div").show();
				return;
			}
			
			fileLoading.setLoadingPanel();
			fileLoading.bindLoadingPanel();
				
			$.post("php/folders.php", {fun:"addDir", dirname:dirname, newDirname:newDirname, fetchDir:dirname}, function(res){
				if (res.res == "OK") {
				    initSubFolders($folder, res.folders);
					resetFolderTree($folder);
				
					var dir = $("#folderList .selectee[directory='" + dirname + "/" + newDirname + "']");
					openDir(dir);
					dir.mousedown();
					dir.mouseup();
				} else if (res.res == "ERROR") {
					alert(_(res.msg));
				} else {
					alert(_("Can't get folder list"));				
				}
			}, "json");			    	

			$(this).dialog('close');
		}
	    buttons[_('Cancel')] = function() {
			$(this).dialog('close');
		}

	    $('<div title="' + _("Enter a directory name") + '"><div class="ui-state-error ui-corner-all" style="display:none"><span class="ui-icon ui-icon-alert" style="float:left"></span><span class="ui-error-message"></span></div><input type="text" value="" style="border:solid 1px black;width:330px" maxlength="200" /></div>').dialog({
	    	buttons: buttons,
	    	close: function(){$(this).remove();$(this).dialog("destroy");},
	    	open: function(){
	    		var input = $(this).children("input");
	    		setTimeout(function(){input.focus()}, 100);
	    	},
	    	minHeight: 50,
			modal: true,
			resizable: false,
			width: 360
	    });
	}

	function renameDir()
	{	    
	    var dir = $("#folders .ui-selected");

	    if (dir.attr("id") == "rootDir") {
	    	alert(_("Can't rename root dir"));
	    	return;
	    }	 
	    
	    var buttons = new Object();
	    buttons[_('Ok')] = function() {
			var newDirname = $(this).children("input").val();					
			if (newDirname != dir.attr("dirname")) {
				
				if(!checkFileName(newDirname)) {
					$(this).find(".ui-error-message").html("<strong>" + _("Alert") + "!</strong><br />" + _("Use only allowed chars") + ":<br />.A-Z0-9_ !@#$%^&()+={}[]',~`-");
					$(this).children("div").show();
					return;
				}
				
			    fileLoading.setLoadingPanel();
			    fileLoading.bindLoadingPanel();
			    
			    var $parentFolder = dir.parents("li").eq(1);
			    var parentFolderName = $parentFolder.find("span.selectee").attr("directory");
				
				$.post("php/folders.php", {fun:"renameDir", dirname:dir.attr("directory"), newDirname:newDirname, fetchDir:parentFolderName}, function(res){
					if (res.res == "OK") {
					    initSubFolders($parentFolder, res.folders);
						resetFolderTree($parentFolder);
										
						var newDirectory = parentFolderName + "/" + newDirname;

						dir = $("#folderList .selectee[directory='" + newDirectory + "']");
						openDir(dir);
						dir.mousedown();
						dir.mouseup();
					} else if (res.res == "ERROR") {
						alert(_(res.msg));
					} else {
						alert(_("Can't rename dir"));				
					}
				}, "json");										
			}
			$(this).dialog('close');
		}
	    buttons[_('Cancel')] = function() {
			$(this).dialog('close');
		}
	    
	    $('<div title="' + _("Enter a new directory name") + '"><div class="ui-state-error ui-corner-all" style="display:none"><span class="ui-icon ui-icon-alert" style="float:left"></span><span class="ui-error-message"></span></div><input type="text" value="' + dir.attr("dirname") + '" style="border:solid 1px black;width:330px" maxlength="200" /></div>').dialog({
	    	buttons: buttons,
	    	close: function(){$(this).remove();$(this).dialog("destroy");},
	    	open: function(){
	    		var input = $(this).children("input");
	    		setTimeout(function(){input.focus()}, 100);
	    	},
	    	minHeight: 50,
			modal: true,
			resizable: false,
			width: 360
	    });		
	}
	
	function moveDir()
	{
		if ($dropDir == null) return;
		
	    fileLoading.setLoadingPanel();	    
	    fileLoading.bindLoadingPanel();		
	    
	    var $dir = $("#folders .ui-selected");
	    var $parentDir = $dir.parents("li").eq(1);
	    var dropDirName = $dropDir.attr("directory"); 
	    	    					    
		$.post("php/folders.php", {fun:"moveDir", dir:$dir.attr("directory"),dirname:$dir.attr("dirname"),toDir:dropDirName,fetchDir:dropDirName}, function(res){
			if (res.res == "OK") {
				if ($dir.parents("ul").eq(0).find("li").length == 1) $dir.parents("ul").eq(0).remove();
				else $dir.parents("li").eq(0).remove(); 
				resetFolderTree($parentDir);
				var $folder = $dropDir.parents("li").eq(0);
			    initSubFolders($folder, res.folders);
				resetFolderTree($folder);
			
				var newDirectory = $parentDir.find("span.selectee").attr("directory");
				var $openDir = $("#folderList .selectee[directory='" + newDirectory + "']");
				openDir($openDir);
				newDirectory = dropDirName + "/" + $dir.attr("dirname");
				$openDir = $("#folderList .selectee[directory='" + newDirectory + "']");
				openDir($openDir);
				$openDir.mousedown();
				$openDir.mouseup();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't move dir"));				
			}
		}, "json");				
	}
		
	function deleteDir()
	{
	    fileLoading.setLoadingPanel();
	    fileLoading.bindLoadingPanel();

	    var $dir = $("#folders .ui-selected");
	    var $parentDir = $dir.parents("li").eq(1);
	    
	    if ($dir.attr("id") == "rootDir") {
	    	alert(_("Can't delete root dir"));
	    	return;
	    }
	    
		$.post("php/folders.php", {fun:"deleteDir", dirname:$dir.attr("directory")}, function(res){
			if (res.res == "OK") {			
				var $parent
				if ($dir.parents("ul").eq(0).find("li").length == 1) $dir.parents("ul").eq(0).remove();
				else $dir.parents("li").eq(0).remove(); 
				resetFolderTree($parentDir);
				
				var newDirectory = $parentDir.find("span.selectee").attr("directory");
				var $openDir = $("#folderList .selectee[directory='" + newDirectory + "']");
				openDir($openDir);
				$parentDir.find("div.hitarea").eq(0).click();
				//$openDir.mousedown();
				//$openDir.mouseup();
			} else if (res.res == "ERROR") {
				alert(_(res.msg));
			} else {
				alert(_("Can't delete dir"));				
			}
		}, "json");			    	
	}
	
	function sendUrl(e, obj)
	{
		//For fckeditor
		if (window.opener && window.opener.SetUrl) {
			window.opener.SetUrl(options.rootDir + currentDir + "/" + obj.attr("filename")) ;
			window.close() ;	
		} else if(window.opener && window.opener.CKEDITOR) { 
			window.opener.CKEDITOR.tools.callFunction(options.ckEditorFuncNum, options.rootDir + currentDir + "/" + obj.attr("filename"));
			window.close() ;	
		}else {
			preview();
		}
	}
	
	function preview()
	{
		var $file = $("#files .ui-selected");
		
		if ($file.attr("fileType") == "image") {
			var a = $("<a>");
			a.attr("href", options.rootDir + currentDir + "/" + $file.attr("filename") + "?" + $file.attr("md5"));
			
			a.fancybox();
			a.click();
			
			a.remove();
		}
		
	}
	
    function checkFileName(filename)
    {
    	regexp = new RegExp("^[.A-Z0-9_ !@#$%^&()+={}\\[\\]\\',~`-]+$", "i");
    	if(filename.match(regexp)) return true;
    	else return false;
    }	
}