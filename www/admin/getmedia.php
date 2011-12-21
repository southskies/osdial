<?php
# tocsv.php - OSDial
# 
# Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
require_once("include/dbconnect.php");
require_once("include/functions.php");

$mimetype = get_variable("mimetype");
$filename = get_variable("filename");
$download = get_variable('download');
if ($download=='') {
    $download='inline';
} else {
    $download='attachment';
}


if ($filename!='' and $mimetype!='') {
    header("Content-type: $mimetype; charset=utf-8");
    header("Content-Disposition: $download; filename=\"$filename\"");

    echo media_get_filedata($link,$filename);
}

?>
