<?
# 
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# make sure you have added a user to the osdial_users MySQL table with at least
# user_level 1 or greater to access this page. Also, you need to have the login
# and pass of a phone listed in the asterisk.phones table. The page grabs the 
# server info and other details from this login and pass.
#
# This script works best with Firefox or Mozilla, but will run for a couple
# hours on Internet Explorer before the memory leaks cause a crash.
#
# Other scripts that this application depends on:
# - vdc_db_query.php: Updates information in the database
# - manager_send.php: Sends manager actions to the DB for execution
#
# CHANGELOG
# 50628-1620 - Added some basic formatting and worked on process flow
# 50628-1715 - Startup variables mapped to javascript variables
# 50629-1303 - Added Login Closer in-groups selection box and vla update
# 50629-1530 - Rough layout for customer info form section and button links
# 50630-1453 - Rough Manual Dial/Hangup with customer info displayed
# 50701-1450 - Added osdial_log entries on dial and hangup
# 50701-1634 - Added Logout function
# 50705-1259 - Added call disposition functionality
# 50705-1432 - Added lead info DB update function
# 50705-1658 - Added web form functionality
# 50706-1043 - Added call park and pickup functions
# 50706-1234 - Added Start/Stop Recording functionality
# 50706-1614 - Added conference channels display option
# 50711-1333 - Removed call check redundancy and fixed a span bug
# 50727-1424 - Added customer channel and participant present sensing/alerts
# 50804-1057 - Added SendDTMF function and reconfigured the transfer span
# 50804-1224 - Added Local and Internal Closer transfer functions
# 50804-1628 - Added Blind transfer, activated LIVE CALL image and fixed bugs
# 50804-1808 - Added button images for left buttons
# 50815-1151 - Added 3Way calling functions to Transfer-conf frame
# 50815-1602 - Added images and buttons for xfer functions
# 50816-1813 - Added basic autodial outbound call pickup functions
# 50817-1113 - Fixes to auto_dialing call receipt
# 50817-1234 - Added inbound call receipt capability
# 50817-1541 - Added customer time display
# 50817-1541 - Added customer time display
# 50818-1327 - Added stop-all-recordings-after-each-osdial-call option
# 50818-1703 - Added pretty login section
# 50825-1200 - Modified form field lengths, added double-click dispositions
# 50831-1603 - Fixed customer time bug and fronter display bug for CLOSER
# 50901-1314 - Fixed CLOSER IN-GROUP Web Form bug
# 50903-0904 - Added preview-lead code for manual dialing
# 50904-0016 - Added ability to hangup manual dials before pickup
# 50906-1319 - Added override for filters on xfer calls, fixed login display bug
# 50909-1243 - Added hotkeys functionality for quick dispoing in auto-dial mode
# 50912-0958 - Modified hotkeys function, agent must have user_level >= 5 to use
# 50913-1212 - Added campaign_cid to 3rd party calls
# 50923-1546 - Modified to work with language translation
# 50926-1656 - Added campaign pull-down at login of active campaigns
# 50928-1633 - Added manual dial alternate number dial option
# 50930-1538 - Added session_id empty login failure and fixed 2 minor bugs
# 51004-1656 - Fixed recording filename bug and new Spanish translation
# 51020-1103 - Added campaign-specific recording control abilities
# 51020-1352 - Added Basic osdial_agent_log framework
# 51021-1050 - Fixed custtime display and disable Enter/Return keypresses
# 51021-1718 - Allows for multi-line comments (changes \n to !N in database)
# 51110-1432 - Fixed non-standard http port issue
# 51111-1047 - Added osdial_agent_log lead_id earlier for manual dial
# 51118-1305 - Activate multi-line comments from $multi_line_comments var
# 51118-1313 - Move Transfer DIV to a floating span to preserve 800x600 view
# 51121-1506 - Small PHP optimizations in many scripts and disabled globalize
# 51129-1010 - Added ability to accept calls from other OSDIAL servers
# 51129-1254 - Fixed Hangups of other agents channels when customer hangs up
# 51208-1732 - Created user-first login that looks for default phone info
# 51219-1526 - Added variable framework for campaign and in-group scripts
# 51221-1200 - Added SCRIPT tab, layout and functionality
# 51221-1714 - Added auto-switch-to-SCRIPT-tab and auto-webform-popup
# 51222-1605 - Added VMail message blind transfer button to xfer-conf frame
# 51229-1028 - Added checks on web_form_address to allow for var in the DB value
# 60117-1312 - Added Transfer-conf frame toggle on button press
# 60208-1152 - Added DTMF-xfernumber preset links to xfer-conf frame
# 60213-1129 - Added osdial_users.hotkeys_active  for any user hotkeys
# 60213-1210 - Added ability to sort routing of calls by user_level
# 60214-0932 - Initial Callback calendar display framework
# 60214-1407 - Added ability to minimize the dispo screen to see info below
# 60215-1104 - Added ANYONE scheduled callbacks functionality
# 60410-1116 - Added persistant pause after dispo option and change dispo text
#            - Added web form submit that opens new window with dispo on submit
#            - Added PREVIOUS CALLBACK in customer info to flag callbacks
#            - Added link to try to hangup the call again in the dispo screen
#            - Added link noone-in-session screen to call agent phone again
#            - Added link customer-hungup screen to go straight to dispo screen
# 60410-1532 - Added agent status and campaign calls dialing display option
# 60411-1547 - Add ability to set callback as USERONLY and some basic formatting
# 60413-1752 - Add basic USERONLY callback frame and listings
# 60414-1039 - Changed manual dial preview and alt dial checkboxes to spans
#            - Added beta-level USERONLY callback functionality
#            - Added beta-level manual dialing with lead insertion functionality
# 60415-1534 - Fixed manual dial lead preview and fixed manuald dial override bug
# 60417-1108 - Added capability to do alt-number-dialing in auto-dial mode
#            - Changed several permissions to database-defined
# 60419-1529 - Prevent manual dial or callbacks when alt-dial lead not finished
# 60420-1647 - Fixed DiaLDiaLAltPhonE error, Call Agent Again DialControl error
# 60421-1229 - Check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60424-1005 - Fixed Alt phone disabled bug for callbacks and manual dials
# 60426-1058 - Added osdial_user setting for default blended check for CLOSER
# 60501-1008 - Added option to manual dial screen to manually lookup phone number
# 60503-1653 - Fixed agentonly_callback not-defined bug in scheduled callbacks screen
# 60504-1032 - Fixed manual dial display bug and transfer dispo alert bug
#            - Fixed recording filename display to not overrun 25 characters
# 60510-1051 - Added Wrapup timer and wrapup message on wrapup screen after dispo
# 60608-1453 - Added CLOSER campaign allowable in-groups limitations
# 60609-1123 - Added add-number-to-DNC-list function and manual dial check DNC
# 60619-1047 - Added variable filters to close security holes for login form
# 60804-1710 - fixed scheduled CALLBK for other languages build
# 60808-1145 - Added consultative transfers with customer data
# 60808-2232 - Added campaign name to pulldown for login screen
# 60809-1603 - Added option to locally transfer consult xfers
# 60809-1732 - Added recheck of transferred channels before customer gone mesg
# 60810-1011 - Fixed CXFER leave 3way call bugs
# 60816-1602 - Added ALLCALLS recording delay option allcalls_delay
# 60816-1716 - Fixed customer time display bug and client DST setting
# 60821-1555 - Added option to omit phone_code on dialout of leads
# 60821-1628 - Added ALLFORCE recording option
# 60821-1643 - Added no_delete_sessions option to not delete sessions
# 60822-0512 - Changed phone number fields to be maxlength of 12
# 60829-1531 - Made compatible with WeBRooTWritablE setting in dbconnect.php
# 60906-1152 - Added Previous CallBack info display span
# 60906-1715 - Allow for Local phone extension conferences
# 61004-1729 - Add ability to control volume per channel in "calls in this session"
# 61122-1341 - Added osdial_user_groups allowed_campaigns restrictions
# 61122-1523 - Added more SCRIPT variables
# 61128-2229 - Added osdial_live_agents and osdial_auto_calls manual dial entries
# 61130-1617 - Added lead_id to MonitorConf for recording_log
# 61221-1212 - Changed width to 760 to better fit 800x600 screens, widened SCRIPT
# 70109-1128 - Fixed wrapup timer bug
# 70109-1635 - Added option for HotKeys automatically dialing next number in manual mode
#            - Added option for alternate number dialing with hotkeys
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70118-1517 - Added osdial_agent_log and osdial_user_log logging of user_group
# 70201-1249 - Added FAST DIAL option for manually dialing, added UTF8 compatible code
# 70201-1703 - Fixed cursor bug for most text input fields
# 70202-1453 - Added first portions of Agent Pause Codes
# 70203-0108 - Finished Agent Pause Codes functionality
# 70203-0930 - Added dialed_number to webform output
# 70203-1010 - Added dialed_label to webform output
# 70206-1201 - Fixed allow_closers bug
# 70206-1332 - Added osdial_recording_override users setting function
# 70212-1252 - Fixed small issue with CXFER
# 70213-1018 - Changed CXFER and AXFER to update customer information before transfer
# 70214-1233 - Added queuemetrics_log_id field for server_id in queue_log
# 70215-1240 - Added queuemetrics_log_id field for server_id in queue_log
# 70222-1617 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70226-1252 - Added Mute/UnMute to agent screen
# 70309-1035 - Allow amphersands and questions marks in comments to pass through
# 70313-1052 - Allow pound signs(hash) in comments to pass through
# 70316-1406 - Moved the MUTE button to be accessible during a transfer/conf
# 70319-1446 - Added agent-deactive-display and disable customer info update functions
# 70319-1626 - Added option to allow agent logins to campaigns with no leads in the hopper
# 70320-1501 - Added option to allow retry of leave-3way-call from dispo screen
# 70322-1545 - Added sipsak display ability
# 70510-1319 - Added onUnload force Logout
# 70806-1530 - Added Presets Dial links above agent mute button
# 70823-2118 - Fixed XMLHTTPRequest, HotKeys and Scheduled Callbacks issues with MSIE
# 70828-1443 - Added source_id to output of SCRIPTtab-IFRAME and WEBFORM
# 71022-1427 - Added formatting of the customer phone number in the main status bar
# 71029-1848 - Changed CLOSER-type campaign to not use campaign_id restrictions
# 71101-1204 - Fixed bug in callback calendar with DST
# 71116-0957 - Added campaign_weight and calls_today to the vla table insertion
# 71120-1719 - Added XMLHTPRequest lookup of allowable campaigns for agents during login
# 71122-0256 - Added auto-pause notification
# 71125-1751 - Changed Transfer section to allow for selection of in-groups to send calls to
# 71127-0408 - Added height and width settings for easier modification of screen size
# 71129-2025 - restricted callbacks count and list to campaign only
# 71226-1117 - added option to kick all calls from conference upon logout
# 80330-0823 - Added new AST_blind monitoring ability and minor cleanup for release
# 80402-0121 - Fixes for Manual Dial hangups and transfers
# 80428-0413 - UTF8 changes and testing
# 80507-0932 - Fixed Script display bug (+ instead of space)
# 80519-1425 - Added calls in queue display
#
# 90201-1001 - Branched to OSDial
#
# 90201-0931 - Modified screens to get uniform colors and a more professional look
#			Replaced all buttons, added a second Web Form,
#			Resized to work on 1024 wide screen, added additional section for new fields
#			Changed to new version (from 2.0.4-121)

$version = '2.1.0';
$build = '80519-1425/90102';

require("dbconnect.php");

if (isset($_GET["DB"]))						    {$DB=$_GET["DB"];}
        elseif (isset($_POST["DB"]))            {$DB=$_POST["DB"];}
if (isset($_GET["phone_login"]))                {$phone_login=$_GET["phone_login"];}
        elseif (isset($_POST["phone_login"]))   {$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_pass"]))					{$phone_pass=$_GET["phone_pass"];}
        elseif (isset($_POST["phone_pass"]))    {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["VD_login"]))					{$VD_login=$_GET["VD_login"];}
        elseif (isset($_POST["VD_login"]))      {$VD_login=$_POST["VD_login"];}
if (isset($_GET["VD_pass"]))					{$VD_pass=$_GET["VD_pass"];}
        elseif (isset($_POST["VD_pass"]))       {$VD_pass=$_POST["VD_pass"];}
if (isset($_GET["VD_campaign"]))                {$VD_campaign=$_GET["VD_campaign"];}
        elseif (isset($_POST["VD_campaign"]))   {$VD_campaign=$_POST["VD_campaign"];}
if (isset($_GET["relogin"]))					{$relogin=$_GET["relogin"];}
        elseif (isset($_POST["relogin"]))       {$relogin=$_POST["relogin"];}
	if (!isset($phone_login)) 
		{
		if (isset($_GET["pl"]))                {$phone_login=$_GET["pl"];}
				elseif (isset($_POST["pl"]))   {$phone_login=$_POST["pl"];}
		}
	if (!isset($phone_pass))
		{
		if (isset($_GET["pp"]))                {$phone_pass=$_GET["pp"];}
				elseif (isset($_POST["pp"]))   {$phone_pass=$_POST["pp"];}
		}
	if (isset($VD_campaign))
		{
		$VD_campaign = strtoupper($VD_campaign);
		$VD_campaign = eregi_replace(" ",'',$VD_campaign);
		}
	if (!isset($flag_channels))
		{
		$flag_channels=0;
		$flag_string='';
		}

### security strip all non-alphanumeric characters out of the variables ###
	$DB=ereg_replace("[^0-9a-z]","",$DB);
	$phone_login=ereg_replace("[^0-9a-zA-Z]","",$phone_login);
	$phone_pass=ereg_replace("[^0-9a-zA-Z]","",$phone_pass);
	$VD_login=ereg_replace("[^0-9a-zA-Z]","",$VD_login);
	$VD_pass=ereg_replace("[^0-9a-zA-Z]","",$VD_pass);
	$VD_campaign=ereg_replace("[^0-9a-zA-Z_]","",$VD_campaign);


$forever_stop=0;

if ($force_logout)
{
    echo "You have now logged out. Thank you\n";
    exit;
}

