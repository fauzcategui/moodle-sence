<?php
class block_sence extends block_base {
    public function init() {
        $this->title = get_string('sence', 'block_sence');
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.

    public function get_content() {
        global $USER;
        if ($this->content !== null) {
          return $this->content;
        }

        $this->content         =  new stdClass;
        $this->content->text   = 'Hola ' . $USER->firstname . ' bienvenido!!';
        $this->content->footer = 'Footer here...';

        return $this->content;
    }

}