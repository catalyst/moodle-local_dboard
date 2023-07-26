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
 * Global plugin functions.
 *
 * @package   local_dboard
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get dashboard id for local_dboard.
 *
 * @param int $dashboardid dashboard id
 */
function local_dboard_reset_default_dashboard($dashboardid) {
    global $CFG, $DB;

    // Default settings.
    $defsettings             = new stdClass();
    $defsettings->id         = $dashboardid;
    $defsettings->layout     = 'mydashboard';
    $defsettings->showinmenu = '1';

    $settingsid = $DB->update_record('local_dboard', $defsettings);

    local_dboard_delete_dashboard_blocks($dashboardid);
}

/**
 * Delete dashboard blocks in local_dboard.
 *
 * @param int|null $dashboardid dashboard id
 */
function local_dboard_delete_dashboard_blocks($dashboardid = null) {
    global $CFG, $DB;

    $select = 'pagetypepattern = ?';
    $params = ['dboard-' . $dashboardid];

    $blocks = $DB->get_records_select('block_instances', $select, $params);

    foreach ($blocks as $block) {
        blocks_delete_instance($block);
    }
}

// Called when plugin is uninstalled.
function local_dboard_plugin_uninstall() {
    global $CFG, $DB;

    $likesql = $DB->sql_like('pagetypepattern', ':type', false);
    $select   = $likesql . ' OR pagetypepattern = :manage
                                        OR pagetypepattern = :edit
                                        OR pagetypepattern = :delete';
    $params['type']   = "dboard-%";
    $params['manage'] = "local-dashboard-manage";
    $params['edit']   = "local-dashboard-edit";
    $params['delete'] = "local-dashboard-delete";

    $blocks = $DB->get_records_select('block_instances', $select, $params);

    foreach ($blocks as $block) {
        blocks_delete_instance($block);
    }
}

/**
 * Get assignable roles in local_dboard.
 *
 * @return array of role ids and names.
 */
function local_dboard_get_assignable_roles() {
    global $DB;

    $roleids = $DB->get_fieldset_select('role_context_levels', 'DISTINCT roleid',
        'contextlevel = ? OR contextlevel = ? OR contextlevel = ?', array('10', '40', '50'));

    $insql = 'IN (' . implode(',', $roleids) . ')';

    $sql = 'SELECT id, shortname FROM {role} WHERE id ' . $insql . ' ORDER BY id';

    $rolenames = $DB->get_records_sql_menu($sql);

    return $rolenames;

}

/**
 * Get role IDs for dashboard user.
 *
 * @param int|null $contextid (optional) Get role IDs within a specific context.
 * @return array An array of role IDs.
 */
function local_dboard_get_user_role_ids($contextid=null) {
    global $USER, $COURSE;

    if (empty($contextid)) {
        // Default to using current course context.
        $userroles = get_user_roles(context_course::instance($COURSE->id), $USER->id);
    } else {
        $userroles = get_user_roles(context::instance_by_id($contextid), $USER->id);
    }

    $rolenames = array();
    foreach ($userroles as $role) {
        $rolenames[] = $role->roleid;
    }

    return $rolenames;

}

/**
 * Get access rolesfor dashboard user.
 *
 * @param int $dashboardid.
 * @return array An array of role names.
 */
function local_dboard_get_access_roles($dashboardid) {
    global $DB;
    $roles      = $DB->get_records('local_dboard_right', array('objectid' => $dashboardid, 'objecttype' => 'dashboard'));
    $roleids    = array_column($roles, 'roleid');
    $rolenames = $DB->get_records_list('role', 'id', $roleids, $sort = '', $fields = 'shortname');
    return implode(', ', array_column($rolenames, 'shortname'));

}

/**
 * Determine whether the current user has any valid access role for the dashboard.
 *
 * @param int $dashboardid
 * @param int $contextid Check in a specific context.
 * @return bool Whether the user has a role
 */
function local_dboard_user_role_check($dashboardid, $contextid=SYSCONTEXTID) {
    global $DB, $USER;

    $dashboardroles = $DB->get_records('local_dboard_right',
        array('objectid' => $dashboardid, 'objecttype' => 'dashboard'));

    $userroles = local_dboard_get_user_role_ids($contextid);

    if (!empty($dashboardroles)) {
        foreach ($dashboardroles as $dashboardrole) {
            if (in_array($dashboardrole->roleid, $userroles)) {
                return true;  // User has role.
            }
        }
    } else {
        return true;  // No role restriction.
    }

    return false;  // No role match found.
}
