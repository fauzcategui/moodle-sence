<?php

class block_sence_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        /**
         * Otecs Previamente Guardadas
         */
        if( Engine::is_multiotec() ){
            $otecs = Engine::get_otecs();
            $mform->addElement('select', 'config_otec', 'Selecciona OTEC', $otecs);
            $mform->setDefault('config_otec', 'XX;YY');
        }


        /**
         * Lineas de Capacitación
         */
        $lineascap = [
            3 => 'Impulsa Personas (3)',
            1 => 'Programas Sociales o Becas Labores (1)',
        ];
        $mform->addElement('select', 'config_lineaCap', get_string('lineadecap', 'block_sence'), $lineascap);
        $mform->setDefault('config_lineaCap', 3);

        /**
         * Código SENCE del Curso
         */
        $mform->disabledIf('config_codigoCurso', 'config_lineaCap', 'eq', 1);
        $mform->addElement('text', 'config_codigoCurso', get_string('codigocurso', 'block_sence'), ['size' => '15', 'maxlength' => '10']);
        $mform->setType('config_codigoCurso', PARAM_TEXT);
        // $mform->setDefault('config_codigoCurso','');

        /**
         * Grupo de Becarios del Curso
         */
        $mform->addElement('text', 'config_grupoBecas', get_string('grupobecarios', 'block_sence'), ['size' => '10', 'maxlength' => '10']);
        $mform->setType('config_grupoBecas', PARAM_TEXT);
        $mform->setDefault('config_grupoBecas','Becarios');

        /**
         *  Opcional
         */
        $mform->addElement('advcheckbox', 'config_senceTiempoCierre', 'Solicitar cierre de Sesión SENCE' );
        $mform->setDefault('config_senceTiempoCierre', false);

        /**
         *  True: Pedirá asistencia nueva cuando la anterior tenga más de 3 Horas
         *  False: Solicitará una asistencia en toda la vida del curso
         */
        $mform->addElement('advcheckbox', 'config_muestraLogo', 'Mostrar logo SENCE en en Bloque' );
        $mform->setDefault('config_muestraLogo', false);

        /**
         *  Bloquea/Desbloquea el contenido del Curso hasta que se logre una asistencia exitosa.
         */
        // $mform->addElement('advcheckbox', 'config_asistenciaObligatoria', 'Solicitar asistencia obligatoria' );
        // $mform->setDefault('config_asistenciaObligatoria', true);
    }
}