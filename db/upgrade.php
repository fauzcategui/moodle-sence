<?php
/**
 * File containing Sence Module Block class.
 *
 * @package    block_sence
 * @copyright  2020 onwards Felipe Uzcátegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the HTML block.
 *
 * @param int $oldversion
 */
function xmldb_block_sence_upgrade($oldversion) {
    global $CFG, $DB;
    $DB->delete_records( 'config_plugins', ['plugin' => 'sence_block'] );
    $DB->delete_records( 'config_plugins', ['plugin' => 'block_sence'] );
    return $DB->insert_record('config_plugins',[
        'plugin' => 'block_sence',
        'name' => 'otecs',
        'value' => json_encode( ['multiotec' => false,'otecs' => [] ])
    ]);

    return true;
}
