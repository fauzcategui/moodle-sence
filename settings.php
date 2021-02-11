<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $CFG->cache = false;

    $PAGE->requires->js('/blocks/sence/sence.js');

     $settings->add(new admin_setting_configtextarea('sence_block/otecs', 'RUTs y Tokens de Otecs',
    'Ingresar Rut de Otecs Acá', '', PARAM_RAW));

}