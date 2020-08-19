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

defined('MOODLE_INTERNAL') || die();

function local_vxg_dashboard_reset_default_dashboard($dashboardid)
{
    global $CFG, $DB;

    // Default settings
    $def_settings             = new stdClass();
    $def_settings->id         = $dashboardid;
    $def_settings->layout     = 'mydashboard';
    $def_settings->showinmenu = '1';

    $settings_id = $DB->update_record('local_vxg_dashboard', $def_settings);

    local_vxg_dashboard_delete_dashboard_blocks($dashboardid);
}

function local_vxg_dashboard_delete_dashboard_blocks($dashboardid = null)
{
    global $CFG, $DB;

    $select = 'pagetypepattern = ?';
    $params = ['veloxnet-dashboard-' . $dashboardid];

    $blocks = $DB->get_records_select('block_instances', $select, $params);

    foreach ($blocks as $block) {
        blocks_delete_instance($block);
    }
}

// Called when plugin is uninstalled
function local_vxg_dashboard_plugin_uninstall()
{
    global $CFG, $DB;

    $sql_like = $DB->sql_like('pagetypepattern', ':type', false);
    $select   = $sql_like . ' OR pagetypepattern = :manage
                                        OR pagetypepattern = :edit
                                        OR pagetypepattern = :delete';
    $params['type']   = "veloxnet-dashboard-%";
    $params['manage'] = "local-dashboard-manage";
    $params['edit']   = "local-dashboard-edit";
    $params['delete'] = "local-dashboard-delete";

    $blocks = $DB->get_records_select('block_instances', $select, $params);

    foreach ($blocks as $block) {
        blocks_delete_instance($block);
    }
}

function local_vxg_dashboard_get_assignable_roles()
{
    global $DB;

    $role_ids = $DB->get_fieldset_select('role_context_levels', 'DISTINCT roleid',
        'contextlevel = ? OR contextlevel = ? OR contextlevel = ?', array('10', '40', '50'));

    $insql = 'IN (' . implode(',', $role_ids) . ')';

    $sql = 'SELECT id, shortname FROM {role} WHERE id ' . $insql . ' ORDER BY id';

    $role_names = $DB->get_records_sql_menu($sql);

    return $role_names;

}

function local_vxg_dashboard_get_user_role_ids()
{
    global $USER, $COURSE;

    $user_roles = get_user_roles(context_course::instance($COURSE->id), $USER->id);

    $role_names = array();
    foreach ($user_roles as $role) {
        $role_names[] = $role->roleid;
    }

    return $role_names;

}

function local_vxg_dashboard_get_access_roles($dashboardid)
{
    global $DB;
    $roles      = $DB->get_records('local_vxg_dashboard_right', array('objectid' => $dashboardid, 'objecttype' => 'dashboard'));
    $roleids    = array_column($roles, 'roleid');
    $role_names = $DB->get_records_list('role', 'id', $roleids, $sort = '', $fields = 'shortname');
    return implode(', ', array_column($role_names, 'shortname'));

}
