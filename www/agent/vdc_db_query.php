<?php
# vdc_db_query.php
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
#
# This script is designed purely to send whether the meetme conference has live channels connected and which they are
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the osdial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $ACTION - ('regCLOSER','manDiaLnextCALL','manDiaLskip','manDiaLonly','manDiaLlookCALL','manDiaLlogCALL','userLOGout','updateDISPO','VDADpause','VDADready','VDADcheckINCOMING','CalLBacKLisT','CalLBacKCounT','PauseCodeSubmit','LogiNCamPaigns')
#  - $stage - ('start','finish','lookup','new')
#  - $closer_choice - ('CL_TESTCAMP_L CL_OUT123_L -')
#  - $conf_exten - ('8600011',...)
#  - $exten - ('123test',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $campaign - ('testcamp',...)
#  - $dial_timeout - ('60','26',...)
#  - $dial_prefix - ('9','8',...)
#  - $campaign_cid - ('3125551212','0000000000',...)
#  - $MDnextCID - ('M06301413000000002',...)
#  - $uniqueid - ('1120232758.2406800',...)
#  - $lead_id - ('36524',...)
#  - $list_id - ('101','123456',...)
#  - $length_in_sec - ('12',...)
#  - $phone_code - ('1',...)
#  - $phone_number - ('3125551212',...)
#  - $channel - ('Zap/12-1',...)
#  - $start_epoch - ('1120236911',...)
#  - $vendor_lead_code - ('1234test',...)
#  - $title - ('Mr.',...)
#  - $first_name - ('Bob',...)
#  - $middle_initial - ('L',...)
#  - $last_name - ('Wilson',...)
#  - $address1 - ('1324 Main St.',...)
#  - $address2 - ('Apt. 12',...)
#  - $address3 - ('co Robert Wilson',...)
#  - $city - ('Chicago',...)
#  - $state - ('IL',...)
#  - $province - ('NA',...)
#  - $postal_code - ('60054',...)
#  - $country_code - ('USA',...)
#  - $gender - ('M',...)
#  - $date_of_birth - ('1970-01-01',...)
#  - $alt_phone - ('3125551213',...)
#  - $email - ('bob@bob.com',...)
#  - $custom1 - ('Hello',...)
#  - $comments - ('Good Customer',...)
#  - $auto_dial_level - ('0','1','1.2',...)
#  - $VDstop_rec_after_each_call - ('0','1')
#  - $conf_silent_prefix - ('7','8','',...)
#  - $extension - ('123','user123','25-1',...)
#  - $protocol - ('Zap','SIP','IAX2',...)
#  - $user_abb - ('1234','6666',...)
#  - $preview - ('YES','NO',...)
#  - $called_count - ('0','1','2',...)
#  - $agent_log_id - ('123456',...)
#  - $agent_log - ('NO',...)
#  - $favorites_list - (",'cc160','cc100'",...)
#  - $CallBackDatETimE - ('2006-04-21 14:30:00',...)
#  - $recipient - ('ANYONE,'USERONLY')
#  - $callback_id - ('12345','12346',...)
#  - $use_internal_dnc - ('Y','N')
#  - $omit_phone_code - ('Y','N')
#  - $no_delete_sessions - ('0','1')
#  - $LogouTKicKAlL - ('0','1');

# CHANGELOG:
# 50629-1044 - First build of script
# 50630-1422 - Added manual dial action and MD channel lookup
# 50701-1451 - Added dial log for start and end of osdial calls
# 50705-1239 - Added call disposition update
# 50804-1627 - Fixed updateDispo to update osdial_log entry
# 50816-1605 - Added VDADpause/ready for auto dialing
# 50816-1811 - Added basic autodial call pickup functions
# 50817-1005 - Altered logging functions to accomodate auto_dialing
# 50818-1305 - Added stop-all-recordings-after-each-osdial-call option
# 50818-1411 - Added hangup of agent phone after Logout
# 50901-1315 - Fixed CLOSER IN-GROUP Web Form bug
# 50902-1507 - Fixed CLOSER log length_in_sec bug
# 50902-1730 - Added functions for manual preview dialing and revert
# 50913-1214 - Added agent random update to leadupdate
# 51020-1421 - Added agent_log_id framework for detailed agent activity logging
# 51021-1717 - Allows for multi-line comments (changes \n to !N in database)
# 51111-1046 - Added osdial_agent_log lead_id earlier for manual dial
# 51121-1445 - Altered echo statements for several small PHP speed optimizations
# 51122-1328 - Fixed UserLogout issue not removing conference reservation
# 51129-1012 - Added ability to accept calls from other OSDIAL servers
# 51129-1729 - Changed manual dial to use the '/n' flag for calls
# 51221-1154 - Added SCRIPT id lookup and sending to osdial.php for display
# 60105-1059 - Added Updating of osdial favorites in the DB
# 60208-1617 - Added dtmf buttons output per call
# 60213-1521 - Added closer_campaigns update to osdial_users
# 60215-1036 - Added Callback date-time entry into osdial_callbacks table
# 60413-1541 - Added USERONLY Callback listings output - CalLBacKLisT
#            - Added USERONLY Callback count output - CalLBacKCounT
# 60414-1140 - Added Callback lead lookup for manual dialing
# 60419-1517 - After CALLBK is sent to agent, update callback record to INACTIVE
# 60421-1419 - Check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60427-1236 - Fixed closer_choice error for CLOSER campaigns
# 60609-1148 - Added ability to check for manual dial numbers in DNC
# 60619-1117 - Added variable filters to close security holes for login form
# 60623-1414 - Fixed variable filter for phone_code and fixed manual dial logic
# 60821-1600 - Added ability to omit the phone code on osdial lead dialing
# 60821-1647 - Added ability to not delete sessions at logout
# 60906-1124 - Added lookup and sending of callback data for CALLBK calls
# 61128-2229 - Added osdial_live_agents and osdial_auto_calls manual dial entries
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70115-1733 - Added alt_dial functionality in auto-dial modes
# 70118-1501 - Added user_group to osdial_log,_agent_log,_closer_log,_callbacks
# 70123-1357 - Fixed bug that would not update osdial_closer_log status to dispo
# 70202-1438 - Added pause code submit function
# 70203-0930 - Added dialed_number to lead info output
# 70203-1030 - Added dialed_label to lead info output
# 70206-1126 - Added INBOUND status for inbound/closer calls in osdial_live_agents
# 70212-1253 - Fixed small issue with CXFER
# 70213-1431 - Added QueueMetrics PAUSE/UNPAUSE/AGENTLOGIN/AGENTLOGOFF actions
# 70214-1231 - Added queuemetrics_log_id field for server_id in queue_log
# 70215-1210 - Added queuemetrics COMPLETEAGENT action
# 70216-1051 - Fixed double call complete queuemetrics logging
# 70222-1616 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70309-1034 - Allow amphersands and questions marks in comments to pass through
# 70313-1052 - Allow pound signs(hash) in comments to pass through
# 70319-1544 - Added agent disable update customer data function
# 70322-1545 - Added sipsak display ability
# 70413-1253 - Fixed bug for outbound call time in CLOSER-type blended campaigns
# 70424-1100 - Fixed bug for fronter/closer calls that would delete vdac records
# 70802-1729 - Fixed bugs with pause_sec and wait_sec under certain call handling 
# 70828-1443 - Added source_id to output of SCRIPTtab-IFRAME and WEBFORM
# 71029-1855 - removed campaign_id naming restrictions for CLOSER-type campaigns
# 71030-2047 - added hopper priority for auto alt dial entries
# 71116-1011 - added calls_today count updating of the osdial_live_agents upon INCALL
# 71120-1520 - added LogiNCamPaigns to show only allowed campaigns for agents upon login
# 71125-1751 - Added inbound-group default inbound group sending to osdial.php
# 71129-2025 - restricted callbacks count and list to campaign only
# 71226-1117 - added option to kick all calls from conference upon logout
# 80128-1159 - fixed osdial_log bugs
# 80402-0121 - Fixes for manual dial transfers on some systems, removed /n persist flag
# 80424-0442 - Added non_latin lookup from system_settings
#
# 090410-1159 - Added custom2 field
# 090410-1744 - Added allow_tab_switch
# 090428-0936 - Added external_key

$version = '2.0.4-69';
$build = '80424-0442';

$DB = 0;
$length_in_sec = 0;
$user = '';
$pass = '';
$phone_code = '';
$phone_number = '';


require("dbconnect.php");
require('functions.php');

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))						{$user=$_GET["user"];}
    elseif (isset($_POST["user"]))				{$user=$_POST["user"];}
if (isset($_GET["pass"]))						{$pass=$_GET["pass"];}
    elseif (isset($_POST["pass"]))				{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))					{$server_ip=$_GET["server_ip"];}
    elseif (isset($_POST["server_ip"]))			{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))				{$session_name=$_GET["session_name"];}
    elseif (isset($_POST["session_name"]))		{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))						{$format=$_GET["format"];}
    elseif (isset($_POST["format"]))			{$format=$_POST["format"];}
if (isset($_GET["ACTION"]))						{$ACTION=$_GET["ACTION"];}
    elseif (isset($_POST["ACTION"]))			{$ACTION=$_POST["ACTION"];}
if (isset($_GET["stage"]))						{$stage=$_GET["stage"];}
    elseif (isset($_POST["stage"]))				{$stage=$_POST["stage"];}
if (isset($_GET["closer_choice"]))				{$closer_choice=$_GET["closer_choice"];}
    elseif (isset($_POST["closer_choice"]))		{$closer_choice=$_POST["closer_choice"];}
if (isset($_GET["conf_exten"]))					{$conf_exten=$_GET["conf_exten"];}
    elseif (isset($_POST["conf_exten"]))		{$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["exten"]))						{$exten=$_GET["exten"];}
    elseif (isset($_POST["exten"]))				{$exten=$_POST["exten"];}
if (isset($_GET["ext_context"]))				{$ext_context=$_GET["ext_context"];}
    elseif (isset($_POST["ext_context"]))		{$ext_context=$_POST["ext_context"];}
if (isset($_GET["ext_priority"]))				{$ext_priority=$_GET["ext_priority"];}
    elseif (isset($_POST["ext_priority"]))		{$ext_priority=$_POST["ext_priority"];}
if (isset($_GET["campaign"]))					{$campaign=$_GET["campaign"];}
    elseif (isset($_POST["campaign"]))			{$campaign=$_POST["campaign"];}
if (isset($_GET["dial_timeout"]))				{$dial_timeout=$_GET["dial_timeout"];}
    elseif (isset($_POST["dial_timeout"]))		{$dial_timeout=$_POST["dial_timeout"];}
if (isset($_GET["dial_prefix"]))				{$dial_prefix=$_GET["dial_prefix"];}
    elseif (isset($_POST["dial_prefix"]))		{$dial_prefix=$_POST["dial_prefix"];}
if (isset($_GET["campaign_cid"]))				{$campaign_cid=$_GET["campaign_cid"];}
    elseif (isset($_POST["campaign_cid"]))		{$campaign_cid=$_POST["campaign_cid"];}
if (isset($_GET["campaign_cid_name"]))				{$campaign_cid_name=$_GET["campaign_cid_name"];}
    elseif (isset($_POST["campaign_cid_name"]))		{$campaign_cid_name=$_POST["campaign_cid_name"];}
if (isset($_GET["MDnextCID"]))					{$MDnextCID=$_GET["MDnextCID"];}
    elseif (isset($_POST["MDnextCID"]))			{$MDnextCID=$_POST["MDnextCID"];}
if (isset($_GET["uniqueid"]))					{$uniqueid=$_GET["uniqueid"];}
    elseif (isset($_POST["uniqueid"]))			{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["lead_id"]))					{$lead_id=$_GET["lead_id"];}
    elseif (isset($_POST["lead_id"]))			{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))					{$list_id=$_GET["list_id"];}
    elseif (isset($_POST["list_id"]))			{$list_id=$_POST["list_id"];}
if (isset($_GET["length_in_sec"]))				{$length_in_sec=$_GET["length_in_sec"];}
    elseif (isset($_POST["length_in_sec"]))		{$length_in_sec=$_POST["length_in_sec"];}
if (isset($_GET["phone_code"]))					{$phone_code=$_GET["phone_code"];}
    elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
    elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["channel"]))					{$channel=$_GET["channel"];}
    elseif (isset($_POST["channel"]))			{$channel=$_POST["channel"];}
if (isset($_GET["start_epoch"]))				{$start_epoch=$_GET["start_epoch"];}
    elseif (isset($_POST["start_epoch"]))		{$start_epoch=$_POST["start_epoch"];}
if (isset($_GET["dispo_choice"]))				{$dispo_choice=$_GET["dispo_choice"];}
    elseif (isset($_POST["dispo_choice"]))		{$dispo_choice=$_POST["dispo_choice"];}
if (isset($_GET["vendor_lead_code"]))			{$vendor_lead_code=$_GET["vendor_lead_code"];}
    elseif (isset($_POST["vendor_lead_code"]))	{$vendor_lead_code=$_POST["vendor_lead_code"];}
if (isset($_GET["title"]))						{$title=$_GET["title"];}
    elseif (isset($_POST["title"]))				{$title=$_POST["title"];}
if (isset($_GET["first_name"]))					{$first_name=$_GET["first_name"];}
    elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["middle_initial"]))				{$middle_initial=$_GET["middle_initial"];}
    elseif (isset($_POST["middle_initial"]))	{$middle_initial=$_POST["middle_initial"];}
if (isset($_GET["last_name"]))					{$last_name=$_GET["last_name"];}
    elseif (isset($_POST["last_name"]))			{$last_name=$_POST["last_name"];}
if (isset($_GET["address1"]))					{$address1=$_GET["address1"];}
    elseif (isset($_POST["address1"]))			{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))					{$address2=$_GET["address2"];}
    elseif (isset($_POST["address2"]))			{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))					{$address3=$_GET["address3"];}
    elseif (isset($_POST["address3"]))			{$address3=$_POST["address3"];}
if (isset($_GET["city"]))						{$city=$_GET["city"];}
    elseif (isset($_POST["city"]))				{$city=$_POST["city"];}
if (isset($_GET["state"]))						{$state=$_GET["state"];}
    elseif (isset($_POST["state"]))				{$state=$_POST["state"];}
if (isset($_GET["province"]))					{$province=$_GET["province"];}
    elseif (isset($_POST["province"]))			{$province=$_POST["province"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
    elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
    elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["gender"]))						{$gender=$_GET["gender"];}
    elseif (isset($_POST["gender"]))			{$gender=$_POST["gender"];}
if (isset($_GET["date_of_birth"]))				{$date_of_birth=$_GET["date_of_birth"];}
    elseif (isset($_POST["date_of_birth"]))		{$date_of_birth=$_POST["date_of_birth"];}
if (isset($_GET["alt_phone"]))					{$alt_phone=$_GET["alt_phone"];}
    elseif (isset($_POST["alt_phone"]))			{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))						{$email=$_GET["email"];}
    elseif (isset($_POST["email"]))				{$email=$_POST["email"];}
if (isset($_GET["custom1"]))			{$custom1=$_GET["custom1"];}
    elseif (isset($_POST["custom1"]))	{$custom1=$_POST["custom1"];}
if (isset($_GET["custom2"]))			{$custom2=$_GET["custom2"];}
    elseif (isset($_POST["custom2"]))	{$custom2=$_POST["custom2"];}
if (isset($_GET["comments"]))					{$comments=$_GET["comments"];}
    elseif (isset($_POST["comments"]))			{$comments=$_POST["comments"];}
