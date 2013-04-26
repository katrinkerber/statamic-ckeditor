<?php
class Hooks_ckeditor extends Hooks {

  public function control_panel__add_to_head()
  {
      if (URL::getCurrent(false) == '/publish') {
        return $this->css->link('contents.css');
      }
  }

  public function control_panel__add_to_foot()
  {
      if (URL::getCurrent(false) == '/publish') {
        $html = $this->js->link('ckeditor.js');
        
        $html .= "<script>
          function initiateCKEditors() {
            $('.ckeditor').each(function(){
              CKEDITOR.replace( $(this).attr('name') );
            });
          }

          // initiate CKEditor for field(s) in a newly added grid row
          function newRowEditor() {
            setTimeout(function() {
              var newRowCell = $('table.grid tbody tr:last td.cell-ckeditor');
              // double-check that new row doesn't contain editor already
              if ( newRowCell.not(':has(div.cke)') ) {
                newRowCell.find('textarea.ckeditor').each(function(){
                  CKEDITOR.replace( $(this).attr('name') );
                });
              }
            }, 50);
          }

          $(document).ready(function() {

            initiateCKEditors();

            $('.grid-add-row').click(function() {
              newRowEditor();
            });
          });
        </script>";
        
        return $html;
      }
  }
}
