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
 * Creates settings and links to Rose-Hulman Course Archive tool.
 *
 * @package    tool_mayhem
 * @copyright  2015 Proyecto 50
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_mayhem', get_string('mayhem_settings', 'tool_mayhem'));

    $name = new lang_string('mayhempath', 'tool_mayhem');
    $description = new lang_string('mayhempath_help', 'tool_mayhem');
    $default = 'CourseArchives';
    $settings->add(new admin_setting_configtext('tool_mayhem/mayhempath',
                                                $name,
                                                $description,
                                                $default));

    // Default email for upcoming hiding of courses.
    $name = new lang_string('hidewarningemailsetting', 'tool_mayhem');
    $description = new lang_string('hidewarningemailsetting_help', 'tool_mayhem');
    $default = get_string('hidewarningemailsettingdefault', 'tool_mayhem');
    $settings->add(new admin_setting_configtextarea('tool_mayhem/hidewarningemailsetting',
                                                    $name,
                                                    $description,
                                                    $default));

    // Default email for upcoming course archiving.
    $name = new lang_string('archivewarningemailsetting', 'tool_mayhem');
    $description = new lang_string('archivewarningemailsetting_help', 'tool_mayhem');
    $default = get_string('archivewarningemailsettingdefault', 'tool_mayhem');
    $settings->add(new admin_setting_configtextarea('tool_mayhem/archivewarningemailsetting',
                                                    $name,
                                                    $description,
                                                    $default));
    // Automatic opt out in months.
    $settings->add(new admin_setting_configtext('tool_mayhem/optoutmonthssetting',
                   get_string('optoutmonthssetting', 'tool_mayhem'),
                   get_string('optoutmonthssetting_help', 'tool_mayhem'), 24, PARAM_INT));

    // Link to Course Archiver tool.
    $ADMIN->add('courses', new admin_externalpage('toolmayhem',
        get_string('mayhem', 'tool_mayhem'), "$CFG->wwwroot/$CFG->admin/tool/mayhem/index.php"));

    // Add the category to the admin tree.
    $ADMIN->add('tools', $settings);
}
