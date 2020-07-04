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
            $this->content->text  = 'Los Custom Fields requeridos para este Pugin no están configurados';
            return $this->content;
        }

        if( !$sence->es_curso_sence() ){
            $this->content->text  = 'Este curso no tiene código SENCE';
            return $this->content;
        }

        if( !$sence->es_alumno() ){
            $this->content->text  = 'Bienvenido '. $USER->firstname;
            return $this->content;
        }

        if( !$sence->tiene_run() ){
            $this->content->text  = 'RUT no configurado o incorrecto';
            $this->content->footer ='<style>#region-main{filter:blur(5px);pointer-events:none;}</style>';
            return $this->content;
        }

        if( !$sence->es_alumno_sence() ){
            $this->content->text  = 'Bienvenido '. $USER->firstname;
            return $this->content;
        }
        if( $sence->tiene_asistencia() ){
            $this->content = 'Bienvenido ' . $USER->firstname . '<br>¡Ya registraste tu asistencia!';
            return $this->content;
        }

        if( isset( $_POST['RunAlumno'] ) ){
            $this->content->text  = $sence->procesa_respuesta( $_POST, $this->page->url );
            return $this->content;
        }

        $this->content->text = $sence->prepare_form( $this->page->url );
        $this->content->footer ='<style>#region-main{filter:blur(5px);pointer-events:none;}</style>';
        return $this->content;
    }

    public function applicable_formats() {
        return array(
                'course-view' => true, 
                'all' => false,
        );
      }
}