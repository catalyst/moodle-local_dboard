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
 * Language strings for the local_dboard plugin.
 *
 * @package   local_dboard
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['pluginname']                    = 'Dashboard';
$string['dboard:managedashboard'] = 'Manage dashboard';
$string['privacy:metadata']              = 'The Dashboard plugin does not store any personal data.';
$string['dashboard']                     = 'Dashboards';
$string['name']                          = 'Name';
$string['icon']                          = 'Icon';

$string['roles']          = 'Access roles';
$string['layout']         = 'Layout';
$string['layout_help']    = 'How many blocks should be in a row?';
$string['manage']         = 'Manage dashboards';
$string['showinmenu']     = 'Show in system menu';
$string['add_new']        = 'New dashboard';
$string['edit']           = 'Edit dashboard';
$string['delete']         = 'Delete';
$string['delete_confirm'] = 'Are you sure you want to delete this dashboard: {$a}';
$string['back']           = 'Back';
$string['classic']        = 'Classic';
$string['col2']           = 'Two Columns';
$string['col3']           = 'Three Columns';
$string['colmore']        = 'As many as it fit';

// Events.
$string['eventdboardviewed'] = 'Dashboard viewed';
$string['eventdboardcreated'] = 'Dashboard created';
$string['eventdboarddeleted'] = 'Dashboard deleted';
$string['eventdboardupdated'] = 'Dashboard updated';

// Icon-selection.
$string['select-icon']   = 'Choose Icon';
$string['iconselection'] = 'Icon selection';

// Context level setting.
$string['contextlevel']      = 'Context level';
$string['contextlevel_help'] = 'The context level at which this dashboard will be made available. A "contextid" value should be passed through for all dashboards with a context below system level.';
$string['context_mismatch']  = 'The dashboard "{$a}" is not available in the specified context.';
$string['context_norole']    = 'The dashboard "{$a}" is unavailable to users with your role(s). Please contact your site administrator if you believe this is in error.';

$string['privacy:metadata:local_dboard_right'] = 'The dashboard';
$string['privacy:metadata:local_dboard_right:userid'] = 'The userid of the dashboard';
$string['privacy:metadata:local_dboard_right:roleid'] = 'The roleid of the dashboard';
$string['privacy:metadata:local_dboard_right:children'] = 'The children of the dashboard';
$string['privacy:metadata:local_dboard_right:righttype'] = 'The righttype of the dashboard';
$string['privacy:metadata:local_dboard_right:timemodified'] = 'The timemodified of the dashboard';
$string['privacy:metadata:local_dboard_right:usermodified'] = 'The usermodified of the dashboard';
