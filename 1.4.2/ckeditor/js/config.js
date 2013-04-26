/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

// If your Statamic installation runs in a subdirectory, comment out the line below and specify it
// CKEDITOR.config.contentsCss="/your-subdirectory/_add-ons/ckeditor/css/contents.css";

// Example of a custom 'Styles' dropdown - for info and options see http://docs.ckeditor.com/#!/guide/dev_styles
    // CKEDITOR.stylesSet.add( 'custom_styles', [
    //     // Block-level styles
    //     { name: 'Paragraph', element: 'p' },
    //     { name: 'Heading 2', element: 'h2' },
    //     { name: 'Heading 3', element: 'h3' },
    //     { name: 'Heading 4', element: 'h4' },
    //     { name: 'CSS code', element: 'pre', attributes: { 'class': 'language-css' } },
    //     { name: 'HTML code', element: 'pre', attributes: { 'class': 'language-markup' } }
    // ]);

// Define changes to default configuration here
CKEDITOR.editorConfig = function( config ) {
  // toolbar customisations
    // use custom styles dropdown
        //config.stylesSet = 'custom_styles';
    // force plain text on paste
        //config.forcePasteAsPlainText = true;
    // example of a custom toolbar - to see all available toolbar buttons see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar
        // config.toolbar = [
        //     { name: 'styles', items : [ 'Styles' ] },
        //     { name: 'removeformat', items : [ 'RemoveFormat' ] },
        //     { name: 'basics', items : [ 'Bold','Italic','Strike','Subscript','Superscript' ] },
        //     { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Blockquote'] },
        //     { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
        //     { name: 'insert', items : [ 'Image' ] },
        //     { name: 'tools', items : [ 'Source', '-', 'Maximize', '-', 'ShowBlocks' ] }
        // ];


  // ONLY THE BELOW IF THE CKEDTOR FOLDER DOES NOT SIT IN YOUR _ADD-ONS FOLDER
  // pgrfilemanager plugin
    // initate plugin
    config.extraPlugins = 'pgrfilemanager';
    // browse and upload paths - if you are running yoru Statamic installation in a subdirectory, make sure to include it in the path
    config.filebrowserBrowseUrl = '/_add-ons/ckeditor/js/plugins/pgrfilemanager/PGRFileManager.php?langCode=en&type=Link';
    config.filebrowserImageBrowseUrl = '/_add-ons/ckeditor/js/plugins/pgrfilemanager/PGRFileManager.php?langCode=en&type=Image';
    // the normal CKEditor upload tab and behaviour doesn't work with PGRFileManger
    // haven't figured out how to fix this, so for now we hide the 'Upload' tab by using the 'null' value
    // to upload new files, use the 'Browse Server' button and use PGRFileManager upload new files
    config.filebrowserUploadUrl = null;
    config.filebrowserImageUploadUrl = null;
};
