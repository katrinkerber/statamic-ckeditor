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
jQuery.fn.PGRContextMenu = function(options) {
		
	var settings = jQuery.extend({
		accept: $(this),
		idMenu: ""		
	}, options);
	
	var idMenu = settings.idMenu;
	
	$(this).bind("contextmenu", function(e){
		if (!$(e.target).is(settings.accept)) return false; 
		var menu = null;
		if (typeof idMenu == "function") menu = $("#" + idMenu());
		else if (typeof idMenu == "object") menu = idMenu.menu;
		else menu = $("#" + idMenu);
		PGRContextMenu.showMenu(menu, e);
		return false;
	});
};

function PGRContextMenu(idMenu, menuClass)
{
	var idMenu = idMenu;
  
	var menu = null;
	if (typeof(menuClass) == "object") {
		menu = menuClass.menu.clone(true);
		menu.attr("id", idMenu);
	} else {
		menu = $("<ul>");
		menu.attr("id", idMenu);
		menu.addClass(menuClass);
	}
	$("body").append(menu);
  
	this.menu = menu;
    
	function createItem(name, fn, itemClass)
	{
		var item = $("<li>");
		item.html("<a href=\"#\">" + name + "</a>");
		item.addClass(itemClass);
		item.mousedown(function(){fn();});
		
		return item;
	}
	
	this.addItem = function(name, fn, itemClass) {
		menu.append(createItem(name, fn, itemClass));
	};
	
	this.addItemAt = function(name, fn, itemClass, position) {
		$element = menu.children("li").eq(position);
		$element.after(createItem(name, fn, itemClass));
	};
  
	function createSeparator()
	{
		return "<li class=\"separator\"><a>&nbsp;</a></li>";
	}
	
	this.addSeparator = function() {
		menu.append(createSeparator());
	};

	this.addSeparatorAt = function(position) {
		$element = menu.children("li").eq(position);
		$element.after(createSeparator());
	};
	
	this.bind = function(selector) {
		$(selector).PGRContextMenu(this);
	};
};

PGRContextMenu.showMenu = function(menu, e)
{
	var pageHeight = $(document.body).height();
	var pageWidth = $(document.body).width();
	var x = 0;
	var y = 0;
	if (pageWidth/2 < e.pageX) x = menu.outerWidth();
	if (pageHeight/2 < e.pageY) y = menu.outerHeight();
	menu.css("top", e.pageY - y + "px");
	menu.css("left",  e.pageX - x + "px");
	menu.css("display", "inline-block");
	$(document).one("mousedown", function(){
		menu.css("display", "none");      
	});
}
