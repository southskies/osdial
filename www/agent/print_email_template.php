<?php
# print_email_template.php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
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

header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

echo "<html>\n";
echo "<head>\n";
echo "  <title>Email Template Print</title>\n";
echo "</head>\n";
echo "<body bgcolor=white>\n";

require("dbconnect.php");
require('functions.php');

$user = get_variable('user');
$pass = get_variable('pass');
$et_id = get_variable('et_id');
$lead_id = get_variable('lead_id');

$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);

$stmt="SELECT count(*) from osdial_users where user='$user' and pass='$pass' and user_level > 0;";
$auth = get_first_record($link, 'osdial_users', 'count(*) AS count', sprintf("user='%s' AND pass='%s'",mres($user),mres($pass)));
if ($auth['count'] == 0) {
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
}

sleep(2);

$et = get_first_record($link, 'osdial_email_templates', '*', "et_id='" . $et_id . "'");
$lead = get_first_record($link, 'osdial_list', '*', "lead_id='" . $lead_id . "'");

$forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
foreach ($forms as $form) {
    $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
    foreach ($fields as $field) {
        $vdlf = get_first_record($link, 'osdial_list_fields', '*', "lead_id='" . $lead_id . "' AND field_id='" . $field['id'] . "'");
        if ($vdlf['value'] != '') $lead[$form['name'] . '_' . $field['name']] = $vdlf['value'];
    }
}

foreach ($lead as $k => $v) {
    $et['et_body_html'] = preg_replace('/\[\[' . $k . '\]\]/imU', $v, $et['et_body_html']);
    $et['et_body_text'] = preg_replace('/\[\[' . $k . '\]\]/imU', $v, $et['et_body_text']);
}

echo $et['et_body_html'] . "\n\n";

echo "<script type=\"text/javascript\" language=\"javascript\">\n";
echo "window.print();\n";
echo "window.close();\n";
echo "</script>\n";
echo "</body>\n";
exit;
?>
