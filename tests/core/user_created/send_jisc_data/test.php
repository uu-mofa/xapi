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

namespace tests\core\user_created\send_jisc_data;
defined('MOODLE_INTERNAL') || die();

class test extends \tests\xapi_test_case {
    protected function get_test_dir() {
        return __DIR__;
    }

    protected function get_transformer_config() {
        $testdata = $this->get_test_data();
        $transformerconfig = parent::get_transformer_config();
        return array_merge($transformerconfig, [
            'send_jisc_data' => true,
        ]);
    }
}