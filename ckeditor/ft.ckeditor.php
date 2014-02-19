<?php
class Fieldtype_ckeditor extends Fieldtype {

  var $meta = array(
    'name'       => 'CKEditor',
    'version'    => '1.3',
    'author'     => 'Katrin Kerber',
    'author_url' => 'http://katrinkerber.com'
  );

  function render() {
    $html = "<div class='ckeditor-container'><textarea id='ckeditor-{$this->fieldname}' class='ckeditor' name='{$this->fieldname}' tabindex='{$this->tabindex}'>{$this->field_data}</textarea></div>";
    
    return $html;
  }

  public function process() {
    return trim($this->field_data);
  }
}
