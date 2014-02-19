# Statamic CKEditor add-on
This is a [CKEditor](http://ckeditor.com) fieldtype add-on for [Statamic](http://statamic.com/).

It has been tested with Statamic v1.7.1. 

## Screenshots
![Customised CKEditor field](http://katrinkerber.com/assets/screenshot-ckeditor.png)

## Important
This fieldtype clashes with the Redactor fieldtype. Once you have installed it and use a Redactor field, the field will show the CKEditor toolbar on field focus.

I made this add-on to replace Redactor, so this is not an issue for me. If you want to keep using Redactor in your publish forms, feel free to put on your de-bugging pants and dive into the code to fix this. Do please let me know of the solution when you come across it :)

## Installation
Upload the `ckeditor` folder to your Statamic's `_add-ons` folder.

## Configuration
### CKEditor
By default the editor comes with the whole shebang of toolbar buttons. If you dislike red and yellow highlighted text as much as me and don't want your client going editing crazy, you'll probably want to change this.

Open `_add-ons/ckeditor/config.js` and refer to **lines 12 - 20** and **lines 24 - 43**. They contain examples of how you can customise the styles dropdown and the available buttons in the toolbar. Uncomment them, change them, delete them - the editor configuration world is your oyster! 

Refer to the [CKEditor documentation](http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar) for more help and options.

### Browsing and uploading files or images
If you want to allow users to add link to files or add images to the text, you can use the [Filemanager add-on](https://github.com/katrinkerber/statamic-filemanager).

Once you have that installed and configured, open `_add-ons/ckeditor/config.js` and uncomment the code on **lines 47 and 48**.

### Running in a subdirectory
If you are running Statamic in a subdirectory, follow these steps:

1. Open `_add-ons/ckeditor/config.js`.
2. Add your subdirectory to the CSS path on **line 7**.
3. If you are using Filemanager, add your subdirectory to the file browsers paths on **lines 47 and 48**.

### Declaring a CKEditor fieldtype
Declare **type: ckeditor** in your fieldset settings.

## To do's
* Enable field settings for a basic/complex toolbars and width/height declarations
