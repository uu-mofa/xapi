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

namespace src\transformer\events\core;

defined('MOODLE_INTERNAL') || die();

use src\transformer\utils as utils;

function user_loggedout(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $user = $repo->read_record_by_id('user', $event->userid);
    $lang = $config['source_lang'];

    return [[
        'actor' => utils\get_user($config, $user),
        'verb' => [
            'id' => 'https://brindlewaye.com/xAPITerms/verbs/loggedout/',
            'display' => [
                $lang => 'logged out of'
            ],
        ],
        'object' => utils\get_activity\site($config),
        'timestamp' => utils\get_event_timestamp($event),
        'context' => [
            'platform' => $config['source_name'],
            'language' => $lang,
            'extensions' => utils\extensions\base($config, $event, null),
            'contextActivities' => [
                'category' => [
                    utils\get_activity\source($config)
                ]
            ],
        ]
    ]];
}
