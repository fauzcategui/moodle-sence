<?php

class block_sence_edit_form extends block_edit_form {

    protected function specific_definition($mform){

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Código Token
        $mform->addElement('text', 'config_token', 'Token SENCE');
        $mform->setDefault('config_token', '');
        $mform->setType('config_token', PARAM_RAW);

        // RUT Otec
        $mform->addElement('text', 'config_rut', 'RUT Otec');
        $mform->setDefault('config_rut', '');
        $mform->setType('config_rut', PARAM_RAW);

        // Código Token
        $mform->addElement('text', 'config_lineacap', 'Linea de Capacitación');
        $mform->setDefault('config_lineacap', '');
        $mform->setType('config_lineacap', PARAM_RAW);


    }
}