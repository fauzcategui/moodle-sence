<?php

/**
 * File containing Sence Module Block class.
 *
 * @package    block_sence
 * @copyright  2020 onwards Felipe Uzcátegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require('engine.php');

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
        global $USER;


        $sence = new Engine();
        $this->content =  new stdClass;

        if( !$sence->existen_campos_sence() ){
            $this->content->text  = $sence->formatea_html_error( get_string('error_campos', 'block_sence') );
            $this->content->footer = $sence->print_logo();
            return $this->content;
        }
        
        if( !$sence->es_alumno() ){
            $this->content->text  = $sence->formatea_html_correcto( get_string('bienvenido', 'block_sence'). ' ' . $USER->firstname );
            $this->content->footer = $sence->print_logo();
            return $this->content;
        }

        if( !$sence->tiene_run() ){
            $this->content->text  = $sence->formatea_html_error( get_string('error_run', 'block_sence') );
            $this->content->footer = $sence->print_logo() . $sence->style_blocker();
            return $this->content;
        }

        if( !$sence->es_alumno_sence() ){
            $this->content->text  = $sence->formatea_html_correcto( get_string('bienvenido', 'block_sence'). ' ' . $USER->firstname );
            $this->content->footer = $sence->exige_asistencia() ? $sence->print_logo() . $sence->style_blocker() : $sence->print_logo();
            return $this->content;
        }
        if( $sence->tiene_asistencia() ){
            $this->content->text = $sence->formatea_html_correcto( get_string('bienvenido', 'block_sence'). ' '  . $USER->firstname . '<br>¡Ya registraste tu asistencia!' );
            $this->content->footer = $sence->print_logo();
            return $this->content;
        }

        if( isset( $_POST['RunAlumno'] ) ){
            $this->content->text  = $sence->procesa_respuesta( $_POST, $this->page->url );
            $this->content->footer = $sence->print_logo();
            return $this->content;
        }

        $this->content->text = $sence->prepare_form( $this->page->url );
        $this->content->footer = $sence->print_logo() . $sence->style_blocker();
        return $this->content;
    }

    public function applicable_formats() {
        return array(
                'course-view' => true, 
                'all' => false,
        );
      }
}