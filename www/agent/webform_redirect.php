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
# 090428-0927 - Moved variables into function at end for readability.

# This program redirects the webform to a different URL that might need
# different keys/values passed.  Just edit the $url variable and use the
# variables listed below


include("../admin/include/functions.php");
include("../admin/include/variables.php");

# Modify the $url variable to redirect elsewhere.

#   Variables passed to webform:
#    lead_id vendor_id list_id gmt_offset_now phone_code phone_number title
#    first_name middle_initial last_name address1 address2 address3 city state
#    province postal_code country_code gender date_of_birth alt_phone email
#    custom1 custom2 comments user pass campaign phone_login phone_pass fronter
#    closer group channel_group SQLdate epoch uniqueid customer_zap_channel
#    customer_server_ip server_ip SIPexten session_id phone parked_by dispo
#    dialed_number dialed_label source_id external_key

#$url = "http://www.osdial.com/webform_test.php?id=$external_key&lead=$lead_id&list=$list_id&number=$phone_number";
$url = "http://www.osdial.com/webform_test.php?id=$external_key";




# You shouldn't need to edit anything below this line, but feel free...
?>
<html>
    <title>Webform Redirection</title>
</html>
<body>
    If you are not automatically redirected, click <a href="<?= $url ?>">here.</a>
    <script language="javascript">
        window.location = "<?= $url ?>";
    </script>
</body>
