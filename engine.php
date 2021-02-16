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

class Engine
{

    /**
     * GitHub README
     */
    private $linkReadme = "https://github.com/fauzcategui/moodle-sence/blob/stable/README.md";


    /**
     * Links del Sence
     */
    private $urlInicio = 'https://sistemas.sence.cl/rce/Registro/IniciarSesion';
    private $urlInicioTest = 'https://sistemas.sence.cl/rcetest/Registro/IniciarSesion';
    private $urlCierre = '#';
    private $urlCierreTest = '#';

    /**
     * Otros Links de Interés del SENCE
     */
    private $linkRegistrar = 'https://cus.sence.cl/Account/Registrar';
    private $linkSolicitar = 'https://cus.sence.cl/Account/RecuperarClave';
    private $linkCambiar = 'https://cus.sence.cl/Account/CambiarClave';
    private $linkActualizar = 'https://cus.sence.cl/Account/ActualizarDatos';

     /**
     * Posibles errores a reportar por SENCE según Documentación
     */
    private  $erroresSence = [
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


    /**
     * Parametros para el Formulario SENCE
     */
    private $token, $rutOtec, $lineaCap, $codCurso, $codAlumno, $runAlumno, $sesionAlumno;

    /**
     * Instancia del Bloque
     */
    private $blockInstance;

    /**
     * Sera llenado por los alumnos del Formulario
     */
    private $alumnos;
    private $coursecontext;

    /**
     * Tiempo de Sesión (3 Horas)
     */
    private $tiempoSesion = 3600 * 3;


    function __construct(){
        global $DB, $COURSE, $USER;
        /**
         * Genera Instancia del Bloque
         */
        $this->coursecontext = context_course::instance($COURSE->id);
        $block = $DB->get_record('block_instances', array('blockname' => 'sence', 'parentcontextid' => $this->coursecontext->id), '*', MUST_EXIST);
        $this->blockInstance = block_instance('sence', $block);

        $this->runAlumno = $this->run_alumno();
        $otec = $this->info_otec();
        $this->rutOtec = $otec['rut'];
        $this->token = $otec['token'];
        $this->codCurso = $this->get_instance_config('codigoCurso') ? $this->get_instance_config('codigoCurso') : '';
        $this->lineaCap = $this->get_instance_config('lineaCap') ? $this->get_instance_config('lineaCap') : '';
        $this->sesionAlumno = $USER->sesskey;

        /**
         * Carga los alumnos del Bloque SENCE
         */
        $this->alumnos = $this->get_instance_config('senceAlumnos') ? json_decode( $this->get_instance_config('senceAlumnos'), true ) : [];
    }

    public function content(){
        return $this->es_alumno() ? $this->content_alumno() : $this->content_editor();
    }

    private function content_alumno(){
        global $PAGE;

        /**
         * Caso: POST desde SENCE
         */
        if( isset( $_POST['RunAlumno'] ) ){
            if( isset($_POST['GlosaError']) ){
                $PAGE->requires->js('/blocks/sence/js/locker.js');
                $this->es_alumno_sence();
                return $this->formatea_error( $_POST['GlosaError'] ) . $this->asistencia_form();
            }
            $this->registra_asistencia();
        }

        if( !$this->es_alumno_sence() ){
            if( $this->solo_sence() ){
                $PAGE->requires->js('/blocks/sence/js/locker.js');
                return 'Alumno no permitido';
            }
            return '';
        }

        if( $this->asistencia_vigente() ){
            return 'Asistencia Registrada';
        }

        $PAGE->requires->js('/blocks/sence/js/locker.js');
        return  $this->asistencia_form();


    }

    private function asistencia_vigente(){
        global $DB, $USER, $COURSE;
        if( $this->get_instance_config('senceTiempoCierre') ){
            $asistencia = $DB->get_records( 'block_sence', [ 'courseid' => $COURSE->id, 'userid' => $USER->id ] );
            if( count( $asistencia ) > 0 ){
                $asistencia  =  array_values( $asistencia );
                return ( time() - $asistencia[count($asistencia)-1]->timecreated ) < $this->tiempoSesion;
            }
            return false;
        }

        return $DB->record_exists( 'block_sence', [ 'courseid' => $COURSE->id, 'userid' => $USER->id ] );
    }

    private function content_editor(){
        return "
        <p>Gestionar las siguientes configuraciones en el bloque:</p>
        <ul>
            <li>Asignar OTEC</li>
            <li>Agregar código SENCE del Curso</li>
            <li>Agregar Alumnos con su código SENCE</li>
            <li>Habilitar/Deshabilitar Curso para Alumnos sin SENCE</li>
            <li>Configurar cierre de Sesión automático cada 3 Horas</li>
        </ul>
        <span>Información más detallada de este bloque <a href='{$this->linkReadme}'>Aquí</a></span>
        ";
    }

    private function es_alumno_sence(){
        if( count($this->alumnos) > 0 && $this->runAlumno ){
            foreach( $this->alumnos as $alumno ){
                if( $alumno['rut'] == $this->runAlumno ){
                    $this->codAlumno = $alumno['cod'];
                    return true;
                }
            }
        }

        return false;
    }

    private function es_alumno(){
        return !has_capability('moodle/course:viewhiddensections', $this->coursecontext);
    }

    private function registra_asistencia(){
        global $DB, $COURSE, $USER;

        $data = [
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
            'timecreated' => time(),
        ];

        return $DB->insert_record( 'block_sence' , $data);
    }

    private function formatea_error($glosa){
        $show = '';
        $errores = explode(';', $glosa);
        foreach($errores as $error){
            $msj = isset( $this->erroresSence[trim($error)] ) ? $this->erroresSence[trim($error)] : 'Error desconocido';
            $show = $show . "<div style='padding:10px; background-color:#ee928f; color:fff; border-radius:5px; margin-bottom:10px;'>{$msj}</div>";
        }
        return $show;
    }

    private function asistencia_form(){
        global $PAGE, $CFG;
        return "<form style='text-align:center;' method='POST' action='{$this->urlInicio}'>
                    <button type='submit' class='btn btn-primary btn-block btn-lg'>
                        Iniciar Sesión
                    </button>
                    <div style='display:none'>
                        <input value='{$this->rutOtec}' type='text' name='RutOtec' placeholder='RutOtec' class='form-control'>
                        <input value='{$this->token}' type='text' name='Token' placeholder='Token' class='form-control'>
                        <input value='{$this->lineaCap}' type='text' name='LineaCapacitacion' placeholder='LineaCapacitacion' class='form-control'>
                        <input value='{$this->runAlumno}' type='text' name='RunAlumno' placeholder='RunAlumno' class='form-control'>
                        <input value='{$this->sesionAlumno}' type='text' name='IdSesionAlumno' placeholder='IdSesionAlumno' class='form-control'>
                        <input value='{$PAGE->url}' type='text' name='UrlRetoma' placeholder='UrlRetoma' class='form-control'>
                        <input value='{$PAGE->url}' type='text' name='UrlError' placeholder='UrlError' class='form-control'>
                        <input value='{$this->codAlumno}' type='text' name='CodSence' placeholder='CodSence' class='form-control'>
                        <input value='{$this->codCurso}' type='text' name='CodigoCurso' placeholder='CodigoCurso' class='form-control'>
                    </div>
                </form>
                <div style='display:flex; margin-top:30px;'>
                <div style='width:50%;' id='relevant-links'>
                    <h4>Enlaces de Interés</h4>
                    <ul>
                        <li><a target='_blank' href='{$this->linkRegistrar}'>Registrar Clave SENCE</a></li>
                        <li><a target='_blank' href='{$this->linkSolicitar}'>Solicitar Nueva Clave SENCE</a></li>
                        <li><a target='_blank' href='{$this->linkCambiar}'>Cambiar Clave SENCE</a></li>
                        <li><a target='_blank' href='{$this->linkActualizar}'>Actualizar Datos</a></li>
                    </ul>
                </div>
            </div>";
    }

    private function run_alumno(){
        global $USER;

        if( preg_match('/\d*-[0-9kK]/', $USER->username) ){
            return strtolower($USER->username);
        }

        if( preg_match('/\d*-[0-9kK]/', $USER->idnumber) ){
            return strtolower($USER->idnumber);
        }

        return false;
    }

    /**
     * Obtiene JSON de Otecs de la base de datos y las formatea para el <select />
     */
    static function get_otecs(){
        $otecs = get_config('sence_block', 'otecs');
        $otecs = json_decode( $otecs, true );

        $options = [
            'none' => 'Seleccione una OTEC'
        ];

        if( count($otecs) > 0){
            foreach( $otecs as $otec ){
                $value = "{$otec['rut']};{$otec['token']}";
                $options[ $value ] = "{$otec['name']} | {$otec['rut']}";
            }
        }

        return $options;
    }

    private function solo_sence(){
        return $this->get_instance_config('senceSolo') ? true : false;
    }

    private function info_otec(){
        $result = [ 'rut' => '', 'token' => '' ];
        $otec = $this->get_instance_config('otec') ? $this->get_instance_config('otec') : 'XX;YY';
        $t = explode(';', $otec);
        if( count($t) == 2 ){
            $result['rut'] = $t[0];
            $result['token'] = $t[1];
        }
        return $result;
    }

    private function get_instance_config($param){
        if( isset( $this->blockInstance->config ) ){
            return $this->blockInstance->config->{$param};
        }
        return false;
    }
}