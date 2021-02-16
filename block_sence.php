<?php

/**
 * File containing Sence Module Block class.
 *
 * @package    block_sence
 * @copyright  2020 onwards Felipe Uzcátegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require('engine_dev.php');

class block_sence extends block_base {

    public $alumnos, $codigo_sence;

    public function init() {
        $this->title = get_string('pluginname', 'block_sence');
    }
    
    function has_config() {
        return true;
    }
    
    function instance_allow_config() {
        return true;
    }
    
    public function instance_allow_multiple() {
        return false;
    }
    
    public function get_content() {
        global $USER, $PAGE;
        
        $sence = new Engine();

        $this->content =  new stdClass;
        $this->content->text = $sence->content();
        return $this->content;
    }

    public function applicable_formats() {
        return array(
                'course-view' => true,
                'all' => false,
        );
    }
}