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

defined('MOODLE_INTERNAL') || die;

// Category
// $ADMIN->add('admin', new admin_category('local_vxg_dashboard',
//     get_string('dashboard', 'local_vxg_dashboard')));

$returnurl = new moodle_url('/admin/search.php');
$settingurl = new moodle_url('/local/vxg_dashboard/manage.php', array('returnurl' => $returnurl));
$managesettings = new admin_externalpage('local_vxg_dashboard',
    get_string('dashboard', 'local_vxg_dashboard'), $settingurl);

$ADMIN->add('root', $managesettings);