$isdst = date("I");
$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$CIDdate = date("ymdHis");
	$month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
	$past_month_date = date("Y-m-d H:i:s",$month_old);


$random = (rand(1000000, 9999999) + 10000000);

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

# options now set in DB:
#$alt_phone_dialing		= '1';	# allow agents to call alt phone numbers
#$scheduled_callbacks	= '1';	# set to 1 to allow agent to choose scheduled callbacks
#   $agentonly_callbacks	= '1';	# set to 1 to allow agent to choose agent-only scheduled callbacks
#$agentcall_manual		= '1';	# set to 1 to allow agent to make manual calls during autodial session

$conf_silent_prefix		= '7';	# osdial_conferences prefix to enter silently
$HKuser_level			= '5';	# minimum osdial user_level for HotKeys
$campaign_login_list	= '1';	# show drop-down list of campaigns at login	
$manual_dial_preview	= '1';	# allow preview lead option when manual dial
$multi_line_comments	= '1';	# set to 1 to allow multi-line comment box
$user_login_first		= '0';	# set to 1 to have the osdial_user login before the phone login
$view_scripts			= '1';	# set to 1 to show the SCRIPTS tab
$dispo_check_all_pause	= '0';	# set to 1 to allow for persistent pause after dispo
$callholdstatus			= '1';	# set to 1 to show calls on hold count
$agentcallsstatus		= '0';	# set to 1 to show agent status and call dialed count
   $campagentstatctmax	= '3';	# Number of seconds for campaign call and agent stats
$show_campname_pulldown	= '1';	# set to 1 to show campaign name on login pulldown
$webform_sessionname	= '1';	# set to 1 to include the session_name in webform URL
$local_consult_xfers	= '1';	# set to 1 to send consultative transfers from original server
$clientDST				= '1';	# set to 1 to check for DST on server for agent time
$no_delete_sessions		= '0';	# set to 1 to not delete sessions at logout
$volumecontrol_active	= '1';	# set to 1 to allow agents to alter volume of channels
$PreseT_DiaL_LinKs		= '1';	# set to 1 to show a DIAL link for Dial Presets
$LogiNAJAX				= '1';	# set to 1 to do lookups
$HidEMonitoRSessionS	= '1';	# set to 1 to hide remote monitoring channels from "session calls"
$LogouTKicKAlL			= '1';	# set to 1 to hangup all calls in session upon agent logout

$TEST_all_statuses		= '0';	# TEST variable allows all statuses in dispo screen

$BROWSER_HEIGHT		= 526;	# set to the minimum browser height, default=500
$BROWSER_WIDTH			= 980;	# set to the minimum browser width, default=770

### SCREEN WIDTH AND HEIGHT CALCULATIONS ###

$MASTERwidth=($BROWSER_WIDTH - 340);
$MASTERheight=($BROWSER_HEIGHT - 200);
if ($MASTERwidth < 430) {$MASTERwidth = '430';} 
if ($MASTERheight < 300) {$MASTERheight = '300';} 

$CAwidth =  ($MASTERwidth + 340);	# 770 - cover all (none-in-session, customer hunngup, etc...)
$MNwidth =  ($MASTERwidth + 330);	# 760 - main frame
$XFwidth =  ($MASTERwidth + 320);	# 750 - transfer/conference
$HCwidth =  ($MASTERwidth + 310);	# 740 - hotkeys and callbacks
$AMwidth =  ($MASTERwidth + 270);	# 700 - agent mute and preset-dial links
$SSwidth =  ($MASTERwidth + 176 - 286);# 46);	# 606 - scroll script
$SDwidth =  ($MASTERwidth + 170 - 296);	# 600 - scroll script, customer data and calls-in-session
$HKwidth =  ($MASTERwidth + 70);	# 500 - Hotkeys button
$HSwidth =  ($MASTERwidth + 1);	# 431 - Header spacer

$HKheight =  ($MASTERheight + 105 + 10);	# 405 - HotKey active Button
$AMheight =  ($MASTERheight + 110);	# 400 - Agent mute and preset dial links
$MBheight =  ($MASTERheight + 65 +92);	# 365 - Manual Dial Buttons
$CBheight =  ($MASTERheight + 140);	# 350 - Agent Callback, pause code, volume control Buttons and agent status
$SSheight =  ($MASTERheight + 31 -25);		# 331 - script content
$HTheight =  ($MASTERheight + 10);		# 310 - transfer frame, callback comments and hotkey
$BPheight =  ($MASTERheight - 250);	# 50 - bottom buffer


$US='_';
$CL=':';
$AT='@';
$DS='-';
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {
	$HTTPprotocol = 'https://';
} else {
	$HTTPprotocol = 'http://';
}
if (($server_port == '80') or ($server_port == '443') ) {
	$server_port='';
} else {
	$server_port = "$CL$server_port";
}
$agcPAGE = "$HTTPprotocol$server_name$server_port$script_name";
$agcDIR = eregi_replace('osdial.php','',$agcPAGE);

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0
echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build -->\n";

if ($campaign_login_list > 0) {
	$camp_form_code  = "<select size=1 name=VD_campaign id=VD_campaign onFocus=\"login_allowable_campaigns()\">\n";
	$camp_form_code .= "<option value=\"\"></option>\n";
	
	$LOGallowed_campaignsSQL='';
	if ($relogin == 'YES')
	{
		$stmt="SELECT user_group from osdial_users where user='$VD_login' and pass='$VD_pass'";
		if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}	
		
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$VU_user_group=$row[0];
	
		$stmt="SELECT allowed_campaigns from osdial_user_groups where user_group='$VU_user_group';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ( (!eregi("ALL-CAMPAIGNS",$row[0])) )
			{
			$LOGallowed_campaignsSQL = eregi_replace(' -','',$row[0]);
			$LOGallowed_campaignsSQL = eregi_replace(' ',"','",$LOGallowed_campaignsSQL);
			$LOGallowed_campaignsSQL = "and campaign_id IN('$LOGallowed_campaignsSQL')";
			}
	}
	
	$stmt="SELECT campaign_id,campaign_name from osdial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id";
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	
	$o=0;
	while ($camps_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		if ($show_campname_pulldown)
			{$campname = " - $rowx[1]";}
		else
			{$campname = '';}
		if ($VD_campaign)
			{
			if ( (eregi("$VD_campaign",$rowx[0])) and (strlen($VD_campaign) == strlen($rowx[0])) )
				{$camp_form_code .= "<option value=\"$rowx[0]\" SELECTED>$rowx[0]$campname</option>\n";}
			else
				{$camp_form_code .= "<option value=\"$rowx[0]\">$rowx[0]$campname</option>\n";}
			}
		else
			{$camp_form_code .= "<option value=\"$rowx[0]\">$rowx[0]$campname</option>\n";}
		$o++;
		}
	$camp_form_code .= "</select>\n";
} else {
	$camp_form_code = "<INPUT TYPE=TEXT NAME=VD_campaign SIZE=10 MAXLENGTH=20 VALUE=\"$VD_campaign\">\n";
}


if ($LogiNAJAX > 0) {

?>
	<script language="Javascript">

		// ################################################################################
		// Send Request for allowable campaigns to populate the campaigns pull-down
		function login_allowable_campaigns() {
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
			try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
			xmlhttp = false;
			}
			}
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				logincampaign_query = "&user=" + document.osdial_form.VD_login.value + "&pass=" + document.osdial_form.VD_pass.value + "&ACTION=LogiNCamPaigns&format=html";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(logincampaign_query); 
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
					//	alert(logincampaign_query);
					//	alert(xmlhttp.responseText);
						document.getElementById("LogiNCamPaigns").innerHTML = Nactiveext;
						document.getElementById("LogiNReseT").innerHTML = "<INPUT TYPE=BUTTON VALUE=\"Refresh Campaign List\" OnClick=\"login_allowable_campaigns()\">";
						document.getElementById("VD_campaign").focus();
					}
				}
				delete xmlhttp;
			}
		}
	</script>


<?
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";

//  Relogin
if ($relogin == 'YES') {
	echo "<title>OSDial web client: Re-Login</title>\n";
	// echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
	echo "</head>\n";
	
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
	echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
	echo "</TR></TABLE>\n";
	
	echo "<FORM NAME=osdial_form ID=osdial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	//echo "<BR><BR><BR><CENTER><TABLE frame=border WIDTH=460 CELLPADDING=0 CELLSPACING=0 BGCOLOR=365783><TR BGCOLOR=365783>"; //#E0C2D6
	
	echo "<div class=containera>";
	
	echo "<TABLE class=acrosslogin2 WIDTH=500 CELLPADDING=0 CELLSPACING=0 border=0>";
	echo "<tr>";
	echo "	<td width=22><img src=images/AgentTopLeft.png width=22 height=22 align=left></td>";
	echo "	<td class=across-top align=center colspan=2>&nbsp;</td>";
	echo "	<td width=22><img src=images/AgentTopRightS.png width=22 height=22 align=right></td>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD align=left>&nbsp;</TD>";
	echo "	<TD ALIGN=center colspan=2><font color=#1C4754><b>Agent Login</b></TD>";
	echo "	<TD align=left class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD ALIGN=right COLSPAN=4 class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD align=left></TD>";
	echo "	<TD ALIGN=right><font color=#1C4754>Phone Login:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"$phone_login\"></TD>";
	echo "	<TD align=left class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD align=left></TD>";
	echo "	<TD ALIGN=RIGHT><font color=#1C4754>Phone Password:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"$phone_pass\"></TD>";
	echo "	<TD align=right class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD align=left></TD>";
	echo "	<TD ALIGN=RIGHT><font color=#1C4754>User Login:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\"></TD>";
	echo "	<TD align=left class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD align=left></TD>";
	echo "	<TD ALIGN=RIGHT><font color=#1C4754>User Password:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"></TD>";
	echo "	<TD align=left class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD align=left></TD>";
	echo "	<TD ALIGN=RIGHT><font color=#1C4754>Campaign:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT>$camp_form_code</TD>";
	echo "	<TD align=left class=rborder>&nbsp;</TD>";
	echo "</tr>";
	echo "<tr><td colspan=4 class=rborder>&nbsp;</td></tr>";
	echo "<tr>";
	echo "	<TD ALIGN=CENTER COLSPAN=4 class=rborder><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT></TD>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD ALIGN=LEFT COLSPAN=4 class=rbborder><font size=1>&nbsp;Version: $version&nbsp;&nbsp;&nbsp;Build: $build</TD>";
	echo "</tr>";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
}

if ($user_login_first == 1) {
	if ( (strlen($VD_login)<1) or (strlen($VD_pass)<1) or (strlen($VD_campaign)<1) ) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";
		echo "<title>OSDial web client: Campaign Login</title>\n";
		//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
		echo "</head>\n";
		echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 NAME=osdial>\n";
		
		echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
		echo "</TR></TABLE>\n";
		
		echo "<FORM  NAME=osdial_form ID=osdial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
		echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
		#echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
		#echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
		echo "<CENTER><BR><B>User Login</B><BR><BR>";
		echo "<TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0><TR>";
		echo "<TD ALIGN=LEFT VALIGN=BOTTOM><b>&nbsp;&nbsp;<font color=white>OSDial</b><!--IMG SRC=\"./images/vdc_tab_osdial.gif\" BORDER=0 --></TD>";
		echo "<TD ALIGN=CENTER VALIGN=MIDDLE> <font color=white>Campaign Login </TD>";
		echo "</TR>\n";
		echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT><font color=white>User Login:  </TD>";
		echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\"></TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT><font color=white>User Password:  </TD>";
		echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"></TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT><font color=white>Campaign:  </TD>";
		echo "<TD ALIGN=LEFT><span id=\"LogiNCamPaigns\">$camp_form_code</span></TD></TR>\n";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT> &nbsp; \n";
		echo "<span id=\"LogiNReseT\"></span></TD></TR>\n";
		echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</TD></TR>\n";
		echo "</TABLE>\n";
		echo "</FORM>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
	} else {
		if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) ) {
			$stmt="SELECT phone_login,phone_pass from osdial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0;";
			if ($DB) {echo "|$stmt|\n";}
			
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$phone_login=$row[0];
			$phone_pass=$row[1];
	
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";
			echo "<title>OSDial web client: Login</title>\n";
			//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
			echo "</head>\n";
			echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
			echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
			echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
			echo "</TR></TABLE>\n";
			echo "<FORM  NAME=osdial_form ID=osdial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
			echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
			echo "<BR><BR><BR><CENTER><TABLE class=accrosslogin WIDTH=460 CELLPADDING=0 CELLSPACING=0><TR>";
			echo "<TD ALIGN=LEFT VALIGN=BOTTOM>&nbsp;&nbsp;<font color=blue>OSDial</TD>";
			echo "<TD ALIGN=CENTER VALIGN=MIDDLE> <font color=white>Login </TD>";
			echo "</TR>\n";
			echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1> &nbsp; </TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT><font color=white>Phone Login: </TD>";
			echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"$phone_login\"></TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT><font color=white>Phone Password:  </TD>";
			echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"$phone_pass\"></TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT><font color=white>User Login:  </TD>";
			echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\"></TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT><font color=white>User Password:  </TD>";
			echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"></TD></TR>\n";
			echo "<TR><TD ALIGN=RIGHT><font color=white>Campaign:  </TD>";
			echo "<TD ALIGN=LEFT><span id=\"LogiNCamPaigns\">$camp_form_code</span></TD></TR>\n";
			echo "<tr><td colspan=2>&nbsp;</td></tr>";
			echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT> &nbsp; \n";
			echo "<span id=\"LogiNReseT\"></span></TD></TR>\n";
			echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</TD></TR>\n";
			echo "</TABLE>\n";
			echo "</FORM>\n\n";
			echo "</body>\n\n";
			echo "</html>\n\n";
			exit;
		}
	}
}

