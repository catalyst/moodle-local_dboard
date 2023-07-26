<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade steps for the local_dboard plugin.
 *
 * @package   local_dboard
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_dboard_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021102100) {

        // Define field contextlevel to be added to local_dboard.
        $table = new xmldb_table('local_dboard');
        $field = new xmldb_field('contextlevel', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '10', 'lang');

        // Conditionally launch add field contextlevel.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // dboard savepoint reached.
        upgrade_plugin_savepoint(true, 2021102100, 'local', 'dboard');
    }

}