if (isset($_GET["auto_dial_level"]))			{$auto_dial_level=$_GET["auto_dial_level"];}
    elseif (isset($_POST["auto_dial_level"]))	{$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["VDstop_rec_after_each_call"]))				{$VDstop_rec_after_each_call=$_GET["VDstop_rec_after_each_call"];}
    elseif (isset($_POST["VDstop_rec_after_each_call"]))		{$VDstop_rec_after_each_call=$_POST["VDstop_rec_after_each_call"];}
if (isset($_GET["conf_silent_prefix"]))				{$conf_silent_prefix=$_GET["conf_silent_prefix"];}
    elseif (isset($_POST["conf_silent_prefix"]))	{$conf_silent_prefix=$_POST["conf_silent_prefix"];}
if (isset($_GET["extension"]))					{$extension=$_GET["extension"];}
    elseif (isset($_POST["extension"]))			{$extension=$_POST["extension"];}
if (isset($_GET["protocol"]))					{$protocol=$_GET["protocol"];}
    elseif (isset($_POST["protocol"]))			{$protocol=$_POST["protocol"];}
if (isset($_GET["user_abb"]))					{$user_abb=$_GET["user_abb"];}
    elseif (isset($_POST["user_abb"]))			{$user_abb=$_POST["user_abb"];}
if (isset($_GET["preview"]))					{$preview=$_GET["preview"];}
    elseif (isset($_POST["preview"]))			{$preview=$_POST["preview"];}
if (isset($_GET["called_count"]))				{$called_count=$_GET["called_count"];}
    elseif (isset($_POST["called_count"]))		{$called_count=$_POST["called_count"];}
if (isset($_GET["agent_log_id"]))				{$agent_log_id=$_GET["agent_log_id"];}
    elseif (isset($_POST["agent_log_id"]))		{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["agent_log"]))					{$agent_log=$_GET["agent_log"];}
    elseif (isset($_POST["agent_log"]))			{$agent_log=$_POST["agent_log"];}
if (isset($_GET["favorites_list"]))				{$favorites_list=$_GET["favorites_list"];}
    elseif (isset($_POST["favorites_list"]))	{$favorites_list=$_POST["favorites_list"];}
if (isset($_GET["CallBackDatETimE"]))			{$CallBackDatETimE=$_GET["CallBackDatETimE"];}
    elseif (isset($_POST["CallBackDatETimE"]))	{$CallBackDatETimE=$_POST["CallBackDatETimE"];}
if (isset($_GET["PostDatETimE"]))			{$PostDatETimE=$_GET["PostDatETimE"];}
    elseif (isset($_POST["PostDatETimE"]))	{$PostDatETimE=$_POST["PostDatETimE"];}
if (isset($_GET["recipient"]))					{$recipient=$_GET["recipient"];}
    elseif (isset($_POST["recipient"]))			{$recipient=$_POST["recipient"];}
if (isset($_GET["callback_id"]))				{$callback_id=$_GET["callback_id"];}
    elseif (isset($_POST["callback_id"]))		{$callback_id=$_POST["callback_id"];}
if (isset($_GET["use_internal_dnc"]))			{$use_internal_dnc=$_GET["use_internal_dnc"];}
    elseif (isset($_POST["use_internal_dnc"]))	{$use_internal_dnc=$_POST["use_internal_dnc"];}
if (isset($_GET["omit_phone_code"]))			{$omit_phone_code=$_GET["omit_phone_code"];}
    elseif (isset($_POST["omit_phone_code"]))	{$omit_phone_code=$_POST["omit_phone_code"];}
if (isset($_GET["phone_ip"]))				{$phone_ip=$_GET["phone_ip"];}
    elseif (isset($_POST["phone_ip"]))		{$phone_ip=$_POST["phone_ip"];}
if (isset($_GET["enable_sipsak_messages"]))				{$enable_sipsak_messages=$_GET["enable_sipsak_messages"];}
    elseif (isset($_POST["enable_sipsak_messages"]))	{$enable_sipsak_messages=$_POST["enable_sipsak_messages"];}
if (isset($_GET["status"]))						{$status=$_GET["status"];}
    elseif (isset($_POST["status"]))			{$status=$_POST["status"];}
if (isset($_GET["LogouTKicKAlL"]))				{$LogouTKicKAlL=$_GET["LogouTKicKAlL"];}
    elseif (isset($_POST["LogouTKicKAlL"]))		{$LogouTKicKAlL=$_POST["LogouTKicKAlL"];}
if (isset($_GET["oldphone"]))						{$oldphone=$_GET["oldphone"];}
    elseif (isset($_POST["oldphone"]))			{$oldphone=$_POST["oldphone"];}
if (isset($_GET["oldlead"]))						{$oldlead=$_GET["oldlead"];}
    elseif (isset($_POST["oldlead"]))			{$oldlead=$_POST["oldlead"];}
if (isset($_GET["lookup"]))						{$lookup=$_GET["lookup"];}
    elseif (isset($_POST["lookup"]))			{$lookup=$_POST["lookup"];}
if (isset($_GET["script_id"]))						{$script_id=$_GET["script_id"];}
    elseif (isset($_POST["script_id"]))			{$script_id=$_POST["script_id"];}
if (isset($_GET["script_button_id"]))						{$script_button_id=$_GET["script_button_id"];}
    elseif (isset($_POST["script_button_id"]))			{$script_button_id=$_POST["script_button_id"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,enable_multicompany FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
    {
    $row=mysql_fetch_row($rslt);
    $non_latin = $row[0];
    $multicomp = $row[1];
    $i++;
    }
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
{
$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);
$length_in_sec = ereg_replace("[^0-9]","",$length_in_sec);
$phone_code = ereg_replace("[^0-9]","",$phone_code);
$phone_number = ereg_replace("[^0-9]","",$phone_number);
}

# default optional vars if not set
if (!isset($format))   {$format="text";}
    if ($format == 'debug') {$DB=1;}
if (!isset($ACTION))   {$ACTION="refresh";}

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
$MT[0]='';
$agents='@agents';

if ($ACTION == 'LogiNCamPaigns')
    {
    $skip_user_validation=1;
    }
else
    {
    $stmt="SELECT count(*) from osdial_users where user='$user' and pass='$pass' and user_level > 0;";
    if ($DB) {echo "|$stmt|\n";}
    if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $auth=$row[0];

    if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
    {
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
    }
    else
    {
    if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
        {
        echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
        exit;
        }
    else
        {
        $stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
        if ($DB) {echo "|$stmt|\n";}
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $SNauth=$row[0];
          if($SNauth==0)
            {
            echo "Invalid session_name: |$session_name|$server_ip|\n";
            exit;
            }
          else
            {
            # do nothing for now
            }
        }
    }
}

if ($format=='debug')
{
echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build    USER: $user   server_ip: $server_ip-->\n";
echo "<title>OSDiaL Database Query Script";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}



################################################################################
### LogiNCamPaigns - generates an HTML SELECT list of allowed campaigns for a 
###                  specific agent on the login screen
################################################################################
if ($ACTION == 'LogiNCamPaigns')
{
    if ( (strlen($user)<1) )
    {
    echo "<select size=1 name=VD_campaign id=VD_campaign onfocus=\"login_focus();\">\n";
    echo "<option value=\"\">-- ERROR --</option>\n";
    echo "</select>\n";
    exit;
    }
    else
    {

    $stmt="SELECT user_group from osdial_users where user='$user' and pass='$pass'";
    if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $VU_user_group=$row[0];

    $LOGallowed_campaignsSQL='';

    $stmt="SELECT allowed_campaigns from osdial_user_groups where user_group='$VU_user_group';";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ( (!eregi("ALL-CAMPAIGNS",$row[0])) )
        {
        $LOGallowed_campaignsSQL = eregi_replace(' -','',$row[0]);
        $LOGallowed_campaignsSQL = eregi_replace(' ',"','",$LOGallowed_campaignsSQL);
        $LOGallowed_campaignsSQL = "and campaign_id IN('$LOGallowed_campaignsSQL')";
        }

    if ($multicomp > 0) {
        $stmt=sprintf("SELECT count(*) FROM osdial_companies WHERE id='%s' AND status='ACTIVE';",((substr($user,0,3) * 1) - 100) );
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] < 1) {
            echo "<select size=1 name=VD_campaign id=VD_campaign onfocus=\"login_focus();\">\n";
            echo "<option value=\"\">-- ERROR --</option>\n";
            echo "</select>\n";
            exit;
        }

        $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' AND campaign_id LIKE '%s__%%' %s order by campaign_id;",substr($user,0,3),$LOGallowed_campaignsSQL);
    } else {
        $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' %s order by campaign_id;",$LOGallowed_campaignsSQL);
    }
    $rslt=mysql_query($stmt, $link);
    $camps_to_print = mysql_num_rows($rslt);

    echo "<select style=\"font-size:8pt;\" size=1 name=VD_campaign id=VD_campaign>\n";
    echo "<option value=\"\">-- PLEASE SELECT A CAMPAIGN --</option>\n";

    $o=0;
    while ($camps_to_print > $o) 
        {
        $rowx=mysql_fetch_row($rslt);
        echo "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
        $o++;
        }
    echo "</select>\n";
    exit;
    }
}



