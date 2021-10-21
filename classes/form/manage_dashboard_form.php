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

namespace local_vxg_dashboard\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/vxg_dashboard/locallib.php');

class manage_dashboard_form extends \moodleform
{

    public function definition() {
        global $CFG, $OUTPUT;

        $mform = $this->_form;

        $roles    = local_vxg_dashboard_get_assignable_roles();
        $size     = count($roles);
        $iconname = $this->_customdata['iconname'];
        $iconcomp = $this->_customdata['iconcomp'];

        $styles = array('style' => 'width:50%;');
        $mform->addElement('text', 'dashboard_name', get_string('name', 'local_vxg_dashboard'), $styles);
        $mform->setType('dashboard_name', PARAM_TEXT);

        $coltwotring   = get_string('col2', 'local_vxg_dashboard');
        $colthreetring = get_string('col3', 'local_vxg_dashboard');
        $colmoretring  = get_string('colmore', 'local_vxg_dashboard');
        $classictring  = get_string('classic', 'local_vxg_dashboard');

        $layouts = [
            'classic' => $classictring,
            'col2'    => $coltwotring,
            'col3'    => $colthreetring,
            'colmore' => $colmoretring];

        $select = $mform->addElement('select', 'layout', get_string('layout', 'local_vxg_dashboard'), $layouts);
        $mform->addHelpButton('layout', 'layout', 'local_vxg_dashboard');
        $select->setSize(4);
        $select->setSelected('classic');

        $icongroup   = array();
        $icongroup[] = &$mform->createElement('html', $OUTPUT->pix_icon($iconname, 'icon', $iconcomp,
            array('class' => 'selected_icon')));
        $icongroup[] = &$mform->createElement('html', '<button type="button" class="btn btn-primary" data-key="icon_picker">' .
            get_string('select-icon', 'local_vxg_dashboard') . '</button>');
        $mform->addGroup($icongroup, 'icongroup', get_string('icon', 'local_vxg_dashboard'), ' ', false);

        $mform->addElement('hidden', 'icon', $iconcomp . '/' . $iconname);
        $mform->setType('icon', PARAM_RAW);

        $mform->addElement('advcheckbox', 'showinmenu', get_string('showinmenu', 'local_vxg_dashboard'));
        $mform->setType('showinmenu', PARAM_INT);

        $select = $mform->addElement('select', 'roles',
            get_string('roles', 'local_vxg_dashboard'), $roles);
        $select->setMultiple(true);
        $select->setSize($size);

        // Context helper maps context level numbers into human language strings.
        $contextlevels = \context_helper::get_all_levels();
        array_walk($contextlevels, function(&$labelvalue, $levelkey) {
            $labelvalue = \context_helper::get_level_name($levelkey);
        });

        $mform->addElement('select', 'contextlevel',
            get_string('contextlevel', 'local_vxg_dashboard'), $contextlevels);
        $mform->setDefault('contextlevel', CONTEXT_SYSTEM);
        $mform->setType('contextlevel', PARAM_INT);
        $mform->addHelpButton('contextlevel', 'contextlevel', 'local_vxg_dashboard');

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'returnurl', 0);
        $mform->setType('returnurl', PARAM_LOCALURL);

        $this->add_action_buttons();

    }

}
