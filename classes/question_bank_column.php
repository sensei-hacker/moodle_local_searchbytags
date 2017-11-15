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

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');


class local_searchbytags_question_bank_column extends core_question\bank\column_base {

    protected function get_classes() {
        $classes = $this->get_extra_classes();
        $classes[] = get_class($this);
        return implode(' ', $classes);
    }

    public function get_name() {
        return 'local_searchbytags|tags';
    }

    protected function get_title() {
        return get_string('tags');
    }

    protected function display_content($question, $rowclasses) {
        if (!empty($question->tags)) {
            echo rtrim(rtrim($question->tags, ','));
        }
    }

    public function get_extra_joins() {
        return array();
    }

    public function get_required_fields() {
        switch($CFG->dbtype) {
            case 'sqlsrv':
            case 'mssql':
                return array("(SELECT tag.name + ', ' FROM {tag} tag 
                              LEFT JOIN {tag_instance} tagi ON tag.id=tagi.tagid 
                              WHERE tagi.itemid=q.id FOR XML PATH('')) AS tags");
            case 'psql':
                return array("(SELECT STRING_AGG(name, ', ') AS tags FROM (SELECT t.name FROM {tag} t LEFT_JOIN {tag_instance} ti ON t.id = ti.tagid WHERE ti.itemid = q.id ORDER BY t.name) AS name)");
            case 'mysqli':
            case 'mariadb':
                return array("(SELECT GROUP_CONCAT(name) AS tags FROM {tag_instance} ti LEFT JOIN {tag} t ON t.id = ti.tagid WHERE itemid = q.id)");
            default:
                die("Don't know how to handle dbtype '$CFG->dbtype'");
        }
    }
}

