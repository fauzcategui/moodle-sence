<?php

class block_sence_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $PAGE;

        $PAGE->requires->js('/blocks/sence/js/edit_form.js');

        // $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        /**
         * Otecs Previamente Guardadas
         */
        $otecs = Engine::get_otecs();
        $mform->addElement('select', 'config_otec', 'Selecciona OTEC', $otecs);
        $mform->setDefault('config_otec', 'XX;YY');


        /**
         * Lineas de Capacitación
         */
        $lineascap = [
            3 => 'Impulsa Personas (3)',
            // 1 => 'Programas Sociales o Becas Labores (1)',
        ];
        $mform->addElement('select', 'config_lineaCap', get_string('lineadecap', 'block_sence'), $lineascap);
        $mform->setDefault('config_lineaCap', 3);


        /**
         * Código SENCE del Curso
         */
        $mform->addElement('text', 'config_codigoCurso', get_string('codigocurso', 'block_sence'), ['size' => '10', 'maxlength' => '10']);
        $mform->setType('config_codigoCurso', PARAM_TEXT);
        $mform->setDefault('config_codigoCurso','');

        /**
         * Habilita/Deshabilita curso solo para alumnos registrados en la lista del Bloque
         */
        $mform->addElement('advcheckbox', 'config_senceSolo', get_string('permit', 'block_sence') );
        $mform->setDefault('config_senceSolo', true);

        /**
         *  True: Pedirá asistencia nueva cuando la anterior tenga más de 3 Horas
         *  False: Solicitará una asistencia en toda la vida del curso
         */
        $mform->addElement('advcheckbox', 'config_senceTiempoCierre', 'Cerrar sesión del Alumno después de 3 Horas' );
        $mform->setDefault('config_senceTiempoCierre', false);

        /**
         * Campo Oculta manejado por "js/edit_form.js" para almacenar los alumnos con sus respectivos códigos SENCE a través de un JSON
         */
        $mform->addElement('textarea', 'config_senceAlumnos', get_string('confalumnos', 'block_sence'), 'wrap="virtual" rows="8" cols="50"');
        $mform->setType('config_senceAlumnos', PARAM_TEXT);
        $mform->setDefault('config_senceAlumnos', '[]');


    }
}