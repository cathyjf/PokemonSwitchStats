<?php
/**
 * ShoddySticks: An IRC bot that implements a variant of the game of Nim.
 * Copyright (C) 2009  Cathy Fitzpatrick <cathy@cathyjf.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 **/

function getLadderId($lines) {
    foreach ($lines as $i) {
        if (preg_match('/^Ladder: (.*)$/', $i, $matches)) {
            return (int)$matches[1];
        }
    }
    return -1;
}

function getSwitchInfo($line) {
    if (!preg_match('/^\(..:..:..\) (.*) switched in (.*) \(lvl .*? ([^ ]*) ?.?\)/', $line, $matches)) {
        if (!preg_match('/^\(..:..:..\) (.*) sent out (.*) \(lvl .*? ([^ ]*) ?.?\)/', $line, $matches))
            return FALSE;
    }
    return array($matches[1], $matches[3], $matches[2]);
}

function getFaintedInfo($line, &$trainer, &$nickname) {
    $line = strstr($line, ' ');
    if (strpos($line, ':') !== FALSE)
        return -1;
    for ($i = 0; $i < 2; ++$i) {
        $a = @$trainer[$i];
        $b = @$nickname[$i];
        if (!$a || !$b)
            continue;
        $s = "$a's $b fainted.";
        if (strpos($line, $s) !== FALSE)
            return $i;
    }
    return -1;
}

function parseFile($file, &$results) {
    $lines = explode("\n", file_get_contents($file));
    if (strpos($lines[2], "Ladder Match") === FALSE)
        return;
    $ladder = getLadderId($lines);
    if (!isset($results[$ladder])) {
        $results[$ladder] = array();
    }
    $data =& $results[$ladder];

    $trainer = array();
    $trainerIdx = array();
    $field = array('', '');
    $nickname = array('', '');
    $count = 0;
    
    foreach ($lines as $i) {
        $info = getSwitchInfo($i);
        if ($info === FALSE) {
            $fainted = getFaintedInfo($i, $trainerIdx, $nickname);
            if ($fainted != -1) {
                $field[$fainted] = $nickname[$fainted] = '';
            }
            continue;
        }
        ++$count;
        if (!isset($trainer[$info[0]])) {
            $v = $trainer[$info[0]] = count($trainer);
            $trainerIdx[$v] = $info[0];
        }
        $idx = $trainer[$info[0]];
        $pokemon = $info[1];
        $opponent = $field[1 - $idx];
        if (($count > 2) && ($opponent != '')) {
            if (!isset($data[$opponent])) {
                $data[$opponent] = array();
            }
            $p =& $data[$opponent];
            if (!isset($p[$pokemon])) {
                $p[$pokemon] = 0;
            }
            ++$p[$pokemon];
        }
        $field[$idx] = $pokemon;
        $nickname[$idx] = $info[2];
    }
}

?>
