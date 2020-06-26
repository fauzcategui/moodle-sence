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
        global $USER, $CFG, $COURSE, $DB;

        $this->content =  new stdClass;

        if( !$this->fields_exists() ){
            $content = 'Los Custom Fields requeridos para este Pugin no están configurados';
            $this->content->text  = $content;
            return $this->content;
        }

        if( !$this->es_curso_sence() ){
            $content = 'Este curso no tiene código SENCE';
            $this->content->text  = $content;
            return $this->content;
        }

        if( !$this->es_alumno_sence() ){
            // Pendiente de buscar el campo nombre del alumno
            $content = 'Bienvenido '. $USER->id;
            $this->content->text  = $content;
            return $this->content;
        }

        if( $this->tiene_asistencia() ){
            $content = 'Bienvenido ' . $USER->id . '<br>¡Ya registraste tu asistencia!';
            $this->content = $content;
            return $this->content;
        }

        $content = $this->prepare_form();
        $this->content->text = $content;
        $this->content->footer ='<style>#region-main{filter:blur(5px);pointer-events:none;}</style>';

        return $this->content;

    }

    // function get_all_custom_fields_data() {
    //     global $DB, $COURSE;

    //     $sence_curso_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_curso']);
    //     $sence_alumno_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_alumno']);

    //     if( !$sence_curso_id || !$sence_alumno_id ){
    //         return 101;
    //     }

    //     $sence_curso_data = $DB->get_record( 'customfield_data', ['instanceid'=>  $COURSE->id, 'fieldid' => $sence_curso_id] )->value;
    //     $sence_alumno_data = $DB->get_record( 'customfield_data', ['instanceid'=>  $COURSE->id, 'fieldid' => $sence_alumno_id] )->value;

    //     return [ $sence_curso_data, $sence_alumno_data];

    // }

    // function get_customfieldid($shortname){
    //     global $DB;
    //     return $DB->get_record( 'customfield_data', ['shortname'=>  $shortname] )->id;
    // }

    function es_curso_sence(){
        global $DB;
        return true;
        // Esta función debe revisar el el campo codigo_sence_curso contenga un código registrado.
    }

    function es_alumno_sence(){
        global $DB, $USER;
        return true;
        // Busca el run del alumno
        // Busca en codigo_sence_alumno si ese run se encuentra allí con el el dato del código

    }

    function prepare_form(){
        // Prepara el formulario para mandar a sence

        $RutOtec = 'cualquiercosa';
        $Token = 'cualquiercosa';
        $LineaCapacitacion = 'cualquiercosa';
        $RunAlumno = 'cualquiercosa';
        $IdSesionAlumno = 'cualquiercosa';
        $UrlRetoma = 'cualquiercosa';
        $UrlError = 'cualquiercosa';
        $CodSence = 'cualquiercosa';
        $CodigoCurso = 'cualquiercosa';

        return '
            <form  method="POST" action="#">
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

    function fields_exists(){
        global $DB;
        $sence_curso_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_curso']);
        $sence_alumno_id = $DB->get_record('customfield_field', ['shortname' => 'codigo_sence_alumno']);

        // if( !$sence_curso_id || !$sence_alumno_id ){
        //     return false;
        // }

        return true;
    }

    function tiene_asistencia(){
        global $USER, $COURSE;
        return $COURSE->id == 2;
    }


    // 1 - Chequea que el curso tenga los campos de sence configurados.
    //  R / - continua - si no avisa que se deben crear los campos con link a la doc.

    // 2 - Chequea que el curso tenga su codigo sence configurado.
    //  R / - Se procede a chequear entonces los alumnos contra el run del alumno actual - si no deja pasar al alumno como sin verificar

    // 3 - chequea que el alumno en ese curso ya marco asistencia en alguna ocasión (POR DEFINIR)
    //  SI - CONTINUA CON EL CURSO
    //  NO - PROCEDE A OFUSCAR O BLOQUEAR EL CURSO


}