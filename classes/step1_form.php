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
 * Step 1 form.
 *
 * @package    tool_mayhem
 * @copyright  2017 Proyecto 50
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Moodle form for step 1 of course archive tool.
 *
 * @package    tool_mayhem
 * @copyright  2017 Proyecto 50
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_mayhem_step1_form extends moodleform {

    /**
     * The standard form definiton.
     * @return void
     */
    public function definition () {
        $mform = $this->_form;
        $mform->addElement('header', 'searchhdr', 'Search');

        $mform->addElement('select',
                           'savestates',
                           get_string('resume', 'tool_mayhem'),
                           tool_mayhem_processor::get_saves());

        $mform->addElement('text', 'searches[short]', get_string('courseshortname', 'tool_mayhem'));
        $mform->setType('searches[short]', PARAM_TEXT);
        $mform->setDefault('searches[short]', "");

        $mform->addElement('text', 'searches[full]', get_string('coursefullname', 'tool_mayhem'));
        $mform->setType('searches[full]', PARAM_TEXT);
        $mform->setDefault('searches[full]', "");

        $mform->addElement('text', 'searches[idnum]', get_string('courseidnum', 'tool_mayhem'));
        $mform->setType('searches[idnum]', PARAM_TEXT);
        $mform->setDefault('searches[idnum]', "");

        $mform->addElement('text', 'searches[id]', get_string('courseid', 'tool_mayhem'));
        $mform->setType('searches[id]', PARAM_TEXT);
        $mform->addRule('searches[id]', null, 'numeric', null, 'client');
        $mform->setDefault('searches[id]', "");

        $mform->addElement('text', 'searches[teacher]', get_string('courseteacher', 'tool_mayhem'));
        $mform->setType('searches[teacher]', PARAM_TEXT);
        $mform->setDefault('searches[teacher]', "");

        $displaylist = array(get_string('anycategory', 'tool_mayhem'));
        $displaylist += coursecat::make_categories_list('moodle/course:create');
        $mform->addElement('select', 'searches[catid]', get_string('category', 'tool_mayhem'), $displaylist);
        $mform->setDefault('searches[catid]', "");

        $createdbefore = array();
        $createdbefore[] =& $mform->createElement('date_selector', 'createdbefore');
        $createdbefore[] =& $mform->createElement('checkbox', 'createdbeforeenabled', '', get_string('enable'));
        $mform->addGroup($createdbefore, 'createdbefore', get_string('createdbefore', 'tool_mayhem'), ' ', false);
        $mform->disabledIf('createdbefore', 'createdbeforeenabled');

        $lastaccessgroup = array();
        $lastaccessgroup[] =& $mform->createElement('date_selector', 'access');
        $lastaccessgroup[] =& $mform->createElement('checkbox', 'lastaccessenabled', '', get_string('enable'));
        $mform->addGroup($lastaccessgroup, 'lastaccessgroup', get_string('access', 'tool_mayhem'), ' ', false);
        $mform->disabledIf('lastaccessgroup', 'lastaccessenabled');

        $mform->addElement('checkbox', 'emptyonly', get_string('emptyonly', 'tool_mayhem'));
        $this->add_action_buttons(false, get_string('search', 'tool_mayhem'));
        $this->add_action_buttons(false, get_string('optoutlist', 'tool_mayhem'));
    }

    /**
     * Validate search form.
     *
     * @param array $data array of form field data.
     * @param array $files optional form file uploads.
     * @return void
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $searchstring = "";
        $timecode = "";

        if (empty($data["savestates"])) {
            foreach ($data["searches"] as $value) {
                $searchstring .= $value;
            }

            if (!empty($data["createdbeforeenabled"])) {
                $timecode = mktime(null, null, null, $data["createdbefore"]["month"],
                                   $data["createdbefore"]["day"], $data["createdbefore"]["year"]);
            }
            $searchstring .= $timecode;

            if (!empty($data["lastaccessenabled"])) {
                $timecode = mktime(null, null, null, $data["access"]["month"],
                                   $data["access"]["day"], $data["access"]["year"]);
            }
            $searchstring .= $timecode;

            if (!empty($data["emptyonly"])) {
                $searchstring .= "emptyonly";
            }

            if (empty($searchstring)) {
                $errors['step'] = get_string('erroremptysearch', 'tool_mayhem');
            }
        }
        return $errors;
    }
}
