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
jQuery.fn.PGRSelectable = function(options) {
  
  if (!jQuery.fn.PGRSelectable.firstInastance) {
    jQuery.fn.PGRSelectable.firstInastance = true;
    $(document).mousemove(function(e){
      jQuery.fn.PGRSelectable.pageX = e.pageX;
      jQuery.fn.PGRSelectable.pageY = e.pageY;
    }); 
    
    jQuery.fn.PGRSelectable.unselectPanel = $("<div>");
    jQuery.fn.PGRSelectable.unselectPanel.css({
      "top" : "0px",
      "left" : "0px",
      "height" : "100%",
      "width" : "100%",
      "position" : "absolute",
      "z-index" : "1000",
      "display" : "none",
      "background" : "black",
      "zoom" : "1"
    });
    jQuery.fn.PGRSelectable.unselectPanel.fadeTo(0,0);
    $("body").append(jQuery.fn.PGRSelectable.unselectPanel);
    jQuery.fn.PGRSelectable.unselectPanel.hide();
  }
  
  var PGRSelectable = $(this);
  
  /*IE*/
  if ($.browser.msie) {
    PGRSelectable.css("zoom", "1");
  }
  /*IE*/
  
  var selecting = false;
  
  var settings = jQuery.extend({
     accept: "li",
     multiselect: true,
     selectingClass: "ui-selecting",
     selectedClass: "ui-selected",
     start: function() {},
     stop: function() {},
     onselect: function() {},
     ondblclick: function() {}
  }, options);
    
  var selRect = null;
  
  this.setMultiselectData = function() {
    if (settings.multiselect) {
      PGRSelectable.find(settings.accept).each(function() {
        var $this = $(this);
	      var pos = $this.position();
	      $.data(this, "selectable-item", {
          element: this,
			    $element: $this,
			    left: pos.left,
			    top: pos.top,
			    right: pos.left + $this.outerWidth(),
			    bottom: pos.top + $this.outerHeight()
		    });
		  });
	  } 
	}
	
  this.setMultiselectData();
  
  this.selectAll = function() {
	  settings.start();	  
	  $(this).find(settings.accept).addClass("ui-selected");
	  settings.stop();	  
  }

  this.unselectAll = function() {
	  settings.start();	  
	  $(this).find(settings.accept).removeClass("ui-selected");
	  settings.stop();	  
  }
  
  function selectingRectangle(mouseX, mouseY)
  {
    var self = this;
    
    jQuery.fn.PGRSelectable.unselectPanel.css("display", "block");
    
    var rect = $("<div class=\"ui-selectingRectangle\" style=\"position:absolute;border:dotted 1px gray;height:0px;width:0px;z-index:10\">");
    rect.css("top", mouseY + "px");
    rect.css("left", mouseX + "px");
    PGRSelectable.append(rect);
          
    var correctTop = 0;
    var correctLeft = 0;
  
    var parent = PGRSelectable.parent(); 
    var parentTop = parent.offset().top;
    var parentLeft = parent.offset().left;
    var parentHeight = parentTop + parent.height();
    
    function setCorrectTopLeft()
    {
      correctTop = parentTop - PGRSelectable.parent().scrollTop() + 3;
      correctLeft = parentLeft - PGRSelectable.parent().scrollLeft() + 3;
    }
    
    setCorrectTopLeft();
    
    jQuery.fn.PGRSelectable.pageX = mouseX;
    jQuery.fn.PGRSelectable.pageY = mouseY;

    mouseX -= correctLeft;
    mouseY -= correctTop;
            
    self.top = mouseY;
    self.left = mouseX;
    self.width = 0;
    self.height = 0;

    var maxY = PGRSelectable.outerHeight();
    var maxX = PGRSelectable.outerWidth();
                
    function setRect(x,y)
    {
      if(jQuery.fn.PGRSelectable.pageY - parentHeight > 0) parent.scrollTop(parent.scrollTop() + jQuery.fn.PGRSelectable.pageY - parentHeight);
      else if(jQuery.fn.PGRSelectable.pageY - parentTop < 0) parent.scrollTop(parent.scrollTop() + jQuery.fn.PGRSelectable.pageY - parentTop);
      
      if (y > maxY) y = maxY;
      else if(y < 0) y = 0;
      if (x > maxX) x = maxX;
      else if(x < 0) x = 0;
      
      if ((x >= mouseX) && (y >= mouseY)) {
        self.left = mouseX;
        self.top = mouseY;
        self.width = x - mouseX;
        self.height = y - mouseY;  
      } else if ((x > mouseX) && (y < mouseY)) {
        self.left = mouseX;
        self.top = y;
        self.width = x - mouseX;
        self.height = mouseY - y;                  
      } else if ((x < mouseX) && (y < mouseY)) {
        self.left = x;
        self.top = y;
        self.width = mouseX - x;
        self.height = mouseY - y;                  
      } else if ((x < mouseX) && (y > mouseY)) {
        self.left = x;
        self.top = mouseY;
        self.width = mouseX - x;
        self.height = y - mouseY;                  
      }
    }
    
    var intervalId = setInterval(function() {
      setCorrectTopLeft();
      setRect(jQuery.fn.PGRSelectable.pageX - correctLeft, jQuery.fn.PGRSelectable.pageY - correctTop);
      
      rect.css({
        "top"   : self.top + "px",
        "left"  : self.left + "px",
        "width" : self.width + "px",
        "height": self.height + "px"
      });
    }, 10);
          
    self.destroy = function() {
      jQuery.fn.PGRSelectable.unselectPanel.css("display", "none");
      clearInterval(intervalId);
      $(".ui-selectingRectangle").remove();  
    }
  }
  
  PGRSelectable.mousedown(function(e){
    var obj = $(e.target);
    if (obj.hasClass(settings.selectedClass)) return;
    settings.start();
    if (obj.is(settings.accept)) {
      if (!e.ctrlKey || !settings.multiselect) PGRSelectable.find("." + settings.selectedClass).removeClass(settings.selectedClass);
      obj.addClass(settings.selectingClass);
    }
    if (settings.multiselect) selRect = new selectingRectangle(e.pageX, e.pageY);
    selecting = true;    
  });

  $(document).mouseup(function(e){
    if (selecting) {
      if (selRect) {
        if ((selRect.width > 0) || (selRect.height > 0)) {
          var x1 = selRect.left;
          var y1 = selRect.top;
          var x2 = selRect.left + selRect.width; 
          var y2 = selRect.top + selRect.height;
          var selectee = null;        
          PGRSelectable.find(settings.accept).each(function(){
            var selectee = $.data(this, "selectable-item"); 
            if ((selectee.bottom > y1) &&
                (selectee.top < y2) &&
                (selectee.right > x1) && 
                (selectee.left < x2)) {
              selectee.$element.addClass(settings.selectedClass);
            }
          });          
        }
        selRect.destroy();
        delete selRect;
      }
      var obj = PGRSelectable.find(settings.accept + "." + settings.selectingClass);
      obj.removeClass(settings.selectingClass);
      obj.addClass(settings.selectedClass);
      selecting = false;
      settings.stop();   
      if (obj.is(settings.accept)) {
    	  settings.onselect();
      }
    }
  });
  
  PGRSelectable.dblclick(function(e){
	  if ($(e.target).is(settings.accept)) {
		  settings.ondblclick(e, $(e.target));
	  } 
  })
  
  return this;
}