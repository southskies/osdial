<?php

# Database connectivity
require("dbconnect.php");

# Display and formating variables.
require("display.php");

# Various functions.
require("functions.php");

# Admin and Form variables.
# TODO: Write function for GET/POST retreival
require("variables.php");

# Validation for form variables.
require("validation.php");


# Authentication page (basic auth).
require("auth.php");


# Help page amd variables.
require("help.php");


# Unfunctionalized misc routines.
# TODO: Functionalize variables.
require("init.php");


# Menu: content and ADD to header name translations
# TODO: Separation into callable functions
require("menu.php");

?>
