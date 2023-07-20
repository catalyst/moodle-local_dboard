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

use local_dboard\event\dboard_created;
use local_dboard\event\dboard_updated;

require_once('../../config.php');

global $DB, $CFG, $USER;

$id        = optional_param('id', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login();
require_capability('local/dboard:managedashboard', context_system::instance());

if ($id > 0) {
    $heading = get_string('edit', 'local_dboard');
} else {
    $heading = get_string('add_new', 'local_dboard');
}
$PAGE->set_context(context_system::instance());
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->set_url('/local/dboard/edit.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add(get_string('manage', 'local_dboard'), '/local/dboard/manage.php');

$PAGE->requires->js_call_amd('local_dboard/icon_picker', 'init');
$PAGE->requires->css('/local/dboard/styles.css');

if ($id > 0) {
    $dashboardsettings = $DB->get_record('local_dboard', array('id' => $id));
    $selectedroles     = $DB->get_records('local_dboard_right', array('objectid' => $id, 'objecttype' => 'dashboard'));
}

$iconname = 't/editstring';
$iconcomp = 'core';
if (isset($dashboardsettings) && !empty($dashboardsettings->icon)) {
    $iconarr  = explode('/', $dashboardsettings->icon, 2);
    $iconname = $iconarr[1];
    $iconcomp = $iconarr[0];
}

$mform = new \local_dboard\form\manage_dashboard_form(null, array('iconname' => $iconname, 'iconcomp' => $iconcomp));

$mform->set_data(array('id' => $id, 'returnurl' => $returnurl));
$redirecturl = new moodle_url('/local/dboard/manage.php');
if ($mform->is_cancelled()) {
    $redirecturl->params(array('returnurl' => $returnurl));
    redirect($redirecturl);
} else if ($data = $mform->get_data()) {

    $redirecturl->params(array('returnurl' => $returnurl));

    if ($id > 0) {
        $insert                 = new stdClass();
        $insert->id             = $dashboardsettings->id;
        $insert->dashboard_name = $data->dashboard_name;
        $insert->layout         = $data->layout;
        $insert->showinmenu     = $data->showinmenu;
        $DB->delete_records('local_dboard_right', array('objecttype' => 'dashboard', 'objectid' => $insert->id));
        if (isset($data->roles)) {
            foreach ($data->roles as $roleid) {
                $role               = new stdClass();
                $role->objecttype   = 'dashboard';
                $role->objectid     = $insert->id;
                $role->roleid       = $roleid;
                $role->timemodified = date("Y-m-d H:i:s");
                $role->usermodified = $USER->id;
                $DB->insert_record('local_dboard_right', $role);
            }
        }
        $insert->icon = $data->icon;
        $insert->contextlevel = $data->contextlevel;

        $newid = $DB->update_record('local_dboard', $insert);

        if ($insert->layout != 'classic') {
            $blocks = $DB->get_records('block_instances',
                array('pagetypepattern' => 'dboard-' . $dashboardsettings->id), 'id');

            foreach ($blocks as $block) {
                $block->defaultregion = 'content';
                $DB->update_record('block_instances', $block);
            }
        } else {
            $blocks = $DB->get_records('block_instances',
                array('pagetypepattern' => 'dboard-' . $dashboardsettings->id), 'id');
            $counter = 0;
            foreach ($blocks as $block) {
                if ($counter % 2 == 0) {
                    $block->defaultregion = 'side-pre';
                    $DB->update_record('block_instances', $block);
                }
                $counter++;
            }
        }

        // Trigger event, dboard dashboard updated.
        $eventparams = array('context' => $PAGE->context, 'objectid' => $id);
        $event = dboard_updated::create($eventparams);
        $event->trigger();

    } else {
        $insert                 = new stdClass();
        $insert->dashboard_name = $data->dashboard_name;
        $insert->layout         = $data->layout;
        $insert->showinmenu     = $data->showinmenu;
        $insert->icon           = $data->icon;
        $insert->contextlevel   = $data->contextlevel;

        $newid = $DB->insert_record('local_dboard', $insert);

        /* Create new access roles, linked to new dashboard.
         *
         * If the $id is 0, there shouldn't be anything here.
         * Leaving delete_records() call in for a sanity check.
         */
        $DB->delete_records('local_dboard_right', array('objecttype' => 'dashboard', 'objectid' => $data->id));
        foreach ($data->roles as $roleid) {
            $role               = new stdClass();
            $role->objecttype   = 'dashboard';
            $role->objectid     = $newid;
            $role->roleid       = $roleid;
            $role->timemodified = date("Y-m-d H:i:s");
            $role->usermodified = $USER->id;
            $DB->insert_record('local_dboard_right', $role);
        }

        // Trigger event, dboard dashboard created.
        $eventparams = array('context' => $PAGE->context, 'objectid' => $newid);
        $event = dboard_created::create($eventparams);
        $event->trigger();
    }
    redirect($redirecturl);

}

echo $OUTPUT->header();

if ($id > 0) {
    $mform->set_data(array(
        'id'             => $id,
        'dashboard_name' => $dashboardsettings->dashboard_name,
        'showinmenu'     => $dashboardsettings->showinmenu,
        'icon'           => $dashboardsettings->icon,
        'roles'          => array_column($selectedroles, 'roleid'),
        'layout'         => $dashboardsettings->layout,
        'contextlevel'   => $dashboardsettings->contextlevel));
}
$mform->display();
echo $OUTPUT->footer();
