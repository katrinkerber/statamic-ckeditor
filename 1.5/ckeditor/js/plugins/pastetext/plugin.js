  1 ﻿/*
  2 Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
  3 For licensing, see LICENSE.html or http://ckeditor.com/license
  4 */
  5 
  6 /**
  7  * @file Paste as plain text plugin
  8  */
  9 
 10 (function()
 11 {
 12   // The pastetext command definition.
 13   var pasteTextCmd =
 14   {
 15     exec : function( editor )
 16     {
 17       var clipboardText = CKEDITOR.tools.tryThese(
 18         function()
 19         {
 20           var clipboardText = window.clipboardData.getData( 'Text' );
 21           if ( !clipboardText )
 22             throw 0;
 23           return clipboardText;
 24         }
 25         // Any other approach that's working...
 26         );
 27 
 28       if ( !clipboardText )   // Clipboard access privilege is not granted.
 29       {
 30         editor.openDialog( 'pastetext' );
 31         return false;
 32       }
 33       else
 34         editor.fire( 'paste', { 'text' : clipboardText } );
 35 
 36       return true;
 37     }
 38   };
 39 
 40   // Register the plugin.
 41   CKEDITOR.plugins.add( 'pastetext',
 42   {
 43     init : function( editor )
 44     {
 45       var commandName = 'pastetext',
 46         command = editor.addCommand( commandName, pasteTextCmd );
 47 
 48       editor.ui.addButton( 'PasteText',
 49         {
 50           label : editor.lang.pasteText.button,
 51           command : commandName
 52         });
 53 
 54       CKEDITOR.dialog.add( commandName, CKEDITOR.getUrl( this.path + 'dialogs/pastetext.js' ) );
 55 
 56       if ( editor.config.forcePasteAsPlainText )
 57       {
 58         // Intercept the default pasting process.
 59         editor.on( 'beforeCommandExec', function ( evt )
 60         {
 61           var mode = evt.data.commandData;
 62           // Do NOT overwrite if HTML format is explicitly requested.
 63           if ( evt.data.name == 'paste' && mode != 'html' )
 64           {
 65             editor.execCommand( 'pastetext' );
 66             evt.cancel();
 67           }
 68         }, null, null, 0 );
 69 
 70         editor.on( 'beforePaste', function( evt )
 71         {
 72           evt.data.mode = 'text';
 73         });
 74       }
 75 
 76       editor.on( 'pasteState', function( evt )
 77         {
 78           editor.getCommand( 'pastetext' ).setState( evt.data );
 79         });
 80     },
 81 
 82     requires : [ 'clipboard' ]
 83   });
 84 
 85 })();
 86 
 87 
 88 /**
 89  * Whether to force all pasting operations to insert on plain text into the
 90  * editor, loosing any formatting information possibly available in the source
 91  * text.
 92  * <strong>Note:</strong> paste from word is not affected by this configuration.
 93  * @name CKEDITOR.config.forcePasteAsPlainText
 94  * @type Boolean
 95  * @default false
 96  * @example
 97  * config.forcePasteAsPlainText = true;
 98  */
 99 
