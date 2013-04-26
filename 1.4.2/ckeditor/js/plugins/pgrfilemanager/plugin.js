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

window.CKEDITOR = CKEDITOR;

CKEDITOR.plugins.add( 'pgrfilemanager' );

CKEDITOR.config.filebrowserBrowseUrl = CKEDITOR.basePath+'plugins/pgrfilemanager/PGRFileManager.php',
CKEDITOR.config.filebrowserImageBrowseUrl = CKEDITOR.basePath+'plugins/pgrfilemanager/PGRFileManager.php?type=Image',
CKEDITOR.config.filebrowserFlashBrowseUrl = CKEDITOR.basePath+'plugins/pgrfilemanager/PGRFileManager.php?type=Flash',
CKEDITOR.config.filebrowserUploadUrl = CKEDITOR.basePath+'plugins/pgrfilemanager/PGRFileManager.php?type=Files',
CKEDITOR.config.filebrowserImageUploadUrl = CKEDITOR.basePath+'plugins/pgrfilemanager/PGRFileManager.php?type=Image',
CKEDITOR.config.filebrowserFlashUploadUrl = CKEDITOR.basePath+'plugins/pgrfilemanager/PGRFileManager.php?type=Flash'
