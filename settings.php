<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_sence_token', 'Token Sence',
                       'Token generado en la web SENCE', '', PARAM_RAW));

    $settings->add(new admin_setting_configtext('block_sence_rut', 'RUT OTEC',
                       'RUT de la OTEC Registrada en SENCE', '', PARAM_RAW));

}