################################################################################
### regCLOSER - update the osdial_live_agents table to reflect the closer
###             inbound choices made upon login
################################################################################
if ($ACTION == 'regCLOSER') {
    $row='';
    $rowx='';
    $channel_live=1;
    if ( (strlen($closer_choice)<1) || (strlen($user)<1) ) {
        $channel_live=0;
        echo "Group Choice $closer_choice is not valid\n";
        exit;
    } else {
        $stmt="SELECT closer_campaigns,xfer_agent2agent FROM osdial_users where user='$user' LIMIT 1;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $row=mysql_fetch_row($rslt);
        $closer_campaigns =$row[0];
        $xfer_agent2agent =$row[1];

        if ($closer_choice == "MGRLOCK-") $closer_choice = $closer_campaigns;
        if ($xfer_agent2agent > 0) $closer_choice = rtrim($closer_choice,'-') . "A2A_$user -";

        $random = (rand(1000000, 9999999) + 10000000);
        $stmt="UPDATE osdial_live_agents set closer_campaigns='$closer_choice',random_id='$random' where user='$user' and server_ip='$server_ip';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $stmt="DELETE FROM osdial_live_inbound_agents where user='$user';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        $in_groups_pre = preg_replace('/-$/','',$closer_choice);
        $in_groups = explode(" ",$in_groups_pre);
        $in_groups_ct = count($in_groups);
        $k=1;
        while ($k < $in_groups_ct) {
            if (strlen($in_groups[$k])>1) {
                $stmt="SELECT group_weight,calls_today FROM osdial_inbound_group_agents where user='$user' and group_id='$in_groups[$k]';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $viga_ct = mysql_num_rows($rslt);
                if (preg_match('/^A2A_/',$in_groups[$k])) {
                    $group_weight = 10;
                    $calls_today  = $row[1];
                } elseif ($viga_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $group_weight = $row[0];
                    $calls_today  = $row[1];
                } else {
                    $group_weight = 0;
                    $calls_today  = 0;
                }
                $stmt="INSERT INTO osdial_live_inbound_agents set user='$user',group_id='$in_groups[$k]',group_weight='$group_weight',calls_today='$calls_today',last_call_time='$NOW_TIME',last_call_finish='$NOW_TIME';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }
            $k++;
        }
    }
    echo "Closer In Group Choice $closer_choice has been registered to user $user\n";
}


################################################################################
### manDiaLnextCALL - for manual OSDiaL dialing this will grab the next lead
###                   in the campaign, reserve it, send data back to client and
###                   place the call by inserting into osdial_manager
################################################################################
if ($ACTION == 'manDiaLnextCaLL')
{
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (strlen($conf_exten)<1) || (strlen($campaign)<1)  || (strlen($ext_context)<1) )
    {
    $channel_live=0;
    echo "HOPPER EMPTY\n";
    echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
    exit;
    }
    else
    {

    ##### grab number of calls today in this campaign and increment
    $stmt="SELECT calls_today FROM osdial_live_agents WHERE user='$user' and campaign_id='$campaign';";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {echo "$stmt\n";}
    $vla_cc_ct = mysql_num_rows($rslt);
    if ($vla_cc_ct > 0)
        {
        $row=mysql_fetch_row($rslt);
        $calls_today =$row[0];
        }
    else
        {$calls_today ='0';}
    $calls_today++;

    ### check if this is a callback, if it is, skip the grabbing of a new lead and mark the callback as INACTIVE
    if ( (strlen($callback_id)>0) and (strlen($lead_id)>0) )
        {
        $affected_rows=1;
        $CBleadIDset=1;

        $stmt = "UPDATE osdial_callbacks set status='INACTIVE' where callback_id='$callback_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        }
    else
        {
        if (strlen($phone_number)>3) {
            if ($use_internal_dnc=='Y') {
                $dncs=0;
                $dncsskip=0;

                if ($multicomp > 0) {
                    $dnc_method='';
                    $stmt=sprintf("SELECT id,dnc_method FROM osdial_companies WHERE id='%s';",((substr($user,0,3) * 1) - 100));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) {echo "$stmt\n";}
                    $row=mysql_fetch_row($rslt);
                    $comp_id=$row[0];
                    $dnc_method=$row[1];

                    if (preg_match('/COMPANY|BOTH/',$dnc_method)) {
                        $stmt=sprintf("SELECT count(*) FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';",$comp_id,$phone_number);
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) {echo "$stmt\n";}
                        $row=mysql_fetch_row($rslt);
                        $dncs+=$row[0];
                    }

                    if (preg_match('/COMPANY/',$dnc_method)) $dncsskip++;
                }

                if ($dncsskip==0) {
                    $stmt="SELECT count(*) FROM osdial_dnc WHERE phone_number='$phone_number';";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) {echo "$stmt\n";}
                    $row=mysql_fetch_row($rslt);
                    $dncs+=$row[0];
                }

                if ($dncs > 0) {
                    echo "DNC NUMBER\n";
                    exit;
                }
            }
            if ($stage=='lookup')
                {
                $stmt=sprintf("SELECT lead_id FROM osdial_list JOIN osdial_lists ON (osdial_list.list_id=osdial_lists.list_id) WHERE campaign_id='%s' AND phone_number='%s' ORDER BY modify_date DESC LIMIT 1;",$campaign,$phone_number);
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $man_leadID_ct = mysql_num_rows($rslt);
                if ($man_leadID_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $affected_rows=1;
                    $lead_id =$row[0];
                    $CBleadIDset=1;
                    }
                else
                    {
                    ### insert a new lead in the system with this phone number
                    $stmt = "INSERT INTO osdial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                    $lead_id = mysql_insert_id($link);
                    $CBleadIDset=1;
                    }
                }
            else
                {
                ### insert a new lead in the system with this phone number
                $stmt = "INSERT INTO osdial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                $lead_id = mysql_insert_id($link);
                $CBleadIDset=1;
                }
            }
        else
            {
            $stmt="SELECT manual_dial_new_limit FROM osdial_users WHERE user='$user';";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $mdn_user_ct = mysql_num_rows($rslt);
            if ($mdn_user_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $mdn_limit =$row[0];
            }
            $stmt="SELECT manual_dial_new_today FROM osdial_campaign_agent_stats WHERE user='$user' AND campaign_id='$campaign';";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $mdn_user_ct = mysql_num_rows($rslt);
            if ($mdn_user_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $mdn_today =$row[0];
            }
            ### grab the next lead in the hopper for this campaign and reserve it for the user
            if ($mdn_limit < 0 or $mdn_today <= $mdn_limit) {
                $stmt = "UPDATE osdial_hopper SET status='QUEUE', user='$user' WHERE campaign_id='$campaign' AND status IN ('API','READY') ORDER BY status DESC, priority DESC, hopper_id LIMIT 1;";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
            } else {
                $stmt = "SELECT hopper_id FROM osdial_hopper AS oh,osdial_list AS ol WHERE oh.lead_id=ol.lead_id AND oh.campaign_id='$campaign' AND oh.status IN ('API','READY') AND ol.status!='NEW' ORDER BY oh.status DESC, oh.priority DESC, hopper_id LIMIT 1";
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $mdn_hopper_ct = mysql_num_rows($rslt);
                if ($mdn_hopper_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $hopper_id =$row[0];

                    $stmt = "UPDATE osdial_hopper SET status='QUEUE', user='$user' WHERE hopper_id='$hopper_id';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                }
            }
        }
    }

    if ($affected_rows > 0)
        {
        if (!$CBleadIDset)
            {
            ##### grab the lead_id of the reserved user in osdial_hopper
            $stmt="SELECT lead_id FROM osdial_hopper WHERE campaign_id='$campaign' AND status='QUEUE' AND user='$user' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $hopper_leadID_ct = mysql_num_rows($rslt);
            if ($hopper_leadID_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $lead_id =$row[0];
                }
            }

            ##### grab the data from osdial_list for the lead_id
            $stmt="SELECT * FROM osdial_list where lead_id='$lead_id' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $list_lead_ct = mysql_num_rows($rslt);
            if ($list_lead_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
            #	$lead_id		= trim("$row[0]");
                $dispo			= trim("$row[3]");
                $tsr			= trim("$row[4]");
                $vendor_id		= trim("$row[5]");
                $source_id		= trim("$row[6]");
                $list_id		= trim("$row[7]");
                $gmt_offset_now	= trim("$row[8]");
                $phone_code		= trim("$row[10]");
                $phone_number	= trim("$row[11]");
                $title			= trim("$row[12]");
                $first_name		= trim("$row[13]");
                $middle_initial	= trim("$row[14]");
                $last_name		= trim("$row[15]");
                $address1		= trim("$row[16]");
                $address2		= trim("$row[17]");
                $address3		= trim("$row[18]");
                $city			= trim("$row[19]");
                $state			= trim("$row[20]");
                $province		= trim("$row[21]");
                $postal_code	= trim("$row[22]");
                $country_code	= trim("$row[23]");
                $gender			= trim("$row[24]");
                $date_of_birth	= trim("$row[25]");
                $alt_phone		= trim("$row[26]");
                $email			= trim("$row[27]");
                $custom1		= trim("$row[28]");
                $comments		= trim("$row[29]");
                $called_count	= trim("$row[30]");
                $custom2		= trim("$row[31]");
                $external_key	= trim("$row[32]");
                $post_date  	= trim("$row[35]");
                }

            $called_count++;

            ##### check if system is set to generate logfile for transfers
            $stmt="SELECT enable_agc_xfer_log FROM system_settings;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $enable_agc_xfer_log_ct = mysql_num_rows($rslt);
            if ($enable_agc_xfer_log_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $enable_agc_xfer_log =$row[0];
                }

            if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) )
                {
                #	DATETIME|campaign|lead_id|phone_number|user|type
                #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|M
                $fp = fopen ("./xfer_log.txt", "a");
                fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|M\n");
                fclose($fp);
                }

            ##### if lead is a callback, grab the callback comments
            $CBentry_time =		'';
            $CBcallback_time =	'';
            $CBuser =			'';
            $CBcomments =		'';
            if (ereg("CALLBK",$dispo))
                {
                $stmt="SELECT entry_time,callback_time,user,comments FROM osdial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $cb_record_ct = mysql_num_rows($rslt);
                if ($cb_record_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $CBentry_time =		trim("$row[0]");
                    $CBcallback_time =	trim("$row[1]");
                    $CBuser =			trim("$row[2]");
                    $CBcomments =		trim("$row[3]");
                    }
                }

            if ($hopper_leadID_ct > 0) {
                if (ereg("NEW",$dispo)) {
                    $stmt = "UPDATE osdial_campaign_agent_stats SET manual_dial_new_today=manual_dial_new_today+1 WHERE user='$user' AND campaign_id='$campaign';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $link);
                }
            }

            $stmt = "SELECT local_gmt FROM servers where active='Y' limit 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $server_ct = mysql_num_rows($rslt);
            if ($server_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $local_gmt =    $row[0];
                }
            $LLCT_DATE_offset = ($local_gmt - $gmt_offset_now);
            $LLCT_DATE = date("Y-m-d H:i:s", mktime(date("H")-$LLCT_DATE_offset,date("i"),date("s"),date("m"),date("d"),date("Y")));

            if (ereg('Y',$called_since_last_reset))
                {
                $called_since_last_reset = ereg_replace('Y','',$called_since_last_reset);
                if (strlen($called_since_last_reset) < 1) {$called_since_last_reset = 0;}
                $called_since_last_reset++;
                $called_since_last_reset = "Y$called_since_last_reset";
                }
            else {$called_since_last_reset = 'Y';}
            ### flag the lead as called and change it's status to INCALL
            $stmt = "UPDATE osdial_list set status='INCALL', called_since_last_reset='$called_since_last_reset', called_count='$called_count',user='$user',last_local_call_time='$LLCT_DATE' where lead_id='$lead_id';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);

            if (!$CBleadIDset)
                {
                ### delete the lead from the hopper
                $stmt = "DELETE FROM osdial_hopper WHERE lead_id='$lead_id';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                }

            $stmt="UPDATE osdial_agent_log set lead_id='$lead_id',comments='MANUAL',prev_status='$dispo',lead_called_count='$called_count' where agent_log_id='$agent_log_id';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);

            ### if preview dialing, do not send the call	
            if ( (strlen($preview)<1) || ($preview == 'NO') )
                {
                ### prepare variables to place manual call from OSDiaL
                $CCID_on=0;
                $CCID='';
                $CCID_NAME='';
                $local_DEF = 'Local/';
                $local_AMP = '@';
                $Local_out_prefix = '9';
                $Local_dial_timeout = '60';
            #	$Local_persist = '/n';
                                $Local_persist = '';
                if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
                $Local_dial_timeout = ($Local_dial_timeout * 1000);
                if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
                if (strlen($campaign_cid) > 6) {
                    $CCID = "$campaign_cid";
                    $CCID_NAME = "$campaign_cid_name";
                    $CCID_on++;
                }
                if (eregi("x",$dial_prefix)) {$Local_out_prefix = '';}

                $PADlead_id = sprintf("%09s", $lead_id);
                    while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}

                # Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
                    $MqueryCID = "M$CIDdate$PADlead_id";
                if ($CCID_on) {$CIDstring = "\"$CCID_NAME\" <$CCID>";}
                else {$CIDstring = "\"\" <0000000000>";}

                ### whether to omit phone_code or not
                if (eregi('Y',$omit_phone_code)) 
                    {$Ndialstring = "$Local_out_prefix$phone_number";}
                else
                    {$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}
                ### insert the call action into the osdial_manager table to initiate the call
                #	$stmt = "INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
                $stmt = "INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','Account: $MqueryCID','','','');";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);

                $stmt = "INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$phone_number','$NOW_TIME','OUT')";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);

                ### update the agent status to INCALL in osdial_live_agents
                $random = (rand(1000000, 9999999) + 10000000);
                $stmt = "UPDATE osdial_live_agents set status='INCALL',last_call_time='$NOW_TIME',callerid='$MqueryCID',lead_id='$lead_id',comments='MANUAL',calls_today='$calls_today',random_id='$random' where user='$user' and server_ip='$server_ip';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);

                ### update calls_today count in osdial_campaign_agents
                $stmt = "UPDATE osdial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                }

            $comments = eregi_replace("\r",'',$comments);
            $comments = eregi_replace("\n",'!N',$comments);

            $LeaD_InfO =	$MqueryCID . "\n";
            $LeaD_InfO .=	$lead_id . "\n";
            $LeaD_InfO .=	$dispo . "\n";
            $LeaD_InfO .=	$tsr . "\n";
            $LeaD_InfO .=	$vendor_id . "\n";
            $LeaD_InfO .=	$list_id . "\n";
            $LeaD_InfO .=	$gmt_offset_now . "\n";
            $LeaD_InfO .=	$phone_code . "\n";
            $LeaD_InfO .=	$phone_number . "\n";
            $LeaD_InfO .=	$title . "\n";
            $LeaD_InfO .=	$first_name . "\n";
            $LeaD_InfO .=	$middle_initial . "\n";
            $LeaD_InfO .=	$last_name . "\n";
            $LeaD_InfO .=	$address1 . "\n";
            $LeaD_InfO .=	$address2 . "\n";
            $LeaD_InfO .=	$address3 . "\n";
            $LeaD_InfO .=	$city . "\n";
            $LeaD_InfO .=	$state . "\n";
            $LeaD_InfO .=	$province . "\n";
            $LeaD_InfO .=	$postal_code . "\n";
            $LeaD_InfO .=	$country_code . "\n";
            $LeaD_InfO .=	$gender . "\n";
            $LeaD_InfO .=	$date_of_birth . "\n";
            $LeaD_InfO .=	$alt_phone . "\n";
            $LeaD_InfO .=	$email . "\n";
            $LeaD_InfO .=	$custom1 . "\n";
            $LeaD_InfO .=	$comments . "\n";
            $LeaD_InfO .=	$called_count . "\n";
            $LeaD_InfO .=	$CBentry_time . "\n";
            $LeaD_InfO .=	$CBcallback_time . "\n";
            $LeaD_InfO .=	$CBuser . "\n";
            $LeaD_InfO .=	$CBcomments . "\n";
            $LeaD_InfO .=	$phone_number . "\n";
            $LeaD_InfO .=	"MAIN\n";
            $LeaD_InfO .=	$source_id . "\n";
            $LeaD_InfO .=	$custom2 . "\n";
            $LeaD_InfO .=	$external_key . "\n";
            $LeaD_InfO .=	$post_date . "\n";

            $web_form_address = "";
            $web_form_address2 = "";
            $web_form_extwindow = "";
            $web_form2_extwindow = "";
            $campaign_script = "";

            # Get web_form_address vars from campaign.
            $stmt = "SELECT web_form_address,web_form_address2,web_form_extwindow,web_form2_extwindow,campaign_script FROM osdial_campaigns WHERE campaign_id='$campaign';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $list_cnt = mysql_num_rows($rslt);
            if ($list_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                $web_form_address = $row[0];
                $web_form_address2 = $row[1];
                $web_form_extwindow = $row[2];
                $web_form2_extwindow = $row[3];
                $campaign_script = $row[4];
            }

            # Get web_form_address vars from list.
            $stmt = "SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='$list_id';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $list_cnt = mysql_num_rows($rslt);
            if ($list_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if ($row[0] != "") $web_form_address = $row[0];
                if ($row[1] != "") $web_form_address2 = $row[1];
                if ($row[2] != "") $campaign_script = $row[2];
            }

            # Get script override from user.
            $stmt = "SELECT script_override FROM osdial_users WHERE user='$user';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $user_cnt = mysql_num_rows($rslt);
            if ($user_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if ($row[0] != "") $campaign_script = $row[0];
            }

            $LeaD_InfO .=	$web_form_address . "\n";
            $LeaD_InfO .=	$web_form_address2 . "\n";
            $LeaD_InfO .=	$web_form_extwindow . "\n";
            $LeaD_InfO .=	$web_form2_extwindow . "\n";
            $LeaD_InfO .=	$campaign_script . "\n";

            $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
            $cnt = 0;
            foreach ($forms as $form) {
                $fcamps = split(',',$form['campaigns']);
                foreach ($fcamps as $fcamp) {
                    if ($fcamp == 'ALL' or $fcamp == $campaign) {
                        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
                        if (is_array($fields)) {
                            foreach ($fields as $field) {
                                $vdlf = get_first_record($link, 'osdial_list_fields', '*', "lead_id='" . $lead_id . "' AND field_id='" . $field['id'] . "'");
                                $LeaD_InfO .= $vdlf['value'] . "\n";
                                $cnt++;
                            }
                        }
                    }
                }
            }

            echo $LeaD_InfO;

        }
        else
        {
        echo "HOPPER EMPTY\n";
        }
    }
}


################################################################################
### manDiaLskip - for manual OSDiaL dialing this skips the lead that was
###               previewed in the step above and puts it back in orig status
################################################################################
if ($ACTION == 'manDiaLskip')
{
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (strlen($stage)<1) || (strlen($called_count)<1) || (strlen($lead_id)<1) )
    {
        $channel_live=0;
        echo "LEAD NOT REVERTED\n";
        echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        exit;
    }
    else
    {
        $called_count = ($called_count - 1);
        ### flag the lead as called and change it's status to INCALL
        $stmt = "UPDATE osdial_list set status='$stage', called_count='$called_count',user='$user' where lead_id='$lead_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);


        echo "LEAD REVERTED\n";
    }
}


################################################################################
### manDiaLonly - for manual OSDiaL dialing this sends the call that was
###               previewed in the step above
################################################################################
if ($ACTION == 'manDiaLonly')
{
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (strlen($conf_exten)<1) || (strlen($campaign)<1) || (strlen($ext_context)<1) || (strlen($phone_number)<1) || (strlen($lead_id)<1) )
    {
        $channel_live=0;
        echo " CALL NOT PLACED\n";
        echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        exit;
    }
    else
    {
        ##### grab number of calls today in this campaign and increment
        $stmt="SELECT calls_today FROM osdial_live_agents WHERE user='$user' and campaign_id='$campaign';";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $vla_cc_ct = mysql_num_rows($rslt);
        if ($vla_cc_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
            $calls_today =$row[0];
            }
        else
            {$calls_today ='0';}
        $calls_today++;

        ### prepare variables to place manual call from OSDiaL
        $CCID_on=0;
        $CCID='';
        $CCID_NAME='';
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $Local_out_prefix = '9';
        $Local_dial_timeout = '60';
        $Local_persist = '/n';
        if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
        $Local_dial_timeout = ($Local_dial_timeout * 1000);
        if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
        if (strlen($campaign_cid) > 6) {
            $CCID = "$campaign_cid";
            $CCID_NAME = "$campaign_cid_name";
            $CCID_on++;
        }
        if (eregi("x",$dial_prefix)) {$Local_out_prefix = '';}

        $PADlead_id = sprintf("%09s", $lead_id);
            while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}

        # Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
            $MqueryCID = "M$CIDdate$PADlead_id";
        if ($CCID_on) {$CIDstring = "\"$CCID_NAME\" <$CCID>";}
        else {$CIDstring = "\"\" <0000000000>";}

        ### whether to omit phone_code or not
        if (eregi('Y',$omit_phone_code)) 
            {$Ndialstring = "$Local_out_prefix$phone_number";}
        else
            {$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}
        ### insert the call action into the osdial_manager table to initiate the call
        #	$stmt = "INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
        $stmt = "INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','Account: $MqueryCID','','','');";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        $stmt = "INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$phone_number','$NOW_TIME','OUT')";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        ### update the agent status to INCALL in osdial_live_agents
        $random = (rand(1000000, 9999999) + 10000000);
        $stmt = "UPDATE osdial_live_agents set status='INCALL',last_call_time='$NOW_TIME',callerid='$MqueryCID',lead_id='$lead_id',comments='MANUAL',calls_today='$calls_today',random_id='$random' where user='$user' and server_ip='$server_ip';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        $stmt = "UPDATE osdial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        echo "$MqueryCID\n";
    }
}


