<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $PAGE->requires->js('/blocks/sence/js/settings.js');

    /**
     * Campor de Texto ocultado por JavaScript para enviar el JSON de Otecs a la Base de Datos
     */
    $settings->add(new admin_setting_configtextarea('sence_block/otecs', '', '', '', PARAM_RAW));

}