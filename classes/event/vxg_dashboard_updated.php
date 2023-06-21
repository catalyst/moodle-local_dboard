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
 * The dashboard updated event.
 *
 * @package    local_vxg_dashboard
 * @copyright  2021 Alex Morris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_vxg_dashboard\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The dashboard updated event class.
 *
 * @package     local_vxg_dashboard
 * @copyright   2021 Alex Morris
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vxg_dashboard_updated extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'local_vxg_dashboard';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('eventvxgdashboardupdated', 'local_vxg_dashboard');
    }

    public function get_description() {
        return "The user with id '$this->userid' updated the vxg dashboard with id `$this->objectid`.";
    }
}
