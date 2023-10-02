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
 * Index dashboard page.
 *
 * @package   local_dboard
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_dboard\event\dboard_viewed;

require_once('../../config.php');
require_once(__DIR__ . '/locallib.php');

$id = optional_param('id', 0, PARAM_INT);
$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
$context = context::instance_by_id($contextid, MUST_EXIST);

$edit = optional_param('edit', null, PARAM_BOOL); // Turn editing on and off.
$reset = optional_param('reset', null, PARAM_BOOL);
$redirecturl = new moodle_url('/my');

$coursecontext = $context->get_course_context(false);
if (!empty($coursecontext)) {
    require_course_login($coursecontext->instanceid);
} else {
    require_login();
}

if ($id == 0) {
    redirect($redirecturl);
}

$dashboardsettings = $DB->get_record('local_dboard', array('id' => $id));

if ($dashboardsettings->dashboard_name == null && $dashboardsettings->dashboard_name == '') {
    $dashboard = get_string('dashboard', 'local_dboard');
} else {
    $dashboard = $dashboardsettings->dashboard_name;
}
if ($contextid == SYSCONTEXTID) {
    $systemcontextid = 1;
}
$canmanage = has_capability('local/dboard:managedashboard', context_system::instance());

// If a user context level is specified, pick up this user's context by default.
if ($contextid == SYSCONTEXTID && $dashboardsettings->contextlevel == CONTEXT_USER && !$canmanage) {
    $context = context_user::instance($USER->id);
    $contextid = $context->id;
}

// Ensure dashboard contextlevel matches for the supplied ID.
if ($dashboardsettings->contextlevel != $context->contextlevel && !$canmanage) { // Allow admin user to configure.
    redirect($redirecturl, get_string('context_mismatch', 'local_dboard', $dashboard),
        null, \core\output\notification::NOTIFY_ERROR);
}

// Ensure user has access by role in this context.
if (!is_siteadmin() && !local_dboard_user_role_check($id, $contextid)) {
    redirect($redirecturl, get_string('context_norole', 'local_dboard', $dashboard),
        null, \core\output\notification::NOTIFY_ERROR);
}

$userid = $USER->id;
$header = $dashboard;
$pagetitle = $dashboard;
// Start setting up the page.
$params = array('id' => $id);
// Default behaviour for system context; no contextid needed.
if ($contextid != SYSCONTEXTID) {
    $params['contextid'] = $contextid;
}
$PAGE->set_context($context);
$PAGE->set_url('/local/dboard/index.php', $params);

$PAGE->set_pagetype('dboard-' . $dashboardsettings->id);
$PAGE->blocks->add_region('content');
$PAGE->set_title($pagetitle);
if ($PAGE->context->contextlevel == CONTEXT_USER) {
    if ($USER->id == $PAGE->context->instanceid) {
        $header = fullname($USER) . ' - '. $header;
    } else {
        $user = $DB->get_record('user', ['id' => $PAGE->context->instanceid]);
        $header = fullname($user) . ' - '. $header;
    }
} else if ($PAGE->context->contextlevel == CONTEXT_COURSE) {
    $course = get_course($PAGE->context->instanceid);
    $header = format_string($course->shortname, true, array('context' => $PAGE->context)). ' - '. $header;
} else if ($PAGE->context->contextlevel == CONTEXT_MODULE) {
    require_once($CFG->dirroot . '/course/modlib.php');
    $course = get_course($coursecontext->instanceid);
    $cm = get_coursemodule_from_id('', $PAGE->context->instanceid, $coursecontext->instanceid);
    $PAGE->set_cm($cm);
    $header = format_string($course->shortname, true, array('context' => $PAGE->context)). ' - '.
              format_string($cm->name, true, array('context' => $PAGE->context)). ' - '. $header;
}
$PAGE->set_heading($header);
$PAGE->requires->css(new \moodle_url('/local/dboard/styles.css'));

// Allow to configure a dashboard on a system context level only.
if (!isset($systemcontextid)) {
    $PAGE->add_body_class('disable-editmode');
    $USER->editing = 0;
}
// Toggle the editing state and switches.
if ($PAGE->user_allowed_editing() && isset($systemcontextid)) {
    if ($edit !== null) { // Editing state was specified.
        $USER->editing = $edit; // Change editing state.
    }
    // Add button for editing page.
    $params = array('edit' => !$edit);

    $resetbutton = '';
    $resetstring = get_string('resetpage', 'my');
    $reseturl = new moodle_url("/local/dboard/index.php", array('id' => $id, 'contextid' => $contextid,
        'edit' => 1, 'reset' => 1));

    if (has_capability('local/dboard:managedashboard', $context)) {

        if (!isset($USER->editing) || !$USER->editing) {
            $editstring = get_string('updatemymoodleon');
            $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
        } else {
            $editstring = get_string('updatemymoodleoff');
            $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
        }

        $params['id'] = $id;
        if ($contextid != SYSCONTEXTID) {
            $params['contextid'] = $contextid;
        }
        $editurl = new moodle_url("/local/dboard/index.php", $params);
        $editbutton = $OUTPUT->single_button($editurl, $editstring);

        $returnurl = new moodle_url('/local/dboard/index.php', $params);
        $manageurl = new moodle_url("/local/dboard/manage.php", array('returnurl' => $returnurl));
        $managebutton = $OUTPUT->single_button($manageurl, get_string('manage', 'local_dboard'));
        $PAGE->set_button($managebutton . $editbutton);
    }
} else {
    $USER->editing = $edit = 0;
}

if ($dashboardsettings->layout != 'classic') {
    $PAGE->blocks->set_default_region('content');
}

echo $OUTPUT->header();

if (!empty($dashboardsettings->layout)) {
    if ($dashboardsettings->layout == 'col2') {
        echo html_writer::tag('div', $OUTPUT->custom_block_region('content'), array('class' => 'two-block-columns'));
    } else if ($dashboardsettings->layout == 'col3') {
        echo html_writer::tag('div', $OUTPUT->custom_block_region('content'), array('class' => 'three-block-columns'));
    } else if ($dashboardsettings->layout == 'colmore') {
        echo html_writer::tag('div', $OUTPUT->custom_block_region('content'), array('class' => 'auto-fit-block-columns'));
    } else {
        echo $OUTPUT->custom_block_region('content');
    }
} else {
    echo $OUTPUT->custom_block_region('content');
}

// Trigger event, dboard dashboard viewed.
$eventparams = array('context' => $PAGE->context, 'objectid' => $id);
$event = dboard_viewed::create($eventparams);
$event->trigger();

echo $OUTPUT->footer();
