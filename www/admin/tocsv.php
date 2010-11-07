<?php
# tocsv.php - OSDial
# 
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
#     This file is part of OSDial.
#
#     OSDial is free software: you can redistribute it and/or modify
#     it under the terms of the GNU Affero General Public License as
#     published by the Free Software Foundation, either version 3 of
#     the License, or (at your option) any later version.
#
#     OSDial is distributed in the hope that it will be useful,
#     but WITHOUT ANY WARRANTY; without even the implied warranty of
#     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#     GNU Affero General Public License for more details.
#
#     You should have received a copy of the GNU Affero General Public
#     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
#
#
#
# Includes
require("include/functions.php");

$name = get_variable("name");
$rows = get_variable("rows");
$glob = get_variable("glob");


if ($name!='' and ($rows!='' or $glob!='')) {
    header("Content-type: text/csv; charset=utf-8");
    header("Content-Disposition: inline; filename=\"" . $name . "_" . date("Ymd-His") . ".csv\"");

    $dncdata=array();
    $postsize=0;
    if ($glob!='') {
        $rows=0;
        $postsize = strlen($glob);
        foreach (explode("\n",preg_replace('/\r/','',$glob)) as $gline) {
	        $dncdata[]=explode('|',$gline);
            $rows++;
        }
    } else {
        $currow = -1;
        while ($currow++ < $rows-1) {
	        $postrow = get_variable("row" . $currow);
            $postsize += strlen($postrow);
	        $dncdata[]=explode('|',$postrow);
        }
    }
    outputCSV($dncdata);

} else {
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>tocsv.php</title>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "Error, Missing Variables: 'name' is required and also either 'rows' or 'glob'.\n";
    echo "</body>\n";
    echo "</html>\n";
}






function outputCSV($data) {
    $outstream = fopen("php://output", 'w');
    function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals, ',', '"');
    }
    array_walk($data, '__outputCSV', $outstream);
    fclose($outstream);
}

?>
