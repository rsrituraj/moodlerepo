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
 * This file contains the Activity modules block.
 *
 * @package    course_activity_list
 * @author     Rituraj Saxena <rsrituraj793@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
class block_course_activity_list extends block_list {
    public function init() {
        $this->title = get_string('activity_lists', 'block_course_activity_list');
    }

    public function get_content() {
        global $COURSE, $CFG, $USER, $DB;
        if (isloggedin()) {
            $activityname = '';
            $modinfo = get_fast_modinfo($COURSE->id);
            $this->content = new stdClass();
            $this->content->items = array();
            $this->content->icons = array();
            $this->content->footer = "";
            foreach ($modinfo->cms as $cm) {
                $coursemod = $modinfo->get_cm($cm->id);
                if (!$cm->uservisible or!$cm->has_view()) {
                    continue;
                }
                $url = new moodle_url($CFG->wwwroot . '/mod/' . $coursemod->modname . '/view.php', array('id' => $coursemod->id));
                $activityname = $coursemod->name;
                $cdmid = $coursemod->id;
                $createddate = date('d-M-Y', $coursemod->added);
                $modulecomption = $DB->get_field('course_modules_completion', 'completionstate', array('coursemoduleid' => $cdmid, 'userid' => $USER->id));

                if ($modulecomption == 1) {
		// With completion status
                    $this->content->items[] .= html_writer::link($url, $cdmid.' - '.$activityname.' - '.$createddate. ' ' .get_string('completed', 'block_course_activity_list'));
                } else {
                    $this->content->items[] .= html_writer::link($url, $cdmid.' - '. $activityname .' - '.$createddate);
                }
            }

            if (empty($this->content->items)) {
                $this->content->items[] = get_string('activitynotfound', 'block_course_activity_list');
            }
            return $this->content;
        }
    }

    public function applicable_formats() {
        return array(
            'course-view' => true,
            'course-view-social' => false
        );
    }
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('activity_lists', 'block_course_activity_list');
            } else {
                $this->title = $this->config->title;
            }
            if (empty($this->config->text)) {
                $this->config->text = get_string('activity_lists', 'block_course_activity_list');
            }
        }
    }
}