################################################################################
### manDiaLlookCALL - for manual OSDiaL dialing this will attempt to look up
###                   the trunk channel that the call was placed on
################################################################################
if ($ACTION == 'manDiaLlookCaLL')
{
    $MT[0]='';
    $row='';   $rowx='';
if (strlen($MDnextCID)<18)
    {
    echo "NO\n";
    echo "MDnextCID $MDnextCID is not valid\n";
    exit;
    }
else
    {
    ##### look for the channel in the UPDATED osdial_manager record of the call initiation
    $stmt="SELECT uniqueid,channel FROM osdial_manager where callerid='$MDnextCID' and server_ip='$server_ip' and status IN ('UPDATED','DEAD') LIMIT 1;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {echo "$stmt\n";}
    $VM_mancall_ct = mysql_num_rows($rslt);
    if ($VM_mancall_ct > 0)
        {
        $row=mysql_fetch_row($rslt);
        $uniqueid =$row[0];
        $channel =$row[1];
        echo "$uniqueid\n$channel";

        $wait_sec=0;
        $stmt = "select wait_epoch,wait_sec from osdial_agent_log where agent_log_id='$agent_log_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
            $wait_sec = (($StarTtime - $row[0]) + $row[1]);
            if ($wait_sec<0) $wait_sec=0;

            $stmt="UPDATE osdial_agent_log set wait_sec='$wait_sec',wait_epoch='$StarTtime',talk_epoch='$StarTtime',lead_id='$lead_id' where agent_log_id='$agent_log_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
            }

        $stmt="UPDATE osdial_auto_calls set uniqueid='$uniqueid',channel='$channel' where callerid='$MDnextCID';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        }
    else
        {
        echo "NO\n";
        }
    }
}



################################################################################
### manDiaLlogCALL - for manual OSDiaL logging of calls places record in
###                  osdial_log and then sends process to call_log entry
################################################################################
if ($ACTION == 'manDiaLlogCaLL')
{
    $MT[0]='';
    $row='';   $rowx='';

if ($stage == "start")
    {
    if ( (strlen($uniqueid)<1) || (strlen($lead_id)<1) || (strlen($list_id)<1) || (strlen($phone_number)<1) || (strlen($campaign)<1) )
        {
        echo "LOG NOT ENTERED\n";
        echo "uniqueid $uniqueid or lead_id: $lead_id or list_id: $list_id or phone_number: $phone_number or campaign: $campaign is not valid\n";
        exit;
        }
    else
        {
            $user_group='';
            $stmt="SELECT user_group FROM osdial_users where user='$user' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $user_group =		trim("$row[0]");
                }
        ##### insert log into osdial_log for manual OSDiaL call
        $stmt="INSERT INTO osdial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,server_ip) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtime','INCALL','$phone_code','$phone_number','$user','MANUAL','N','$user_group','$server_ip');";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $affected_rows = mysql_affected_rows($link);

        if ($affected_rows > 0)
            {
            echo "OSDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n";
            echo "$StarTtime\n";
            }
        else
            {
            echo "LOG NOT ENTERED\n";
            }

        $stmt = "UPDATE osdial_auto_calls SET uniqueid='$uniqueid' where lead_id='$lead_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

    #	##### insert log into call_log for manual OSDiaL call
    #	$stmt = "INSERT INTO call_log (uniqueid,channel,server_ip,extension,number_dialed,caller_code,start_time,start_epoch) values('$uniqueid','$channel','$server_ip','$exten','$phone_code$phone_number','MD $user $lead_id','$NOW_TIME','$StarTtime')";
    #	if ($DB) {echo "$stmt\n";}
    #	$rslt=mysql_query($stmt, $link);
    #	$affected_rows = mysql_affected_rows($link);

    #	if ($affected_rows > 0)
    #		{
    #		echo "CALL_LOG Inserted: $uniqueid|$channel|$NOW_TIME";
    #		}
    #	else
    #		{
    #		echo "LOG NOT ENTERED\n";
    #		}
        }
    }

if ($stage == "end")
    {
    $LAcomments='NONE';
    $stmt = "select comments from osdial_live_agents where user='$user' order by last_update_time desc limit 1;";
    if ($format=='debug') {echo "\n<!-- $stmt -->";}
    $rslt=mysql_query($stmt, $link);
    $LAcnt = mysql_num_rows($rslt);
    if ($LAcnt>0) {
        $row=mysql_fetch_row($rslt);
        $LAcomments = $row[0];
    }

    if ( (strlen($uniqueid)<1) || (strlen($lead_id)<1) )
        {
        echo "LOG NOT ENTERED\n";
        echo "uniqueid $uniqueid or lead_id: $lead_id is not valid\n";
        exit;
        }
    else
        {
        $term_reason='NONE';
        $four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
        if ($start_epoch < 1000)
            {
            if ($LAcomments == 'INBOUND')
                {
                ##### look for the start epoch in the osdial_closer_log table
                $stmt="SELECT start_epoch,term_reason,closecallid,campaign_id FROM osdial_closer_log where phone_number='$phone_number' and lead_id='$lead_id' and user='$user' and call_date > '$four_hours_ago' order by closecallid desc limit 1;";
                }
            else
                {
                ##### look for the start epoch in the osdial_log table
                $stmt="SELECT start_epoch,term_reason,uniqueid,campaign_id FROM osdial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
                }
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $VM_mancall_ct = mysql_num_rows($rslt);
            if ($VM_mancall_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $start_epoch =$row[0];
                $Lterm_reason =$row[1];
                $Luniqueid =$row[2];
                $Lcampaign_id =$row[3];
                $length_in_sec = ($StarTtime - $start_epoch);
                }
            else
                {
                $length_in_sec = 0;
                }

            if ( ($length_in_sec < 1) and ($LAcomments == 'INBOUND') )
                {
                ##### start epoch in the osdial_log table, couldn't find one in osdial_closer_log
                $stmt="SELECT start_epoch,term_reason,campaign_id FROM osdial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $VM_mancall_ct = mysql_num_rows($rslt);
                if ($VM_mancall_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $start_epoch =$row[0];
                    $Lterm_reason =$row[1];
                    $Lcampaign_id =$row[2];
                    $length_in_sec = ($StarTtime - $start_epoch);
                    }
                else
                    {
                    $length_in_sec = 0;
                    }
                }
            }
        else {$length_in_sec = ($StarTtime - $start_epoch);}

        if (strlen($Lcampaign_id)<1) {$Lcampaign_id = $campaign;}

        if ($LAcomments == 'INBOUND')
            {
            $stmt = "UPDATE osdial_closer_log set end_epoch='$StarTtime', length_in_sec='$length_in_sec' where lead_id='$lead_id' and user = '$user' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            if ($affected_rows > 0)
                {
                echo "$uniqueid\n$channel\n";
                }
            }

        #############################################
        ##### START QUEUEMETRICS LOGGING LOOKUP #####
        $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $qm_conf_ct = mysql_num_rows($rslt);
        $i=0;
        while ($i < $qm_conf_ct)
            {
            $row=mysql_fetch_row($rslt);
            $enable_queuemetrics_logging =	$row[0];
            $queuemetrics_server_ip	=		$row[1];
            $queuemetrics_dbname =			$row[2];
            $queuemetrics_login	=			$row[3];
            $queuemetrics_pass =			$row[4];
            $queuemetrics_log_id =			$row[5];
            $i++;
            }
        ##### END QUEUEMETRICS LOGGING LOOKUP #####
        ###########################################

        if ($auto_dial_level > 0)
            {
            ### check to see if campaign has alt_dial enabled
            $stmt="SELECT auto_alt_dial FROM osdial_campaigns where campaign_id='$campaign';";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $VAC_mancall_ct = mysql_num_rows($rslt);
            if ($VAC_mancall_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $auto_alt_dial =$row[0];
                }
            else {$auto_alt_dial = 'NONE';}
            if (eregi("(ALT_ONLY|ADDR3_ONLY|ALT_AND_ADDR3)",$auto_alt_dial))
                {
                ### check to see if lead should be alt_dialed
                $stmt="SELECT alt_dial FROM osdial_auto_calls where lead_id='$lead_id';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $VAC_mancall_ct = mysql_num_rows($rslt);
                if ($VAC_mancall_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $alt_dial =$row[0];
                    }
                else {$alt_dial = 'NONE';}

                if ( (eregi("(NONE|MAIN)",$alt_dial)) and (eregi("(ALT_ONLY|ALT_AND_ADDR3)",$auto_alt_dial)) )
                    {
                    $stmt="SELECT alt_phone,gmt_offset_now,state FROM osdial_list where lead_id='$lead_id';";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) {echo "$stmt\n";}
                    $VAC_mancall_ct = mysql_num_rows($rslt);
                    if ($VAC_mancall_ct > 0)
                        {
                        $row=mysql_fetch_row($rslt);
                        $alt_phone =		$row[0];
                        $alt_phone = eregi_replace("[^0-9]","",$alt_phone);
                        $gmt_offset_now =	$row[1];
                        $state =			$row[2];
                        }
                    else {$alt_phone = '';}
                    if (strlen($alt_phone)>5)
                        {
                        ### insert record into osdial_hopper for alt_phone call attempt
                        $stmt = "INSERT INTO osdial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='ALT',user='',priority='25';";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysql_query($stmt, $link);
                        }
                    }
                if ( ( (eregi("(ALT)",$alt_dial)) and (eregi("ALT_AND_ADDR3",$auto_alt_dial)) ) or ( (eregi("(NONE|MAIN)",$alt_dial)) and (eregi("ADDR3_ONLY",$auto_alt_dial)) ) )
                    {
                    $stmt="SELECT address3,gmt_offset_now,state FROM osdial_list where lead_id='$lead_id';";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) {echo "$stmt\n";}
                    $VAC_mancall_ct = mysql_num_rows($rslt);
                    if ($VAC_mancall_ct > 0)
                        {
                        $row=mysql_fetch_row($rslt);
                        $address3 =			$row[0];
                        $address3 = eregi_replace("[^0-9]","",$address3);
                        $gmt_offset_now =	$row[1];
                        $state =			$row[2];
                        }
                    else {$address3 = '';}
                    if (strlen($address3)>5)
                        {
                        ### insert record into osdial_hopper for address3 call attempt
                        $stmt = "INSERT INTO osdial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='ADDR3',user='',priority='20';";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysql_query($stmt, $link);
                        }
                    }
                }

            if ($enable_queuemetrics_logging > 0)
                {
                ### check to see if lead should be alt_dialed
                $stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid from osdial_auto_calls where lead_id='$lead_id';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $VAC_qm_ct = mysql_num_rows($rslt);
                if ($VAC_qm_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $auto_call_id	= $row[0];
                    $CLlead_id		= $row[1];
                    $CLphone_number	= $row[2];
                    $CLstatus		= $row[3];
                    $CLcampaign_id	= $row[4];
                    $CLphone_code	= $row[5];
                    $CLalt_dial		= $row[6];
                    $CLstage		= $row[7];
                    $CLcallerid		= $row[8];
                    $CLuniqueid		= $row[9];
                    }

                $CLstage = preg_replace("/.*-/",'',$CLstage);
                if (strlen($CLstage) < 1) {$CLstage=0;}

                $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                mysql_select_db("$queuemetrics_dbname", $linkB);

                $stmt="SELECT count(*) from queue_log where call_id='$MDnextCID' and verb='COMPLETECALLER';";
                $rslt=mysql_query($stmt, $linkB);
                if ($DB) {echo "$stmt\n";}
                $VAC_cc_ct = mysql_num_rows($rslt);
                if ($VAC_cc_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $caller_complete	= $row[0];
                    }

                if ($caller_complete < 1)
                    {
                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$campaign',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='1',serverid='$queuemetrics_log_id';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    }
                mysql_close($linkB);
                }

            ### delete call record from  osdial_auto_calls
            $stmt = "DELETE from osdial_auto_calls where lead_id='$lead_id' and campaign_id='$campaign' and uniqueid='$uniqueid';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);


            $random = (rand(1000000, 9999999) + 10000000);
            $stmt = "UPDATE osdial_live_agents set status='PAUSED',uniqueid='',lead_id='',callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',random_id='$random',comments='DISPO' where user='$user' and server_ip='$server_ip';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            if ($affected_rows > 0) 
                {
                if ($enable_queuemetrics_logging > 0)
                    {
                    $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                    mysql_select_db("$queuemetrics_dbname", $linkB);

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);

                    mysql_close($linkB);
                    }
                }
            }
        else
            {
            if ($enable_queuemetrics_logging > 0)
                {
                ### check to see if lead should be alt_dialed
                $stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid from osdial_auto_calls where lead_id='$lead_id';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) {echo "$stmt\n";}
                $VAC_qm_ct = mysql_num_rows($rslt);
                if ($VAC_qm_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $auto_call_id	= $row[0];
                    $CLlead_id		= $row[1];
                    $CLphone_number	= $row[2];
                    $CLstatus		= $row[3];
                    $CLcampaign_id	= $row[4];
                    $CLphone_code	= $row[5];
                    $CLalt_dial		= $row[6];
                    $CLstage		= $row[7];
                    $CLcallerid		= $row[8];
                    $CLuniqueid		= $row[9];
                    }

                $CLstage = preg_replace("/XFER|CLOSER|-/",'',$CLstage);
                if ($CLstage < 0.25) {$CLstage=0;}

                $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                mysql_select_db("$queuemetrics_dbname", $linkB);

                $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$campaign',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='1',serverid='$queuemetrics_log_id';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);

                mysql_close($linkB);
                }

            $stmt = "DELETE from osdial_auto_calls where lead_id='$lead_id' and campaign_id='$campaign' and callerid LIKE \"M%\";";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);

            $random = (rand(1000000, 9999999) + 10000000);
            $stmt = "UPDATE osdial_live_agents set status='PAUSED',uniqueid='',lead_id='',callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',random_id='$random',comments='DISPO' where user='$user' and server_ip='$server_ip';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            if ($affected_rows > 0) 
                {
                if ($enable_queuemetrics_logging > 0)
                    {
                    $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                    mysql_select_db("$queuemetrics_dbname", $linkB);

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);

                    mysql_close($linkB);
                    }
                }
            }

        if (preg_match('/AUTO|MANUAL/',$LAcomments))
            {
            $SQLterm = "term_reason='$term_reason',";

            if ( (ereg("NONE",$term_reason)) or (ereg("NONE",$Lterm_reason)) or (strlen($Lterm_reason) < 1) )
                {
                ### check to see if lead should be alt_dialed
                $stmt="SELECT term_reason,uniqueid from osdial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
                $rslt=mysql_query($stmt, $link);
                $VAC_qm_ct = mysql_num_rows($rslt);
                if ($VAC_qm_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $Lterm_reason  = $row[0];
                    $Luniqueid  = $row[1];
                    }
                if (ereg("CALLER",$Lterm_reason))
                    {
                    $SQLterm = "";
                    }
                else
                    {
                    $SQLterm = "term_reason='AGENT',";
                    }
                }

            ### check to see if the osdial_log record exists, if not, insert it
            $stmt="SELECT count(*) from osdial_log where uniqueid='$uniqueid' and lead_id='$lead_id';";
            $rslt=mysql_query($stmt, $link);
            $VAC_vld_ct = mysql_num_rows($rslt);
            if ($VAC_vld_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $VLD_count  = $row[0];
                if ($VLD_count < 1)
                    {
                    ##### insert log into osdial_log for manual OSDiaL call
                    $stmt="INSERT INTO osdial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,server_ip) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtime','DONEM','$phone_code','$phone_number','$user','MANUAL','N','$user_group','$server_ip');";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);

                    if ($affected_rows > 0)
                        {
                        echo "OSDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n";
                        echo "$StarTtime\n";
                        }
                    else
                        {
                        echo "LOG NOT ENTERED\n";
                        }
                    }
                }

            ##### update the duration and end time in the osdial_log table
            $stmt="UPDATE osdial_log set $SQLterm end_epoch='$StarTtime', length_in_sec='$length_in_sec' where uniqueid='$uniqueid' and lead_id='$lead_id' and user='$user' order by call_date desc limit 1;";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);

            if ($affected_rows > 0)
                {
                echo "$uniqueid\n$channel\n";
                }
            else
                {
                echo "LOG NOT ENTERED\n\n";
                }
            }
        else
            {
            $SQLterm = "term_reason='$term_reason'";
            $QL_term='';

            if ( (ereg("NONE",$term_reason)) or (ereg("NONE",$Lterm_reason)) or (strlen($Lterm_reason) < 1) )
                {
                ### check to see if lead should be alt_dialed
                $stmt="SELECT term_reason,closecallid from osdial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
                $rslt=mysql_query($stmt, $link);
                $VAC_qm_ct = mysql_num_rows($rslt);
                if ($VAC_qm_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $Lterm_reason  = $row[0];
                    $Luniqueid  = $row[1];
                    }
                if (ereg("CALLER",$Lterm_reason))
                    {
                    $SQLterm = "";
                    }
                else
                    {
                    $SQLterm = "term_reason='AGENT'";
                    $QL_term = 'COMPLETEAGENT';
                    }
                }

            if (strlen($SQLterm) > 0)
                {
                ##### update the duration and end time in the osdial_log table
                $stmt="UPDATE osdial_closer_log set $SQLterm where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                }

            if ($enable_queuemetrics_logging > 0)
                {
                if ( (strlen($QL_term) > 0) and ($leaving_threeway > 0) )
                    {
                    $stmt="SELECT count(*) from queue_log where call_id='$MDnextCID' and verb='COMPLETEAGENT' and queue='$Lcampaign_id';";
                    $rslt=mysql_query($stmt, $linkB);
                    if ($DB) {echo "$stmt\n";}
                    $VAC_cc_ct = mysql_num_rows($rslt);
                    if ($VAC_cc_ct > 0)
                        {
                        $row=mysql_fetch_row($rslt);
                        $agent_complete = $row[0];
                        }
                    if ($agent_complete < 1)
                        {
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$Lcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='1',serverid='$queuemetrics_log_id';";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysql_query($stmt, $linkB);
                        $affected_rows = mysql_affected_rows($linkB);
                        }
                    }
                }
            }
        }

    echo $VDstop_rec_after_each_call . '|' . $extension . '|' . $conf_silent_prefix . '|' . $conf_exten . '|' . $user_abb . "|\n";

    ##### if OSDiaL call and hangup_after_each_call activated, find all recording 
    ##### channels and hang them up while entering info into recording_log and 
    ##### returning filename/recordingID
    if ($VDstop_rec_after_each_call == 1)
        {
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $total_rec=0;
        $loop_count=0;
        $stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten' order by channel desc;";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        if ($rslt) {$rec_list = mysql_num_rows($rslt);}
            while ($rec_list>$loop_count)
            {
            $row=mysql_fetch_row($rslt);
            if (preg_match("/Local\/$conf_silent_prefix$conf_exten\@/i",$row[0]))
                {
                $rec_channels[$total_rec] = "$row[0]";
                $total_rec++;
                }
            if ($format=='debug') {echo "\n<!-- $row[0] -->";}
            $loop_count++; 
            }

        $total_recFN=0;
        $loop_count=0;
        $filename=$MT;		# not necessary : and cmd_line_f LIKE \"%_$user_abb\"
        $stmt="SELECT cmd_line_f FROM osdial_manager where server_ip='$server_ip' and action='Originate' and cmd_line_b = 'Channel: $local_DEF$conf_silent_prefix$conf_exten$local_AMP$ext_context' order by entry_date desc limit $total_rec;";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        if ($rslt) {$recFN_list = mysql_num_rows($rslt);}
            while ($recFN_list>$loop_count)
            {
            $row=mysql_fetch_row($rslt);
            $filename[$total_recFN] = preg_replace("/Callerid: /i","",$row[0]);
            if ($format=='debug') {echo "\n<!-- $row[0] -->";}
            $total_recFN++;
            $loop_count++; 
            }

        $loop_count=0;
        while($loop_count < $total_rec)
            {
            if (strlen($rec_channels[$loop_count])>5)
                {
                $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtime$loop_count','Channel: $rec_channels[$loop_count]','','','','','','','','','');";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysql_query($stmt, $link);

                echo "REC_STOP|$rec_channels[$loop_count]|$filename[$loop_count]|";
                if (strlen($filename)>2)
                    {
                    $stmt="SELECT recording_id,start_epoch FROM recording_log where filename='$filename[$loop_count]'";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysql_query($stmt, $link);
                    if ($rslt) {$fn_count = mysql_num_rows($rslt);}
                    if ($fn_count)
                        {
                        $row=mysql_fetch_row($rslt);
                        $recording_id = $row[0];
                        $start_time = $row[1];

                        $length_in_sec = ($StarTtime - $start_time);
                        $length_in_min = ($length_in_sec / 60);
                        $length_in_min = sprintf("%8.2f", $length_in_min);

                        $stmt="UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec='$length_in_sec',length_in_min='$length_in_min',uniqueid='$uniqueid' where filename='$filename[$loop_count]' and end_epoch is NULL;";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysql_query($stmt, $link);

                        echo "$recording_id|$length_in_min|";
                        }
                    else {echo "||";}
                    }
                else {echo "||";}
                echo "\n";
                }
            $loop_count++;
            }
        }


    $talk_sec=0;
    $talk_epochSQL='';
    $StarTtime = date("U");
    $stmt = "select talk_epoch,talk_sec,wait_sec from osdial_agent_log where agent_log_id='$agent_log_id';";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);

    $VDpr_ct = mysql_num_rows($rslt);
    if ($VDpr_ct > 0) {
        $row=mysql_fetch_row($rslt);
        if ( (eregi("NULL",$row[0])) or ($row[0] < 1000) ) {
            $talk_epochSQL=",talk_epoch='$StarTtime'";
            $row[0]=$row[2];
        }
        $talk_sec = (($StarTtime - $row[0]) + $row[1]);
        if ($talk_sec<0) $talk_sec=0;
        $stmt="UPDATE osdial_agent_log set talk_sec='$talk_sec',dispo_epoch='$StarTtime',uniqueid='$uniqueid' $talk_epochSQL where agent_log_id='$agent_log_id';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        }

    }
}


