<?php
/**
 * A program that prepares statistics regarding Pokemon switching.
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

require_once('parser.php');

function parseDirectory($dir, &$results) {
    foreach (glob($dir . '/*') as $i) {
        if (is_dir($i)) {
            if (strpos($i, 'chat') === FALSE) {
                parseDirectory($i, $results);
            }
        } else {
            echo "Parsing $i\n";
            parseFile($i, $results);
        }
    }
}

$results = array();
parseDirectory('logs', $results);
file_put_contents('results', serialize($results));

?>
