<?php
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
function curPageURL() {
        $pageURL = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))?'https':'http'; 
        $pageURL .= '://' . $_SERVER['SERVER_NAME'];
        if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != '80')) {
            $pageURL .= ':' . $_SERVER['SERVER_PORT'];
        } 
        $pageURL .= $_SERVER['REQUEST_URI'];
        return $pageURL;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>PGRFileManager v2.1.0</title>
  </head>
  <body>
  	<h1>YOUR ROOTPATH IS:</h1>
  	<?php echo dirname(__FILE__)?>
  	<h1>YOUR URLPATH IS:</h1>
  	<?php echo dirname(curPageURL())?>
  </body>
</html>   