################################################################################
### VDADREcheckINCOMING - for auto-dial OSDiaL dialing this will recheck for
###                       calls to see if the channel has updated
################################################################################
if ($ACTION == 'VDADREcheckINCOMING')
{
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (strlen($campaign)<1) || (strlen($server_ip)<1) || (strlen($lead_id)<1) )
    {
    $channel_live=0;
    echo "0\n";
    echo "Campaign $campaign is not valid\n";
    echo "lead_id $lead_id is not valid\n";
    exit;
    }
    else
    {
    ### grab the call and lead info from the osdial_live_agents table
    $stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip FROM osdial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and lead_id='$lead_id';";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    $queue_leadID_ct = mysql_num_rows($rslt);

    if ($queue_leadID_ct > 0)
        {
        $row=mysql_fetch_row($rslt);
        $lead_id	=$row[0];
        $uniqueid	=$row[1];
        $callerid	=$row[2];
        $channel	=$row[3];
        $call_server_ip	=$row[4];
            if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
        echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";
        }
    }
}


################################################################################
### VDADcheckINCOMING - for auto-dial OSDiaL dialing this will check for calls
###                     in the osdial_live_agents table in QUEUE status, then
###                     lookup the lead info and pass it back to osdial.php
################################################################################
if ($ACTION == 'VDADcheckINCOMING')
{
    $Ctype = 'A';
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (strlen($campaign)<1) || (strlen($server_ip)<1) )
    {
    $channel_live=0;
    echo "0\n";
    echo "Campaign $campaign is not valid\n";
    exit;
    }
    else
    {
    ### grab the call and lead info from the osdial_live_agents table
    $stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip FROM osdial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and status='QUEUE';";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    $queue_leadID_ct = mysql_num_rows($rslt);

    if ($queue_leadID_ct > 0)
        {
        $row=mysql_fetch_row($rslt);
        $lead_id	=$row[0];
        $uniqueid	=$row[1];
        $callerid	=$row[2];
        $channel	=$row[3];
        $call_server_ip	=$row[4];
            if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
        echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";

        ##### grab number of calls today in this campaign and increment
        $stmt="SELECT calls_today FROM osdial_live_agents WHERE user='$user' and campaign_id='$campaign';";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $vla_cc_ct = mysql_num_rows($rslt);
        if ($vla_cc_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
            $calls_today =$row[0];
            }
        else
            {$calls_today ='0';}
        $calls_today++;

        ### update the agent status to INCALL in osdial_live_agents
        $random = (rand(1000000, 9999999) + 10000000);
        $stmt = "UPDATE osdial_live_agents set status='INCALL',last_call_time='$NOW_TIME',comments='AUTO',calls_today='$calls_today',random_id='$random' where user='$user' and server_ip='$server_ip';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        $stmt = "UPDATE osdial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        ##### grab the data from osdial_list for the lead_id
        $stmt="SELECT * FROM osdial_list where lead_id='$lead_id' LIMIT 1;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $list_lead_ct = mysql_num_rows($rslt);
        if ($list_lead_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
        #	$lead_id		= trim("$row[0]");
            $dispo			= trim("$row[3]");
            $tsr			= trim("$row[4]");
            $vendor_id		= trim("$row[5]");
            $source_id		= trim("$row[6]");
            $list_id		= trim("$row[7]");
            $gmt_offset_now	= trim("$row[8]");
            $phone_code		= trim("$row[10]");
            $phone_number	= trim("$row[11]");
            $title			= trim("$row[12]");
            $first_name		= trim("$row[13]");
            $middle_initial	= trim("$row[14]");
            $last_name		= trim("$row[15]");
            $address1		= trim("$row[16]");
            $address2		= trim("$row[17]");
            $address3		= trim("$row[18]");
            $city			= trim("$row[19]");
            $state			= trim("$row[20]");
            $province		= trim("$row[21]");
            $postal_code	= trim("$row[22]");
            $country_code	= trim("$row[23]");
            $gender			= trim("$row[24]");
            $date_of_birth	= trim("$row[25]");
            $alt_phone		= trim("$row[26]");
            $email			= trim("$row[27]");
            $custom1		= trim("$row[28]");
            $comments		= stripslashes(trim("$row[29]"));
            $called_count	= trim("$row[30]");
            $custom2		= trim("$row[31]");
            $external_key	= trim("$row[32]");
            $post_date  	= trim("$row[35]");
            }

        ##### if lead is a callback, grab the callback comments
        $CBentry_time =		'';
        $CBcallback_time =	'';
        $CBuser =			'';
        $CBcomments =		'';
        if (ereg("CALLBK",$dispo))
            {
            $stmt="SELECT entry_time,callback_time,user,comments FROM osdial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $cb_record_ct = mysql_num_rows($rslt);
            if ($cb_record_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $CBentry_time =		trim("$row[0]");
                $CBcallback_time =	trim("$row[1]");
                $CBuser =			trim("$row[2]");
                $CBcomments =		trim("$row[3]");
                }
            }

        ### update the lead status to INCALL
        $stmt = "UPDATE osdial_list set status='INCALL', user='$user' where lead_id='$lead_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);

        ### update the log status to INCALL
        $user_group='';
            $stmt="SELECT user_group FROM osdial_users where user='$user' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $user_group =		trim("$row[0]");
                }


        $stmt = "select campaign_id,phone_number,alt_dial,call_type from osdial_auto_calls where callerid = '$callerid' order by call_time desc limit 1;";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $VDAC_cid_ct = mysql_num_rows($rslt);
        if ($VDAC_cid_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
            $VDADchannel_group	=$row[0];
            $dialed_number		=$row[1];
            $dialed_label		=$row[2];
            $call_type			=$row[3];
            }
        else
            {
            $dialed_number = $phone_number;
            $dialed_label = 'MAIN';
            if (preg_match('/^M|^V/',$callerid))
                {
                $call_type = 'OUT';
                $VDADchannel_group = $campaign;
                }
            else
                {
                $call_type = 'IN';
                $stmt = "select campaign_id from osdial_closer_log where lead_id = '$lead_id' order by call_date desc limit 1;";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $VDCL_mvac_ct = mysql_num_rows($rslt);
                if ($VDCL_mvac_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $VDADchannel_group  =$row[0];
                    }
                }

            if ($WeBRooTWritablE > 0)
                {
                $fp = fopen ("./osdial_debug.txt", "a");
                fwrite ($fp, "$NOW_TIME|INBND|$callerid|$user|$user_group|$list_id|$lead_id|$phone_number|$uniqueid|\n");
                fclose($fp);
                }
            }

        if ( ($call_type=='OUT') or ($call_type=='OUTBALANCE') )
            {
            $stmt = "UPDATE osdial_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' where lead_id='$lead_id' and uniqueid='$uniqueid';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);

            $stmt = "select web_form_address,campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,allow_tab_switch,web_form_address2,web_form_extwindow,web_form2_extwindow from osdial_campaigns where campaign_id='$campaign';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $VDIG_cid_ct = mysql_num_rows($rslt);
            if ($VDIG_cid_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $VDCL_web_form_address      = $row[0];
                $VDCL_campaign_script       = $row[1];
                $VDCL_get_call_launch       = $row[2];
                $VDCL_xferconf_a_dtmf       = $row[3];
                $VDCL_xferconf_a_number     = $row[4];
                $VDCL_xferconf_b_dtmf       = $row[5];
                $VDCL_xferconf_b_number     = $row[6];
                $VDCL_default_xfer_group    = $row[7];
                $VDCL_allow_tab_switch      = $row[8];
                $VDCL_web_form_address2     = $row[9];
                $VDCL_web_form_extwin       = $row[10];
                $VDCL_web_form_extwin2      = $row[11];
                }

            $stmt = "SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='$list_id';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $list_cnt = mysql_num_rows($rslt);
            if ($list_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if ($row[0] != "") $VDCL_web_form_address = $row[0];
                if ($row[1] != "") $VDCL_web_form_address2 = $row[1];
                if ($row[2] != "") $VDCL_campaign_script = $row[2];
            }

            # Get script override from user.
            $stmt = "SELECT script_override FROM osdial_users WHERE user='$user';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $user_cnt = mysql_num_rows($rslt);
            if ($user_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if ($row[0] != "") $VDCL_campaign_script = $row[0];
            }

            echo "$VDCL_web_form_address|||||$VDCL_campaign_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_web_form_address2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n|\n";

            $stmt = "select phone_number,alt_dial from osdial_auto_calls where callerid = '$callerid' order by call_time desc limit 1;";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $VDAC_cid_ct = mysql_num_rows($rslt);
            if ($VDAC_cid_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $dialed_number  =$row[0];
                $dialed_label   =$row[1];
                }
            else
                {
                $dialed_number = $phone_number;
                $dialed_label = 'MAIN';
                }
            }
        else
            {
            ### update the osdial_closer_log user to INCALL
            $stmt = "UPDATE osdial_closer_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' where lead_id='$lead_id' order by closecallid desc limit 1;";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);

            $stmt = "select count(*) from osdial_log where lead_id='$lead_id' and uniqueid='$uniqueid';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $VDL_cid_ct = mysql_num_rows($rslt);
            if ($VDL_cid_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $VDCL_front_VDlog = $row[0];
                }

            $stmt = "select * from osdial_inbound_groups where group_id='$VDADchannel_group';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $VDIG_cid_ct = mysql_num_rows($rslt);
            if ($VDIG_cid_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $VDCL_group_name        = $row[1];
                $VDCL_group_color       = $row[2];
                $VDCL_group_web         = $row[4];
                $VDCL_fronter_display   = $row[7];
                $VDCL_ingroup_script    = $row[8];
                $VDCL_get_call_launch   = $row[9];
                $VDCL_xferconf_a_dtmf   = $row[10];
                $VDCL_xferconf_a_number = $row[11];
                $VDCL_xferconf_b_dtmf   = $row[12];
                $VDCL_xferconf_b_number = $row[13];
                $VDCL_default_xfer_group= $row[28];
                $VDCL_group_web2        = $row[29];
                $VDCL_allow_tab_switch  = $row[30];
                $VDCL_web_form_extwin   = $row[31];
                $VDCL_web_form_extwin2  = $row[32];

                $stmt = "SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='$list_id';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $list_cnt = mysql_num_rows($rslt);
                if ($list_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if ($row[0] != "") $VDCL_group_web = $row[0];
                    if ($row[1] != "") $VDCL_group_web2 = $row[1];
                    if ($row[2] != "") $VDCL_ingroup_script = $row[2];
                }

                $stmt = "select campaign_script,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,web_form_address,web_form_address2 from osdial_campaigns where campaign_id='$campaign';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $VDIG_cidOR_ct = mysql_num_rows($rslt);
                if ($VDIG_cidOR_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    if ( ( (ereg('NONE',$VDCL_ingroup_script)) and (strlen($VDCL_ingroup_script) < 5) ) or (strlen($VDCL_ingroup_script) < 1) )
                        {$VDCL_ingroup_script =     $row[0];}
                    if (strlen($VDCL_xferconf_a_dtmf) < 1)
                        {$VDCL_xferconf_a_dtmf =    $row[1];}
                    if (strlen($VDCL_xferconf_a_number) < 1)
                        {$VDCL_xferconf_a_number =  $row[2];}
                    if (strlen($VDCL_xferconf_b_dtmf) < 1)
                        {$VDCL_xferconf_b_dtmf =    $row[3];}
                    if (strlen($VDCL_xferconf_b_number) < 1)
                        {$VDCL_xferconf_b_number =  $row[4];}
                    if (strlen($VDCL_group_web) < 1)
                        {$VDCL_group_web =  $row[5];}
                    if (strlen($VDCL_group_web2) < 1)
                        {$VDCL_group_web2 =  $row[6];}
                    }


                ### update the comments in osdial_live_agents record
                $random = (rand(1000000, 9999999) + 10000000);
                $stmt = "UPDATE osdial_live_agents set comments='INBOUND',random_id='$random' where user='$user' and server_ip='$server_ip';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);

                $Ctype = 'I';
                }
            else
                {
                $stmt = "select campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,allow_tab_switch,web_form_address,web_form_address2 from osdial_campaigns where campaign_id='$VDADchannel_group';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $link);
                $VDIG_cid_ct = mysql_num_rows($rslt);
                if ($VDIG_cid_ct > 0)
                    {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_ingroup_script    = $row[0];
                    $VDCL_get_call_launch   = $row[1];
                    $VDCL_xferconf_a_dtmf   = $row[2];
                    $VDCL_xferconf_a_number = $row[3];
                    $VDCL_xferconf_b_dtmf   = $row[4];
                    $VDCL_xferconf_b_number = $row[5];
                    $VDCL_allow_tab_switch  = $row[6];
                    $VDCL_group_web         = $row[7];
                    $VDCL_group_web2        = $row[8];
                    }

                    $stmt = "SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='$list_id';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $link);
                    $list_cnt = mysql_num_rows($rslt);
                    if ($list_cnt > 0) {
                        $row=mysql_fetch_row($rslt);
                        if ($row[0] != "") $VDCL_group_web = $row[0];
                        if ($row[1] != "") $VDCL_group_web2 = $row[1];
                        if ($row[2] != "") $VDCL_ingroup_script = $row[2];
                    }
                }

            # Get script override from user.
            $stmt = "SELECT script_override FROM osdial_users WHERE user='$user';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $user_cnt = mysql_num_rows($rslt);
            if ($user_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if ($row[0] != "") $VDCL_ingroup_script = $row[0];
            }

            #### if web form is set then send on to osdial.php for override of WEB_FORM address
            #if ( (strlen($VDCL_group_web)>5) or (strlen($VDCL_group_name)>0) ) {echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_group_web2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n";}
            #else {echo "|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch||||\n";}
            echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_group_web2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n";

            $stmt = "SELECT full_name from osdial_users where user='$tsr';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $VDU_cid_ct = mysql_num_rows($rslt);
            if ($VDU_cid_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
                $fronter_full_name  = $row[0];
                echo $fronter_full_name . '|' . $tsr . "\n";
                }
            else {echo '|' . $tsr . "\n";}
            }

        $comments = eregi_replace("\r",'',$comments);
        $comments = eregi_replace("\n",'!N',$comments);

        $LeaD_InfO =    $callerid . "\n";
        $LeaD_InfO .=   $lead_id . "\n";
        $LeaD_InfO .=   $dispo . "\n";
        $LeaD_InfO .=   $tsr . "\n";
        $LeaD_InfO .=   $vendor_id . "\n";
        $LeaD_InfO .=   $list_id . "\n";
        $LeaD_InfO .=   $gmt_offset_now . "\n";
        $LeaD_InfO .=   $phone_code . "\n";
        $LeaD_InfO .=   $phone_number . "\n";
        $LeaD_InfO .=   $title . "\n";
        $LeaD_InfO .=   $first_name . "\n";
        $LeaD_InfO .=   $middle_initial . "\n";
        $LeaD_InfO .=   $last_name . "\n";
        $LeaD_InfO .=   $address1 . "\n";
        $LeaD_InfO .=   $address2 . "\n";
        $LeaD_InfO .=   $address3 . "\n";
        $LeaD_InfO .=   $city . "\n";
        $LeaD_InfO .=   $state . "\n";
        $LeaD_InfO .=   $province . "\n";
        $LeaD_InfO .=   $postal_code . "\n";
        $LeaD_InfO .=   $country_code . "\n";
        $LeaD_InfO .=   $gender . "\n";
        $LeaD_InfO .=   $date_of_birth . "\n";
        $LeaD_InfO .=   $alt_phone . "\n";
        $LeaD_InfO .=   $email . "\n";
        $LeaD_InfO .=   $custom1 . "\n";
        $LeaD_InfO .=   $comments . "\n";
        $LeaD_InfO .=   $called_count . "\n";
        $LeaD_InfO .=   $CBentry_time . "\n";
        $LeaD_InfO .=   $CBcallback_time . "\n";
        $LeaD_InfO .=   $CBuser . "\n";
        $LeaD_InfO .=   $CBcomments . "\n";
        $LeaD_InfO .=   $dialed_number . "\n";
        $LeaD_InfO .=   $dialed_label . "\n";
        $LeaD_InfO .=   $source_id . "\n";
        $LeaD_InfO .=   $custom2 . "\n";
        $LeaD_InfO .=   $external_key . "\n";
        $LeaD_InfO .=   $post_date . "\n";

        $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
        $cnt = 0;
        foreach ($forms as $form) {
            $fcamps = split(',',$form['campaigns']);
            foreach ($fcamps as $fcamp) {
                if ($fcamp == 'ALL' or $fcamp == $campaign) {
                    $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
                    foreach ($fields as $field) {
                        $vdlf = get_first_record($link, 'osdial_list_fields', '*', "lead_id='" . $lead_id . "' AND field_id='" . $field['id'] . "'");
                        $LeaD_InfO .= $vdlf['value'] . "\n";
                        $cnt++;
                    }
                }
            }
        }

        echo $LeaD_InfO;



        $wait_sec=0;
        $StarTtime = date("U");
        $stmt = "select wait_epoch,wait_sec from osdial_agent_log where agent_log_id='$agent_log_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
            $wait_sec = (($StarTtime - $row[0]) + $row[1]);
            if ($wait_sec<0) $wait_sec=0;
            }
        $stmt="UPDATE osdial_agent_log set wait_sec='$wait_sec',talk_epoch='$StarTtime',lead_id='$lead_id',uniqueid='$uniqueid',prev_status='$dispo',lead_called_count='$called_count' where agent_log_id='$agent_log_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        ### If CALLBK, change osdial_callback record to INACTIVE
        if (eregi("CALLBK|CBHOLD", $dispo))
            {
            $stmt="UPDATE osdial_callbacks set status='INACTIVE' where lead_id='$lead_id' and status NOT IN('INACTIVE','DEAD','ARCHIVE');";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
            }

        ##### check if system is set to generate logfile for transfers
        $stmt="SELECT enable_agc_xfer_log FROM system_settings;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $enable_agc_xfer_log_ct = mysql_num_rows($rslt);
        if ($enable_agc_xfer_log_ct > 0)
            {
            $row=mysql_fetch_row($rslt);
            $enable_agc_xfer_log =$row[0];
            }

        if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) )
            {
            #	DATETIME|campaign|lead_id|phone_number|user|type
            #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|A
            $fp = fopen ("./xfer_log.txt", "a");
            fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|$Ctype\n");
            fclose($fp);
            }

        }
        else
        {
        echo "0\n";
    #   echo "No calls in QUEUE for $user on $server_ip\n";
        exit;
        }
    }
}


