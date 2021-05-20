<?php

/**
 * File containing Sence Module Block class.
 *
 * @package    block_sence
 * @copyright  2020 onwards Felipe Uzcátegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('engine.php');

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
        if ($this->content !== null) {
            return $this->content;
        }

        $sence = new Engine();
        $this->content =  new stdClass;
        $this->content->text = $sence->content();
        $this->content->footer = $sence->get_footer();

        return $this->content;
    }

    public function instance_delete(){
        global $DB, $COURSE;
        $DB->delete_records('block_sence', ['courseid' => $COURSE->id ] );
    }

    public function applicable_formats() {
        return array(
                'course-view' => true,
                'all' => false,
        );
    }
}