// Phone Login from welcome scren
if ( (strlen($phone_login)<2) or (strlen($phone_pass)<2) ) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";
	echo "<title>OSDial web client:  Phone Login</title>\n";
	//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
	echo "</head>\n";
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
	
	echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
	echo "</TR></TABLE>\n";
	
	echo "<FORM  NAME=osdial_form ID=osdial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	
	echo "<div class=containera>";
	echo "<TABLE align=center class=acrosslogin2 WIDTH=460 CELLPADDING=0 CELLSPACING=0 border=0>";
	echo "<tr>";
	echo "	<td width='22'><img src='images/AgentTopLeft.png' width='22' height='22' align='left'></td>";
	echo "	<td class='across-top' align='center' colspan=2></td>";
	echo "	<td width='22'><img src='images/AgentTopRightS.png' width='22' height='22' align='right'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	//echo "	<TD ALIGN=LEFT VALIGN=BOTTOM><font color=navy></TD>";
	echo "	<TD ALIGN=center colspan=2><font color=#1C4754><b>Login To Your Phone</TD>";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</TR>\n";
	echo "<TR>";
	echo "	<TD colspan=4 class=rborder>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<TR>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<TD ALIGN=RIGHT><font color=#1C4754>Phone Login:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"\"></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<TR>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<TD ALIGN=RIGHT><font color=#1C4754>Phone Password:&nbsp;</TD>";
	echo "	<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"\"></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<tr><td colspan=4 class=rborder>&nbsp;</td></tr>";
	echo "<TR>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT> &nbsp; \n";
	echo "	<span id=\"LogiNReseT\"></span></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<TR>";
	echo "	<TD ALIGN=LEFT COLSPAN=4 class=rbborder><font size=1><BR>&nbsp;Version: $version &nbsp; &nbsp; &nbsp; Build: $build</TD>\n";
	echo "</tr>";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
} else {
if ($WeBRooTWritablE > 0) {$fp = fopen ("./osdial_auth_entries.txt", "a");}
	$VDloginDISPLAY=0;

	if ( (strlen($VD_login)<2) or (strlen($VD_pass)<2) or (strlen($VD_campaign)<2) )
	{
	$VDloginDISPLAY=1;
	}
	else
	{
	$stmt="SELECT count(*) from osdial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if($auth>0)
		{
		$login=strtoupper($VD_login);
		$password=strtoupper($VD_pass);
		##### grab the full name of the agent
		$stmt="SELECT full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,closer_default_blended,user_group,osdial_recording_override from osdial_users where user='$VD_login' and pass='$VD_pass'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname=$row[0];
		$user_level=$row[1];
		$VU_hotkeys_active=$row[2];
		$VU_agent_choose_ingroups=$row[3];
		$VU_scheduled_callbacks=$row[4];
		$agentonly_callbacks=$row[5];
		$agentcall_manual=$row[6];
		$VU_osdial_recording=$row[7];
		$VU_osdial_transfers=$row[8];
		$VU_closer_default_blended=$row[9];
		$VU_user_group=$row[10];
		$VU_osdial_recording_override=$row[11];

		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "vdweb|GOOD|$date|$VD_login|$VD_pass|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		$user_abb = "$VD_login$VD_login$VD_login$VD_login";
		while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
			{$user_abb = eregi_replace("^.","",$user_abb);   $forever_stop++;}

		$stmt="SELECT allowed_campaigns from osdial_user_groups where user_group='$VU_user_group';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGallowed_campaigns		=$row[0];

		if ( (!eregi(" $VD_campaign ",$LOGallowed_campaigns)) and (!eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) )
			{
			echo "<title>OSDial web client: OSDial Campaign Login</title>\n";
			//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
			echo "</head>\n";
			echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
			echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
			echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
			echo "</TR></TABLE>\n";
			echo "<B>Sorry, you are not allowed to login to this campaign: $VD_campaign</B>\n";
			echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>\n";
			echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
			echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
			echo "Login: <INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\">\n<br>";
			echo "Password: <INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"><br>\n";
			echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
			echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT> &nbsp; \n";
			echo "<span id=\"LogiNReseT\"></span>\n";
			echo "</FORM>\n\n";
			echo "</body>\n\n";
			echo "</html>\n\n";
			exit;
			}

		##### check to see that the campaign is active
		$stmt="SELECT count(*) FROM osdial_campaigns where campaign_id='$VD_campaign' and active='Y';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$CAMPactive=$row[0];
		if($CAMPactive>0)
			{
			if ($TEST_all_statuses > 0) {$selectableSQL = '';}
			else {$selectableSQL = "selectable='Y' and";}
			$VARstatuses='';
			$VARstatusnames='';
			##### grab the statuses that can be used for dispositioning by an agent
			$stmt="SELECT status,status_name FROM osdial_statuses WHERE $selectableSQL status != 'NEW' order by status limit 50;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$VD_statuses_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $VD_statuses_ct)
				{
				$row=mysql_fetch_row($rslt);
				$statuses[$i] =$row[0];
				$status_names[$i] =$row[1];
				$VARstatuses = "$VARstatuses'$statuses[$i]',";
				$VARstatusnames = "$VARstatusnames'$status_names[$i]',";
				$i++;
				}

			##### grab the campaign-specific statuses that can be used for dispositioning by an agent
			$stmt="SELECT status,status_name FROM osdial_campaign_statuses WHERE $selectableSQL status != 'NEW' and campaign_id='$VD_campaign' order by status limit 50;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$VD_statuses_camp = mysql_num_rows($rslt);
			$j=0;
			while ($j < $VD_statuses_camp)
				{
				$row=mysql_fetch_row($rslt);
				$statuses[$i] =$row[0];
				$status_names[$i] =$row[1];
				$VARstatuses = "$VARstatuses'$statuses[$i]',";
				$VARstatusnames = "$VARstatusnames'$status_names[$i]',";
				$i++;
				$j++;
				}
			$VD_statuses_ct = ($VD_statuses_ct+$VD_statuses_camp);
			$VARstatuses = substr("$VARstatuses", 0, -1); 
			$VARstatusnames = substr("$VARstatusnames", 0, -1); 

			##### grab the campaign-specific HotKey statuses that can be used for dispositioning by an agent
			$stmt="SELECT hotkey,status,status_name FROM osdial_campaign_hotkeys WHERE selectable='Y' and status != 'NEW' and campaign_id='$VD_campaign' order by hotkey limit 9;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$HK_statuses_camp = mysql_num_rows($rslt);
			$w=0;
			$HKboxA='';
			$HKboxB='';
			$HKboxC='';
			while ($w < $HK_statuses_camp)
				{
				$row=mysql_fetch_row($rslt);
				$HKhotkey[$w] =$row[0];
				$HKstatus[$w] =$row[1];
				$HKstatus_name[$w] =$row[2];
				$HKhotkeys = "$HKhotkeys'$HKhotkey[$w]',";
				$HKstatuses = "$HKstatuses'$HKstatus[$w]',";
				$HKstatusnames = "$HKstatusnames'$HKstatus_name[$w]',";
				if ($w < 3)
					{$HKboxA = "$HKboxA <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<BR>";}
				if ( ($w >= 3) and ($w < 6) )
					{$HKboxB = "$HKboxB <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<BR>";}
				if ($w >= 6)
					{$HKboxC = "$HKboxC <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<BR>";}
				$w++;
				}
			$HKhotkeys = substr("$HKhotkeys", 0, -1); 
			$HKstatuses = substr("$HKstatuses", 0, -1); 
			$HKstatusnames = substr("$HKstatusnames", 0, -1); 

			##### grab the statuses to be dialed for your campaign as well as other campaign settings
			$stmt="SELECT park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups FROM osdial_campaigns where campaign_id = '$VD_campaign';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
				$park_ext =				$row[0];
				$park_file_name =			$row[1];
				$web_form_address =			$row[2];
				$allow_closers =			$row[3];
				$auto_dial_level =			$row[4];
				$dial_timeout =			$row[5];
				$dial_prefix =				$row[6];
				$campaign_cid =			$row[7];
				$campaign_vdad_exten =		$row[8];
				$campaign_rec_exten =		$row[9];
				$campaign_recording =		$row[10];
				$campaign_rec_filename =		$row[11];
				$campaign_script =			$row[12];
				$get_call_launch =			$row[13];
				$campaign_am_message_exten = 	$row[14];
				$xferconf_a_dtmf =			$row[15];
				$xferconf_a_number =		$row[16];
				$xferconf_b_dtmf =			$row[17];
				$xferconf_b_number =		$row[18];
				$alt_number_dialing =		$row[19];
				$VC_scheduled_callbacks =	$row[20];
				$wrapup_seconds =			$row[21];
				$wrapup_message =			$row[22];
				$closer_campaigns =			$row[23];
				$use_internal_dnc =			$row[24];
				$allcalls_delay =			$row[25];
				$omit_phone_code =			$row[26];
				$agent_pause_codes_active =	$row[27];
				$no_hopper_leads_logins =	$row[28];
				$campaign_allow_inbound =	$row[29];
				$manual_dial_list_id =		$row[30];
				$default_xfer_group =		$row[31];
				$xfer_groups =				$row[32];
				//$web_form_address2 = 		$row[69]; // debug needs table update

			if ( (!ereg('DISABLED',$VU_osdial_recording_override)) and ($VU_osdial_recording > 0) )
				{
				$campaign_recording = $VU_osdial_recording_override;
				print "<!-- USER RECORDING OVERRIDE: |$VU_osdial_recording_override|$campaign_recording| -->\n";
				}
			if ( ($VC_scheduled_callbacks=='Y') and ($VU_scheduled_callbacks=='1') )
				{$scheduled_callbacks='1';}
			if ($VU_osdial_recording=='0')
				{$campaign_recording='NEVER';}
			if ($alt_number_dialing=='Y')
				{$alt_phone_dialing='1';}
			else
				{$alt_phone_dialing='0';}
			$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
			$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
			$closer_campaigns = "'$closer_campaigns'";

			if ($agent_pause_codes_active=='Y')
				{
				##### grab the pause codes for this campaign
				$stmt="SELECT pause_code,pause_code_name FROM osdial_pause_codes WHERE campaign_id='$VD_campaign' order by pause_code limit 50;";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$VD_pause_codes = mysql_num_rows($rslt);
				$j=0;
				while ($j < $VD_pause_codes)
					{
					$row=mysql_fetch_row($rslt);
					$pause_codes[$i] =$row[0];
					$pause_code_names[$i] =$row[1];
					$VARpause_codes = "$VARpause_codes'$pause_codes[$i]',";
					$VARpause_code_names = "$VARpause_code_names'$pause_code_names[$i]',";
					$i++;
					$j++;
					}
				$VD_pause_codes_ct = ($VD_pause_codes_ct+$VD_pause_codes);
				$VARpause_codes = substr("$VARpause_codes", 0, -1); 
				$VARpause_code_names = substr("$VARpause_code_names", 0, -1); 
				}

			##### grab the inbound groups to choose from if campaign contains CLOSER
			$VARingroups="''";
			if ($campaign_allow_inbound == 'Y')
				{
				$VARingroups='';
				$stmt="select group_id from osdial_inbound_groups where active = 'Y' and group_id IN($closer_campaigns) order by group_id limit 60;";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$closer_ct = mysql_num_rows($rslt);
				$INgrpCT=0;
				while ($INgrpCT < $closer_ct)
					{
					$row=mysql_fetch_row($rslt);
					$closer_groups[$INgrpCT] =$row[0];
					$VARingroups = "$VARingroups'$closer_groups[$INgrpCT]',";
					$INgrpCT++;
					}
				$VARingroups = substr("$VARingroups", 0, -1); 
				}

			##### grab the allowable inbound groups to choose from for transfer options
			$xfer_groups = preg_replace("/^ | -$/","",$xfer_groups);
			$xfer_groups = preg_replace("/ /","','",$xfer_groups);
			$xfer_groups = "'$xfer_groups'";
			$VARxfergroups="''";
			if ($allow_closers == 'Y')
				{
				$VARxfergroups='';
				$stmt="select group_id,group_name from osdial_inbound_groups where active = 'Y' and group_id IN($xfer_groups) order by group_id limit 60;";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$xfer_ct = mysql_num_rows($rslt);
				$XFgrpCT=0;
				while ($XFgrpCT < $xfer_ct)
					{
					$row=mysql_fetch_row($rslt);
					$VARxfergroups = "$VARxfergroups'$row[0]',";
					$VARxfergroupsnames = "$VARxfergroupsnames'$row[1]',";
					if ($row[0] == "$default_xfer_group") {$default_xfer_group_name = $row[1];}
					$XFgrpCT++;
					}
				$VARxfergroups = substr("$VARxfergroups", 0, -1); 
				$VARxfergroupsnames = substr("$VARxfergroupsnames", 0, -1); 
				}

			##### grab the number of leads in the hopper for this campaign
			$stmt="SELECT count(*) FROM osdial_hopper where campaign_id = '$VD_campaign' and status='READY';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			   $campaign_leads_to_call = $row[0];
			   print "<!-- $campaign_leads_to_call - leads left to call in hopper -->\n";

			}
		else
			{
			$VDloginDISPLAY=1;
			$VDdisplayMESSAGE = "Campaign not active, please try again<BR>";
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "vdweb|FAIL|$date|$VD_login|$VD_pass|$ip|$browser|\n");
			fclose($fp);
			}
		$VDloginDISPLAY=1;
		$VDdisplayMESSAGE = "Login incorrect, please try again<BR>";
		}
	}
	if ($VDloginDISPLAY)
	{
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";
	echo "<title>OSDial web client: Campaign Login</title>\n";
	//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
	echo "</head>\n";
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
	
	echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
	echo "</TR></TABLE>\n";
	
	echo "<FORM  NAME=osdial_form ID=osdial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
	
	echo "<div class=containera>";
	
	echo "<TABLE align=center class=acrosslogin2 WIDTH=460 CELLPADDING=0 CELLSPACING=0 border=0>";
	echo "<tr>";
	echo "	<td width=22><img src=images/AgentTopLeft.png width=22 height=22 align=left></td>";
	echo "	<td class=across-top align=center colspan=2></td>";
	echo "	<td width=22><img src=images/AgentTopRightS.png width=22 height=22 align=right></td>";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD ALIGN=LEFT>&nbsp;&nbsp;</TD>";
	echo "	<TD ALIGN=center colspan=2><font color=#1C4754><b>Login To A Campaign</b></TD>";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>\n";
	echo "<tr>";
	echo "	<td ALIGN=LEFT COLSPAN=4 class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<td ALIGN=RIGHT><font color=#1C4754>User Login:&nbsp;</TD>";
	echo "	<td ALIGN=LEFT><INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\"></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<rt>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<td ALIGN=RIGHT><font color=#1C4754>User Password:&nbsp;</TD>";
	echo "	<td ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<tr>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<td ALIGN=RIGHT><font color=#1C4754>Campaign:&nbsp;</TD>";
	echo "	<td ALIGN=LEFT><span id=\"LogiNCamPaigns\">$camp_form_code</span></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<tr><td colspan=4 class=rborder>&nbsp;</td></tr>";
	echo "<tr>";
	echo "	<TD ALIGN=LEFT><font size=1>&nbsp;</TD>\n";
	echo "	<td ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>&nbsp;<span id=\"LogiNReseT\"></span></TD>\n";
	echo "	<TD ALIGN=LEFT class=rborder><font size=1>&nbsp;</TD>\n";
	echo "</tr>";
	echo "<TR>";
	echo "	<TD ALIGN=LEFT COLSPAN=4 class=rbborder><font size=1><BR>&nbsp;Version: $version&nbsp;&nbsp;&nbsp;Build: $build</TD>\n";
	echo "</tr>";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}

