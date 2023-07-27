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
 * @package    local_dboard
 * @author     Alex Morris alex.morris@catalyst.net.nz
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dboard\event;

/**
 * The dashboard updated event class.
 *
 * @package     local_dboard
 * @copyright   2021 Alex Morris
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dboard_updated extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'local_dboard';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventdboardupdated', 'local_dboard');
    }

    /**
     * Returns non-localised event description with id's.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' updated the dboard dashboard with id `$this->objectid`.";
    }
}