################################################################################
### userLOGout - Logs the user out of OSDiaL client, deleting db records and 
###              inserting into osdial_user_log
################################################################################
if ($ACTION == 'userLOGout') {
    $MT[0]='';
    $row='';   $rowx='';
    if ( (strlen($campaign)<1) || (strlen($conf_exten)<1) ) {
        echo "NO\n";
        echo "campaign $campaign or conf_exten $conf_exten is not valid\n";
        exit;
    } else {
        $user_group='';
        $stmt="SELECT user_group FROM osdial_users where user='$user' LIMIT 1;";
        $rslt=mysql_query($stmt, $link);
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $ug_record_ct = mysql_num_rows($rslt);
        if ($ug_record_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $user_group = trim("$row[0]");
        }
        ##### Insert a LOGOUT record into the user log
        $stmt="INSERT INTO osdial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user','LOGOUT','$campaign','$NOW_TIME','$StarTtime','$user_group');";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $vul_insert = mysql_affected_rows($link);

        if ($no_delete_sessions < 1) {
            ##### Remove the reservation on the osdial_conferences meetme room
            $stmt="UPDATE osdial_conferences set extension='' where server_ip='$server_ip' and conf_exten='$conf_exten';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $vc_remove = mysql_affected_rows($link);
        }

        ##### Delete the osdial_live_agents record for this session
        $stmt="DELETE from osdial_live_agents where server_ip='$server_ip' and user ='$user';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $vla_delete = mysql_affected_rows($link);

        ##### Delete the osdial_live_inbound_agents records for this session
        $stmt="DELETE from osdial_live_inbound_agents where user ='$user';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $vlia_delete = mysql_affected_rows($link);

        ##### Delete the web_client_sessions
        $stmt="DELETE from web_client_sessions where server_ip='$server_ip' and session_name ='$session_name';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $wcs_delete = mysql_affected_rows($link);

        ##### Hangup the client phone
        $stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$extension%\" order by channel desc;";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) {
            $row=mysql_fetch_row($rslt);
            $agent_channel = "$row[0]";
            if ($format=='debug') echo "\n<!-- $row[0] -->";
            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','ULGH3459$StarTtime','Channel: $agent_channel','','','','','','','','','');";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ($LogouTKicKAlL > 0) {
            $local_DEF = 'Local/5555';
            $local_AMP = '@';
            $kick_local_channel = "$local_DEF$conf_exten$local_AMP$ext_context";
            $queryCID = "ULGH3458$StarTtime";

            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','Account: $queryCID','','','$channel','$exten');";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        $pause_sec=0;
        $stmt = "select pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec from osdial_agent_log where agent_log_id='$agent_log_id';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $pause_epoch = $row[0];
            $pause_sec   = $row[1];
            $wait_epoch  = $row[2];
            $wait_sec    = $row[3];
            $talk_epoch  = $row[4];
            $talk_sec    = $row[5];
            $dispo_epoch = $row[6];
            $dispo_sec   = $row[7];

            if (strlen($talk_epoch)<5 and strlen($dispo_epoch)<5) $wait_epoch = $StarTtime;
            if (strlen($talk_epoch)<5) $talk_epoch = $StarTtime;
            if (strlen($dispo_epoch)<5) $dispo_epoch = $StarTtime;
            $pause_sec = ($wait_epoch - $pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch - $wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            $dispo_sec = ($StarTtime - $talk_epoch);
            if ($dispo_sec<0) $dispo_sec=0;

            $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_epoch='$wait_epoch',wait_sec='$wait_sec',talk_epoch='$talk_epoch',talk_sec='$talk_sec',dispo_epoch='$dispo_epoch',dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ($vla_delete > 0) {
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
            $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,allow_sipsak_messages FROM system_settings;";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $qm_conf_ct = mysql_num_rows($rslt);
            $i=0;
            while ($i < $qm_conf_ct) {
                $row=mysql_fetch_row($rslt);
                $enable_queuemetrics_logging    = $row[0];
                $queuemetrics_server_ip         = $row[1];
                $queuemetrics_dbname            = $row[2];
                $queuemetrics_login             = $row[3];
                $queuemetrics_pass              = $row[4];
                $queuemetrics_log_id            = $row[5];
                $allow_sipsak_messages          = $row[6];
                $i++;
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
            if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) ) {
                $SIPSAK_message = 'LOGGED OUT';
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_message\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
            }

            if ($enable_queuemetrics_logging > 0) {
                $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                mysql_select_db("$queuemetrics_dbname", $linkB);

            #   $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='$campaign',agent='Agent/$user',verb='PAUSE',serverid='1';";
            #   if ($DB) {echo "$stmt\n";}
            #
            #   $rslt=mysql_query($stmt, $linkB);
            #   $affected_rows = mysql_affected_rows($linkB);

                $stmt = "SELECT time_id FROM queue_log where agent='Agent/$user' and verb='AGENTLOGIN' order by time_id desc limit 1;";
                $rslt=mysql_query($stmt, $linkB);
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $li_conf_ct = mysql_num_rows($rslt);
                $i=0;
                while ($i < $li_conf_ct) {
                    $row=mysql_fetch_row($rslt);
                    $logintime = $row[0];
                    $i++;
                }

                $time_logged_in = ($StarTtime - $logintime);
                if ($time_logged_in > 1000000) {$time_logged_in=1;}

                $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='$campaign',agent='Agent/$user',verb='AGENTLOGOFF',data1='$user$agents',data2='$time_logged_in',serverid='$queuemetrics_log_id';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);
                mysql_close($linkB);
            }
        }

        echo "$vul_insert|$vc_remove|$vla_delete|$wcs_delete|$agent_channel|$vlia_delete\n";
    }
}


