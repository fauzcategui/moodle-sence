<?php

class block_sence_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $PAGE;

        $PAGE->requires->js('/blocks/sence/js/edit_form.js');

        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $attributes = ['size' => '10', 'maxlength' => '10'];

        $otecs = Engine::get_otecs();

        $mform->addElement('select', 'config_otec', 'Selecciona OTEC', $otecs);

        $lineascap = [
            1 => 'Programas Sociales o Becas Labores (1)',
            3 => 'Impulsa Personas (3)',
        ];

        $mform->addElement('select', 'config_lineadecap', get_string('lineadecap', 'block_sence'), $lineascap);

        $mform->disabledIf('config_codigocurso', 'config_lineasdecap', 'eq', 1);

        $mform->addElement('text', 'config_codigocurso', get_string('codigocurso', 'block_sence'), $attributes);
        $mform->setType('config_codigocurso', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'config_bloqueacurso', get_string('permit', 'block_sence') );

        $mform->addElement('textarea', 'config_alumnos', get_string('confalumnos', 'block_sence'), 'wrap="virtual" rows="8" cols="50"');
        $mform->setType('config_alumnos', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'config_senceRedirect', 'Redireccionar Alumno a otra plataforma una vez iniciado sesión' );

    }
}