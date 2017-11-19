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
 * Optout List.
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

global $SESSION, $OUTPUT;

$context = context_system::instance();
$PAGE->set_url(new \moodle_url('/admin/tool/mayhem/optoutlist.php'));
$PAGE->navbar->add(get_string('mayhem', 'tool_mayhem'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('mayhem', 'tool_mayhem'));

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('mayhem', 'tool_mayhem'), 'mayhem', 'tool_mayhem');

$list = tool_mayhem_processor::get_optoutlist();

echo $list;

echo $OUTPUT->footer();