################################################################################
### updateDISPO - update the osdial_list table to reflect the agent choice of
###               call disposition for that leand
################################################################################
if ($ACTION == 'updateDISPO') {
    $MT[0]='';
    $row='';   $rowx='';
    if ( (strlen($dispo_choice)<1) || (strlen($lead_id)<1) ) {
        echo "Dispo Choice $dispo or lead_id $lead_id is not valid\n";
        exit;
    } else {
        $random = (rand(1000000, 9999999) + 10000000);
        $stmt="UPDATE osdial_live_agents set comments='',lead_id='',last_call_finish='$NOW_TIME',random_id='$random' where user='$user' and server_ip='$server_ip';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        $stmt="UPDATE osdial_list set status='$dispo_choice', user='$user' where lead_id='$lead_id';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        $stmt="UPDATE osdial_campaigns SET campaign_lastcall=NOW() WHERE campaign_id='$campaign';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        if ($dispo_choice == 'PD') {
            $stmt="UPDATE osdial_list set post_date='$PostDatETimE' where lead_id='$lead_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        }

        $stmt = "select count(*) from osdial_inbound_groups where group_id='$stage';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            $stmt = "UPDATE osdial_closer_log set status='$dispo_choice' where lead_id='$lead_id' and user='$user' order by closecallid desc limit 1;";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        } else {
            $stmt="UPDATE osdial_log set status='$dispo_choice' where lead_id='$lead_id' and user='$user' order by uniqueid desc limit 1;";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        }

        if ( ($use_internal_dnc=='Y') and ($dispo_choice=='DNC') ) {
            $dnc_method='';
            $comp_id=0;
            if ($multicomp > 0) {
                $dnc_method='';
                $stmt=sprintf("SELECT id,dnc_method FROM osdial_companies WHERE id='%s';",((substr($user,0,3) * 1) - 100));
                $rslt=mysql_query($stmt, $link);
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $row=mysql_fetch_row($rslt);
                $comp_id=$row[0];
                $dnc_method=$row[1];
            }

            $stmt = "select phone_number from osdial_list where lead_id='$lead_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);

            if (preg_match('/COMPANY|BOTH/',$dnc_method)) {
                $stmt="INSERT INTO osdial_dnc_company (company_id,phone_number) values('$comp_id','$row[0]');";
            } else {
                $stmt="INSERT INTO osdial_dnc (phone_number) values('$row[0]');";
            }
            $rslt=mysql_query($stmt, $link);
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
        }

        $user_group='';
        $stmt="SELECT user_group FROM osdial_users where user='$user' LIMIT 1;";
        $rslt=mysql_query($stmt, $link);
        if ($format=='debug') {echo "\n<!-- $stmt -->";}

        $ug_record_ct = mysql_num_rows($rslt);
        if ($ug_record_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $user_group = trim("$row[0]");
        }

        $dispo_sec=0;
        $dispo_epochSQL='';
        $StarTtime = date("U");
        $stmt = "select dispo_epoch,dispo_sec,talk_epoch,talk_sec,wait_epoch,wait_sec,pause_epoch,pause_sec from osdial_agent_log where agent_log_id='$agent_log_id' AND status IS NULL;";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $dispo_epoch = $row[0];
            $dispo_sec   = $row[1];
            $talk_epoch  = $row[2];
            $talk_sec    = $row[3];
            $wait_epoch  = $row[4];
            $wait_sec    = $row[5];
            $pause_epoch = $row[6];
            $pause_sec   = $row[7];
            #if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
            #    $dispo_epochSQL=",dispo_epoch='$StarTtime'";
            #    $dispo_epoch=$talk_epoch;
            #}
            #$dispo_sec = (($StarTtime - $dispo_epoch) + $dispo_sec);
            #$stmt="UPDATE osdial_agent_log set dispo_sec='$dispo_sec',status='$dispo_choice' $dispo_epochSQL where agent_log_id='$agent_log_id';";
            #if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #$rslt=mysql_query($stmt, $link);
            if (strlen($talk_epoch)<5 and strlen($dispo_epoch)<5) $wait_epoch = $StarTtime;
            if (strlen($talk_epoch)<5) $talk_epoch = $StarTtime;
            if (strlen($dispo_epoch)<5) $dispo_epoch = $StarTtime;
            $pause_sec = ($wait_epoch - $pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch - $wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            $dispo_sec = ($StarTtime - $dispo_epoch);
            if ($dispo_sec<0) $dispo_sec=0;

            $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_epoch='$wait_epoch',wait_sec='$wait_sec',talk_epoch='$talk_epoch',talk_sec='$talk_sec',dispo_epoch='$dispo_epoch',dispo_sec='$dispo_sec',status='$dispo_choice' where agent_log_id='$agent_log_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        } else {
            $stmt="INSERT INTO osdial_agent_log SET user='$user',server_ip='$server_ip',event_time='$NOW_TIME',campaign_id='$campaign',lead_id='$lead_id',user_group='$user_group',pause_epoch='$StarTtime',pause_sec='0',wait_epoch='$StarTtime',wait_sec='0',talk_epoch='$StarTtime',talk_sec='0',dispo_epoch='$StarTtime',dispo_sec='0',status='$dispo_choice';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        }

        if ($auto_dial_level == 0 or $VDpr_ct == 0) {
            $stmt="INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime','$user_group');";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            $agent_log_id = mysql_insert_id($link);
        }



    #    if ($auto_dial_level < 1) {
    #        if ( (eregi("NULL",$wait_epoch)) or ($wait_epoch < 1000) ) {
    #            $wait_epoch = $StarTtime;
    #            $pause_sec = ($wait_epoch - $pause_epoch);
    #            $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_epoch='$wait_epoch' where agent_log_id='$agent_log_id';";
    #            if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #            $rslt=mysql_query($stmt, $link);
    #        } else {
    #            $pause_sec = ($wait_epoch - $pause_epoch);
    #            $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec' where agent_log_id='$agent_log_id';";
    #            if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #            $rslt=mysql_query($stmt, $link);
    #        }
    #        if ( (eregi("NULL",$talk_epoch)) or ($talk_epoch < 1000) ) {
    #            $talk_epoch = $StarTtime;
    #            $wait_sec = ($talk_epoch - $wait_epoch);
    #            $stmt="UPDATE osdial_agent_log set wait_sec='$wait_sec',talk_epoch='$talk_epoch' where agent_log_id='$agent_log_id';";
    #            if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #            $rslt=mysql_query($stmt, $link);
    #        } else {
    #            $wait_sec = ($talk_epoch - $wait_epoch);
    #            $stmt="UPDATE osdial_agent_log set wait_sec='$wait_sec' where agent_log_id='$agent_log_id';";
    #            if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #            $rslt=mysql_query($stmt, $link);
    #        }
    #        if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
    #            $dispo_epoch = $StarTtime;
    #            $talk_sec = ($dispo_epoch - $talk_epoch);
    #            $stmt="UPDATE osdial_agent_log set talk_sec='$talk_sec',dispo_epoch='$dispo_epoch' where agent_log_id='$agent_log_id';";
    #            if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #            $rslt=mysql_query($stmt, $link);
    #        } else {
    #            $talk_sec = ($dispo_epoch - $talk_epoch);
    #            $stmt="UPDATE osdial_agent_log set talk_sec='$talk_sec' where agent_log_id='$agent_log_id';";
    #            if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #            $rslt=mysql_query($stmt, $link);
    #        }
    #        $dispo_sec = ($StarTtime - $dispo_epoch);
    #        $stmt="UPDATE osdial_agent_log set dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
    #        if ($format=='debug') {echo "\n<!-- $stmt -->";}
    #        $rslt=mysql_query($stmt, $link);
    #    
    #    
    #        $stmt="INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime','$user_group');";
    #        if ($DB) {echo "$stmt\n";}
    #        $rslt=mysql_query($stmt, $link);
    #        $affected_rows = mysql_affected_rows($link);
    #        $agent_log_id = mysql_insert_id($link);
    #    }

        ### CALLBACK ENTRY
        if ( ($dispo_choice == 'CBHOLD') and (strlen($CallBackDatETimE)>10) ) {
            $stmt=sprintf("INSERT INTO osdial_callbacks (lead_id,list_id,campaign_id,status,entry_time,callback_time,user,recipient,comments,user_group) values('%s','%s','%s','ACTIVE','%s','%s','%s','%s','%s','%s');",mres($lead_id),mres($list_id),mres($campaign),mres($NOW_TIME),mres($CallBackDatETimE),mres($user),mres($recipient),mres($comments),mres($user_group));
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        }

        $stmt="SELECT auto_alt_dial_statuses from osdial_campaigns where campaign_id='$campaign';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);

        if ( ($auto_dial_level > 0) and (ereg(" $dispo_choice ",$row[0])) ) {
            $stmt = "SELECT count(*) FROM osdial_hopper WHERE lead_id='$lead_id' AND status='HOLD';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);

            if ($row[0] > 0) {
                $stmt="UPDATE osdial_hopper SET status='READY' WHERE lead_id='$lead_id' AND status='HOLD' LIMIT 1;";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysql_query($stmt, $link);
            }
        } else {
            $stmt="DELETE FROM osdial_hopper WHERE lead_id='$lead_id' AND status='HOLD';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        }

        #############################################
        ##### START QUEUEMETRICS LOGGING LOOKUP #####
        $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
        $rslt=mysql_query($stmt, $link);
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $qm_conf_ct = mysql_num_rows($rslt);
        $i=0;
        while ($i < $qm_conf_ct) {
            $row=mysql_fetch_row($rslt);
            $enable_queuemetrics_logging    = $row[0];
            $queuemetrics_server_ip         = $row[1];
            $queuemetrics_dbname            = $row[2];
            $queuemetrics_login             = $row[3];
            $queuemetrics_pass              = $row[4];
            $queuemetrics_log_id            = $row[5];
            $i++;
        }
        ##### END QUEUEMETRICS LOGGING LOOKUP #####
        ###########################################
        if ($enable_queuemetrics_logging > 0) {
            $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
            mysql_select_db("$queuemetrics_dbname", $linkB);

            $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$campaign',agent='Agent/$user',verb='CALLSTATUS',data1='$dispo_choice',serverid='$queuemetrics_log_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $linkB);
            $affected_rows = mysql_affected_rows($linkB);

            mysql_close($linkB);
        }

        echo 'Lead ' . $lead_id . ' has been changed to ' . $dispo_choice . " Status\nNext agent_log_id:\n" . $agent_log_id . "\n";
    }
}

################################################################################
### updateLEAD - update the osdial_list table to reflect the values that are
###              in the agents screen at time of call hangup
################################################################################
if ($ACTION == 'updateLEAD') {
    $MT[0]='';
    $row='';   $rowx='';
    $DO_NOT_UPDATE=0;
    $DO_NOT_UPDATE_text='';
    if ( (strlen($phone_number)<1) || (strlen($lead_id)<1) ) {
        echo "phone_number $phone_number or lead_id $lead_id is not valid\n";
        exit;
    } else {
        $stmt = "SELECT disable_alter_custdata FROM osdial_campaigns where campaign_id='$campaign'";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $dac_conf_ct = mysql_num_rows($rslt);
        $i=0;
        while ($i < $dac_conf_ct) {
            $row=mysql_fetch_row($rslt);
            $disable_alter_custdata = $row[0];
            $i++;
        }
        if (ereg('Y',$disable_alter_custdata)) {
            $DO_NOT_UPDATE=1;
            $DO_NOT_UPDATE_text=' NOT';
            $stmt = "SELECT alter_custdata_override FROM osdial_users where user='$user'";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $aco_conf_ct = mysql_num_rows($rslt);
            $i=0;
            while ($i < $aco_conf_ct) {
                $row=mysql_fetch_row($rslt);
                $alter_custdata_override = $row[0];
                $i++;
            }
            if (ereg('ALLOW_ALTER',$alter_custdata_override)) {
                $DO_NOT_UPDATE=0;
                $DO_NOT_UPDATE_text='';
            }
        }

        if ($DO_NOT_UPDATE < 1) {
            $comments = eregi_replace("\r",'',$comments);
            $comments = eregi_replace("\n",'!N',$comments);
            $comments = eregi_replace("--AMP--",'&',$comments);
            $comments = eregi_replace("--QUES--",'?',$comments);
            $comments = eregi_replace("--POUND--",'#',$comments);

            $stmt="UPDATE osdial_list set vendor_lead_code='" . mysql_real_escape_string($vendor_lead_code) . "', title='" . mysql_real_escape_string($title) . "', first_name='" . mysql_real_escape_string($first_name) . "', middle_initial='" . mysql_real_escape_string($middle_initial) . "', last_name='" . mysql_real_escape_string($last_name) . "', address1='" . mysql_real_escape_string($address1) . "', address2='" . mysql_real_escape_string($address2) . "', address3='" . mysql_real_escape_string($address3) . "', city='" . mysql_real_escape_string($city) . "', state='" . mysql_real_escape_string($state) . "', province='" . mysql_real_escape_string($province) . "', postal_code='" . mysql_real_escape_string($postal_code) . "', country_code='" . mysql_real_escape_string($country_code) . "', gender='" . mysql_real_escape_string($gender) . "', date_of_birth='" . mysql_real_escape_string($date_of_birth) . "', alt_phone='" . mysql_real_escape_string($alt_phone) . "', email='" . mysql_real_escape_string($email) . "', custom1='" . mysql_real_escape_string($custom1) . "', custom2='" . mysql_real_escape_string($custom2) ."', comments='" . mysql_real_escape_string($comments) . "' where lead_id='$lead_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);

            $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
            $cnt = 0;
            foreach ($forms as $form) {
                $fcamps = split(',',$form['campaigns']);
                foreach ($fcamps as $fcamp) {
                    if ($fcamp == 'ALL' or $fcamp == $campaign) {
                        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
                        foreach ($fields as $field) {
                            $afvar = get_variable('AF' . $field['id']);
                            if ($afvar != '') {
                                $stmt="INSERT INTO osdial_list_fields (lead_id,field_id,value) VALUES ('" . mysql_real_escape_string($lead_id) . "','" . mysql_real_escape_string($field['id']) . "','" . mysql_real_escape_string($afvar) . "') ON DUPLICATE KEY UPDATE value='" . mysql_real_escape_string($afvar) . "';";
                                $rslt=mysql_query($stmt, $link);
                            }
                            $cnt++;
                        }
                    }
                }
            }
        }

        $random = (rand(1000000, 9999999) + 10000000);
        $stmt="UPDATE osdial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        echo "Lead $lead_id information has$DO_NOT_UPDATE_text been updated\n";
    }
}


