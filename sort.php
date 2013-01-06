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

ini_set('memory_limit', '10000M');

function tidyCode($code) {
    $specs = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'r')
    );
    $pipes = array();
    $proc = proc_open(dirname(__FILE__) . '/tidy -i -c -asxml -q -w 255 --indent-spaces 3 --alt-text "Image"', $specs, $pipes);
    if (!is_resource($proc)) {
        return $code;
    }
    fwrite($pipes[0], $code);
    fclose($pipes[0]);
    $ret = '';
    while (!feof($pipes[1])) {
        $ret .= fgets($pipes[1], 1024);
    }
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($proc);
    if ($ret == '') return $code;
    // Remove unwanted <meta> tag.
    $ret = str_replace("\n   <meta name=\"generator\" content=\"HTML Tidy for Linux/x86 (vers 1 July 2005), see www.w3.org\" />\n", '', $ret);
    return $ret;
}

function sortResults(&$results) {
    unset($results[-1]);
    foreach ($results as &$i) {
        ksort($i);
        foreach ($i as &$j) {
            arsort($j);
        }
    }
}

function getOtherType($type) {
    if ($type == 'by_object')
        return 'by_subject';
    return 'by_object';
}

function getLadderName($idx) {
    $arr = array('Standard', 'Ubers', 'Underused', 'Suspect');
    return $arr[$idx];
}

function makePage($desc, $msg, $type, $idx, &$data) {
    $ladder = getLadderName($idx);
    $str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>' . $ladder . ' ladder switching stats ' . strip_tags($desc) . ' - August 2009</title>
    <meta http-equiv="content-type" content="application/xhtml+xml;charset=utf-8" />
</head>
<body>';
    $str .= "<p>Switching stats for the <strong>$ladder</strong> ladder for August 2009. These statistics are listed $desc.</p><p>&mdash; <a href='https://cathyjf.com'>Cathy Fitzpatrick</a> (cathyjf)";
    $str .= '<ul>';
    foreach ($data as $i => &$j) {
        $str .= "<li><a href='#$i'>$i</a></li>";
    }
    $str .= '</ul>';

    $lcase = strtolower($ladder);
    $other = getOtherType($type);

    foreach ($data as $i => &$j) {
        $str .= "<hr /><h1><a id='$i' href='#'>&sect;</a> $i</h1>";
        $m = str_replace('$', $i, $msg);
        $str .= "<p>$m on the $ladder ladder.</p>";
        $str .= '<ol>';
        $sum = array_sum($j);
        foreach ($j as $k => $l) {
            $percent = round($l / $sum * 10000) / 100;
            $str .= "<li><a href='${lcase}_${other}.htm#$k'>$k</a> - ${percent}% ($l)</li>";
        }
        $str .= '</ol>';
    }

    $str .= '</body></html>';

    $file = "html/${lcase}_${type}.htm";
    file_put_contents($file, tidyCode($str));
}

function makeOutput($desc, $msg, $type, &$results) {
    foreach ($results as $i => &$j) {
        makePage($desc, $msg, $type, $i, $j);
    }
}

if ($argc != 2) {
    die("Requies an argument.\n");
}

$file = $argv[1];
$results = unserialize(file_get_contents($file));
sortResults($results);

$inverse = array();
foreach ($results as $i => &$j) { // for each metagame
    $inverse[$i] = array();
    $p =& $inverse[$i];
    foreach ($j as $k => &$l) { // for each pokemon
        foreach ($l as $m => $n) { // for each common switch in to that pokemon
            if (!isset($p[$m])) {
                $p[$m] = array();
            }
            $q =& $p[$m];
            if (!isset($q[$k])) {
                $q[$k] = 0;
            }
            $q[$k] += $n;
        }
    }
}
sortResults($inverse);

makeOutput('by <strong>object</strong> of the switch', 'These are the most common pokemon <strong>to switch into</strong> $', 'by_object', $results);
makeOutput('by <strong>subject</strong> of the switch', 'These are the most common pokemon <strong>that $ switches into</strong>', 'by_subject', $inverse);

?>
