# CKEditor + PGRFileManager add-on for Statamic
This is a [CKEditor](http://ckeditor.com) fieldtype add-on for [Statamic](http://statamic.com/), which includes the [PGRFileManager](http://pgrfilemanager.sourceforge.net/) plugin.

It's been tested with Statamic v1.4.2. 

## Screenshots
![Customised CKEditor field](http://katrinkerber.com/assets/screenshot-ckeditor.png)

![PGRFileManager](http://katrinkerber.com/assets/screenshot-PGRFileManager.png)

## Important notes
1. This fieldtype clashes with the Redactor fieldtype. Once you have installed it and use a Redactor field, the field will show the CKEditor toolbar on field focus.I made this add-on to replace Redactor, so this is not an issue for me. If you want to keep using Redactor in your publish forms, feel free to put on your de-bugging pants and dive into the code to fix this. Do please let me know of the solution when you come across it :)

2. There is known issues when the editor is part of a grid field: When deleting a row and then adding one the ckeditor create function causes an error. Overall the handling of this field in a grid is buggy and I have an idea how to fix this; [it's on my list](#to-dos). If you fancy jumping in and fixing this, I would love your help.

## Installation
Upload the *ckeditor* folder to Statamic's *_add-ons* folder.

## Configuration
### CKEditor
By default the editor comes with the whole shebang of toolbar buttons. If you dislike red and yellow highlighted text as much as me and don't want your client going editing crazy, you'll probably want to change this.

Open *_add-ons/ckeditor/config.js*. Lines 6 - 31 contain examples of how you could customise the styles dropdown and toolbar. Uncomment them, change them, delete them - the editor configuration world is your oyster!

### PGRFileManager
To ensure that PGRFileManager references the right folder and shows thumbnails, follow these steps:

1. Open *_add-ons/ckeditor/js/plugins/pgrfilemanager/myconfig.php* and change **line 32** and **line 37** to the paths to your upload folder. You can also configure accepted file types, maximum sizes and user authentication here.
2. Set the *_add-ons/ckeditor/js/plugins/pgrfilemanager/PGRThumb/cache* to **writeable on the server** - this is used for the thumbnail creation.

### Declaring a CKEditor fieldtype
Declare **type: ckeditor** in your fieldset settings.

## To do's
* The way a CKEditor field for a new grid row is initiated is shabby and buggy. Improve and fix.
* Enable field settings for a basic/complex toolbar and height
* Spring-clean and streamline PGRFileManager files
* I've already replaced the horrible default theme and icons for PGRFileManager, but it could still be made prettier
* I am not too happy about the flash uploader in PGRFileManager, would like to set up something else (based on HTML5 goodness?)
* The PGRFileManager would also be good as an alternative to the current Statamic file field which currently doesn't have a browsing functionality
