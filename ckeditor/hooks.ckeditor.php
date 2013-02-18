<?php
class Hooks_ckeditor extends Hooks {

  function add_to_control_panel_head() {
    if (self::current_route() == 'publish') {
      return self::include_css('contents.css');
    }
  }

  function add_to_control_panel_foot() {
    if (self::current_route() == 'publish') {
      $html = self::include_js('ckeditor.js');

      $html .= self::inline_js("
        function createCKEditors(){
          $('.ckeditor').each(function(){
              CKEDITOR.replace( $(this).attr('id') );
          });
        }

        $(document).ready(function() {
          createCKEditors();
          $('.grid-add-row').click(function() {
              for(name in CKEDITOR.instances) {
                  CKEDITOR.instances[name].destroy()
              }
              setTimeout(function() {
                  createCKEditors();
              }, 100);
          });
        });
      ");
      
      return $html;
    }

  }

}
