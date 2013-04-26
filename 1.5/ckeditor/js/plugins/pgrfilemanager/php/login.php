<?php
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>PGRFileManager v2.1.0</title>
  <link rel="stylesheet" type="text/css" href="css/sunny2/jquery-ui-1.8.1.custom.css" />
  <link rel="stylesheet" type="text/css" href="css/PGRFileManager.css" />
  <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
  <script type="text/javascript" src="js/jquery.i18n.js"></script>
  <script type="text/javascript" src="lang/lang.js"></script>
  <script type="text/javascript">
      function _(str)
      {
          return $.i18n._(str);
      }
	  $.i18n.setDictionary(PGRFileManagerDict["<?php echo $PGRLang?>"]);
	  $(function(){
	      $(".translate").each(function(){
		      $(this).text(_($(this).text()));
		  });
	  });    
  </script>    
  </head>
  <body>   
    <form id="loginPanel" method="post">
      <div class="ui-dialog ui-widget ui-widget-content ui-corner-all">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
          <span id="ui-dialog-title-dialog" class="ui-dialog-title">PGRFileManager</span>
        </div>
        <div class="ui-dialog-content ui-widget-content">
          <?php if (isset($_POST) && isset($_POST['user']) && !isset($_SESSION['PGRFileManagerAuthorized'])):?>
          <div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all"> 
		    <p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span> 
			<span class="translate">Invalid user or password</span></p>
		  </div>        
		  <?php endif;?>
          <span class="translate">User</span>:<br /><input type="text" name="user" /><br />
          <span class="translate">Password</span>:<br /><input type="password" name="pass" /><br />
        </div>
		<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		  <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">
		  	<span class="ui-button-text"><span class="translate">Sign In</span></span>
		  </button>
		</div>  
      </div>
    </form>
  </body>
</html>