$authphone=0;
$stmt="SELECT count(*) from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$authphone=$row[0];
if (!$authphone)
	{
	echo "<title>OSDial web client: Phone Login Error</title>\n";
	//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
	echo "</head>\n";
	echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
	echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
	echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
	echo "</TR></TABLE>\n";
	echo "<FORM  NAME=osdial_form ID=osdial_form ACTION=\"$agcPAGE\" METHOD=POST>\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=VD_login VALUE=\"$VD_login\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=VD_pass VALUE=\"$VD_pass\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=VD_campaign VALUE=\"$VD_campaign\">\n";
	echo "<BR><BR><BR><CENTER><TABLE WIDTH=460 CELLPADDING=0 CELLSPACING=0><TR>";
	echo "<TD ALIGN=LEFT VALIGN=BOTTOM>&nbsp;&nbsp;<font color=white>OSDial</TD>";
	echo "<TD ALIGN=CENTER VALIGN=MIDDLE> <font color=white>Login Error</TD>";
	echo "</TR>\n";
	echo "<TR><TD ALIGN=CENTER COLSPAN=2><font size=1> &nbsp; <BR><FONT SIZE=3><font color=white>Sorry, your phone login and password are not active in this system, please try again: <BR> &nbsp;</TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT><font color=white>Phone Login: </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=TEXT NAME=phone_login SIZE=10 MAXLENGTH=20 VALUE=\"$phone_login\"></TD></TR>\n";
	echo "<TR><TD ALIGN=RIGHT><font color=white>Phone Password:  </TD>";
	echo "<TD ALIGN=LEFT><INPUT TYPE=PASSWORD NAME=phone_pass SIZE=10 MAXLENGTH=20 VALUE=\"$phone_pass\"></TD></TR>\n";
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<TR><TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT></TD></TR>\n";
	echo "<TR><TD ALIGN=LEFT COLSPAN=2><font size=1><BR>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</TD></TR>\n";
	echo "</TABLE>\n";
	echo "</FORM>\n\n";
	echo "</body>\n\n";
	echo "</html>\n\n";
	exit;
	}
else
	{
	echo "<title>OSDial web client</title>\n";
	$stmt="SELECT * from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$extension=$row[0];
	$dialplan_number=$row[1];
	$voicemail_id=$row[2];
	$phone_ip=$row[3];
	$computer_ip=$row[4];
	$server_ip=$row[5];
	$login=$row[6];
	$pass=$row[7];
	$status=$row[8];
	$active=$row[9];
	$phone_type=$row[10];
	$fullname=$row[11];
	$company=$row[12];
	$picture=$row[13];
	$messages=$row[14];
	$old_messages=$row[15];
	$protocol=$row[16];
	$local_gmt=$row[17];
	$ASTmgrUSERNAME=$row[18];
	$ASTmgrSECRET=$row[19];
	$login_user=$row[20];
	$login_pass=$row[21];
	$login_campaign=$row[22];
	$park_on_extension=$row[23];
	$conf_on_extension=$row[24];
	$OSDiaL_park_on_extension=$row[25];
	$OSDiaL_park_on_filename=$row[26];
	$monitor_prefix=$row[27];
	$recording_exten=$row[28];
	$voicemail_exten=$row[29];
	$voicemail_dump_exten=$row[30];
	$ext_context=$row[31];
	$dtmf_send_extension=$row[32];
	$call_out_number_group=$row[33];
	$client_browser=$row[34];
	$install_directory=$row[35];
	$local_web_callerID_URL=$row[36];
	$OSDiaL_web_URL=$row[37];
	$AGI_call_logging_enabled=$row[38];
	$user_switching_enabled=$row[39];
	$conferencing_enabled=$row[40];
	$admin_hangup_enabled=$row[41];
	$admin_hijack_enabled=$row[42];
	$admin_monitor_enabled=$row[43];
	$call_parking_enabled=$row[44];
	$updater_check_enabled=$row[45];
	$AFLogging_enabled=$row[46];
	$QUEUE_ACTION_enabled=$row[47];
	$CallerID_popup_enabled=$row[48];
	$voicemail_button_enabled=$row[49];
	$enable_fast_refresh=$row[50];
	$fast_refresh_rate=$row[51];
	$enable_persistant_mysql=$row[52];
	$auto_dial_next_number=$row[53];
	$VDstop_rec_after_each_call=$row[54];
	$DBX_server=$row[55];
	$DBX_database=$row[56];
	$DBX_user=$row[57];
	$DBX_pass=$row[58];
	$DBX_port=$row[59];
	$enable_sipsak_messages=$row[66];

	if ($clientDST)
		{
		$local_gmt = ($local_gmt + $isdst);
		}
	if ($protocol == 'EXTERNAL')
		{
		$protocol = 'Local';
		$extension = "$dialplan_number$AT$ext_context";
		}
	$SIP_user = "$protocol/$extension";

	$stmt="SELECT asterisk_version from servers where server_ip='$server_ip';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$asterisk_version=$row[0];

	# If a park extension is not set, use the default one
	if ( (strlen($park_ext)>0) && (strlen($park_file_name)>0) )
		{
		$OSDiaL_park_on_extension = "$park_ext";
		$OSDiaL_park_on_filename = "$park_file_name";
		print "<!-- CAMPAIGN CUSTOM PARKING:  |$OSDiaL_park_on_extension|$OSDiaL_park_on_filename| -->\n";
		}
		print "<!-- CAMPAIGN DEFAULT PARKING: |$OSDiaL_park_on_extension|$OSDiaL_park_on_filename| -->\n";

	# If a web form address is not set, use the default one
	if (strlen($web_form_address)>0)
		{
		$OSDiaL_web_form_address = "$web_form_address";
		print "<!-- CAMPAIGN CUSTOM WEB FORM:   |$OSDiaL_web_form_address| -->\n";
		}
	else
		{
		$OSDiaL_web_form_address = "$OSDiaL_web_URL";
		print "<!-- CAMPAIGN DEFAULT WEB FORM:  |$OSDiaL_web_form_address| -->\n";
		$OSDiaL_web_form_address_enc = rawurlencode($OSDiaL_web_form_address);

		}
	# If web form 2 address is not set, use the default one
	if (strlen($web_form_address2)>0) {
		$OSDiaL_web_form_address2 = "$web_form_address2";
		print "<!-- CAMPAIGN CUSTOM WEB FORM2:   |$OSDiaL_web_form_address2| -->\n";
	} else {
		$OSDiaL_web_form_address = "$OSDiaL_web_URL";
		print "<!-- CAMPAIGN DEFAULT WEB FORM2:  |$OSDiaL_web_form_address| -->\n";
		$OSDiaL_web_form_address_enc = rawurlencode($OSDiaL_web_form_address);
	}
	$OSDiaL_web_form_address_enc = rawurlencode($OSDiaL_web_form_address);

	# If closers are allowed on this campaign
	if ($allow_closers=="Y")
		{
		$OSDiaL_allow_closers = 1;
		print "<!-- CAMPAIGN ALLOWS CLOSERS:    |$OSDiaL_allow_closers| -->\n";
		}
	else
		{
		$OSDiaL_allow_closers = 0;
		print "<!-- CAMPAIGN ALLOWS NO CLOSERS: |$OSDiaL_allow_closers| -->\n";
		}


	$session_ext = eregi_replace("[^a-z0-9]", "", $extension);
	if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
	$session_rand = (rand(1,9999999) + 10000000);
	$session_name = "$StarTtimE$US$session_ext$session_rand";

	if ($webform_sessionname)
		{$webform_sessionname = "&session_name=$session_name";}
	else
		{$webform_sessionname = '';}

	$stmt="DELETE from web_client_sessions where start_time < '$past_month_date' and extension='$extension' and server_ip = '$server_ip' and program = 'osdial';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	$stmt="INSERT INTO web_client_sessions values('$extension','$server_ip','osdial','$NOW_TIME','$session_name');";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	if ( ($campaign_allow_inbound == 'Y') || ($campaign_leads_to_call > 0) || (ereg('Y',$no_hopper_leads_logins)) )
		{
		### insert an entry into the user log for the login event
		$stmt = "INSERT INTO osdial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$VD_login','LOGIN','$VD_campaign','$NOW_TIME','$StarTtimE','$VU_user_group')";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
		$stmt="SELECT conf_exten FROM osdial_conferences where extension='$SIP_user' and server_ip = '$server_ip' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$prev_login_ct = mysql_num_rows($rslt);
		$i=0;
		while ($i < $prev_login_ct)
			{
			$row=mysql_fetch_row($rslt);
			$session_id =$row[0];
			$i++;
			}
		if ($prev_login_ct > 0)
			{print "<!-- USING PREVIOUS MEETME ROOM - $session_id - $NOW_TIME - $SIP_user -->\n";}
		else
			{
			##### grab the next available osdial_conference room and reserve it
			$stmt="SELECT conf_exten FROM osdial_conferences where server_ip = '$server_ip' and ((extension='') or (extension is null)) LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$free_conf_ct = mysql_num_rows($rslt);
			$i=0;
			while ($i < $free_conf_ct)
				{
				$row=mysql_fetch_row($rslt);
				$session_id =$row[0];
				$i++;
				}
			$stmt="UPDATE osdial_conferences set extension='$SIP_user' where server_ip='$server_ip' and conf_exten='$session_id';";
			$rslt=mysql_query($stmt, $link);
			print "<!-- USING NEW MEETME ROOM - $session_id - $NOW_TIME - $SIP_user -->\n";

			}

		$stmt="UPDATE osdial_list set status='N', user='' where status IN('QUEUE','INCALL') and user ='$VD_login';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- old QUEUE and INCALL reverted list:   |$affected_rows| -->\n";

		$stmt="DELETE from osdial_hopper where status IN('QUEUE','INCALL','DONE') and user ='$VD_login';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- old QUEUE and INCALL reverted hopper: |$affected_rows| -->\n";

		$stmt="DELETE from osdial_live_agents where user ='$VD_login';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- old osdial_live_agents records cleared: |$affected_rows| -->\n";

		$stmt="DELETE from osdial_live_inbound_agents where user ='$VD_login';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- old osdial_live_inbound_agents records cleared: |$affected_rows| -->\n";

	#	print "<B>You have logged in as user: $VD_login on phone: $SIP_user to campaign: $VD_campaign</B><BR>\n";
		$OSDiaL_is_logged_in=1;

		### set the callerID for manager middleware-app to connect the phone to the user
		$SIqueryCID = "S$CIDdate$session_id";

		#############################################
		##### START SYSTEM_SETTINGS LOOKUP #####
		$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,osdial_agent_disable,allow_sipsak_messages FROM system_settings;";
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
			$osdial_agent_disable =		$row[6];
			$allow_sipsak_messages =		$row[7];
			$i++;
			}
		##### END QUEUEMETRICS LOGGING LOOKUP #####
		###########################################

		if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP",$protocol)) )
			{
			$SIPSAK_prefix = 'LIN-';
			print "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
			passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$VD_campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
			$SIqueryCID = "$SIPSAK_prefix$VD_campaign$DS$CIDdate";
			}

		### insert a NEW record to the osdial_manager table to be processed
		$stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$SIqueryCID','Channel: $SIP_user','Context: $ext_context','Exten: $session_id','Priority: 1','Callerid: $SIqueryCID','','','','','');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);
		print "<!-- call placed to session_id: $session_id from phone: $SIP_user -->\n";

		if ($auto_dial_level > 0)
			{
			print "<!-- campaign is set to auto_dial_level: $auto_dial_level -->\n";

			##### grab the campaign_weight and number of calls today on that campaign for the agent
			$stmt="SELECT campaign_weight,calls_today FROM osdial_campaign_agents where user='$VD_login' and campaign_id = '$VD_campaign';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$vca_ct = mysql_num_rows($rslt);
			if ($vca_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$campaign_weight =	$row[0];
				$calls_today =		$row[1];
				$i++;
				}
			else
				{
				$campaign_weight =	'0';
				$calls_today =		'0';
				$stmt="INSERT INTO osdial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) values('$VD_login','$VD_campaign','0','0','$calls_today');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				print "<!-- new osdial_campaign_agents record inserted: |$affected_rows| -->\n";
				}
			$closer_chooser_string='';
			$stmt="INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,user_level,campaign_weight,calls_today) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$closer_chooser_string','$user_level','$campaign_weight','$calls_today');";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$affected_rows = mysql_affected_rows($link);
			print "<!-- new osdial_live_agents record inserted: |$affected_rows| -->\n";

			if ($enable_queuemetrics_logging > 0)
				{
				$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
				mysql_select_db("$queuemetrics_dbname", $linkB);

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='AGENTLOGIN',data1='$VD_login@agents',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
				$affected_rows = mysql_affected_rows($linkB);
				print "<!-- queue_log AGENTLOGIN entry added: $VD_login|$affected_rows -->\n";

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
				$affected_rows = mysql_affected_rows($linkB);
				print "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

				mysql_close($linkB);
				mysql_select_db("$VARDB_database", $link);
				}


			if ($campaign_allow_inbound == 'Y')
				{
				print "<!-- CLOSER-type campaign -->\n";
				}
			}
		else
			{
			print "<!-- campaign is set to manual dial: $auto_dial_level -->\n";

			$stmt="INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$user_level');";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$affected_rows = mysql_affected_rows($link);
			print "<!-- new osdial_live_agents record inserted: |$affected_rows| -->\n";

			if ($enable_queuemetrics_logging > 0)
				{
				$linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
				mysql_select_db("$queuemetrics_dbname", $linkB);

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='AGENTLOGIN',data1='$VD_login@agents',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
				$affected_rows = mysql_affected_rows($linkB);
				print "<!-- queue_log AGENTLOGIN entry added: $VD_login|$affected_rows -->\n";

				$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $linkB);
				$affected_rows = mysql_affected_rows($linkB);
				print "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

				mysql_close($linkB);
				mysql_select_db("$VARDB_database", $link);
				}
			}
		}
	else
		{
		echo "<title>OSDial web client: OSDial Campaign Login</title>\n";
		//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
		echo "</head>\n";
		echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
		echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
		echo "</TR></TABLE>\n";
		echo "<B>Sorry, there are no leads in the hopper for this campaign</B>\n";
		echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>\n";
		echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
		echo "Login: <INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\">\n<br>";
		echo "Password: <INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"><br>\n";
		echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
		echo "&nbsp;";
		echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT> &nbsp; \n";
		echo "<span id=\"LogiNReseT\"></span>\n";
		echo "</FORM>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}
	if (strlen($session_id) < 1)
		{
		echo "<title>OSDial web client: OSDial Campaign Login</title>\n";
		//echo "<style>a:link {color: blue} a:visited {color: navy} a:active {color: navy}</style>";
		echo "</head>\n";
		echo "<BODY BGCOLOR=WHITE MARGINHEIGHT=0 MARGINWIDTH=0 name=osdial>\n";
		echo "<TABLE WIDTH=100%><TR><TD></TD>\n";
		echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDIAL -->\n";
		echo "</TR></TABLE>\n";
		echo "<B>Sorry, there are no available sessions</B>\n";
		echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>\n";
		echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_login VALUE=\"$phone_login\">\n";
		echo "<INPUT TYPE=HIDDEN NAME=phone_pass VALUE=\"$phone_pass\">\n";
		echo "Login: <INPUT TYPE=TEXT NAME=VD_login SIZE=10 MAXLENGTH=20 VALUE=\"$VD_login\">\n<br>";
		echo "Password: <INPUT TYPE=PASSWORD NAME=VD_pass SIZE=10 MAXLENGTH=20 VALUE=\"$VD_pass\"><br>\n";
		echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
		echo "&nbsp;";
		echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT> &nbsp; \n";
		echo "<span id=\"LogiNReseT\"></span>\n";
		echo "</FORM>\n\n";
		echo "</body>\n\n";
		echo "</html>\n\n";
		exit;
		}

	if (ereg('MSIE',$browser)) 
		{
		$useIE=1;
		print "<!-- client web browser used: MSIE |$browser|$useIE| -->\n";
		}
	else 
		{
		$useIE=0;
		print "<!-- client web browser used: W3C-Compliant |$browser|$useIE| -->\n";
		}

	$StarTtimE = date("U");
	$NOW_TIME = date("Y-m-d H:i:s");
	##### Agent is going to log in so insert the osdial_agent_log entry now
	$stmt="INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$VD_login','$server_ip','$NOW_TIME','$VD_campaign','$StarTtimE','0','$StarTtimE','$VU_user_group','LOGIN');";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$affected_rows = mysql_affected_rows($link);
	$agent_log_id = mysql_insert_id($link);
	print "<!-- osdial_agent_log record inserted: |$affected_rows|$agent_log_id| -->\n";

	$S='*';
	$D_s_ip = explode('.', $server_ip);
	if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
	if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
	if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
	if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
	if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
	if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
	if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
	if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
	$server_ip_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";

	##### grab the datails of all active scripts in the system
	$stmt="SELECT script_id,script_name,script_text FROM osdial_scripts WHERE active='Y' order by script_id limit 100;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$MM_scripts = mysql_num_rows($rslt);
	$e=0;
	while ($e < $MM_scripts)
		{
		$row=mysql_fetch_row($rslt);
		$MMscriptid[$e] =$row[0];
		$MMscriptname[$e] = urlencode($row[1]);
		$MMscripttext[$e] = urlencode($row[2]);
		$MMscriptids = "$MMscriptids'$MMscriptid[$e]',";
		$MMscriptnames = "$MMscriptnames'$MMscriptname[$e]',";
		$MMscripttexts = "$MMscripttexts'$MMscripttext[$e]',";
		$e++;
		}
	$MMscriptids = substr("$MMscriptids", 0, -1); 
	$MMscriptnames = substr("$MMscriptnames", 0, -1); 
	$MMscripttexts = substr("$MMscripttexts", 0, -1); 

	}
}

