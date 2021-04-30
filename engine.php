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

    /**
     * Nombre del Grupo de Becarios
     */
    private $nombreBecarios;

    private $mensajeError = '';


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
        $this->nombreBecarios = $this->get_instance_config('grupoBecas') ? strtolower($this->get_instance_config('grupoBecas')) : 'becarios';
    }

    public function encontar_grupo(){
        global $COURSE, $USER;
        $groups = groups_get_all_groups($COURSE->id);
        foreach( $groups as $group ){
            if( strtolower($group->name) == $this->nombreBecarios || preg_match( '/(?<!x)sence-/', strtolower($group->name) ) ){
                $users = groups_get_members( $group->id,'u.id');
                foreach( $users as $user ){
                    if( $USER->id == $user->id ){
                        return strtolower($group->name);
                    }
                }
            }
        }
        return null;
    }

    public function content(){

        if ( $this->es_profesor_no_editor() ) {
            // Devuelve el contenido vacío por lo tanto no muestra el bloque
            return '';
        }

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
            if( $this->codAlumno != $this->nombreBecarios ){
                $PAGE->requires->js('/blocks/sence/js/locker.js');
                return 'Alumno no autorizado para este curso';
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

    private function config_esta_completa(){
        $error = false;
        $this->mensajeError = '';

        if( !$this->rutOtec || !$this->token ){
            if( self::is_multiotec() ){
                $this->mensajeError = $this->mensajeError.'<li>Seleccionar una OTEC para este Bloque</li>';
            }
            else{
                $this->mensajeError = $this->mensajeError.'<li>Configurar OTEC del Sitio</li>';
            }
            $error = true;
        }
        if( strlen( strval($this->codCurso) ) < 10 ){
            $this->mensajeError = $this->mensajeError.'<li>Configurar un Código de Curso de 10 dígitos</li>';
            $error = true;
        }
        return !$error;
    }

    private function content_editor(){
        $result = '';
        if( $this->config_esta_completa() ){
            $result = "<p>Integración SENCE Activada
                        <br><span>OTEC: <b>{$this->rutOtec}</b></span>
                        <br><span>Código Curso: <b>{$this->codCurso}</b></span>
                        </p>";
        }
        else{
            $result = "<div class='alert alert-danger'><p>¡CONFIGURACIÓN INCOMPLETA!</p>
                        <p>Se debe:</p>
                        <ul>{$this->mensajeError}</ul></div>";
        }

        $cierreSesison = $this->get_instance_config('senceTiempoCierre') ? 'La sesión del participante se cerrará cada 3 Horas' : 'Se pedirá Iniciar sesión una sola vez al participante';
        $grupo = $this->get_instance_config('grupoBecas');
        $instrucciones = "<ul>
                            <li>Recuerda asignar el ID de acción en el nombre del grupo de tus participantes, así: SENCE-XXXXXXX</li>
                            <li>Los usuarios en el Grupo: {$grupo}, no serán requeridos de integrar SENCE</li>
                            <li>{$cierreSesison}</li>
                        </ul>";

        return $result . $instrucciones;
    }

    private function es_alumno_sence(){
        $r = $this->encontar_grupo();
        if( is_null($r) ){
            return false;
        }
        if( $r == $this->nombreBecarios ){
            $this->codAlumno = $this->nombreBecarios;
            return false;
        }
        $t = explode('-', $r);
        if( count($t) == 2 ){
            $this->codAlumno = $t[1];
            return true;
        }

        return false;
    }

    private function es_profesor_no_editor(){
        if( current(get_user_roles($this->coursecontext, $USER->id))->shortname == 'teacher'
            && !has_capability('moodle/course:viewhiddensections', $this->coursecontext)
        ){
            return true;
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
            $show = $show . "<div class='alert alert-danger'>{$msj}</div>";
        }
        return $show;
    }

    private function asistencia_form(){
        global $PAGE;
        if( !$this->runAlumno ){
            return '<div class="alert alert-danger">Se debe configurar el RUN del alumno para continuar</div>';
        }

        $linksDeInteres = "<br>
        <h5>Enlaces de Interés</h5>
        <ul>
            <li><a target='_blank' href='{$this->linkRegistrar}'>Registrar Clave SENCE</a></li>
            <li><a target='_blank' href='{$this->linkSolicitar}'>Solicitar Nueva Clave SENCE</a></li>
            <li><a target='_blank' href='{$this->linkCambiar}'>Cambiar Clave SENCE</a></li>
            <li><a target='_blank' href='{$this->linkActualizar}'>Actualizar Datos</a></li>
        </ul>";

        if( $this->config_esta_completa() ){
            return "<form style='text-align:center;' method='POST' action='{$this->urlInicio}'>
                    <button type='submit' class='btn btn-primary btn-block btn-lg'>Iniciar Sesión</button>
                    <input value='{$this->rutOtec}' type='hidden' name='RutOtec' placeholder='RutOtec' class='form-control'>
                    <input value='{$this->token}' type='hidden' name='Token' placeholder='Token' class='form-control'>
                    <input value='{$this->lineaCap}' type='hidden' name='LineaCapacitacion' placeholder='LineaCapacitacion' class='form-control'>
                    <input value='{$this->runAlumno}' type='hidden' name='RunAlumno' placeholder='RunAlumno' class='form-control'>
                    <input value='{$this->sesionAlumno}' type='hidden' name='IdSesionAlumno' placeholder='IdSesionAlumno' class='form-control'>
                    <input value='{$PAGE->url}' type='hidden' name='UrlRetoma' placeholder='UrlRetoma' class='form-control'>
                    <input value='{$PAGE->url}' type='hidden' name='UrlError' placeholder='UrlError' class='form-control'>
                    <input value='{$this->codCurso}' type='hidden' name='CodSence' placeholder='CodSence' class='form-control'>
                    <input value='{$this->codAlumno}' type='hidden' name='CodigoCurso' placeholder='CodigoCurso' class='form-control'>
                    </form>
                    {$linksDeInteres}";
        }
        return "<div class='alert alert-danger'>Integración SENCE Incompleta. Contacte al Administrador</div>{$linksDeInteres}";
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

    static function is_multiotec(){
        $settings = json_decode( get_config('block_sence', 'otecs'), true );
        return $settings['multiotec'];
    }

    /**
     * Obtiene JSON de Otecs de la base de datos y las formatea para el <select/>
     */
    static function get_otecs(){
        $settings = json_decode( get_config('block_sence', 'otecs'), true );

        $options = [
            'none' => 'Seleccione una OTEC'
        ];

        if( count($settings['otecs']) > 0){
            foreach( $settings['otecs'] as $otec ){
                $value = "{$otec['rut']};{$otec['token']}";
                $options[ $value ] = "{$otec['name']} | {$otec['rut']}";
            }
        }

        return $options;
    }

    private function get_otec(){
        $settings = json_decode( get_config('block_sence', 'otecs'), true );

        if( $settings['otecs'] > 0 ){
            return $settings['otecs'][0];
        }

        return null;
    }

    private function info_otec(){
        if( self::is_multiotec() ){
            $result = [ 'rut' => '', 'token' => '' ];
            $otec = $this->get_instance_config('otec') ? $this->get_instance_config('otec') : 'XX;YY';
            $t = explode(';', $otec);
            if( count($t) == 2 ){
                $result['rut'] = trim( $t[0] );
                $result['token'] = trim( $t[1] );
            }
            return $result;
        }

        return $this->get_otec();
    }

    private function get_instance_config($param){
        if( isset( $this->blockInstance->config ) ){
            return $this->blockInstance->config->{$param};
        }
        return false;
    }

    public function get_footer(){
        global $CFG;
        return  !$this->get_instance_config('muestraLogo') ? "" : "
        <div style='width:100%; text-align:center; margin-top:10px;'>
            <div style='height:2px; width:100%; background:#ffb1b1;'></div>
            <image style='width:150px;' src='{$CFG->wwwroot}/blocks/sence/assets/sence-logo.webp'>
        </div>
        <span>Información más detallada de este bloque <a href='{$this->linkReadme}'>Aquí</a></span>";
    }
}