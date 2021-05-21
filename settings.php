<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $PAGE->requires->js('/blocks/sence/js/settings.js');

    /**
     * Campo de Texto ocultado por JavaScript para enviar el JSON de Otecs a la Base de Datos
     */
    $settings->add(new admin_setting_configtextarea('block_sence/otecs', '', '', '', PARAM_RAW));

    // $settings->add(new admin_setting_heading('block_sence/devzone', 'Ambiente de Pruebas SENCE', 'SOLO PARA REALIZAR PRUEBAS (NO MODIFICAR SI NO ESTA SEGURO)'));

    // $settings->add(new admin_setting_configcheckbox('block_sence/testenv', 'Activar ambiente de pruebas', '' , '0' , '1', '0' ));

}