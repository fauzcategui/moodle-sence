<?php
class block_sence extends block_base {
    public function init() {
        // $this->title = get_string('sence', 'block_sence');
        $this->title = 'Modulo Sence';
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.

    function has_config() {
        return true;
    }

    function instance_allow_config() {
        return true;
    }

    public function get_content() {
        global $USER, $CFG, $COURSE, $PAGE, $DB;

        $this->content =  new stdClass;

        if( !$this->fields_exists() ){
            $content = 'Por favor configure los campos en el curso';
            $this->content->text  = $content;
            return $this->content;
        }

        if ($this->content !== null) {
          return $this->content;
        }

        $content = 'Run Alumno: ' . $USER->idnumber.'<br>'.
        'Código Curso: ' . $COURSE->id.'<br>'.
        'Código Alumno: ' . $USER->idnumber.'<br>'.
        'Necesita Asistencia: '. 'Sí';

        $this->content =  new stdClass;
        $this->content->text  = $content;

        var_dump( $this->get_all_custom_fields_data());

        return $this->content;
    }

    function get_all_custom_fields_data() {
        global $DB, $COURSE;

        $sence_curso_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_curso']);
        $sence_alumno_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_alumno']);

        if( !$sence_curso_id || !$sence_alumno_id ){
            return 101;
        }

        $sence_curso_data = $DB->get_record( 'customfield_data', ['instanceid'=>  $COURSE->id, 'fieldid' => $sence_curso_id] )->value;
        $sence_alumno_data = $DB->get_record( 'customfield_data', ['instanceid'=>  $COURSE->id, 'fieldid' => $sence_alumno_id] )->value;

        return [ $sence_curso_data, $sence_alumno_data];

    }

    function get_customfieldid($shortname){
        global $DB;
        return $DB->get_record( 'customfield_data', ['shortname'=>  $shortname] )->id;
    }

    function fields_exists(){
        global $DB;
        $sence_curso_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_curso']);
        $sence_alumno_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_alumno']);

        if( !$sence_curso_id && !$sence_alumno_id ){
            return false;
        }

        return true;
    }


    // 1 - Chequea que el curso tenga los campos de sence configurados.
    //  R / - continua - si no avisa que se deben crear los campos con link a la doc.

    // 2 - Chequea que el curso tenga su codigo sence configurado.
    //  R / - Se procede a chequear entonces los alumnos contra el run del alumno actual - si no deja pasar al alumno como sin verificar

    // 3 - chequea que el alumno en ese curso ya marco asistencia en alguna ocasión (POR DEFINIR)
    //  SI - CONTINUA CON EL CURSO
    //  NO - PROCEDE A OFUSCAR O BLOQUEAR EL CURSO


}