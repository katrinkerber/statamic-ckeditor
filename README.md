# CKEditor + Filemanager add-on for Statamic
This is a [CKEditor](http://ckeditor.com) fieldtype add-on for [Statamic](http://statamic.com/), which includes [Filemanager](https://github.com/simogeo/Filemanager) for browsing and uploading files.

It has been tested with Statamic v1.5.3. 

## Screenshots
![Customised CKEditor field](http://katrinkerber.com/assets/screenshot-ckeditor.png)

![PGRFileManager](http://katrinkerber.com/assets/screenshot-filemanager.png)

## Important
This fieldtype clashes with the Redactor fieldtype. Once you have installed it and use a Redactor field, the field will show the CKEditor toolbar on field focus.

I made this add-on to replace Redactor, so this is not an issue for me. If you want to keep using Redactor in your publish forms, feel free to put on your de-bugging pants and dive into the code to fix this. Do please let me know of the solution when you come across it :)

## Installation
Upload the `ckeditor` folder to your Statamic's `_add-ons` folder.

## Configuration
### CKEditor
By default the editor comes with the whole shebang of toolbar buttons. If you dislike red and yellow highlighted text as much as me and don't want your client going editing crazy, you'll probably want to change this.

Open `_add-ons/ckeditor/js/config.js` and refer to **lines 12 - 20** and **lines 24 - 38**. They contain examples of how you can customise the styles dropdown and the available buttons in the toolbar. Uncomment them, change them, delete them - the editor configuration world is your oyster! 

Refer to the [CKEditor documentation](http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar) for more help and options.

### Filemanager
By default Filemanager will use the `/assets/` folder to browse and upload images/files to.

If you need to change this, go to `_add-ons/ckeditor/js/plugins/filemanager/scripts/filemanager.config.js` and edit the `fileRoot` value on **line 18**.

#### Editing the .htaccess file
If you are using Statamic’s provided code in your `.htaccess` file, which includes this line of code

    `Rewriterule ^(.*)?\.html$ - [F,L]`

then the Filemanager won't work, as this blocks access to any html files.

I use the following block of code to protect system and template files instead:

    RewriteRule ^(_app) - [F,L]
    RewriteRule ^(_config) - [F,L]
    RewriteRule ^(_content) - [F,L]
    RewriteRule ^(_logs) - [F,L]
    RewriteRule (layouts) - [F,L]
    RewriteRule (templates) - [F,L]
    RewriteRule (partials) - [F,L]
    RewriteRule ^(.*)?\.yml$ - [F,L]
    Rewriterule ^(.*)?\.yaml$ - [F,L]
    RewriteRule ^(.*/)?\.git+ - [F,L]

I cannot guarantee that this is the best way, so do use it at your own risk. If you know of a better solution which allows access to the filemanager `index.html` file, then please do let me know.

### Running in a subdirectory
If you are running Statamic in a subdirectory, follow these steps:

1. Open `_add-ons/ckeditor/js/config.js`, comment out **line 7** and set the CSS path including your subdirectory.
2. Go to `_add-ons/ckeditor/js/plugins/filemanager/scripts/filemanager.config.js` and edit the `fileRoot` value on **line 18**: `"fileRoot": "/yoursubdirectory/assets/"`.
3. Edit the file browser paths declared on **lines 40 + 41** in the `_add-ons/ckeditor/js/config.js` file:

    `config.filebrowserBrowseUrl = 'yoursubdirectory/_add-ons/ckeditor/js/plugins/filemanager/index.html';`
    `config.filebrowserImageBrowseUrl = 'yoursubdirector/_add-ons/ckeditor/js/plugins/filemanager/index.html';`

### Declaring a CKEditor fieldtype
Declare **type: ckeditor** in your fieldset settings.

## To do's
* Enable field settings for a basic/complex toolbars and width/height declarations
