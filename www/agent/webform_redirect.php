<?php

#
# Copyright (C) 2009-2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
# 100217-1156 - Allow more dynamic field-mapping control.

# This program redirects the webform to a different URL that might need
# different keys/values passed.  Just edit the $url variable and use the
# variables listed below

# Modify $url or set the url variable to redirect elsewhere.

#   Variables passed to webform:
#    lead_id vendor_id list_id gmt_offset_now phone_code phone_number title
#    first_name middle_initial last_name address1 address2 address3 city state
#    province postal_code country_code gender date_of_birth alt_phone email
#    custom1 custom2 comments user pass campaign phone_login phone_pass fronter
#    closer group channel_group SQLdate epoch uniqueid customer_zap_channel
#    customer_server_ip server_ip SIPexten session_id phone parked_by dispo
#    dialed_number dialed_label source_id external_key

# Passing the "fields" variable allows you to specify field mappings, given that url is specified.
#   Consider the following:
#     url=http:///www.osdial.com/webform_test.php&fields=id$external_key,lead$lead_id,list$list_id,number$phone_number
#   Would map the following, substituting the given OSDial variable with the current lead's value:
#     http://www.osdial.com/webform_test.php?id=$external_key&lead=$lead_id&list=$list_id&number=$phone_number
#   Which might result in something like this being passed:
#     http://www.osdial.com/webform_test.php?id=3483&lead=120&list=1000&number=4075551212
#  
header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

include("../admin/include/functions.php");
include("../admin/include/variables.php");


$url = get_variable("url");
if ($url == "") {
    #$url = "http://www.osdial.com/webform_test.php?id=$external_key&lead=$lead_id&list=$list_id&number=$phone_number";
    $url = "http://www.osdial.com/webform_test.php?id=$external_key";
} else {
    $fields = get_variable("fields");
    if ($fields != "") {
        $url .= "?";
        # Split out comma-sep fields
        foreach (explode(",",$fields) as $maps) {
            # Split field at $, left=ext_map, left=
            $map = explode("$",$maps);
            # Since we are eval'ing a passed var, lets put add constaints to make a bit more secure.
            if (strlen($map[1]) > 3 and strlen($map[1]) < 20) {
                eval("\$map[1] = \$" . $map[1] . ";");
                $url .= $map[0] . "=" . $map[1] . "&";
            }
        }
        $url = rtrim($url,"&");
    }
}



# You shouldn't need to edit anything below this line, but feel free...
?>
<html>
    <title>Webform Redirection</title>
</html>
<body>
    If you are not automatically redirected, click <a href="<?php echo $url; ?>">here.</a>
    <script language="javascript">
        window.location = "<?php echo $url; ?>";
    </script>
</body>
