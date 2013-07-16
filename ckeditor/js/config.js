/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

// Correct path to CSS
CKEDITOR.config.contentsCss="/_add-ons/ckeditor/css/contents.css";
    // if you are running in a subdirectory, add this too like so:
    // CKEDITOR.config.contentsCss="/your_subdirectory/_add-ons/ckeditor/css/contents.css";

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
    // use custom styles dropdown - see above from line 10
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

    // Filemanager config for file and Image browsing and uploading
    // If you are running in a subdirectory, ensure to add this here too
    config.filebrowserBrowseUrl = '/_add-ons/ckeditor/js/plugins/filemanager/index.html';
    config.filebrowserImageBrowseUrl = '/_add-ons/ckeditor/js/plugins/filemanager/index.html';
};
