
<?php
// This file is part of
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
 * Step 2 form.
 *
 * @package    tool_mayhem
 * @copyright  2017 Proyecto 50
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle form for step 2 of course archive tool
 *
 * @package    tool_mayhem
 * @copyright  2017 Proyecto 50
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_mayhem_step2_form extends moodleform {

    /**
     * The standard form definiton.
     * @return void.
     */
    public function definition () {
        $mform = $this->_form;
        $mform->addElement('submit', 'submit_button', get_string('back', 'tool_mayhem'));
        $data  = $this->_customdata['processor_data'];

        $mform->addElement('hidden', 'formdata');
        $mform->setType('formdata', PARAM_RAW);
        $mform->setDefault('formdata', serialize($data['searches']));

 
        // Do search here and display results.
        $processor = new tool_mayhem_processor(array("mode" => $data["mode"], "data" => $data["searches"]));
        $processor->execute(tool_mayhem_tracker::OUTPUT_HTML, null, $mform, $this);

        $mform->addElement('filemanager', 'attachments', '', null,
                           array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 50,
                                 'accepted_types' => array('document'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));

        if ($processor->total > 0) {
            $buttonarray = array();
            $buttonarray[] = &$mform->createElement('submit', 'submit_button', get_string('hide', 'tool_mayhem'));
            $buttonarray[] = &$mform->createElement('submit', 'submit_button', 'Upload content');
            $buttonarray[] = &$mform->createElement('submit', 'submit_button', get_string('delete', 'tool_mayhem'));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');

            $savearray = array();
            $savearray[] = &$mform->createElement('text', 'save_title');
            $savearray[] = &$mform->createElement('submit', 'submit_button', get_string('save', 'tool_mayhem'));
            $mform->addGroup($savearray, 'savear', '', array(' '), false);
            $mform->closeHeaderBefore('savear');
            $mform->setType('save_title', PARAM_TEXT);
            $mform->setDefault('save_title', get_string('step2savetitle', 'tool_mayhem', date('l jS \of F Y h:i:s A')));
        }

        $this->set_data($data);
    }
}
