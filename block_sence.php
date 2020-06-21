<?php
class block_sence extends block_base {
    public function init() {
        // $this->title = get_string('sence', 'block_sence');
        $this->title = 'Modulo Sence';
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.

    function has_config() {
        return true;
    }

    function instance_allow_config() {
        return true;
    }

    public function get_content() {
        global $USER;
        global $CFG;

        if ($this->content !== null) {
          return $this->content;
        }

        $this->content         =  new stdClass;
        $this->content->text   = 'Hola ' . $USER->firstname . ' bienvenido!!';
        $this->content->footer = $CFG->block_sence_token;

        return $this->content;
    }

    

}