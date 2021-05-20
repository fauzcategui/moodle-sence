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

require_once($CFG->libdir.'/excellib.class.php');

class sence_report
{

    protected $headers = [
        'coursename' => 'CURSO',
        'firstname' => 'NOMBRES',
        'lastname' => 'APELLIDOS',
        'runalumno' => 'RUN',
        'codsence' => 'CODIGO CURSO',
        'codigocurso' => 'ID SENCE',
        'fechahora' => 'FECHA/HORA DE ASISTENCIA'
    ];

    public function handle(){

        $this->get_asistencias();
        

        if( isset( $_GET['xlsreport'] ) ){
            $this->genera_reporte();
        }

    }


    public function bt_descarga(){
        global $COURSE;
        $uri = $_SERVER['REQUEST_URI'];

        return "<div style='margin-bottom:10px; height:2px; width:100%; background:#ffb1b1;'></div>
                <p>Reporte de asistencias SENCE de este curso</p>
                <form method='GET' action='{$uri}'>
                    <input type='hidden' name='id' value='{$COURSE->id}' />
                    <input type='hidden' name='xlsreport' value='1' />
                    <input type='submit' value='Descargar Reporte' class='btn btn-primary btn-block btn-lg' />
                </form>";

    }

    private function genera_reporte(){
        global $COURSE;
        $asistencias = $this->get_asistencias();

        $filename = clean_filename("asistencia_sence-curso_{$COURSE->id}");
        $workbook = new MoodleExcelWorkbook("-");
        $workbook->send($filename);

        $xlsfile =& $workbook->add_worksheet('asistencias');

        $row = 0;
        $col = 0;
        foreach ($this->headers as $header) {
            $xlsfile->write($row, $col++, $header);
        }
        foreach ($asistencias as $datum) {
            if (!is_object($datum)) {
                continue;
            }
            $row++;
            $col = 0;
            foreach ($this->headers as $id => $header) {
                if (isset($datum->{$id})) {
                    $xlsfile->write($row, $col++, $datum->{$id});
                } else {
                    $xlsfile->write($row, $col++, '');
                }
            }
        }
        $workbook->close();
        exit();
    }

    private function get_asistencias(){
        global $DB, $COURSE;
        $asistencias = $DB->get_records("block_sence", ['courseid' => $COURSE->id ], 'fechahora DESC');
        if( count( $asistencias ) < 1 ){
            return null;
        }

        $result = [];

        foreach( $asistencias as $asistencia ){
            $alumnos = $DB->get_records_select('user', "id={$asistencia->userid}", null, '', 'firstname, lastname' );
            foreach( $alumnos as $alumno ){
                $alumno->coursename = $COURSE->fullname;
                $alumno->runalumno = $asistencia->runalumno;
                $alumno->codsence = $asistencia->codsence;
                $alumno->codigocurso = $asistencia->codigocurso;

                $date = new DateTime($asistencia->fechahora);
                $date = $date->format('d-m-Y H:i:s');
                $alumno->fechahora = $date;
                $result[] = $alumno;
            }
        }

        return $result;

    }
}