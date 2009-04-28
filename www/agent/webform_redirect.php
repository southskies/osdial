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

get_vars();

# Modify this variable to redirect elsewhere.
$url = "http://www.callcentersg.com/?id=$external_key";




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
<?

function get_vars() {
    # List of variables passed to the webform.
    $lead_id = get_variable("lead_id");
    $vendor_id = get_variable("vendor_id");
    $list_id = get_variable("list_id");
    $gmt_offset_now = get_variable("gmt_offset_now");
    $phone_code = get_variable("phone_code");
    $phone_number = get_variable("phone_number");
    $title = get_variable("title");
    $first_name = get_variable("first_name");
    $middle_initial = get_variable("middle_initial");
    $last_name = get_variable("last_name");
    $address1 = get_variable("address1");
    $address2 = get_variable("address2");
    $address3 = get_variable("address3");
    $city = get_variable("city");
    $state = get_variable("state");
    $province = get_variable("province");
    $postal_code = get_variable("postal_code");
    $country_code = get_variable("country_code");
    $gender = get_variable("gender");
    $date_of_birth = get_variable("date_of_birth");
    $alt_phone = get_variable("alt_phone");
    $email = get_variable("email");
    $custom1 = get_variable("custom1");
    $custom2 = get_variable("custom2");
    $comments = get_variable("comments");
    $user = get_variable("user");
    $pass = get_variable("pass");
    $campaign = get_variable("campaign");
    $phone_login = get_variable("phone_login");
    $phone_pass = get_variable("phone_pass");
    $fronter = get_variable("fronter");
    $closer = get_variable("closer");
    $group = get_variable("group");
    $channel_group = get_variable("channel_group");
    $SQLdate = get_variable("SQLdate");
    $epoch = get_variable("epoch");
    $uniqueid = get_variable("uniqueid");
    $customer_zap_channel = get_variable("customer_zap_channel");
    $customer_server_ip = get_variable("customer_server_ip");
    $server_ip = get_variable("server_ip");
    $SIPexten = get_variable("SIPexten");
    $session_id = get_variable("session_id");
    $phone = get_variable("phone");
    $parked_by = get_variable("parked_by");
    $dispo = get_variable("dispo");
    $dialed_number = get_variable("dialed_number");
    $dialed_label = get_variable("dialed_label");
    $source_id = get_variable("source_id");
    $external_key = get_variable("external_key");
}

?>