################################################################
### BEGIN - build the callback calendar (12 months)          ###
################################################################
define ('ADAY', (60*60*24));
$CdayARY = getdate();
$Cmon = $CdayARY['mon'];
$Cyear = $CdayARY['year'];
$CTODAY = date("Y-m");
$CTODAYmday = date("j");
$CINC=0;

$Cmonths = Array('January','February','March','April','May','June',
				'July','August','September','October','November','December');
$Cdays = Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

$CCAL_OUT = '';

$CCAL_OUT .= "<table border=0 cellpadding=2 cellspacing=2>";

while ($CINC < 12)
{
if ( ($CINC == 0) || ($CINC == 4) ||($CINC == 8) )
	{$CCAL_OUT .= "<tr>";}

$CCAL_OUT .= "<td valign=top>";

$CYyear = $Cyear;
$Cmonth=	($Cmon + $CINC);
if ($Cmonth > 12)
	{
	$Cmonth = ($Cmonth - 12);
	$CYyear++;
	}
$Cstart= mktime(11,0,0,$Cmonth,1,$CYyear);
$CfirstdayARY = getdate($Cstart);
#echo "|$Cmon|$Cmonth|$CINC|\n";
$CPRNTDAY = date("Y-m", $Cstart);

$CCAL_OUT .= "<table border=1 cellpadding=1 bordercolor=\"000000\" cellspacing=\"0\" bgcolor=\"white\">";
$CCAL_OUT .= "<tr>";
$CCAL_OUT .= "<td colspan=7 bordercolor=\"#ffffff\" bgcolor=\"#FFFFCC\">";
$CCAL_OUT .= "<div align=center><font color=\"#000066\"><b><font face=\"Arial, Helvetica, sans-serif\" size=2>";
$CCAL_OUT .= "$CfirstdayARY[month] $CfirstdayARY[year]";
$CCAL_OUT .= "</font></b></font></div>";
$CCAL_OUT .= "</td>";
$CCAL_OUT .= "</tr>";

foreach($Cdays as $Cday)
{
	$CDCLR="#ffffff";
$CCAL_OUT .= "<td bordercolor=\"$CDCLR\">";
$CCAL_OUT .= "<div align=center><font color=\"#000066\"><b><font face=\"Arial, Helvetica, sans-serif\" size=1>";
$CCAL_OUT .= "$Cday";
$CCAL_OUT .= "</font></b></font></div>";
$CCAL_OUT .= "</td>";
}

for( $Ccount=0;$Ccount<(6*7);$Ccount++)
{
	$Cdayarray = getdate($Cstart);
	if((($Ccount) % 7) == 0)
	{
		if($Cdayarray['mon'] != $CfirstdayARY['mon'])
			break;
		$CCAL_OUT .= "</tr><tr>";
	}
	if($Ccount < $CfirstdayARY['wday'] || $Cdayarray['mon'] != $Cmonth)
	{
		$CCAL_OUT .= "<td bordercolor=\"#ffffff\"><font color=\"#000066\"><b><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">&nbsp;</font></b></font></td>";
	}
	else
	{
		if( ($Cdayarray['mday'] == $CTODAYmday) and ($CPRNTDAY == $CTODAY) )
		{
		$CPRNTmday = $Cdayarray['mday'];
		if ($CPRNTmday < 10) {$CPRNTmday = "0$CPRNTmday";}
		$CBL = "<a href=\"#\" onclick=\"CB_date_pick('$CPRNTDAY-$CPRNTmday');return false;\">";
		$CEL = "</a>";

		$CCAL_OUT .= "<td bgcolor=\"#FFCCCC\" bordercolor=\"#FFCCCC\">";
		$CCAL_OUT .= "<div align=center><font face=\"Arial, Helvetica, sans-serif\" size=1>";
		$CCAL_OUT .= "$CBL$Cdayarray[mday]$CEL";
		$CCAL_OUT .= "</font></div>";
		$CCAL_OUT .= "</td>";
			$Cstart += ADAY;
		}
		else
		{
	$CDCLR="#ffffff";
	if ( ($Cdayarray['mday'] < $CTODAYmday) and ($CPRNTDAY == $CTODAY) )
		{
		$CDCLR="#CCCCCC";
		$CBL = '';
		$CEL = '';
		}
	else
		{
		$CPRNTmday = $Cdayarray['mday'];
		if ($CPRNTmday < 10) {$CPRNTmday = "0$CPRNTmday";}
		$CBL = "<a href=\"#\" onclick=\"CB_date_pick('$CPRNTDAY-$CPRNTmday');return false;\">";
		$CEL = "</a>";
		}

	$CCAL_OUT .= "<td bgcolor=\"$CDCLR\" bordercolor=\"#ffffff\">";
	$CCAL_OUT .= "<div align=center><font face=\"Arial, Helvetica, sans-serif\" size=1>";
	$CCAL_OUT .= "$CBL$Cdayarray[mday]$CEL";
	$CCAL_OUT .= "</font></div>";
	$CCAL_OUT .= "</td>";
		$Cstart += ADAY;
		}
	}
}
$CCAL_OUT .= "</tr>";
$CCAL_OUT .= "</table>";
$CCAL_OUT .= "</td>";

if ( ($CINC == 3) || ($CINC == 7) ||($CINC == 11) )
	{$CCAL_OUT .= "</tr>";}
$CINC++;
}

$CCAL_OUT .= "</table>";

#echo "$CCAL_OUT\n";
################################################################
### END - build the callback calendar (12 months)            ###
################################################################

require('functions.php');

$forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
$cnt = 0;
foreach ($forms as $form) {
	$fcamps = split(',',$form['campaigns']);
	foreach ($fcamps as $fcamp) {
		if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                	$fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
                	foreach ($fields as $field) {
				$jfields[$cnt] = $form['name'] . '_' . $field['name'];
				$ffields[$cnt] = 'AF' . $field['id'];
				$cnt++;
			}
		}
	}
}

    echo "<script language=\"Javascript\">\n";

    require('include/osdial.js');

    echo "</script>\n";

	
