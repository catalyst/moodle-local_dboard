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

require_once(__DIR__ . '/locallib.php');

function local_vxg_dashboard_extend_settings_navigation(settings_navigation $settingsnav, context $context)
{
    return; // Not used anymore!
}

function local_vxg_dashboard_extend_navigation(global_navigation $nav)
{
    global $CFG, $PAGE, $USER, $DB;

    $dashboard_settings = $DB->get_records('local_vxg_dashboard');

    foreach ($dashboard_settings as $dashboard_setting) {

        if ($dashboard_setting->showinmenu == '0' || $dashboard_setting->showinmenu == null) {
            continue;
        }

        // Get roles for dashboard
        $user_roles      = local_vxg_dashboard_get_user_role_ids();
        $dashboard_roles = array();
        $dashboard_roles = $DB->get_records('local_vxg_dashboard_right', array('objectid' => $dashboard_setting->id, 'objecttype' => 'dashboard'));
        // Chech user has roles
        $user_hasrole = false;
        if (!empty($dashboard_roles)) {
            foreach ($dashboard_roles as $dashboard_role) {
                if (in_array($dashboard_role->roleid, $user_roles)) {

                    $user_hasrole = true;
                    continue;
                }
            }
        } else {
            $user_hasrole = true;

        }

        $iconarr = explode('/', $dashboard_setting->icon, 2);
        // Set attributes
        if ($dashboard_setting->dashboard_name == null && $dashboard_setting->dashboard_name == '') {
            $name = get_string('dashboard', 'local_vxg_dashboard');
        } else {
            $name = $dashboard_setting->dashboard_name;
        }
        $url = new moodle_url('/local/vxg_dashboard/index.php', array('id' => $dashboard_setting->id));
        if (isset($dashboard_setting->icon) && !empty($dashboard_setting->icon)) {
            $icon = new pix_icon($iconarr[1], $name, $iconarr[0]);
        } else {
            $icon = new pix_icon('t/editstring', $name);
        }

        // Create node
        $newnode = navigation_node::create(
            $name,
            $url,
            navigation_node::NODETYPE_LEAF,
            $name,
            'vxg_dashboard' . $dashboard_setting->id,
            $icon
        );

        // Make visible in flatnav
        $newnode->showinflatnavigation = true;

        if (isloggedin() && $user_hasrole || is_siteadmin()) {
            $nav->add_node($newnode);
        }

        if ($PAGE->url->compare($url, URL_MATCH_PARAMS)) {
            $newnode->make_active();
        }

    }

}