################################################################################
### VDADpause - update the osdial_live_agents table to show that the agent is
###  or ready   now active and ready to take calls
################################################################################
if ( ($ACTION == 'VDADpause') || ($ACTION == 'VDADready') ) {
    $MT[0]='';
    $row='';   $rowx='';
    if ( (strlen($stage)<2) || (strlen($server_ip)<1) ) {
        echo "stage $stage is not valid\n";
        exit;
    } else {
        $random = (rand(1000000, 9999999) + 10000000);
        $stmt="UPDATE osdial_live_agents set uniqueid='',lead_id='',callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',random_id='$random',comments='',status='$stage' where user='$user' and server_ip='$server_ip';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);

        $affected_rows = mysql_affected_rows($link);
        if ($affected_rows > 0) {
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
            $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $qm_conf_ct = mysql_num_rows($rslt);
            $i=0;
            while ($i < $qm_conf_ct) {
                $row=mysql_fetch_row($rslt);
                $enable_queuemetrics_logging = $row[0];
                $queuemetrics_server_ip = $row[1];
                $queuemetrics_dbname = $row[2];
                $queuemetrics_login = $row[3];
                $queuemetrics_pass = $row[4];
                $queuemetrics_log_id = $row[5];
                $i++;
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
            if ($enable_queuemetrics_logging > 0) {
                if (ereg('READY',$stage)) {$QMstatus='UNPAUSEALL';}
                if (ereg('PAUSE',$stage)) {$QMstatus='PAUSEALL';}
                $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                mysql_select_db("$queuemetrics_dbname", $linkB);

                $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='$QMstatus',serverid='$queuemetrics_log_id';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);
                mysql_close($linkB);
            }
        }

        $pause_sec=0;
        $stmt = "select pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec from osdial_agent_log where agent_log_id='$agent_log_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $pause_epoch = $row[0];
            $pause_sec   = $row[1];
            $wait_epoch  = $row[2];
            $wait_sec    = $row[3];
            $talk_epoch  = $row[4];
            $talk_sec    = $row[5];
            $dispo_epoch = $row[6];
            $dispo_sec   = $row[7];
        }

        if ($ACTION == 'VDADready') {
            if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
                $pause_sec = $StarTtime - $pause_epoch;
                if ($pause_sec<0) $pause_sec=0;
                $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysql_query($stmt, $link);
            }
        } elseif ($ACTION == 'VDADpause') {
            if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
                $pause_sec = ($wait_epoch - $pause_sec);
                if ($pause_sec<0) $pause_sec=0;
                $wait_sec=0;
                if ($wait_epoch > 0) $wait_sec = (($StarTtime - $wait_epoch) + $wait_sec);
                if ($wait_sec<0) $wait_sec=0;
                $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_sec='$wait_sec' where agent_log_id='$agent_log_id';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysql_query($stmt, $link);
            }
        }

        if ($wrapup == 'WRAPUP') {
            if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
                $stmt="UPDATE osdial_agent_log set dispo_epoch='$StarTtime', dispo_sec='0' where agent_log_id='$agent_log_id';";
            } else {
                $dispo_sec = ($StarTtime - $dispo_epoch);
                if ($dispo_sec<0) $dispo_sec=0;
                $talk_sec = ($dispo_epoch - $talk_epoch);
                if ($talk_sec<0) $talk_sec=0;
                $stmt="UPDATE osdial_agent_log set talk_sec='$talk_sec',talk_epoch='$talk_epoch',dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
            }

            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);
        }

        if ($agent_log == 'NEW_ID' and (strlen($talk_epoch)>5 or strlen($dispo_epoch)>5)) {
        #if ($agent_log == 'NEW_ID') {
            $user_group='';
            $stmt="SELECT user_group FROM osdial_users where user='$user' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $user_group =       trim("$row[0]");
            }

            if (strlen($talk_epoch)<5 and strlen($dispo_epoch)<5) $wait_epoch = $StarTtime;
            if (strlen($talk_epoch)<5) $talk_epoch = $StarTtime;
            if (strlen($dispo_epoch)<5) $dispo_epoch = $StarTtime;
            $pause_sec = ($wait_epoch - $pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch - $wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            $dispo_sec = ($StarTtime - $dispo_epoch);
            if ($dispo_sec<0) $dispo_sec=0;

            $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_epoch='$wait_epoch',wait_sec='$wait_sec',talk_epoch='$talk_epoch',talk_sec='$talk_sec',dispo_epoch='$dispo_epoch',dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
            if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysql_query($stmt, $link);

            #if ( (eregi("NULL",$wait_epoch)) or ($wait_epoch < 1000) ) {
            #    $wait_epoch = $StarTtime;
            #    $pause_sec = ($wait_epoch - $pause_epoch);
            #    $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec',wait_epoch='$wait_epoch' where agent_log_id='$agent_log_id';";
            #    if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #    $rslt=mysql_query($stmt, $link);
            #} else {
            #    $pause_sec = ($wait_epoch - $pause_epoch);
            #    $stmt="UPDATE osdial_agent_log set pause_sec='$pause_sec' where agent_log_id='$agent_log_id';";
            #    if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #    $rslt=mysql_query($stmt, $link);
            #}
            #if ( (eregi("NULL",$talk_epoch)) or ($talk_epoch < 1000) ) {
            #    $talk_epoch = $StarTtime;
            #    $wait_sec = ($talk_epoch - $wait_epoch);
            #    $stmt="UPDATE osdial_agent_log set wait_sec='$wait_sec',talk_epoch='$talk_epoch' where agent_log_id='$agent_log_id';";
            #    if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #    $rslt=mysql_query($stmt, $link);
            #} else {
            #    $wait_sec = ($talk_epoch - $wait_epoch);
            #    $stmt="UPDATE osdial_agent_log set wait_sec='$wait_sec' where agent_log_id='$agent_log_id';";
            #    if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #    $rslt=mysql_query($stmt, $link);
            #}
            #if ( (eregi("NULL",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
            #    $dispo_epoch = $StarTtime;
            #    $talk_sec = ($dispo_epoch - $talk_epoch);
            #    $stmt="UPDATE osdial_agent_log set talk_sec='$talk_sec',dispo_epoch='$dispo_epoch' where agent_log_id='$agent_log_id';";
            #    if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #    $rslt=mysql_query($stmt, $link);
            #} else {
            #    $talk_sec = ($dispo_epoch - $talk_epoch);
            #    $stmt="UPDATE osdial_agent_log set talk_sec='$talk_sec' where agent_log_id='$agent_log_id';";
            #    if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #    $rslt=mysql_query($stmt, $link);
            #}
            #$dispo_sec = ($StarTtime - $dispo_epoch);
            #$stmt="UPDATE osdial_agent_log set dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
            #if ($format=='debug') {echo "\n<!-- $stmt -->";}
            #$rslt=mysql_query($stmt, $link);

            $stmt="INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime','$user_group');";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            $agent_log_id = mysql_insert_id($link);
        }

        echo 'Agent ' . $user . ' is now in status ' . $stage . "\nNext agent_log_id:\n$agent_log_id\n";
    }
}



################################################################################
### PauseCodeSubmit - Update osdial_agent_log with pause code
################################################################################
if ($ACTION == 'PauseCodeSubmit') {
    $row='';   $rowx='';
    if ( (strlen($status)<1) || (strlen($agent_log_id)<1) ) {
        echo "agent_log_id $agent_log_id or pause_code $status is not valid\n";
        exit;
    } else {
        $stmt="UPDATE osdial_agent_log set sub_status=\"$status\" where agent_log_id='$agent_log_id';";
        if ($format=='debug') {echo "\n<!-- $stmt -->";}
        $rslt=mysql_query($stmt, $link);
        $affected_rows = mysql_affected_rows($link);
        if ($affected_rows > 0) {
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
            $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,allow_sipsak_messages FROM system_settings;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $qm_conf_ct = mysql_num_rows($rslt);
            $i=0;
            while ($i < $qm_conf_ct) {
                $row=mysql_fetch_row($rslt);
                $enable_queuemetrics_logging = $row[0];
                $queuemetrics_server_ip = $row[1];
                $queuemetrics_dbname = $row[2];
                $queuemetrics_login = $row[3];
                $queuemetrics_pass = $row[4];
                $queuemetrics_log_id = $row[5];
                $allow_sipsak_messages = $row[6];
                $i++;
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
            if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) ) {
                $SIPSAK_prefix = 'BK-';
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$status\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
            }
            if ($enable_queuemetrics_logging > 0) {
                $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                mysql_select_db("$queuemetrics_dbname", $linkB);

                $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='$campaign',agent='Agent/$user',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='$status';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);
                mysql_close($linkB);
            }
        }

        echo "Pause Code has been updated to $status for $agent_log_id\n";
    }
}


################################################################################
### CalLBacKLisT - List the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKLisT') {
    $stmt = "select callback_id,lead_id,campaign_id,status,entry_time,callback_time,comments from osdial_callbacks where recipient='USERONLY' and user='$user' and campaign_id='$campaign' and status NOT IN('INACTIVE','DEAD') order by callback_time;";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    if ($rslt) {$callbacks_count = mysql_num_rows($rslt);}
    echo "$callbacks_count\n";
    $loop_count=0;
    while ($callbacks_count>$loop_count) {
        $row=mysql_fetch_row($rslt);
        $callback_id[$loop_count]   = $row[0];
        $lead_id[$loop_count]       = $row[1];
        $campaign_id[$loop_count]   = $row[2];
        $status[$loop_count]        = $row[3];
        $entry_time[$loop_count]    = $row[4];
        $callback_time[$loop_count] = $row[5];
        $comments[$loop_count]      = $row[6];
        $loop_count++;
    }
    $loop_count=0;
    while ($callbacks_count>$loop_count) {
        $stmt = "select first_name,last_name,phone_number from osdial_list where lead_id='$lead_id[$loop_count]';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);

        echo "$row[0] ~$row[1] ~$row[2] ~$callback_id[$loop_count] ~$lead_id[$loop_count] ~$campaign_id[$loop_count] ~$status[$loop_count] ~$entry_time[$loop_count] ~$callback_time[$loop_count] ~$comments[$loop_count]\n";
        $loop_count++;
    }

}


################################################################################
### CalLBacKCounT - send the count of the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKCounT') {
    $stmt = "select count(*) from osdial_callbacks where recipient='USERONLY' and user='$user' and campaign_id='$campaign' and status NOT IN('INACTIVE','DEAD');";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $cbcount=$row[0];

    echo "$cbcount";
}

################################################################################
### RepullLeadData - Repull the lead data for a given phone number.  Mainly for
###                  inbound calls which dial in using a different number.
################################################################################
if ($ACTION == 'RepullLeadData')
{
    $listSQL = "";
    if ($lookup == "campaign") {
        ##### grab all the lists in the campaign.
        $stmt="SELECT list_id FROM osdial_lists where campaign_id='$campaign';";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {echo "$stmt\n";}
        $list_ct = mysql_num_rows($rslt);
        $cnt = 0;
        $listSQL = "list_id IN (";
        while ($cnt < $list_ct) {
            $row=mysql_fetch_row($rslt);
            $listSQL .= "'" . $row[0] . "',";
            $cnt++;
        }
        $listSQL = rtrim($listSQL,',');
        $listSQL .= ")";
    } elseif ($lookup == "list") {
        $listSQL = "list_id='" . $list_id . "'"; 
    } else {
        $listSQL = "list_id>'999'";
    }
    ##### grab the data from osdial_list for the lead_id
    $stmt="SELECT * FROM osdial_list where phone_number = '$oldphone' AND lead_id != '$oldlead' AND $listSQL LIMIT 1;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {echo "$stmt\n";}
    $list_lead_ct = mysql_num_rows($rslt);
    if ($list_lead_ct > 0) {
        $row=mysql_fetch_row($rslt);
        $lead_id        = trim($row[0]);
        $vendor_id      = trim($row[5]);
        $source_id      = trim($row[6]);
        $list_id        = trim($row[7]);
        $gmt_offset_now = trim($row[8]);
        $phone_code     = trim($row[10]);
        $phone_number   = trim($row[11]);
        $title          = trim($row[12]);
        $first_name     = trim($row[13]);
        $middle_initial = trim($row[14]);
        $last_name      = trim($row[15]);
        $address1       = trim($row[16]);
        $address2       = trim($row[17]);
        $address3       = trim($row[18]);
        $city           = trim($row[19]);
        $state          = trim($row[20]);
        $province       = trim($row[21]);
        $postal_code    = trim($row[22]);
        $country_code   = trim($row[23]);
        $gender         = trim($row[24]);
        $date_of_birth  = trim($row[25]);
        $alt_phone      = trim($row[26]);
        $email          = trim($row[27]);
        $custom1        = trim($row[28]);
        $comments       = trim($row[29]);
        $called_count   = trim($row[30]);
        if ($alt_phone == "") {
            $alt_phone = $oldphone;
        } elseif ($address3 == "") {
            $address3 = $oldphone;
        }
        $custom2        = trim($row[31]);
        $external_key   = trim($row[32]);
        $post_date      = trim($row[35]);
    }

    ### update the old lead status to REPULL
    $stmt = "UPDATE osdial_list set status='REPULL' where lead_id='$oldlead';";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    ### update the lead status to INCALL
    $stmt = "UPDATE osdial_list set status='INCALL', user='$user' where lead_id='$lead_id';";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);

    $stmt = "select call_type from osdial_auto_calls where uniqueid='$uniqueid' order by call_time desc limit 1;";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    $VDAC_cid_ct = mysql_num_rows($rslt);
    if ($VDAC_cid_ct > 0) {
        $row=mysql_fetch_row($rslt);
        $call_type =$row[0];
    }

    # update the logs with the new lead/list ids
    if ( ($call_type=='OUT') or ($call_type=='OUTBALANCE') ) {
        $stmt = "UPDATE osdial_log set list_id='$list_id', lead_id='$lead_id' where lead_id='$oldlead' and uniqueid='$uniqueid';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
    } else {
        ### update the osdial_closer_log user to INCALL
        $stmt = "UPDATE osdial_closer_log set list_id='$list_id', lead_id='$lead_id' where lead_id='$oldlead' order by closecallid desc limit 1;";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
    }

    $comments = eregi_replace("\r",'',$comments);
    $comments = eregi_replace("\n",'!N',$comments);

    $LeaD_InfO =    $lead_id . "\n";
    $LeaD_InfO .=   $vendor_id . "\n";
    $LeaD_InfO .=   $list_id . "\n";
    $LeaD_InfO .=   $gmt_offset_now . "\n";
    $LeaD_InfO .=   $phone_code . "\n";
    $LeaD_InfO .=   $phone_number . "\n";
    $LeaD_InfO .=   $title . "\n";
    $LeaD_InfO .=   $first_name . "\n";
    $LeaD_InfO .=   $middle_initial . "\n";
    $LeaD_InfO .=   $last_name . "\n";
    $LeaD_InfO .=   $address1 . "\n";
    $LeaD_InfO .=   $address2 . "\n";
    $LeaD_InfO .=   $address3 . "\n";
    $LeaD_InfO .=   $city . "\n";
    $LeaD_InfO .=   $state . "\n";
    $LeaD_InfO .=   $province . "\n";
    $LeaD_InfO .=   $postal_code . "\n";
    $LeaD_InfO .=   $country_code . "\n";
    $LeaD_InfO .=   $gender . "\n";
    $LeaD_InfO .=   $date_of_birth . "\n";
    $LeaD_InfO .=   $alt_phone . "\n";
    $LeaD_InfO .=   $email . "\n";
    $LeaD_InfO .=   $custom1 . "\n";
    $LeaD_InfO .=   $comments . "\n";
    $LeaD_InfO .=   $called_count . "\n";
    $LeaD_InfO .=   $custom2 . "\n";
    $LeaD_InfO .=   $external_key . "\n";
    $LeaD_InfO .=   $post_date . "\n";

    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
    $cnt = 0;
    foreach ($forms as $form) {
        $fcamps = split(',',$form['campaigns']);
        foreach ($fcamps as $fcamp) {
            if ($fcamp == 'ALL' or $fcamp == $campaign) {
                $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
                foreach ($fields as $field) {
                    $vdlf = get_first_record($link, 'osdial_list_fields', '*', "lead_id='" . $lead_id . "' AND field_id='" . $field['id'] . "'");
                    $LeaD_InfO .= $vdlf['value'] . "\n";
                    $cnt++;
                }
            }
        }
    }

    echo $LeaD_InfO;
}

################################################################################
### ScriptButtonLog - log the button press within the script.              
################################################################################
if ($ACTION == 'ScriptButtonLog') {
    $stmt = "INSERT INTO osdial_script_button_log SET lead_id='$lead_id',script_id='$script_id',script_button_id='$script_button_id',user='$user';";
    if ($DB) {echo "$stmt\n";}
    $rslt=mysql_query($stmt, $link);

    echo "DONE.";
}




if ($format=='debug') {
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $StarTtime);
    echo "\n<!-- script runtime: $RUNtime seconds -->";
    echo "\n</body>\n</html>\n";
}

exit; 

?>
