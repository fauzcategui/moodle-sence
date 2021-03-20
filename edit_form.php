<?php

class block_sence_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
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
         *  True: Pedirá asistencia nueva cuando la anterior tenga más de 3 Horas
         *  False: Solicitará una asistencia en toda la vida del curso
         */
        $mform->addElement('advcheckbox', 'config_senceTiempoCierre', 'Cerrar sesión del Alumno después de 3 Horas' );
        $mform->setDefault('config_senceTiempoCierre', false);

        /**
         *  True: Pedirá asistencia nueva cuando la anterior tenga más de 3 Horas
         *  False: Solicitará una asistencia en toda la vida del curso
         */
        $mform->addElement('advcheckbox', 'config_muestraLogo', 'Mostrar logo SENCE en en Bloque' );
        $mform->setDefault('config_muestraLogo', false);
    }
}