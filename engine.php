<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * File containing engine sence class.
 *
 * @package    block_sence
 * @copyright  2020 onwards Felipe Uzcátegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class utilizada para procesar la asistencia de alumnos del bloque Sence
 *
 * @package    block_sence
 * @copyright  2020 onwards Felipe Uzcátegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Engine{

    private $alumnos = [];
    private $runAlumno;
    private $CodSence;
    private $lineadecap;
    private $urlInicioTest = 'https://sistemas.sence.cl/rcetest/Registro/IniciarSesion';
    private $urlInicio = 'https://sistemas.sence.cl/rce/Registro/IniciarSesion';
    private $urlRegistro = '#';
    private $urlCambiaCus = '#';
    private $urlActualiza = '#';

    public $lock_status = 1;

    public function procesa_respuesta( $req, $currenturl ){
        $CodSence = isset($req['CodSence']) ? $req['CodSence'] : 0;
        $CodigoCurso = isset($req['CodigoCurso']) ? $req['CodigoCurso'] : 0;
        $IdSesionAlumno = isset($req['IdSesionAlumno']) ? $req['IdSesionAlumno'] : 0;
        $IdSesionSence = isset($req['IdSesionSence']) ? $req['IdSesionSence'] : 0;
        $RunAlumno = isset($req['RunAlumno']) ? $req['RunAlumno'] : 0;
        $FechaHora = isset($req['FechaHora']) ? $req['FechaHora'] : 0;
        $ZonaHoraria = isset($req['ZonaHoraria']) ? $req['ZonaHoraria'] : 0;
        $LineaCapacitacion = isset($req['LineaCapacitacion']) ? $req['LineaCapacitacion'] : 0;
        $GlosaError = isset($req['GlosaError']) ? $req['GlosaError'] : 0;
        if( $GlosaError > 0 ){
            $this->lock_status = 1;
            return $this->describe_error( $GlosaError ) . $this->prepare_form( $currenturl );
        }
        $this->registra_asistencia_moodle();
        $this->lock_status = 0;
        return $this->formatea_html_correcto('Asistencia SENCE Registrada!');
    }

    public function registra_asistencia_moodle(){
        global $DB, $COURSE, $USER;

        $data = [
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
            'timecreated' => time(),
        ];

        if( ! $DB->get_record( 'block_sence', [ 'userid' => $USER->id, 'courseid' => $COURSE->id, ] ) ){
            return $DB->insert_record( 'block_sence' , $data);
        }

    }

    public function style_blocker(){
        return '<style>#region-main{filter:blur(5px);pointer-events:none;}</style>';
    }

    public function describe_error($error){
        $errores_sence = [
            '100' =>  'Contraseña incorrecta.',
            '200' =>  'Parámetros vacíos.',
            '201' =>  'Parámetro UrlError sin datos.',
            '202' =>  'Parámetro UrlError con formato incorrecto.',
            '203' =>  'Parámetro UrlRetoma con formato incorrecto.',
            '204' =>  'Parámetro CodSence con formato incorrecto.',
            '205' =>  'Parámetro CodigoCurso con formato incorrecto.',
            '206' =>  'Línea de capacitación con formato incorrecto.',
            '207' =>  'Parámetro RunAlumno incorrecto.',
            '208' =>  'Parámetro RunAlumno diferente al enviado por OTEC.',
            '209' =>  'Parámetro RutOtec incorrecto.',
            '210' =>  'Sesión caducada.',
            '211' =>  'Token incorrecto.',
            '212' =>  'Token caducado.',
            '300' =>  'Error interno.',
            '301' =>  'Error interno.',
            '302' =>  'Error interno.',
            '303' =>  'Error interno.',
            '304' =>  'Error interno.',
            '305' =>  'Error interno.',
        ];

        if( isset( $errores_sence[$error] )  ){
            return $this->formatea_html_error( $errores_sence[$error] ) . '<br>' . $this->style_blocker();
        }

        return $this->formatea_html_error('Error desconocido. <br> Contacte a la Otec');

    }

    public function es_alumno_sence(){
        global $DB, $COURSE, $USER;

        $coursecontext = context_course::instance($COURSE->id);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'sence', 'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('sence', $blockrecord);

        $this->alumnos = $this->parsear_codigo_alumnos( $blockinstance->config->alumnos );
        $this->lineadecap = $blockinstance->config->lineadecap;
        
        return array_key_exists( strtolower($this->runAlumno  ), $this->alumnos);
    }

    public function es_alumno(){
        global $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        return !has_capability('moodle/course:viewhiddensections', $coursecontext);
    }

    public function tiene_run(){
        global $USER;

        if( preg_match('/\d*-[0-9kK]/', $USER->username) ){
            $this->runAlumno = strtolower($USER->username);
            return true;
        }

        if( preg_match('/\d*-[0-9kK]/', $USER->idnumber) ){
            $this->runAlumno = strtolower($USER->idnumber);
            return true;
        }

        return false;

    }

    public function prepare_form( $currenturl ){
        global $USER, $CFG, $COURSE;
        $RunAlumno = $this->runAlumno;
        $CodigoCurso = $this->alumnos[ $RunAlumno ];
        $IdSesionAlumno = '2';
        $CodSence = $this->CodSence;

        return '<form style="text-align:center;" method="POST" action="'.$this->urlInicio.'">
                    <button type="submit" style="padding:10px;background:#0056a8;color:#fff;font-weight:700;border-radius:5px;border:0px;">
                        Iniciar Sesión
                    </button>
                    <div style="display:none;">
                        <input value="'.$CFG->block_sence_rut.'" type="text" name="RutOtec" class="form-control">
                        <input value="'.$CFG->block_sence_token.'" type="text" name="Token" class="form-control">
                        <input value="'.$this->lineadecap.'" type="text" name="LineaCapacitacion" class="form-control">
                        <input value="'.$RunAlumno.'" type="text" name="RunAlumno" class="form-control">
                        <input value="'.$IdSesionAlumno.'" type="text" name="IdSesionAlumno" class="form-control">
                        <input value="'.$currenturl.'" type="text" name="UrlRetoma" class="form-control">
                        <input value="'.$currenturl.'" type="text" name="UrlError" class="form-control">
                        <input value="'.$CodSence.'" type="text" name="CodSence" class="form-control">
                        <input value="'.$CodigoCurso.'" type="text" name="CodigoCurso" class="form-control">
                    </div>
                </form>';
    }

    public function existen_campos_sence(){
        global $DB, $COURSE;

        $coursecontext = context_course::instance($COURSE->id);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'sence', 'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('sence', $blockrecord);
        
        if( isset( $blockinstance->config ) ){
            $this->CodSence = $blockinstance->config->codigocurso;
            return strlen( $this->CodSence ) > 5;
        }

        return false;
    }

    public function tiene_asistencia(){
        global $DB, $USER, $COURSE;
        return $DB->record_exists( 'block_sence', [ 'courseid' => $COURSE->id, 'userid' => $USER->id ] );
    }

    public function parsear_codigo_alumnos($stralumnos){

        preg_match_all('/\d*-[0-9kK]\s\d*/', $stralumnos, $alumnos );
        if( ! count($alumnos[0]) < 1 ){
            $result = [];
            foreach($alumnos[0] as $alumno){
                $exploded = explode(' ', $alumno);
                $result[ $exploded[0] ] = $exploded[1];
            }
            return $result;
        }
        return [];

    }

    public function exige_asistencia(){
        global $DB, $COURSE;

        $coursecontext = context_course::instance($COURSE->id);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'sence', 'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('sence', $blockrecord);
        
        if( isset( $blockinstance->config ) ){
            return $blockinstance->config->bloqueacurso;
        }

        return false;

    }

    public function formatea_html_error($string){
        return '<div style="padding:10px; background-color:#ee928f; color:fff; border-radius:5px;">'. $string .'</div>';
    }

    public function formatea_html_correcto($string){
        return '<div style="padding:10px; background-color:#ebf2b8; border-radius:5px;">'. $string .'</div>';
    }

    public function print_logo(){
        global $CFG;
        return '<div style="width:100%; text-align:center;">
                    <image style="width:50%;" src="'.$CFG->wwwroot.'/blocks/sence/assets/sence-logo.webp">
                </div>';
    }
}