?>
	<style type="text/css">
	<!--
		div.scroll_callback {height: 300px; width: 620px; overflow: scroll;}
		div.scroll_list {height: 400px; width: 140px; overflow: scroll;}
		div.scroll_script {height: <?=$SSheight ?>px; width: <?=$SDwidth ?>px; background: #FFF5EC; overflow: scroll; font-size: 12px;  font-family: sans-serif;}
		div.text_input {overflow: auto; font-size: 10px;  font-family: sans-serif;}
	.body_text {font-size: 11px;  font-family: "dejavu sans",sans-serif;}
	.queue_text_red {font-size: 12px;  font-family: sans-serif; font-weight: bold; color: red}
	.queue_text {font-size: 12px;  font-family: sans-serif; color: black}
	.preview_text {font-size: 13px;  font-family: sans-serif; background: #D0E0E7}
	.preview_text_red {font-size: 13px;  font-family: sans-serif; background: #FFCCCC}
	.body_small {font-size: 11px;  font-family: sans-serif;}
	.body_tiny {font-size: 10px;  font-family: sans-serif;}
	.log_text {font-size: 11px;  font-family: monospace;}
	.log_text_red {font-size: 11px;  font-family: monospace; font-weight: bold; background: #FF3333}
	.log_title {font-size: 12px;  font-family: monospace; font-weight: bold;}
	.sd_text {font-size: 16px;  font-family: sans-serif; font-weight: bold;}
	.sh_text {font-size: 14px;  font-family: sans-serif; font-weight: bold;}
	.sb_text {font-size: 12px;  font-family: sans-serif;}
	.sk_text {font-size: 11px;  font-family: sans-serif;}
	.skb_text {font-size: 13px;  font-family: sans-serif; font-weight: bold;}
	.ON_conf {font-size: 11px;  font-family: monospace; color: black; background: #FFFF99}
	.OFF_conf {font-size: 11px;  font-family: monospace; color: black; background: #FFCC77}
	.cust_form {font-family: sans-serif; font-size: 10px; overflow: auto}
	-->
	</style>
	
</head>

<!-- ===================================================================================================================== -->

<BODY onload="begin_all_refresh();"  onunload="BrowserCloseLogout();" name=osdial>
<FORM name=osdial_form>

	<span style="position:absolute;left:0px;top:0px;z-index:2;" id="Header">
		
		<!-- Desktop --><!-- 1st line, login info -->
		<TABLE CELLPADDING=0 CELLSPACING=0 BGCOLOR=white WIDTH=<?=$MNwidth?> MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 VALIGN=TOP border=0> 
			<TR VALIGN=TOP ALIGN=LEFT>
				<TD COLSPAN=3 VALIGN=TOP ALIGN=center>
					<INPUT TYPE=HIDDEN NAME=extension>
					<font class="body_text">
	<? 
					echo "<font color=#1C4754>&nbsp;&nbsp;Logged in as user <b>$VD_login</b> on phone <b>$phone_login</b> to campaign <b>$VD_campaign</b>&nbsp;</font>\n"; ?>
				</TD>
				<TD COLSPAN=3 VALIGN=TOP ALIGN=RIGHT>
				</TD>
			</TR>
		</TABLE>
	</SPAN>
	
	<!-- 2nd line -->
	<span style="position:absolute;left:0px;top:13px;z-index:1;" id="Tabs">
		<table width=<?=$MNwidth-10 ?> height=30 border=0> 
			<TR VALIGN=TOP ALIGN=LEFT>
				<td colspan=2>
					<img id="FormButtons" onclick="ChooseForm();" src="images/vdc_tab_buttons1.gif" border="0" width="223" height="30"></td>
				<TD WIDTH=<?=$HSwidth ?> VALIGN=MIDDLE ALIGN=center>
					<font class="body_text" color=#1C4754><b><span id=status>LIVE</span></b>
				</td>
				<td valign='middle' width=300>
					<font  class="body_text" color=#FFFFFF>Session ID: <span id=sessionIDspan></span>
				</td>
				<td valign='middle' width=400> 
					&nbsp;<font class="body_text" color=#1C4754><span id=AgentStatusCalls></span>
				</td>
				<td valign='middle'>
					<? echo "&nbsp;<a href=\"#\" onclick=\"LogouT();return false;\"><font size=1 color='red'>LOGOUT</font></a>&nbsp;"; ?>
				</TD>
				<TD WIDTH=110><font class="body_text"><IMG SRC="./images/agc_live_call_OFF.gif" NAME=livecall ALT="Live Call" WIDTH=109 HEIGHT=30 BORDER=0></TD>
			</TR>
		</TABLE>
	</span>
	
	<!--  -->
	<span style="position:absolute;left:0px;top:0px;z-index:3;" id="WelcomeBoxA">
		<table border=0 bgcolor="#FFFFFF" width=<?=$CAwidth ?> height=<?=$HKwidth ?>>
			<TR>
				<TD align=center><BR><span id="WelcomeBoxAt"><font color=1C4754>OSDial</font></span></TD>
			</TR>
		</table>
	</span>
		
	<!-- Manual Dial Link -->
	<span style="position:absolute;left:300px;top:<?=$MBheight-20 ?>px;z-index:12;" id="ManuaLDiaLButtons"><font class="body_text">
		<span id="MDstatusSpan"><a href="#" onclick="NeWManuaLDiaLCalL('NO');return false;">MANUAL DIAL</a>
		</span> &nbsp; &nbsp; &nbsp; <a href="#" onclick="NeWManuaLDiaLCalL('FAST');return false;">FAST DIAL</a><BR></font>
	</span>
		
	<!-- Call Back Link -->
	<span style="position:absolute;left:500px;top:<?=$CBheight-3 ?>px;z-index:13;" id="CallbacksButtons"><font class="body_text">
		<span id="CBstatusSpan">X ACTIVE CALLBACKS</span> <BR>	</font>
	</span>
		
	<!-- Pause Code Link -->
	<span style="position:absolute;left:650px;top:<?=$CBheight-3 ?>px;z-index:14;" id="PauseCodeButtons"><font class="body_text">
		<span id="PauseCodeLinkSpan"></span> <BR></font>
	</span>
	
	<!-- Choose From Available Call Backs -->
	<span style="position:absolute;left:0px;top:18px;z-index:38;" id="CallBacKsLisTBox">
		<table border=1 bgcolor="#7DB2F2" width=<?=$CAwidth ?> height=460>
			<TR>
				<TD align=center VALIGN=top> CALLBACKS FOR AGENT <? echo $VD_login ?>:<BR>Click on a callback below to call the customer back now. If you click on a record below to call it, it will be removed from the list.
					<BR>
					<div class="scroll_callback" id="CallBacKsLisT"></div>
					<BR> &nbsp; 
					<a href="#" onclick="CalLBacKsLisTCheck();return false;">Refresh</a>
					&nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
					<a href="#" onclick="hideDiv('CallBacKsLisTBox');return false;">Go Back</a>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Manual Dial -->
	<span style="position:absolute;left:0px;top:18px;z-index:39;" id="NeWManuaLDiaLBox">
		<table border=1 bgcolor="#84ABB6" width=<?=$CAwidth-10 ?> height=545>
			<TR>
				<TD align=center VALIGN=top><br><font color=#1C4754><b>New Manual Dial Lead For <font color=#FFD000><? echo "$VD_login"?><font color=#1C4754> In Campaign <font color=#FFD000><? echo "$VD_campaign" ?></b><font color=#1C4754><BR><BR>Enter information below for the new lead you wish to call.
				<BR>
				<? 
				if (eregi("X",dial_prefix))
					{
					echo "Note: a dial prefix of $dial_prefix will be added to the beginning of this number<BR>\n";
					}
				?>
				Note: all new manual dial leads will go into list <? echo $manual_dial_list_id ?><BR><BR>
				<table>
					<tr>
						<td align=right><font class="body_text"> <font color=#1C4754>Country Code: </td>
						<td align=left><font class="body_text"><input type=text size=7 maxlength=10 name=MDDiaLCodE class="cust_form" value="1">&nbsp; <font color=#1C4754>(This is usually a 1 in the USA-Canada)</td>
					</tr>
					<tr>
						<td align=right><font class="body_text"> <font color=#1C4754>Phone Number: </td>
						<td align=left><font class="body_text">
							<input type=text size=14 maxlength=12 name=MDPhonENumbeR class="cust_form" value="">&nbsp; <font color=#1C4754>(12 digits max - digits only)
						</td>
					</tr>
					<tr>
						<td align=right><font class="body_text"> <font color=#1C4754>Search Existing Leads: </td>
						<td align=left><font class="body_text"><input type=checkbox name=LeadLookuP size=1 value="0">&nbsp; <font color=#1C4754>(If checked will attempt to Find the phone number in the system before inserting it as a New Lead)</td>
					</tr>
					<tr>
						<td colspan=2><br><!--hr width=50%></td>
					</tr>
					<tr>
						<td align=left colspan=2><BR><font color=#1C4754>If you want to dial a number and have it NOT be added as a new lead, enter in the exact dialstring that you want to call in the Dial Override field below. To hangup this call you will have to open the CALLS IN THIS SESSION link at the bottom of the screen and hang it up by clicking on its channel link there.<BR> &nbsp; </td>
					</tr>
					<tr>
						<td align=right><font class="body_text"> <font color=#1C4754>Dial Override: </td -->
							<!--td align=left><font class="body_text" --><center><font color=1C4754 size=-6>(Future use)</font><input disabled type=text size=1 maxlength=1 name=MDDiaLOverridE class="cust_form" value="">&nbsp; <font color=#84ABB6>(digits only please)</font></center>
						</td>
					</tr>
				</table>
				
				<BR>
				<a href="#" onclick="NeWManuaLDiaLCalLSubmiT();return false;"><b>Dial Now</a>
				&nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
				<a href="#" onclick="hideDiv('NeWManuaLDiaLBox');return false;">Go Back</b></a>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	
	<!-- Disposition Hot Keys Window -->
	<span style="position:absolute;left:92px;top:<?=$HTheight+45 ?>px;z-index:24;" id="HotKeyEntriesBox">
		<table frame=box bgcolor="#B0DFA4" width=610 Oldwidth<?=$HCwidth ?> height=70>
			<TR bgcolor="#9AC38F">
				<TD width=200><font class="sh_text"> Disposition Hot Keys: </font></td>
				<td colspan=2 width=410>
					<font class="body_small">When active, simply press the keyboard key for the desired disposition for this call. The call will then be hungup and dispositioned automatically:</font>
				</td>
			</tr>
			<tr>
				<TD width=200>
					<font class="sk_text">
					<span id="HotKeyBoxA"><? echo $HKboxA ?></span>
					</font>
				</TD>
				<TD width=200><font class="sk_text">
					<span id="HotKeyBoxB"><? echo $HKboxB ?></span>
					</font></TD>
				<TD><font class="sk_text">
					<span id="HotKeyBoxC"><? echo $HKboxC ?></span>
					</font>
				</TD>
			</TR>
		</TABLE>
	</span>
	

<? 	//-- Hot Key Button --
	if ( ($HK_statuses_camp > 0) && ( ($user_level>=$HKuser_level) or ($VU_hotkeys_active > 0) ) ) { ?>
		<span style="position:absolute;left:<?=$HKwidth+40 ?>px;top:<?=$HKheight +50 ?>px;z-index:16;" id="hotkeysdisplay"><a href="#" onMouseOver="HotKeys('ON')"><IMG SRC="./images/vdc_XB_hotkeysactive_OFF.gif" border=0 alt="HOT KEYS INACTIVE"></a>
		</span>
<? 	
	} 
?>
		<!-- D1, D2, Mute Links -->
	<span style="position:absolute;left:<?=$AMwidth-10 ?>px;top:<?=$AMheight+15 ?>px;z-index:22;" id="AgentMuteANDPreseTDiaL">
		<font class="body_text">
<?
		if ($PreseT_DiaL_LinKs) {
			echo "<a href=\"#\" onclick=\"DtMf_PreSet_a_DiaL();return false;\"><font class=\"body_tiny\">D1 - DIAL</font></a>\n";
			echo "<BR>\n";
			echo "<a href=\"#\" onclick=\"DtMf_PreSet_b_DiaL();return false;\"><font class=\"body_tiny\">D2 - DIAL</font></a>\n";
		} else {
			echo "<BR>\n";
		}
?>
		<BR><BR>
		<span id="AgentMuteSpan"></span>
		</font>
	</span>
	<font style="position:relative;left:480px;top:484px;z-index:22;" id="MutedWarning"></font>
	
	<!-- Volume Control Links -->
	<span style="position:absolute;left:935px;top:<?=$CBheight+26 ?>px;z-index:19;" id="VolumeControlSpan">
		<span id="VolumeUpSpan"><IMG SRC="./images/vdc_volume_up_off.gif" BORDER=0></span>
		<br>
		<span id="VolumeDownSpan"><IMG SRC="./images/vdc_volume_down_off.gif" BORDER=0></span>
	</span>
	
	<!-- Agent Status In Progress -->
	<span style="position:absolute;left:35px;top:<?=$CBheight ?>px;z-index:20;" id="AgentStatusSpan"><font class="body_text">
		Your Status: 
		<span id="AgentStatusStatus"></span> 
		<BR>Calls Dialing: <span id="AgentStatusDiaLs"></span> 
		</font>
	</span>
	
	<!-- Transfer Link -->
	<span style="position:absolute;left:5px;top:<?=$HTheight ?>px;z-index:21;" id="TransferMain">
		<table bgcolor="AFCFD7" frame=box width=<?=$XFwidth-255 ?>>
			<tr>
				<td align=left>
					<div class="text_input" id="TransferMaindiv">
						<font class="body_text">
							<table width=100%>
								<tr>
									<td align=center colspan=5>
										<font color=1C4754><b>Transfer & Conference Functions</b><BR>
									</td>
								</tr>
								<tr>
									<td>
										<span id="XfeRGrouPLisT"><select size=1 name=XfeRGrouP class="cust_form"><option>-- SELECT A GROUP TO SEND YOUR CALL TO --</option></select>
										</span>
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="LocalCloser"><IMG SRC="./images/vdc_XB_localcloser_OFF.gif" border=0 alt="LOCAL CLOSER">
										</span> 
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="HangupXferLine"><IMG SRC="./images/vdc_XB_hangupxferline_OFF.gif" border=0 alt="Hangup Xfer Line">
										</span>
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="HangupBothLines"><a href="#" onclick="bothcall_send_hangup();return false;"><IMG SRC="./images/vdc_XB_hangupbothlines.gif" border=0 alt="Hangup Both Lines"></a>
										</span>
									</td>
									<td align=center>
										<a href="#" onclick="DtMf_PreSet_a();return false;"><font class="body_tiny">D1</font></a>
									</td>
								</tr>
								<tr>
									<td>
										<font size=1 color=1C4754>Number to call:&nbsp;<input type=text size=15 name=xfernumber maxlength=25 class="cust_form">
										<input type=hidden name=xferuniqueid>
									</td>
									<td align=center>
										<input type=checkbox name=xferoverride size=1 value="0"><font size=1 color=1C4754>Dial Override</font>
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="Leave3WayCall"><IMG SRC="./images/vdc_XB_leave3waycall_OFF.gif" border=0 alt="LEAVE 3-WAY CALL">
										</span> 
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="DialBlindTransfer"><IMG SRC="./images/vdc_XB_blindtransfer_OFF.gif" border=0 alt="Dial Blind Transfer">
										</span>
									</td>
									<td align=center>
										<a href="#" onclick="DtMf_PreSet_b();return false;"><font class="body_tiny">D2</font></a>
									</td>
								</tr>
								<tr>
									<td>
										<font size=1 color=1C4754>Seconds:<!-- IMG SRC="./images/vdc_XB_seconds.gif" border=0 alt="seconds" --> <input type=text size=2 name=xferlength maxlength=4 class="cust_form">
									</td>
									<td>
										<font size=1 color=1C4754>Channel:&nbsp;<input type=text size=12 name=xferchannel maxlength=100 class="cust_form"> 
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="DialWithCustomer"><a href="#" onclick="SendManualDial('YES');return false;"><IMG SRC="./images/vdc_XB_dialwithcustomer.gif" border=0 alt="Dial With Customer"></a>
										</span> 
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="ParkCustomerDial"><a href="#" onclick="xfer_park_dial();return false;"><IMG SRC="./images/vdc_XB_parkcustomerdial.gif" border=0 alt="Park Customer Dial"></a>
										</span> 
									</td>
									<td align=center>
										<span STYLE="background-color: #CCCCCC" id="DialBlindVMail"><IMG SRC="./images/vdc_XB_ammessage_OFF.gif" border=0 alt="Blind Transfer VMail Message">
										</span>
									</td>
								</tr>
							</table>
						</font>
					</div>
				</td>
			</tr>
		</table>
	</span>
	
	<!-- Dispositioned -->
	<span style="position:absolute;left:5px;top:<?=$HTheight+20 ?>px;z-index:23;" id="HotKeyActionBox">
		<table border=0 bgcolor="#FFDD99" width=<?=$HCwidth ?> height=70>
			<TR bgcolor="#FFEEBB">
				<TD height=70><font class="sh_text"> Lead Dispositioned As: </font><BR><BR>
					<CENTER><font class="sd_text"><span id="HotKeyDispo"> - </span></font></CENTER>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Previous Callback Info -->
	<span style="position:absolute;left:10px;top:<?=$HTheight+20 ?>px;z-index:25;" id="CBcommentsBox">
		<table frame=box bgcolor="#7297A1" width=<?=$HCwidth ?> height=70>
			<TR bgcolor="#9ABCC5">
				<TD align=center><font class="sh_text" color=yellow>&nbsp;Previous Callback Information</font></td>
				<TD align=right><font class="sk_text"> <a href="#" onclick="CBcommentsBoxhide();return false;"><b>CLOSE</b></a>&nbsp;&nbsp;</font></td>
			</tr>
			<tr>
				<TD><font class="sk_text">
					<span id="CBcommentsBoxA"></span><BR>
					<span id="CBcommentsBoxB"></span><BR>
					<span id="CBcommentsBoxC"></span><BR>
					</font>
				</TD>
				<TD width=320><font class="sk_text">
					<span id="CBcommentsBoxD"></span>
					</font>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Phone Is Hungup -->
	<span style="position:absolute;left:0px;top:18px;z-index:26;" id="NoneInSessionBox">
		<table border=1 bgcolor="#AFCFD7" width=<?=$CAwidth ?> height=545>
			<TR>
				<TD align=center><b><font color=#1C4754>Your phone has either not been answered, or was hung up! <br><br><font color=#AFCFD7>(Session ID: <span id="NoneInSessionID"></span>)</font></b><BR><br>
					<a href="#" onclick="NoneInSessionCalL();return false;" style='text-decoration: blink;color:#1C4754;'><u><b>Try Calling Your Phone Here</b></u></a>
					<BR><BR><br>
					<a href="#" onclick="NoneInSessionOK();return false;" style='color:#C0D0E8;'>Go Back</a>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Customer Hungup -->
	<span style="position:absolute;left:0px;top:0px;z-index:27;" id="CustomerGoneBox">
		<table border=1 bgcolor="#CCFFFF" width=<?=$CAwidth ?> height=500>
			<TR>
				<TD align=center> Customer has hung up: <span id="CustomerGoneChanneL"></span><BR>
					<a href="#" onclick="CustomerGoneOK();return false;">Go Back</a>
					<BR><BR>
					<a href="#" onclick="CustomerGoneHangup();return false;">Finish and Disposition Call</a>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Call Wrapup -->
	<span style="position:absolute;left:0px;top:0px;z-index:28;" id="WrapupBox">
		<table border=1 bgcolor="7297A1" width=<?=$CAwidth ?> height=550>
			<TR>
				<TD align=center> Call Wrapup: <span id="WrapupTimer"></span> seconds remaining in wrapup<BR><BR>
					<span id="WrapupMessage"><?=$wrapup_message ?></span>
					<BR><BR>
					<a href="#" onclick="WrapupFinish();return false;">Finish Wrapup and Move On</a>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Session Disabled -->
	<span style="position:absolute;left:0px;top:0px;z-index:29;" id="AgenTDisablEBoX">
		<table class=acrossagent border=0 width=<?=$CAwidth ?> height=564>
			<TR>
				<TD align=center><font color=#1C4754>Your login session has been disabled, you need to logout.<br><BR><a href="#" onclick="LogouT();return false;"><font text-decoration=blink>LOGOUT</font></a><BR><BR><a href="#" onclick="hideDiv('AgenTDisablEBoX');return false;"><font color=grey>Go Back</font></a>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Logout Link -->
	<span style="position:absolute;left:1px;top:1px;z-index:30;background-image: URL('images/loginagain-bg.png');background-repeat:none;" id="LogouTBox">
		<table width=1001 Oldwidth<?=$CAwidth+20 ?> height=608 cellpadding=0 cellspacing=0>
			<TR>
				<TD align=center>
					<BR><span id="LogouTBoxLink">LOGOUT</span>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Hide Disposition Button A -->
	<span style="position:absolute;left:0px;top:70px;z-index:31;" id="DispoButtonHideA">
		<table border=0 bgcolor="7297A1" width=165 height=22>
			<TR>
				<TD align=center VALIGN=top>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Hide Disposition Button B -->
	<span style="position:absolute;left:0px;top:138px;z-index:32;" id="DispoButtonHideB">
		<table border=0 bgcolor="7297A1" width=165 height=250>
			<TR>
				<TD align=center VALIGN=top>&nbsp;</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Hide Disposition Button C -->
	<span style="position:absolute;left:0px;top:18px;z-index:33;" id="DispoButtonHideC">
		<table border=0 bgcolor="7297A1" width=<?=$CAwidth ?> height=47>
			<TR>
				<TD align=center VALIGN=top>Any changes made to the customer information below at this time will not be comitted, You must change customer information before you Hangup the call.</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Disposition Window -->
	<span style="position:absolute;left:0px;top:18px;z-index:34;" id="DispoSelectBox">
		<table class=acrossagent width=<?=$CAwidth+5 ?> height=460>
			<TR>
				<TD align=center VALIGN=top><font color=#1C4754><br> DISPOSITION CALL: <span id="DispoSelectPhonE"></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectHAspan"><a href="#" onclick="DispoHanguPAgaiN()">Hangup Again</a></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectMaxMin"><a href="#" onclick="DispoMinimize()">minimize</a></span><BR>
					<span id="DispoSelectContent"> End-of-call Disposition Selection </span>
					<input type=hidden name=DispoSelection><BR>
					<input type=checkbox name=DispoSelectStop size=1 value="0"> <font color=#1C4754>PAUSE AGENT DIALING <BR>
					<a href="#" onclick="DispoSelectContent_create('','ReSET');return false;">CLEAR FORM</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
					<a href="#" onclick="DispoSelect_submit();return false;"><b>SUBMIT</b></a>
					<BR><BR>
					<a href="#" onclick="WeBForMDispoSelect_submit();return false;">WEB FORM SUBMIT</a>
					<BR><BR> &nbsp; 
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Pause Code Window -->
	<span style="position:absolute;left:0px;top:18px;z-index:40;" id="PauseCodeSelectBox">
		<table class=acrossagent frame=box width=<?=$CAwidth -10 ?> height=500>
			<TR>
				<TD align=center VALIGN=top><br><font color=#1C4754><b>Select A Pause Code</b><br><br>
					<span id="PauseCodeSelectContent"> Pause Code Selection </span>
					<input type=hidden name=PauseCodeSelection>
					<BR><BR> &nbsp; 
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Callback Window -->
	<span style="position:absolute;left:0px;top:18px;z-index:35;" id="CallBackSelectBox">
		<table border=1 bgcolor="7297A1" width=<?=$CAwidth ?> height=480>
			<TR>
				<TD align=center VALIGN=top><font color=#1C4754>Select a CallBack Date :<span id="CallBackDatE"></span><BR>
					<input type=hidden name=CallBackDatESelectioN ID="CallBackDatESelectioN">
					<input type=hidden name=CallBackTimESelectioN ID="CallBackTimESelectioN">
					<span id="CallBackDatEPrinT">Select a Date Below</span> &nbsp;
					<span id="CallBackTimEPrinT"></span> &nbsp; &nbsp;
					Hour: 
					<SELECT SIZE=1 NAME="CBT_hour" ID="CBT_hour">
					<option>01</option>
					<option>02</option>
					<option>03</option>
					<option>04</option>
					<option>05</option>
					<option>06</option>
					<option>07</option>
					<option>08</option>
					<option>09</option>
					<option>10</option>
					<option>11</option>
					<option>12</option>
					</select> &nbsp;
					Minutes: 
					<SELECT SIZE=1 NAME="CBT_minute" ID="CBT_minute">
					<option>00</option>
					<option>05</option>
					<option>10</option>
					<option>15</option>
					<option>20</option>
					<option>25</option>
					<option>30</option>
					<option>35</option>
					<option>40</option>
					<option>45</option>
					<option>50</option>
					<option>55</option>
					</select> &nbsp;
				
					<SELECT SIZE=1 NAME="CBT_ampm" ID="CBT_ampm">
					<option>AM</option>
					<option selected>PM</option>
					</select> &nbsp;<BR>
					<?
					if ($agentonly_callbacks)
						{echo "<input type=checkbox name=CallBackOnlyMe id=CallBackOnlyMe size=1 value=\"0\"> MY CALLBACK ONLY <BR>";}
					?>
					CB Comments: <input type=text name="CallBackCommenTsField" id="CallBackCommenTsField" size=50><BR><BR>
				
					<a href="#" onclick="CallBackDatE_submit();return false;">SUBMIT</a><BR><BR>
					<span id="CallBackDateContent"><?echo"$CCAL_OUT"?></span>
					<BR><BR> &nbsp; 
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Closer Inbound Group Window -->
	<span style="position:absolute;left:0px;top:0px;z-index:36;" id="CloserSelectBox">
			<table class=acrossagent border=0 width=<?=$CAwidth ?> height=565>
			<TR>
				<TD align=center VALIGN=top><br><font size=+1 color=#1C4754><b>Closer Inbound Group Selection</b></font><BR><br>
					<span id="CloserSelectContent"> Closer Inbound Group Selection </span>
					<input type=hidden name=CloserSelectList><BR>
					<input type=checkbox name=CloserSelectBlended size=1 value="0">&nbsp;<font color=#1C4754>BLENDED CALLING (outbound activated)</font><BR><br>
					<a href="#" onclick="CloserSelectContent_create();return false;">RESET</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#" onclick="CloserSelect_submit();return false;"><b>SUBMIT</b></a>
					<BR><BR><BR><BR> &nbsp; 
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Preview hide -->
	<span style="position:absolute;left:0px;top:0px;z-index:37;" id="NothingBox">
		<BUTTON Type=button name="inert_button"><img src="./images/blank.gif"></BUTTON>
		<span id="DiaLLeaDPrevieWHide">Channel</span>
		<span id="DiaLDiaLAltPhonEHide">Channel</span>
<?
		if (!$agentonly_callbacks) {
			echo "<input type=checkbox name=CallBackOnlyMe size=1 value=\"0\"> MY CALLBACK ONLY <BR>";
		}
?>
	</span>
	
	<!-- Script window -->
	<span style="position:absolute;left:190px;top:92px;z-index:17;" id="ScriptPanel">
		<table border=0 bgcolor="7297A1" width=<?=$SSwidth -40 ?> height=<?=$SSheight -30 ?>>
			<TR>
				<TD align=left valign=top><font class="sb_text"><div class="scroll_script" id="ScriptContents">OSDial Script Will Show Here</div></font>
				</TD>
			</TR>
		</TABLE>
	</span>
	
	<!-- Footer Links -->
	<!--span style="position:absolute;left:0px;top:<?=$HKheight ?>px;z-index:15;" id="MaiNfooterspan" -->
	<span style="position:absolute;left:2px;top: 480px;z-index:15;" id="MaiNfooterspan">
		<table id="MaiNfooter" width=<?=$MNwidth+10 ?>>
			<tr height=15>
				<td height=15><font face="Arial,Helvetica" size=1>OSDial Agent version: <? echo $version ?>&nbsp;&nbsp;Build: <? echo $build ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;Server: <? echo $server_ip ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font><BR>
					<font class="body_small"><span id="busycallsdisplay"><a href="#"  onclick="conf_channels_detail('SHOW');">Show conference call channel information</a><BR><BR>&nbsp;</span></font>
				</td>
				<td align=right height=0>
				</td>
			</tr>
			<tr>
				<td colspan=3><span id="outboundcallsspan"></span></td></tr>
			<tr>
				<td colspan=3>
					<font class="body_small">
					<span id="debugbottomspan"></span>
					</font>
				</td>
			</tr>
		</table>
	</span>
	
	
	<!-- =============================   Here is the main OSDIAL display panel  ============================= -->
	
	<span style="position:absolute;left:2px;top:46px;z-index:4;" id="MainPanel">
		<!-- Column widths 205 + 505 + 270 = 980 -->
		<TABLE id="MainTable" class=acrossagent cellpadding=0 cellspacing=0 border=0>
			<TR>
				<td width=22 colspan=2><img src=images/AgentTopLeft.png width=22 height=22 align=left>
					<font class="body_text" color=#1C4754>&nbsp;&nbsp;STATUS:&nbsp;&nbsp;
					<span id="MainStatuSSpan"></span></font>
				</td>
				<td width=22><img src=images/AgentTopRight.png width=22 height=22 align=right></td>
				
			</TR>
			<tr>
				<td colspan=3><span id="busycallsdebug"></span></td>
			</tr>
			<tr>		<!--  Column one, controls -->
				<td width=205 height=330 align=left valign=top>
					<font class="body_text">
						<center>
							<span STYLE="" id="DiaLControl"><a href="#" onclick="ManualDialNext('','','','','');"><IMG SRC="./images/vdc_LB_dialnextnumber_OFF.gif" border=0 alt="Dial Next Number"></a>
							</span><BR>
							<span id="DiaLLeaDPrevieW"><font class="preview_text"> <input type=checkbox name=LeadPreview size=1 value="0"> LEAD PREVIEW<BR></font>
							</span>
							<span id="DiaLDiaLAltPhonE"><font class="preview_text"> <input type=checkbox name=DiaLAltPhonE size=1 value="0"> ALT PHONE DIAL<BR></font>
							</span>
				
							<!--
	<?
							if ( ($manual_dial_preview) and ($auto_dial_level==0) ) {
								echo "<font class=\"preview_text\"> <input type=checkbox name=LeadPreview size=1 value=\"0\"> LEAD PREVIEW<BR></font>";
							}
							if ( ($alt_phone_dialing) and ($auto_dial_level==0) ) {
								echo "<font class=\"preview_text\"> <input type=checkbox name=DiaLAltPhonE size=1 value=\"0\"> ALT PHONE DIAL<BR></font>";
							}
	?>
							-->
							
							<font color=#1C4754>Recording File</font><BR>
							<font class="body_tiny"><span id="RecorDingFilename"></span>&nbsp;</font>
						</center>
						<BR>
						<center><font color=#1C4754>Recording ID:&nbsp;</font><font class="body_small"><span id="RecorDID"></span></font></center>
						<center>
							<!-- <a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + head_conf + "','');return false;\">Record</a> -->
							<span STYLE="Xbackground-color: #CCCCCC" id="RecorDControl"><a href="#" onclick="conf_send_recording('MonitorConf','<?=$session_id ?>','');return false;"><IMG SRC="./images/vdc_LB_startrecording.gif" border=0 alt="Start Recording"></a></span><BR>
							
							<span id="SpacerSpanA"><IMG SRC="./images/blank.gif" width=145 height=16 border=0></span><BR>
							
							<span STYLE="Xbackground-color: #FFFFFF" id="WebFormSpan"><IMG SRC="./images/vdc_LB_webform_OFF.gif" border=0 alt="Web Form"></span>
							<span id="SpacerSpanB"><IMG SRC="./images/blank.gif" width=145 height=2 border=0></span>
							<span STYLE="Xbackground-color: #FFFFFF" id="WebFormSpan2"><IMG SRC="./images/vdc_LB_webform2_OFF.gif" border=0 alt="Web Form"></span><BR>
							
							<span id="SpacerSpanB1"><IMG SRC="./images/blank.gif" width=145 height=16 border=0></span>
							
							<span STYLE="Xbackground-color: #CCCCCC" id="ParkControl"><IMG SRC="./images/vdc_LB_parkcall_OFF.gif" border=0 alt="Park Call"></span>
							<span id="SpacerSpanB"><IMG SRC="./images/blank.gif" width=145 height=2 border=0></span>
							<span STYLE="Xbackground-color: #CCCCCC" id="XferControl"><IMG SRC="./images/vdc_LB_transferconf_OFF.gif" border=0 alt="Transfer - Conference"></span>
							
							<span id="SpacerSpanC"><IMG SRC="./images/blank.gif" width=145 height=16 border=0></span>
							
							<span STYLE="Xbackground-color: #FFCCFF" id="HangupControl"><IMG SRC="./images/vdc_LB_hangupcustomer_OFF.gif" border=0 alt="Hangup Customer"></span>
							
							<span id="SpacerSpanD"><IMG SRC="./images/blank.gif" width=145 height=16 border=0></span>
							
							<div class="text_input" id="SendDTMFdiv">
								<span STYLE="Xbackground-color: #CCCCCC" id="SendDTMF"><a href="#" onclick="SendConfDTMF('<?=$session_id ?>');return false;"><IMG SRC="./images/vdc_LB_senddtmf.gif" border=0 alt="Send DTMF" align=top></a> <input type=text size=6 name=conf_dtmf class="cust_form" value="" maxlength=50>
								</span> <!-- Span and Div were reversed  -->
							</div>
							
							<!-- div id=ShowCallbackInfo>
								<IMG SRC="./images/blank.gif" width=145 height=16 border=0>
								<img src=images/ShowCallbackInfo_OFF.gif alt='Show Callback Info'>
								<IMG SRC="./images/blank.gif" width=145 height=16 border=0>
							</div -->
							
							<BR>
							<span STYLE="Xbackground-color: #FFCCFF" id="RepullControl"></span><BR>
							
							<span id="SpacerSpanE"><IMG SRC="./images/blank.gif" width=145 height=16 border=0></span><BR>
						</center>
					</font>
					
				</td> <!-- ==== Column two, customer info==== -->
				<td width=505 Oldwidth<?=$SDwidth ?> align=left valign=top>
					<input type=hidden name=lead_id value="">
					<input type=hidden name=list_id value="">
					<input type=hidden name=called_count value="">
					<input type=hidden name=gmt_offset_now value="">
					<input type=hidden name=gender value="">
					<input type=hidden name=date_of_birth value="">
					<input type=hidden name=country_code value="">
					<input type=hidden name=uniqueid value="">
					<input type=hidden name=callserverip value="">
				
					<!-- Customer Information -->
					<div class="text_input" id="MainPanelCustInfo">
						<table cellpadding='' cellspacing='2' border=0>
						<tr>
							<!-- Replaced by the next block 
							<td align=right colspan=3><font class="body_text"><font color=#1C4754>Seconds:</font> <div id="callchannel">
							<font class="body_text"><input type=text size=3 name=SecondS class="cust_form" value="">&nbsp;&nbsp;&nbsp;<font color=#1C4754>Channel: <input type=text size=9 name=callchannel class="cust_form" value="" >&nbsp;&nbsp;&nbsp;<font color=#1C4754>Cust Time:</font> <input type=text size=20 name=custdatetime class="cust_form" value="">
							</td> 
							-->
						
							<td align=center colspan=3>
								<table width=100% align=center>
								<tr>
									<!-- td width=1>&nbsp;</td -->
									<td width=30% align=left valign=top><font class="body_text" color=#1C4754>CallDuration:&nbsp;<input type=text size=4 name=SecondS class="cust_form" value="">s</td>
									<!--td width=20%><font class="body_text" color=#AACBD4>Channel:&nbsp;<a id=callchannel class="body_text"></a></td -->
									<td width=25% align=right valign=top><font class="body_text" color=#1C4754>Channel:&nbsp;<a id=callchannel class="body_text"></a></td>
									<td width=45% align=right valign=top><font class="body_text" color=#1C4754>Cust Time:&nbsp;</a><input type=text size=18 name=custdatetime class="cust_form" value=""></td>
								</tr>
								</table>
							</td>
						
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan=2 align=center valign=top><font color=#1C4754><b>Customer Information</b></font><span id="CusTInfOSpaN"></span></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Title:&nbsp;</font></td>
							<td align=left colspan=2><font class="body_text"><input type=text size=4 name=title maxlength=4 class="cust_form" value="">&nbsp; <font color=#1C4754>First:</font> <input type=text size=17 name=first_name maxlength=30 class="cust_form" value="">&nbsp; <font color=#1C4754>MI:</font> <input type=text size=1 name=middle_initial maxlength=1 class="cust_form" value="">&nbsp; <font color=#1C4754>Last:</font> <input type=text size=18 name=last_name maxlength=30 class="cust_form" value=""></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Address1:&nbsp;</font></td>
							<td align=left colspan=2><font class="body_text"><input type=text size=67 name=address1 maxlength=100 class="cust_form" value=""></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Address2:&nbsp;</font></td>
							<td align=left><font class="body_text"><input type=text size=26 name=address2 maxlength=100 class="cust_form" value=""></td><td align=right><font class="body_text"><font color=#1C4754>Address3:&nbsp;</font><input type=text size=26 name=address3 maxlength=100 class="cust_form" value=""></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>City:&nbsp;</font></td>
							<td align=left><font class="body_text"><input type=text size=26 name=city maxlength=50 class="cust_form" value="">&nbsp;</td><td align=right> <font class="body_text"><font color=#1C4754>State:&nbsp;</font><input type=text size=2 name=state maxlength=2 class="cust_form" value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=#1C4754>Zip:&nbsp;</font><input type=text size=9 name=postal_code maxlength=10 class="cust_form" value=""></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Province:&nbsp;</font></td>
							<td align=left><font class="body_text"><input type=text size=26 name=province maxlength=50 class="cust_form" value="">&nbsp;</td><td align=right> <font class="body_text"><font color=#1C4754>Email:&nbsp;</font><input type=text size=26 name=email maxlength=70 class="cust_form" value=""></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Phone:&nbsp;</font></td>
							<td colspan=2><table width=100%><tr><td align=left colspan=2><font class="body_text"><input type=text size=11 name=phone_number maxlength=12 class="cust_form" value=""></td><td align=right><font class="body_text"><font color=#1C4754>CountryCode:&nbsp;</font><input type=text size=4 name=phone_code maxlength=10 class="cust_form" value=""></td><td align=right><font class="body_text"><font color=#1C4754>Alt. Phone:&nbsp;</font><input type=text size=12 name=alt_phone maxlength=12 class="cust_form" value=""></td></td></tr></table>
						</tr>
						<tr>
							<td align=right valign=top><font class="body_text" color=#1C4754>Comments:&nbsp;</font></td>
							<td align=left colspan=2>
								<font class="body_text">
	<?
								if ( ($multi_line_comments) )
									{echo "<TEXTAREA NAME=comments ROWS=2 COLS=65 class=\"cust_form\" value=\"\"></TEXTAREA>\n";}
								else
									{echo "<input type=text size=67 name=comments maxlength=255 class=\"cust_form\" value=\"\">\n";}
	?>
								</font>
							</td>
							
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Custom1:&nbsp;</font></td>
							<td align=left><font class="body_text"><input type=text size=26 name=security_phrase maxlength=100 class="cust_form" value="">&nbsp;</td>
							<td align=right> <font class="body_text"><font color=#1C4754>Custom2:&nbsp;</font><input type=text size=26 name=vendor_lead_code maxlength=20 class="cust_form" value=""></td>
						</tr>
						<!-- tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Custom3:&nbsp;</font></td>
							<td align=left><font class="body_text"><input type=text size=26 name=security_phrase maxlength=100 class="cust_form" value="">&nbsp;</td>
							<td align=right> <font class="body_text"><font color=#1C4754>Custom4:&nbsp;</font><input type=text size=26 name=vendor_lead_code maxlength=20 class="cust_form" value=""></td>
						</tr>
						<tr>
							<td align=right><font class="body_text"> <font color=#1C4754>Custom5:&nbsp;</font></td>
							<td align=left><font class="body_text"><input type=text size=26 name=security_phrase maxlength=100 class="cust_form" value="">&nbsp;</td>
							<td align=right> <font class="body_text"><font color=#1C4754>Custom6:&nbsp;</font><input type=text size=26 name=vendor_lead_code maxlength=20 class="cust_form" value=""></td>
						</tr -->
						</table>
			
					</div>
					</font> <!-- ? -->
					
				</td> <!-- ==== Column three, additional data ==== -->
				<td width=270 align=center valign=top><div class="AFHead">Additional Information</div>
	
					<?
					$cnt = 0;
					foreach ($forms as $form) {
						$fcamps = split(',',$form['campaigns']);
						foreach ($fcamps as $fcamp) {
							if ($fcamp == 'ALL' or $fcamp == $VD_campagin) {
								if ($cnt > 0) {
									$cssvis = 'visibility:hidden;';
								}
								echo "  <div id=\"AddtlForms" . $form['name'] . "\" style=" . $cssvis . "position:absolute;left:710px;top:42px;z-index:6;>\n";
								echo "  <table width=265><tr><td><table align=center>\n";
								echo "      <tr>\n";
								echo "          <td colspan=2 align=center>\n";
								echo "              <font color=#1C4754 class=body_text>" . $form['description'] . "<br />" . $form['description2'] . "</font>\n";
								echo "          </td>\n";
								echo "      </tr>\n";
								$fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
								foreach ($fields as $field) {
									echo "      <tr>\n";
									echo "          <td align=right><font color=#1C4754 class=body_text>" . $field['description'] . ":&nbsp;</font></td>\n";
									echo "          <td>\n";
									if ($field['options'] == '') {
									echo "          <input type=text size=" . $field['length'] . " maxlength=255 name=AF" . $field['id'] . " id=AF" . $field['id'] . " class=cust_form value=\"\">\n";
									} else {
									echo "          <select name=AF" . $field['id'] . " id=AF" . $field['id'] . ">\n";
									$options = split(',',$field['options']);
									foreach ($options as $opt) {
										echo "              <option>" . $opt . "</option>\n";
									}
									echo "          </select>\n";
									}
									echo "          </td>\n";
									echo "      </tr>\n";
								}
								echo "  </table></td></tr></table>\n";
								echo "  </div>\n";
								$cnt++;
							}
						}
					}
					?>
	
				</td>
			</tr>
			<tr>
				<td align=left colspan=3 height=<?=$BPheight ?>>&nbsp;</td>
			</tr>
			<tr>
				<td align=left colspan=3>&nbsp;</td>
			</tr>
			<tr>
				<td align=left colspan=3>&nbsp;</td>
			</tr>
			<tr>
				<td align=left colspan=3>&nbsp;</td>
			</tr>
		</table> <!-- Moved up from just above </body>   -->
	</td>
	<td align=left valign=top>	<!-- The side form selection tab -->
		<br>
		<div id="AddtlFormTab" style="position:absolute;left:980px;top:22px;z-index:9;" onclick="AddtlFormOver();">
			<img src="images/agentsidetab_tab.png" width="10" height="46" border="0">
		</div>
		<div id="AddtlFormTabExpanded" style="visibility:hidden;position:absolute;left:840px;top:22px;z-index:9;" >
			<table width=140 cellspacing=0 cellpadding=0 >
      			<tr background=images/agentsidetab_top.png height=15 onclick="AddtlFormSelect('Cancel');">
      				<td></td>
      			</tr>

<?
					foreach ($forms as $form) {
						$fcamps = split(',',$form['campaigns']);
						foreach ($fcamps as $fcamp) {
							if ($fcamp == 'ALL' or $fcamp == $VD_campagin) {
								echo "  <tr id=AddtlFormBut" . $form['name'] . " style=\"background-image:url(images/agentsidetab_extra.png);\" height=29 ";
								echo "    onmouseover=\"AddtlFormButOver('" . $form['name'] . "');\" onmouseout=\"AddtlFormButOut('" . $form['name'] . "');\">\n";
								echo "      <td align=center onclick=\"AddtlFormSelect('" . $form['name'] . "');\">\n";
								echo "          <div class=AFMenu>" . $form['name'] . "</div>\n";
								echo "      </td>\n";
								echo "  </tr>\n";
							}
						}
					}
?>

				<tr id="AddtlFormButSelect4" background=images/agentsidetab_line.png height=1><td></td></tr>
					</table>
					<div style="position:absolute;left:140px;top:0px;z-index:9;">
						<img src="images/agentsidetab_cancel.png" width="10" height="46" border="0" onclick="AddtlFormSelect('Cancel');">
					</div>
				</div>
						
					</td>
				</tr>
				</table>
	</span>
	
</FORM> <!-- Moved up from just above </body>   -->
<!-- END *********   The end of the main OSDial display panel -->
	
	
<!-- My indentations after <body> are from:
1. form
2. span
3. tr
4. td
5. tr
6. td
7. div

		I think the "left overs" are from unterminated table(s) from up high 


	</TD>
</TR>
	<tr><td>&nbsp;Where?</td>
</tr>
</TABLE>

-->

</body>
</html>

<?
	
exit; 


?>
