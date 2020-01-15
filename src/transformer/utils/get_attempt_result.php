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

namespace src\transformer\utils;
defined('MOODLE_INTERNAL') || die();

function get_attempt_result(array $config, $attempt, $gradeitem, $attemptgrade) {
    $gradesum = floatval(isset($attemptgrade->rawgrade) ? $attemptgrade->rawgrade : 0);

    $minscore = floatval($gradeitem->grademin ?: 0);
    $maxscore = floatval($gradeitem->grademax ?: 0);
    $passscore = floatval($gradeitem->gradepass ?: 0);

    $rawscore = cap_raw_score($gradesum, $minscore, $maxscore);
    $scaledscore = get_scaled_score($rawscore, $minscore, $maxscore);

    $completed = isset($attempt->state) ? $attempt->state === 'finished' : false;
    $success = $gradesum >= $passscore;
    $duration = get_attempt_duration($attempt);

    $result = [
        'score' => [
            'raw' => $rawscore,
            'min' => $minscore,
            'max' => $maxscore,
            'scaled' => $scaledscore,
        ],
        'completion' => $completed,
        'success' => $success,
    ];

    if ($duration != null) {
        $result['duration'] = $duration;
    }

    return $result;
}
