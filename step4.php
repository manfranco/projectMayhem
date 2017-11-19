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
 * Step 4(Confirmation and Action).
 *
 * @package    tool_mayhem
 * @copyright  2017 Proyecto 50
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

header('X-Accel-Buffering: no');

require_login();
admin_externalpage_setup('toolmayhem');

global $SESSION;
$formdata   = isset($SESSION->formdata) ? $SESSION->formdata : optional_param('formdata', false, PARAM_RAW);
$error      = isset($SESSION->error) ? $SESSION->error : optional_param('error', false, PARAM_RAW);
$mode       = isset($SESSION->mode) ? $SESSION->mode : optional_param('mode', false, PARAM_INT);
$folder     = optional_param('folder', false, PARAM_TEXT);
$submitted  = optional_param('submit_button', false, PARAM_RAW);

unset($SESSION->formdata);
unset($SESSION->error);
unset($SESSION->mode);

if (!empty($submitted) && !empty($formdata) && !empty($mode)) { // FORM 4 SUBMITTED.

    if ($submitted == get_string('back', 'tool_mayhem')) { // Button to start over has been pressed.
        unset($SESSION->formdata);
        unset($SESSION->mode);
        unset($SESSION->error);
        $returnurl = new moodle_url('/admin/tool/mayhem/index.php');
        redirect($returnurl);
    }

    if (!empty($error)) {
        echo $OUTPUT->container($error, 'mayhem_myformerror');
    }

    if ($submitted == get_string('confirm', 'tool_mayhem')) {
        if (!isset($mode) || !in_array($mode, array(tool_mayhem_processor::MODE_HIDE,
                                                    tool_mayhem_processor::MODE_ARCHIVE,
                                                    tool_mayhem_processor::MODE_DELETE,
                                                    tool_mayhem_processor::MODE_HIDEEMAIL,
                                                    tool_mayhem_processor::MODE_ARCHIVEEMAIL))) {
            throw new coding_exception('Unknown process mode');
        }

        switch($mode){
            case tool_mayhem_processor::MODE_HIDEEMAIL:
            case tool_mayhem_processor::MODE_ARCHIVEEMAIL:
                echo $OUTPUT->header();
                echo $OUTPUT->heading_with_help(get_string('mayhem', 'tool_mayhem'),
                                                'mayhem',
                                                'tool_mayhem');

                $selected = unserialize($formdata);
                $owners = array();
                foreach ($selected as $s) {
                    $t = explode("_", $s);
                    if (count($t) == 2) { // Both a course and an owner are needed.
                        if (array_key_exists($t[1], $owners)) {
                            $temp = $owners[$t[1]]['courses'];
                            $owners[$t[1]]['courses'] = array_merge($temp, array($t[0] => get_course($t[0])));
                        } else {
                            $owners[$t[1]]['courses'] = array($t[0] => get_course($t[0]));
                            $owners[$t[1]]['user'] = $DB->get_record("user", array("id" => $t[1]));
                        }
                    }
                }

                if (!is_array($owners) || empty($owners)) { // If 0 courses are selected, show message and form again.
                    $SESSION->formdata = $formdata;
                    $SESSION->error = get_string('nousersselected', 'tool_mayhem');
                    $returnurl = new moodle_url('/admin/tool/mayhem/step3.php');
                    redirect($returnurl);
                }
                $processor = new tool_mayhem_processor(array("mode" => $mode, "data" => $owners));
                $processor->execute(tool_mayhem_tracker::OUTPUT_HTML);
                echo $OUTPUT->footer();
                break;
            case tool_mayhem_processor::MODE_HIDE:
            case tool_mayhem_processor::MODE_ARCHIVE:
            case tool_mayhem_processor::MODE_DELETE:
                echo $OUTPUT->header();
                echo $OUTPUT->heading_with_help(get_string('mayhem', 'tool_mayhem'),
                                                'mayhem',
                                                'tool_mayhem');

                $courses = unserialize($formdata);
                if (!is_array($courses) || empty($courses)) { // If 0 courses are selected, show message and form again.
                    $SESSION->formdata = $formdata;
                    $SESSION->error = get_string('nocoursesselected', 'tool_mayhem');
                    $returnurl = new moodle_url('/admin/tool/mayhem/step2.php');
                    redirect($returnurl);
                }
                $processor = new tool_mayhem_processor(array("mode" => $mode, "data" => $courses));
                if (!empty($folder)) {
                    $processor->folder = $folder;
                }
                $processor->execute(tool_mayhem_tracker::OUTPUT_HTML, null);
                echo $OUTPUT->footer();
                break;
            default:
                $SESSION->error = get_string('unknownerror', 'tool_mayhem');
                $returnurl = new moodle_url('/admin/tool/mayhem/index.php');
                redirect($returnurl);
        }
    }

} else if (!empty($formdata) && !empty($mode)) {  // FORM 3 SUBMITTED, SHOW FORM 4.
    echo $OUTPUT->header();
    echo $OUTPUT->heading_with_help(get_string('mayhem', 'tool_mayhem'), 'mayhem', 'tool_mayhem');

    if (!empty($error)) {
        echo $OUTPUT->container($error, 'mayhem_myformerror');
    }

    $param = array("mode" => $mode, "formdata" => $formdata);
    $mform = new tool_mayhem_step4_form(null, array("processor_data" => $param));

    $mform->display();
    echo $OUTPUT->footer();
} else { // IN THE EVENT OF A FAILURE, JUST GO BACK TO THE BEGINNING.
    $SESSION->error = get_string('unknownerror', 'tool_mayhem');
    $returnurl = new moodle_url('/admin/tool/mayhem/index.php');
    redirect($returnurl);
}