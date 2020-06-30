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
 * File containing onlineusers class.
 *
 * @package    block_online_users
 * @copyright  2020 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_sence;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used to list and count online users
 *
 * @package    block_online_users
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine{

    public $db, $user, $course;

    public function __construc( $db, $user, $course ){
        $this->$db = $db;
        $this->$user = $user;
        $this->$course = $course;
    }

    public function procesa_respuesta( $req ){
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
            return $this->describe_error( $GlosaError );
        }

        $this->registra_asistencia_moodle( $req );
        return 'Asistencia SENCE Registrada!';

    }

    public function registra_asistencia_moodle( $req ){
        // Registrara los datos del req en la base de datos;
        return 0;

    }

    public function describe_error($error){

        $errores_sence = [
            '100' =>  'Contraseña incorrecta.', //Contraseña incorrecta.
            '200' =>  'Parámetros vacíos.', //Parámetros vacíos.
            '201' =>  'Parámetro UrlError sin datos.', //Parámetro UrlError sin datos.
            '202' =>  'Parámetro UrlError con formato incorrecto.', //Parámetro UrlError con formato incorrecto.
            '203' =>  'Parámetro UrlRetoma con formato incorrecto.', //Parámetro UrlRetoma con formato incorrecto.
            '204' =>  'Parámetro CodSence con formato incorrecto.', //Parámetro CodSence con formato incorrecto.
            '205' =>  'Parámetro CodigoCurso con formato incorrecto.', //Parámetro CodigoCurso con formato incorrecto.
            '206' =>  'Línea de capacitación con formato incorrecto.', //Línea de capacitación con formato incorrecto.
            '207' =>  'Parámetro RunAlumno incorrecto.', //Parámetro RunAlumno incorrecto.
            '208' =>  'Parámetro RunAlumno diferente al enviado por OTEC.', //Parámetro RunAlumno diferente al enviado por OTEC.
            '209' =>  'Parámetro RutOtec incorrecto.', //Parámetro RutOtec incorrecto.
            '210' =>  'Sesión caducada.', //Sesión caducada.
            '211' =>  'Token incorrecto.', //Token incorrecto.
            '212' =>  'Token caducado.', //Token caducado.
            '300' =>  'Error interno.', //Error interno.
            '301' =>  'Error interno.', //Error interno.
            '302' =>  'Error interno.', //Error interno.
            '303' =>  'Error interno.', //Error interno.
            '304' =>  'Error interno.', //Error interno.
            '305' =>  'Error interno.', //Error interno.
        ];

        return $errores_sence[$error] . '<br><style>#region-main{filter:blur(5px);pointer-events:none;}</style>';

    }

    public function es_curso_sence(){
        return true;
        // Esta función debe revisar el campo codigo_sence_curso contenga un código registrado.
    }

    public function es_alumno_sence(){
        return true;
        // Busca el run del alumno
        // Busca en codigo_sence_alumno si ese run se encuentra allí con el el dato del código

    }

    public function es_alumno(){
        return true;
    }

    public function prepare_form( $currenturl ){
        // Prepara el formulario para mandar a sence

        $RutOtec = '76423250-k';
        $Token = '3EEE939E-9A98-44E9-B6D5-4422D0832535';
        $LineaCapacitacion = '3';
        $RunAlumno = '26107640-3';
        $IdSesionAlumno = '2';
        $CodSence = '1237991108';
        $CodigoCurso = '5911547';
        $UrlRetoma = $currenturl;
        $UrlError = $currenturl;

        $urlInicio = 'https://sistemas.sence.cl/rcetest/Registro/';

        return '<form  method="POST" action="'.$currenturl.'">
                    <button type="submit">Iniciar Sesión</button>
                    <div style="display:none;">
                        <input value="'.$RutOtec.'" type="text" name="RutOtec" class="form-control">
                        <input value="'.$Token.'" type="text" name="Token" class="form-control">
                        <input value="'.$LineaCapacitacion.'" type="text" name="LineaCapacitacion" class="form-control">
                        <input value="'.$RunAlumno.'" type="text" name="RunAlumno" class="form-control">
                        <input value="'.$IdSesionAlumno.'" type="text" name="IdSesionAlumno" class="form-control">
                        <input value="'.$UrlRetoma.'" type="text" name="UrlRetoma" class="form-control">
                        <input value="'.$UrlError.'" type="text" name="UrlError" class="form-control">
                        <input value="'.$CodSence.'" type="text" name="CodSence" class="form-control">
                        <input value="'.$CodigoCurso.'" type="text" name="CodigoCurso" class="form-control">
                    </div>
                </form>';
    }

    public function existen_campos_sence(){
        global $DB;
        $sence_curso_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_curso']);
        $sence_alumno_id = $DB->get_record('customfield_field', ['shortname' => 'codig_sence_alumno']);

        if( !$sence_curso_id && !$sence_alumno_id ){
            return false;
        }

        return true;
    }

    public function tiene_asistencia(){
        global $USER, $COURSE;
        return $COURSE->id == 2;
    }

}