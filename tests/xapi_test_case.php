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

namespace tests;
defined('MOODLE_INTERNAL') || die();

use \PHPUnit_Framework_TestCase as PhpUnitTestCase;
use \Locker\XApi\Statement as LockerStatement;

abstract class xapi_test_case extends PhpUnitTestCase {

    abstract protected function get_test_dir();

    protected function get_test_data() {
        return json_decode(file_get_contents($this->get_test_dir().'/data.json'));
    }

    protected function get_event() {
        return json_decode(file_get_contents($this->get_test_dir().'/event.json'));
    }

    protected function get_expected_statements() {
        return file_get_contents($this->get_test_dir().'/statements.json');
    }

    public function test_create_event() {
        $event = $this->get_event();
        $logerror = function ($message = '') {
            echo("ERROR: $message\n");
        };
        $loginfo = function ($message = '') {
            echo("INFO: $message\n");
        };
        $handlerconfig = [
            'log_error' => $logerror,
            'log_info' => $loginfo,
            'transformer' => $this->get_transformer_config(),
            'loader' => [
                'loader' => 'none',
                'lrs_endpoint' => '',
                'lrs_username' => '',
                'lrs_password' => '',
                'lrs_max_batch_size' => 1,
            ],
        ];
        $loadedevents = \src\handler($handlerconfig, [$event]);
        $statements = array_reduce($loadedevents, function ($result, $loadedevent) {
            $eventstatements = $loadedevent['statements'];
            return array_merge($result, $eventstatements);
        }, []);
        $this->assert_expected_statements($statements);
        foreach ($statements as $statement) {
            $this->assert_valid_xapi_statement($statement);
        }
    }

    protected function get_transformer_config() {
        $testdata = $this->get_test_data();
        return [
            'source_url' => 'http://moodle.org',
            'source_name' => 'Moodle',
            'source_version' => '1.0.0',
            'source_lang' => 'en',
            'send_mbox' => false,
            'send_response_choices' => false,
            'send_short_course_id' => false,
            'send_course_and_module_idnumber' => false,
            'send_username' => false,
            'send_jisc_data' => false,
            'session_id' => 'test_session_id',
            'plugin_url' => 'https://github.com/xAPI-vle/moodle-logstore_xapi',
            'plugin_version' => '0.0.0-development',
            'repo' => new \src\transformer\repos\TestRepository($testdata),
            'app_url' => 'http://www.example.org',
        ];
    }

    private function assert_valid_xapi_statement($statement) {
        $errors = LockerStatement::createFromJson(json_encode($statement))->validate();
        $errorsjson = json_encode(array_map(function ($error) {
            return (string) $error;
        }, $errors));
        $this->assertEmpty($errors, $errorsjson);
    }

    private function assert_expected_statements($statements) {
        $expectedstatements = $this->get_expected_statements();
        $actualstatements = json_encode($statements, JSON_PRETTY_PRINT);
        $this->assertEquals($expectedstatements, $actualstatements);
    }
}