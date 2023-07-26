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
 * Delete dashboard page.
 *
 * @package   local_dboard
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_dboard\event\dboard_deleted;

require_once('../../config.php');
require_once(__DIR__ . '/locallib.php');

global $DB;

$id        = required_param('id', PARAM_INT);
$delete    = optional_param('delete', false, PARAM_BOOL);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

if (!empty($returnurl)) {
    // Unescape any ampersands, etc.
    $returnurl = htmlspecialchars_decode($returnurl);
}

require_login();
require_sesskey();
require_capability('local/dboard:managedashboard', context_system::instance());

$heading = get_string('delete', 'local_dboard');
$PAGE->set_context(context_system::instance());
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->set_url('/local/dboard/delete.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add(get_string('manage', 'local_dboard'), '/local/dboard_users/index.php');

$dashboard   = $DB->get_record('local_dboard', array('id' => $id));
$redirecturl = new moodle_url('/local/dboard/manage.php', array('returnurl' => $returnurl));

if ($delete) {
    $DB->delete_records('local_dboard', array('id' => $id));
    local_dboard_delete_dashboard_blocks($id);
    // Trigger event, dboard dashboard deleted.
    $eventparams = array('context' => $PAGE->context, 'objectid' => $id);
    $event = dboard_deleted::create($eventparams);
    $event->trigger();
    redirect($redirecturl);
}

echo $OUTPUT->header();
echo $OUTPUT->confirm(get_string('delete_confirm', 'local_dboard', $dashboard->dashboard_name),
    new moodle_url('/local/dboard/delete.php', array('id' => $id, 'delete' => true, 'sesskey' => sesskey())),
    new moodle_url('/local/dboard/manage.php', array('returnurl' => $returnurl)));
echo $OUTPUT->footer();
