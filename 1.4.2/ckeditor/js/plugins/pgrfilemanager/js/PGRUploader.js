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
jQuery.fn.PGRUploader = function(options) {

	var settings = jQuery.extend({
		flash_url : "../swfupload/swfupload.swf",
		upload_url: "upload.php",
		post_params: {},
		file_size_limit : "100 MB",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,

		// Button settings
		button_image_url: "images/TestImageNoText_65x29.png",
		button_width: "65",
		button_height: "29",
		button_placeholder_id: "spanButtonPlaceHolder",
		button_text: '<span class="theFont">Hello</span>',
		button_text_style: ".theFont { font-size: 16; }",
		button_text_left_padding: 0,
		button_text_top_padding: 3,
		
		// The event handler functions are defined in handlers.js
		file_queued_handler : fileQueued,
		file_queue_error_handler : null,
		
		file_dialog_complete_handler : null,
		upload_init_handler: null,
		upload_start_handler : null,
		upload_progress_handler : null,
		upload_error_handler : null,
		upload_success_handler : null,
		upload_complete_handler : null,
		upload_all_complete_handler : null	// Queue plugin event*/
	}, options);
	
	var swfu = new SWFUpload(settings);
	var progressPanel = $("#" + settings.custom_settings.progressTarget);
	
	$(window).unload(function(){swfu.destroy()});
	
	function fileQueued(fileObj)
	{		
		//if(document.getElementById(fileObj.id)) return;
		if(progressPanel.find("[filename='" + fileObj.name + "']").is("div")) {
			swfu.cancelUpload(fileObj.id);
			return;
		}
		
		var element = $("<div>");
		element.attr("id", fileObj.id);
		element.addClass("progressElement");
		element.attr("filename", fileObj.name);
		
		var cancel = $("<div>");
		cancel.addClass("progressCancel");
		cancel.attr("href", "#");

		var text = $("<div>");
		text.addClass("progressName");
		text.html(fileObj.name);

		element.append(text);
		element.append(cancel);
		element.append('<div style="clear:both"></div>');
		
		
		progressPanel.append(element);
	}
	
	function fileQueueError()
	{
		alert("pl");
	}
	
	progressPanel.click(function(e){
		if($(e.target).is(".progressCancel")) {
			var fileId = $(e.target).parents().attr("id");
			swfu.cancelUpload(fileId);
			$(e.target).parent().remove();
		}
	});
	
	$("#" + settings.custom_settings.cancelButtonId).click(function(e){
		swfu.stopUpload();
		
		var stats = swfu.getStats();
		while (stats.files_queued > 0) {
			swfu.cancelUpload();
			stats = swfu.getStats();
		}
		
		progressPanel.children().remove();
			
	});
	
	this.startUpload = function() {
		if(jQuery.isFunction(settings.upload_init_handler)) settings.upload_init_handler();
		settings.upload_start_handler = function(fileObj){
			progressPanel.find("#" + fileObj.id + " .progressName").addClass("uploading");
		}
		settings.upload_complete_handler = function(fileObj){
			progressPanel.children("#" + fileObj.id).remove();
			swfu.startUpload()
			if (swfu.getStats().files_queued == 0) {
				if(jQuery.isFunction(settings.upload_all_complete_handler)) settings.upload_all_complete_handler();
			}
		}
		swfu.startUpload();
	}
	
	this.setPostParams = function(objParam)
	{
		swfu.setPostParams(objParam);
	}

	this.addPostParam = function(name, value)
	{
		swfu.addPostParam(name, value);
	}

	return this;
}
