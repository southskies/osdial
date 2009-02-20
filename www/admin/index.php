<?php
# admin.php - OSDial - Heavily modified VICIdial version.
# 
# OSDial modifications:
# Copyright (C) 2008  Lott Caskey   LICENSE: AGPLv2
# Copyright (C) 2008  Steve Szmidt  LICENSE: AGPLv2
# <info@osdial.com>
#
# Original VICIdial source:
# Copyright (C) 2007  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#

# Includes
require("include/includes.php");


# Main Panel Header
require("include/header.php");

# Main Panel Content
require($content);

# Main Panel Footers
require("include/footer.php");

exit;
