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
require_once('sence_report.php');

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
     * Links de SENCE para ambiente de Producción
     */
    private $urlInicioProd = 'https://sistemas.sence.cl/rce/Registro/IniciarSesion';
    private $urlCierreProd = 'https://sistemas.sence.cl/rce/Registro/CerrarSesion';

    /**
     * Links de SENCE para ambiente TEST
     */
    private $urlInicioTest = 'https://sistemas.sence.cl/rcetest/Registro/IniciarSesion';
    private $urlCierreTest = 'https://sistemas.sence.cl/rcetest/Registro/CerrarSesion';


    /**
     * Links que se Utilizaran en el Bloque
     */
    private $urlInicio, $urlCierre;

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
        '100' => 'Contraseña incorrecta o el usuario no tiene Clave SENCE.',
        '200' => 'El POST tiene uno o más parámetros mandatorios sin información.',
        '201' => 'La URL de Retoma y/o URL de Error no tienen información. Ambos parámetros son obligatorios en todos los POST.',
        '202' => 'La URL de Retoma tiene formato incorrecto.',
        '203' => 'La URL de Error tiene formato incorrecto.',
        '204' => 'El => Código SENCE tiene menos de 10 caracteres y/o no es código válido.',
        '205' => 'El Código Curso tiene menos de 7 caracteres y/o no es código válido.',
        '206' => 'La línea de capacitación es incorrecta.',
        '207' => 'El Run Alumno tiene formato incorrecto, o tiene el dígito verificador incorrecto.',
        '208' => 'El Run Alumno no está autorizado para realizar el curso.',
        '209' => 'El Rut OTEC tiene formato incorrecto, o tiene el dígito verificador incorrecto.',
        '210' => 'Expiró el tiempo disponible para el ingreso de RUT y Contraseña. El tiempo disponible es de tres minutos.',
        '211' => 'El Token no pertenece al OTEC.',
        '212' => 'El Token no está vigente.',
        '300' => 'Error interno no clasificado, se debe reportar al SENCE con la mayor cantidad de antecedentes disponibles.',
        '301' => 'No se pudo registrar el ingreso o cierre de sesión. Esto ocurre cuando la Línea de Capacitación es incorrecta, o el Código de Curso es incorrecto.',
        '302' => 'No se pudo validar la información del Organismo, se debe reportar al SENCE con la mayor cantidad de antecedentes disponibles.',
        '303' => 'El Token no existe, o su formato es incorrecto.',
        '304' => 'No se pudieron verificar los datos enviados, se debe reportar al SENCE con la mayor cantidad de antecedentes disponibles.',
        '305' => 'No se pudo registrar la información, se debe reportar al SENCE con la mayor cantidad de antecedentes disponibles.',
        '306' => 'El Código Curso no corresponde al Código SENCE.',
        '307' => 'El Código Curso no tiene modalidad E-Learning.',
        '308' => 'El Código Curso no corresponde al RUT OTEC.',
        '309' => 'Las fechas de ejecución comunicadas para el Código Curso no corresponden a la fecha actual.',
        '310' => 'El Código Curso está en estado Terminado o Anulado.'
    ];


    /**
     * Parametros para el Formulario SENCE
     */
    private $token, $rutOtec, $lineaCap, $codCurso, $codAlumno, $runAlumno, $sesionAlumno, $sesionSence;

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

    private $ultimaSesion;

    /**
     * Nombre del Grupo de Becarios
     */
    private $nombreBecarios;

    private $asistenciaObligatoria;

    private $testEnv;

    private $mensajeError = '';


    private $reporter;

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

        $this->asistenciaObligatoria = boolval( $this->get_instance_config('asistenciaObligatoria' ));

        $this->testEnv = boolval( get_config('block_sence','testenv') );

        $this->urlInicio = $this->testEnv ? $this->urlInicioTest : $this->urlInicioProd;
        $this->urlCierre = $this->testEnv ? $this->urlCierreTest : $this->urlCierreProd;
        $this->reporter = new sence_report( $COURSE->id );
    }

    public function encontar_grupo(){
        global $COURSE, $USER;
        $groups = groups_get_all_groups($COURSE->id);
        foreach( $groups as $group ){
            if( strtolower($group->name) == $this->nombreBecarios || preg_match( '/(?<!x)sence-/', strtolower($group->name) ) ){
                $users = groups_get_members( $group->id,'u.id');
                foreach( $users as $user ){
                    if( $USER->id == $user->id ){
                        return $group->name;
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
                if( $this->asistenciaObligatoria ){
                    $PAGE->requires->js('/blocks/sence/js/locker.js');
                }
                $this->es_alumno_sence();
                return $this->formatea_error( $_POST['GlosaError'] ) . $this->asistencia_inicio_form();
            }
            $this->registra_asistencia($_POST);
        }

        if( !$this->es_alumno_sence() ){
            if( $this->codAlumno != $this->nombreBecarios ){
                if( $this->asistenciaObligatoria ){
                    $PAGE->requires->js('/blocks/sence/js/locker.js');
                    return 'Alumno no autorizado para este curso';
                }
            }
            return '';
        }

        if( $this->asistencia_vigente() ){
            if( $this->get_instance_config('senceTiempoCierre') ){
                return $this->asistencia_cierre_form();
            }

            return 'Asistencia Registrada';
        }

        if( $this->asistenciaObligatoria ){
            $PAGE->requires->js('/blocks/sence/js/locker.js');
        }

        return  $this->asistencia_inicio_form();

    }

    private function asistencia_vigente(){
        global $DB, $USER, $COURSE;
        $asistencia = $DB->get_records( 'block_sence', [ 'courseid' => $COURSE->id, 'userid' => $USER->id ] );
        if( count( $asistencia ) > 0 ){
            if( $this->get_instance_config('senceTiempoCierre') ){
                $asistencia  =  array_values( $asistencia );
                if( $asistencia[count($asistencia)-1]->cierresesion ){
                    return false;
                }
                if( ( time() - $asistencia[count($asistencia)-1]->timecreated ) > $this->tiempoSesion ){
                    return false;
                }
                $this->ultimaSesion = $asistencia[count($asistencia)-1]->timecreated;
                $this->sesionSence = $asistencia[count($asistencia)-1]->idsesionsence;
            }
            return true;
        }

        return false;

    }

    private function config_esta_completa(){
        $ready = true;
        $this->mensajeError = '';

        $longitud = $this->testEnv ? 1 : 10;
        $s = !$this->testEnv ? 's' : '';

        if( !$this->rutOtec || !$this->token ){
            if( self::is_multiotec() ){
                $this->mensajeError = "{$this->mensajeError}'<li>Seleccionar una OTEC para este Bloque</li>";
            }
            else{
                $this->mensajeError = "{$this->mensajeError}<li>Configurar OTEC del Sitio</li>";
            }
            $ready = false;
        }
        if( intval($this->lineaCap) !== 1 ){
            if( strlen( strval($this->codCurso) ) < $longitud ){
                $this->mensajeError = "{$this->mensajeError}<li>Configurar un Código de Curso de {$longitud} dígito{$s}</li>";
                $ready = false;
            }
        }
        return $ready;
    }

    private function content_editor(){
        $result = '';
        $msj = intval($this->lineaCap) !== 1 ? "<br><span>Código Curso: <b>{$this->codCurso}</b></span>" : '';

        if( $this->config_esta_completa() ){
            $result = "<p>Integración SENCE Activada
                        <br><span>OTEC: <b>{$this->rutOtec}</b></span>
                        {$msj}
                        </p>";
        }
        else{
            $result = "<div class='alert alert-danger' style='position:unset !important;'><p>¡CONFIGURACIÓN INCOMPLETA!</p>
                        <p>Se debe:</p>
                        <ul>{$this->mensajeError}</ul></div>";
        }

        $cierreSesison = $this->get_instance_config('senceTiempoCierre') ? 'Se solicitará cierre de sesión al participante' : 'Se solicitará Iniciar sesión una sola vez al participante';

        $grupo = $this->get_instance_config('grupoBecas');
        $instrucciones = "<ul>
                            <li>Recuerda asignar el ID de acción en el nombre del grupo de tus participantes, así: SENCE-XXXXXXX</li>
                            <li>Los usuarios en el Grupo: {$grupo}, no serán requeridos de integrar SENCE</li>
                            <li>{$cierreSesison}</li>
                        </ul>";

        $this->reporter->handle();
        return $result . $instrucciones . $this->reporter->bt_descarga() ;
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

        $this->codAlumno = str_replace('sence-','', $r);
        $this->codAlumno = str_replace('SENCE-','', $this->codAlumno);
        return true;


        return false;
    }

    private function es_profesor_no_editor(){
        global $USER;
        $role = current(get_user_roles($this->coursecontext, $USER->id));

        if( !has_capability('moodle/course:viewhiddensections', $this->coursecontext)
            && $role->shortname == 'teacher'
        ){
            return true;
        }

        return false;
    }

    private function es_alumno(){
        return !has_capability('moodle/course:viewhiddensections', $this->coursecontext);
    }

    private function registra_asistencia($response){
        global $DB, $COURSE, $USER;

        if( !isset($response['IdSesionSence']) ){
            // Cierre de Sesión
            return $DB->set_field( 'block_sence' , 'cierresesion', time(), [ 'idsesionalumno' => $response['IdSesionAlumno'] ] );
        }

        $data = [
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
            'timecreated' => time(),
            'codsence' => isset($response['CodSence']) ? $response['CodSence'] : null,
            'codigocurso' => $response['CodigoCurso'],
            'idsesionalumno' => $response['IdSesionAlumno'],
            'idsesionsence' => $response['IdSesionSence'],
            'runalumno' => $response['RunAlumno'],
            'fechahora' => $response['FechaHora'],
            'zonahoraria' => $response['ZonaHoraria'],
            'lineacapacitacion' => $response['LineaCapacitacion']
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

    private function asistencia_inicio_form(){
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
                    <input value='{$this->rutOtec}' type='hidden' name='RutOtec'>
                    <input value='{$this->token}' type='hidden' name='Token'>
                    <input value='{$this->lineaCap}' type='hidden' name='LineaCapacitacion'>
                    <input value='{$this->runAlumno}' type='hidden' name='RunAlumno'>
                    <input value='{$this->sesionAlumno}' type='hidden' name='IdSesionAlumno'>
                    <input value='{$PAGE->url}' type='hidden' name='UrlRetoma'>
                    <input value='{$PAGE->url}' type='hidden' name='UrlError'>
                    <input value='{$this->codCurso}' type='hidden' name='CodSence'>
                    <input value='{$this->codAlumno}' type='hidden' name='CodigoCurso'>
                    </form>
                    {$linksDeInteres}";
        }
        return "<div class='alert alert-danger'>Integración SENCE Incompleta. Contacte al Administrador</div>{$linksDeInteres}";
    }

    private function asistencia_cierre_form(){
        global $PAGE;
        $PAGE->requires->js('/blocks/sence/js/timer.js');
        $date = time() - $this->ultimaSesion;
        return "
        <style>
            .timer{
                font-size: 25px;
                padding: 10px;
                text-align:center;
        </style>
        <input type='hidden' value='{$date}' id='counter' />
        <div class='timer'>
            <span id='minutes'>00</span>:<span id='seconds'>00</span>
        </div>
        <form style='text-align:center;' method='POST' action='{$this->urlCierre}'>
                    <button type='submit' class='btn btn-primary btn-block btn-lg'>Cerrar Sesión</button>
                    <input value='{$this->rutOtec}' type='hidden' name='RutOtec'>
                    <input value='{$this->token}' type='hidden' name='Token'>
                    <input value='{$this->lineaCap}' type='hidden' name='LineaCapacitacion'>
                    <input value='{$this->runAlumno}' type='hidden' name='RunAlumno'>
                    <input value='{$this->sesionAlumno}' type='hidden' name='IdSesionAlumno'>
                    <input value='{$this->sesionSence}' type='hidden' name='IdSesionSence'>
                    <input value='{$PAGE->url}' type='hidden' name='UrlRetoma'>
                    <input value='{$PAGE->url}' type='hidden' name='UrlError'>
                    <input value='{$this->codCurso}' type='hidden' name='CodSence'>
                    <input value='{$this->codAlumno}' type='hidden' name='CodigoCurso'>
                    </form>";
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

        if( count($settings['otecs']) > 0 ){
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
            return property_exists( $this->blockInstance->config, $param ) ? $this->blockInstance->config->{$param} : false;
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
