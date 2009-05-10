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

header("Content-type: text/csv; charset=utf-8");
header("Content-Disposition: inline; filename=" . $name . "_" . date("Ymd-His") . ".csv");

$currow = -1;
while ($currow++ < $rows-1) {
	$row = get_variable("row" . $currow);
	$items = explode('|',$row);
	echo '"' . implode('","',$items) . "\"\r\n";
}

?>
