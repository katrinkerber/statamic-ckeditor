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
function prepareFilesContent(data, fileListType)
{ 
	if (fileListType == "icons") {
		var res = $('<ul id="files">');
		var element;
		var li;
		
		for ( var i in data ) {
			li = $('<li class="floatLeft">');
			element = '<div class="fileIcon selectee" filename="' + data[i].filename + '" md5="' + data[i].md5 + '"';
			if (data[i].thumb != false) element += ' style="background-image:url(\'' + data[i].thumb + '\')"';
			if (data[i].imageInfo != false) element += ' fileType="image"';
			else if (data[i].ckEdit != false) element += ' fileType="ckEdit"';
			element += '></div>';
			element = $(element);
			element.data("fileData", data[i]);
			li.append(element);
			li.append('<div title="' + data[i].filename + '">' + data[i].shortname + '</div>');		
			res.append(li);
		}  

		res.append('<li class="clearBoth">');

		return res;
	} else if (fileListType == "list") {
        var res = '<div id="fileListTableHeader" class="ui-widget-header">';
        res += '<table>';
        res += '<tr>';
        res += '<td id="NameTableColumnHeader">' + $.i18n._("filename") + '</td>';
        res += '<td id="SizeTableColumnHeader">' + $.i18n._("size") + '</td>';
        res += '<td id="DateTableColumnHeader">' + $.i18n._("date") + '</td>';
        res += '</tr>';
        res += '</table>';
        res += '</div>';            
        res += '<div id="fileListTable">';
        res += '<table id="files">';
		for ( var i in data ) {	
			if (data[i].thumb != false) element += 'style="background-image:url(\'' + data[i].thumb + '\')" fileType="image"';
			
	        res += '<tr><td' + (i==0?' id="NameTableColumn"':'') + ' class="selectee" filename="' + data[i].filename + '"' + (data[i].thumb != false?' fileType="image"':'') + '>' + data[i].filename + '</td><td' + (i==0?' id="SizeTableColumn"':'') + '>' + data[i].size + '</td><td>' + data[i].date + '</td></tr>';
		}
        res += '</table>';
        res += '</div>';
        
        return res;		
	}
}

function prepareFoldersContent(data)
{  
	var res = '<ul id="folders" class="filetree">';
	res += '<li class="collapsable open fetched"><span class="folder"><span id="rootDir" class="selectee" directory="">' + _("root") + '</span></span>';
	
	res += prepareSubfoldersContent(data);
		
	res += '</li></ul>';

	return res;
}

function prepareSubfoldersContent(data)
{
	var res = '<ul>';

	var depth = 0;
	
	for ( var i in data )
	{
		if (depth < data[i].depth) {
			res += '<ul>';
		} else if (depth == data[i].depth && i>0) {
			res += '</li>'
		} else if (depth > data[i].depth) {
			for(x=0; x<depth-data[i].depth; x++) res += '</ul></li>';
		}
		res += '<li class="closed"><span class="folder"><span class="selectee" directory="' + data[i].relativePath + '" dirname="' + data[i].dirname + '" title="' + data[i].dirname + '">' + data[i].shortname + '</span></span>';
		depth = data[i].depth;
	}  

	res += '</ul></li>';
	
	return res;
}

function loadingPanel(content)
{
    //przygtowuje loadingPanel
    var loadingPanel = $("<div>");
    loadingPanel.css({
        "position":"absolute",
        "top":"0px",
        "left":"0px",
        "background":"transparent url('img/ajax-loader.gif') center no-repeat",
        "z-index":"10000"
    }).hide();
    
    content.after(loadingPanel);
    
    this.setLoadingPanel = function()
    {
        loadingPanel.css("top", content.position().top);
        loadingPanel.css("left", content.position().left);
        loadingPanel.width(content.width());
        loadingPanel.height(content.height());     	
    };
    
    this.bindLoadingPanel = function()
    {
        loadingPanel.one("ajaxSend", function(){
        	loadingPanel.show();
        });
        loadingPanel.one("ajaxStop", function(){
        	loadingPanel.hide();
        });    	
    };    
}

function fileInfoPanel()
{
	var mousein = false;
	
	var timeoutId = null;
	
	var panel = $('<div>');
	panel.css({
		"background": "white",
		"border"	: "solid 1px black",
		"display"	: "none",
		"padding"	: "5px",
		"position"	: "absolute",
		"z-index"	: "1000"
	});
	panel.addClass("ui-corner-all");
	$("body").append(panel);
	
	
	function prepareFileData(data)
	{
		panel.empty();
		panel.append(_("filename") + ": " + data.filename + "<br />");
		panel.append(_("file size") + ": " + data.size + "<br />");
		panel.append(_("last modification date") + ": " + data.date + "<br />");
		if(data.imageInfo != false) {
			panel.append(_("image size") + ": " + data.imageInfo.width + "px X " + data.imageInfo.height + "px<br />");			
		}
	}
	
	function showFileInfoPanel(e, elem)
	{
		var pageHeight = $(document.body).height();
		var pageWidth = $(document.body).width();
		var x = 0;
		var y = 0;
		if (pageWidth/2 < e.pageX) y = panel.outerWidth() + elem.outerWidth();
		if (pageHeight/2 < e.pageY) x = panel.outerHeight() - elem.outerHeight();
		panel.css("top", elem.offset().top - x + "px");
		panel.css("left",  elem.offset().left + elem.outerWidth() - y + "px");
		panel.fadeIn("slow");
	}
	
	$("#fileList").mouseover(function(e){
		if ($(e.target).is(".selectee") && !mousein) {
			mousein = true;
			var fileData = $(e.target).data("fileData");
			if (!fileData) return;
			prepareFileData(fileData);
			timeoutId = setTimeout(function(){
				showFileInfoPanel(e, $(e.target));				
			}, 500);
		}
	});

	$("#fileList").mouseout(function(e){
		if ($(e.target).is(".selectee") && mousein) {
			mousein = false;
			clearTimeout(timeoutId);
			panel.hide();
		}
	});
	
}
