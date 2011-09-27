<?php
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


# Database connectivity
require_once("dbconnect.php");

# Various functions.
require_once("functions.php");

# Admin and Form variables.
# TODO: Write function for GET/POST retreival
require_once("variables.php");

# Display and formating variables.
require_once($WeBServeRRooT . "/admin/templates/default/display.php");
include_once($WeBServeRRooT . "/admin/templates/" . $config['settings']['admin_template'] . "/display.php");

# Validation for form variables.
require_once("validation.php");


# Authentication page (basic auth).
require_once("auth.php");


# Help page amd variables.
require_once("help.php");


# Unfunctionalized misc routines.
# TODO: Functionalize variables.
require_once("init.php");


# Menu: content and ADD to header name translations
# TODO: Separation into callable functions
require_once("menu.php");

?>
