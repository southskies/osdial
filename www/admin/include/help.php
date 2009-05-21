<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
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
# 090410-1147 - Added custom2 field
# 090410-1731 - Added allow_tab_swtich
# 090420-1846 - Added answers_per_hour_limit
# 090515-0135 - Added preview_force_dial_time
# 090515-0140 - Added manual_preview_default
# 090519-2234 - Added INBOUND_MAN
# 090520-1916 - Removed unneeded INBOUND_MAN help.


$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$PHP_SELF?ADD=99999";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";

######################
# ADD=99999 display the HELP SCREENS
######################

if ($ADD==99999)
{
header ("Content-type: text/html; charset=utf-8");
echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";
echo "<title>OSDial Administrator: REPORTS</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
echo "<CENTER>\n";
echo "<TABLE frame=box WIDTH=98% BGCOLOR=#AFCFD7 cellpadding=2 cellspacing=0>";
echo "<TR><TD ALIGN=center><font FACE=\"ARIAL,HELVETICA\" COLOR=1C4754 SIZE=4><B>OSDial Help</B></FONT></td>";
echo "</tr>";
echo "<tr><td><FONT FACE=\"ARIAL,HELVETICA\" COLOR=1C4754 SIZE=2><BR><BR>\n";

?>
<B><FONT SIZE=3>OSDial_Users Table</FONT></B><BR><BR>
<A NAME="osdial_users-user">
<BR>
<B>Agent ID -</B> This field is where you put the OSDial agents ID number, can be up to 8 digits in length, Must be at least 2 characters in length.

<BR>
<A NAME="osdial_users-pass">
<BR>
<B>Password -</B> This field is where you put the OSDial agents password. Must be at least 2 characters in length.

<BR>
<A NAME="osdial_users-full_name">
<BR>
<B>Full Name -</B> This field is where you put the OSDial agents full name. Must be at least 2 characters in length.

<BR>
<A NAME="osdial_users-user_level">
<BR>
<B>User Level -</B> This menu is where you select the OSDial agents user level. Must be a level of 1 to log into OSDial, Must be level greater than 2 to log in as a closer, Must be user level 8 or greater to get into admin web section.

<BR>
<A NAME="osdial_users-user_group">
<BR>
<B>User Group -</B> This menu is where you select the OSDial agents group that this user will belong to. This does not have any restrictions at this time, this is just to subdivide users and allow for future features based upon it.

<BR>
<A NAME="osdial_users-phone_login">
<BR>
<B>Phone Login -</B> Here is where you can set a default phone login value for when the user logs into agent.php. This value will populate the phone_login automatically when the user logs in with their user-pass-campaign in the agent.php login screen.

<BR>
<A NAME="osdial_users-phone_pass">
<BR>
<B>Phone Pass -</B> Here is where you can set a default phone pass value for when the user logs into agent.php. This value will populate the phone_pass automatically when the user logs in with their user-pass-campaign in the agent.php login screen.

<BR>
<A NAME="osdial_users-hotkeys_active">
<BR>
<B>Hot Keys Active -</B> This option if set to 1 allows the user to use the Hot Keys quick-dispositioning function in agent.php.

<BR>
<A NAME="osdial_users-agent_choose_ingroups">
<BR>
<B>Agent Choose Ingroups -</B> This option if set to 1 allows the user to choose the ingroups that they will receive calls from when they login to a CLOSER or INBOUND campaign. Otherwise the Manager will need to set this in their user detail screen of the admin page.

<BR>
<A NAME="osdial_users-scheduled_callbacks">
<BR>
<B>Scheduled Callbacks -</B> This option allows an agent to disposition a call as CALLBK and choose the data and time at which the lead will be re-activated.

<BR>
<A NAME="osdial_users-agentonly_callbacks">
<BR>
<B>Agent-Only Callbacks -</B> This option allows an agent to set a callback so that they are the only Agent that can call the customer back. This also allows the agent to see their callback listings and call them back any time they want to.

<BR>
<A NAME="osdial_users-agentcall_manual">
<BR>
<B>Agent Call Manual -</B> This option allows an agent to manually enter a new lead into the system and call them. This also allows the calling of any phone number from their agent screen and puts that call into their session. Use this option with caution.

<BR>
<A NAME="osdial_users-osdial_recording">
<BR>
<B>OSDial Recording -</B> This option can prevent an agent from doing any recordings after they log in to OSDial. This option must be on for OSDial to follow the campaign recording session.

<BR>
<A NAME="osdial_users-osdial_transfers">
<BR>
<B>OSDial Transfers -</B> This option can prevent an agent from opening the transfer - conference session of OSDial. If this is disabled, the agent cannot third party call or blind transfer any calls.

<BR>
<A NAME="osdial_users-closer_default_blended">
<BR>
<B>Closer Default Blended -</B> This option simply defaults the Blended checkbox on a CLOSER login screen.

<BR>
<A NAME="osdial_users-osdial_recording_override">
<BR>
<B>OSDial Recording Override -</B> This option will override whatever the option is in the campaign for recording. DISABLED will not override the campaign recording setting. NEVER will disable recording on the client. ONDEMAND is the default and allows the agent to start and stop recording as needed. ALLCALLS will start recording on the client whenever a call is sent to an agent. ALLFORCE will start recording on the client whenever a call is sent to an agent giving the agent no option to stop recording. For ALLCALLS and ALLFORCE there is an option to use the Recording Delay to cut down on very short recordings and recude system load.

<BR>
<A NAME="osdial_users-osdial_users-campaign_ranks">
<BR>
<B>Campaign Ranks -</B> In this section you can define the rank an agent will have for each campaign. These ranks can be used to allow for preferred call routing when Next Agent Call is set to campaign_rank.

<BR>
<A NAME="osdial_users-closer_campaigns">
<BR>
<B>Inbound Groups -</B> Here is where you select the inbound groups you want to receive calls from if you have selected the CLOSER campaign. You will also be able to set the rank, or skill level, in this section for each of the inbound groups as well as being able to see the number of calls received from each inbound group for this specific agent. Also in this section is the ability to give the agent a rank for each inbound group. These ranks can be used for preferred call routing when that option is selected in the in-group screen.

<BR>
<A NAME="osdial_users-alter_custdata_override">
<BR>
<B>Agent Alter Customer Data Override -</B> This option will override whatever the option is in the campaign for altering of customer data. NOT_ACTIVE will use whatever setting is present for the campaign. ALLOW_ALTER will always allow for the agent to alter the customer data, no matter what the campaign setting is. Default is NOT_ACTIVE.

<BR>
<A NAME="osdial_users-alter_agent_interface_options">
<BR>
<B>Alter Agent Interface Options -</B> This option if set to 1 allows the administrative user to modify the Agents interface options in admin.php.

<BR>
<A NAME="osdial_users-delete_users">
<BR>
<B>Delete Agents -</B> This option if set to 1 allows the user to delete other users of equal or lesser user level from the system.

<BR>
<A NAME="osdial_users-delete_user_groups">
<BR>
<B>Delete User Groups -</B> This option if set to 1 allows the user to delete user groups from the system.

<BR>
<A NAME="osdial_users-delete_lists">
<BR>
<B>Delete Lists -</B> This option if set to 1 allows the user to delete OSDial lists from the system.

<BR>
<A NAME="osdial_users-delete_campaigns">
<BR>
<B>Delete Campaigns -</B> This option if set to 1 allows the user to delete OSDial campaigns from the system.

<BR>
<A NAME="osdial_users-delete_ingroups">
<BR>
<B>Delete In-Groups -</B> This option if set to 1 allows the user to delete OSDial In-Groups from the system.

<BR>
<A NAME="osdial_users-delete_remote_agents">
<BR>
<B>Delete Remote Agents -</B> This option if set to 1 allows the user to delete OSDial remote agents from the system.

<BR>
<A NAME="osdial_users-load_leads">
<BR>
<B>Load Leads -</B> This option if set to 1 allows the user to load OSDial leads into the osdial_list table by way of the web based lead loader.

<BR>
<A NAME="osdial_users-campaign_detail">
<BR>
<B>Campaign Detail -</B> This option if set to 1 allows the user to view and modify the campaign detail screen elements.

<BR>
<A NAME="osdial_users-ast_admin_access">
<BR>
<B>AGC Admin Access -</B> This option if set to 1 allows the user to login to the OSDial admin pages.

<BR>
<A NAME="osdial_users-ast_delete_phones">
<BR>
<B>AGC Delete Phones -</B> This option if set to 1 allows the user to delete phone entries in the OSDial admin pages.

<BR>
<A NAME="osdial_users-delete_scripts">
<BR>
<B>Delete Scripts -</B> This option if set to 1 allows the user to delete Campaign scripts in the script modification screen.

<BR>
<A NAME="osdial_users-modify_leads">
<BR>
<B>Modify Leads -</B> This option if set to 1 allows the user to modify leads in the admin section lead search results page.

<BR>
<A NAME="osdial_users-change_agent_campaign">
<BR>
<B>Change Agent Campaign -</B> This option if set to 1 allows the user to alter the campaign that an agent is logged into while they are logged into it.

<BR>
<A NAME="osdial_users-delete_filters">
<BR>
<B>Delete Filters -</B> This option allows the user to be able to delete OSDial lead filters from the system.

<BR>
<A NAME="osdial_users-delete_call_times">
<BR>
<B>Delete Call Times -</B> This option allows the user to be able to delete OSDial call times records and OSDial state call times records from the system.

<BR>
<A NAME="osdial_users-modify_call_times">
<BR>
<B>Modify Call Times -</B> This option allows the user to view and modify the call times and state call times records. A user doesn't need this option enabled if they only need to change the call times option on the campaigns screen.

<BR>
<A NAME="osdial_users-modify_sections">
<BR>
<B>Modify Sections -</B> These options allow the user to view and modify each sections records. If set to 0, the user will be able to see the section list, but not the detail or modification screen of a record in that section.

<BR>
<A NAME="osdial_users-view_reports">
<BR>
<B>View Reports -</B> This option allows the user to view the OSDial reports.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGNS TABLE</FONT></B><BR><BR>
<A NAME="osdial_campaigns-campaign_id">
<BR>
<B>Campaign ID -</B> This is the short name of the campaign, it is not editable after initial submission, cannot contain spaces and must be between 2 and 8 characters in length.

<BR>
<A NAME="osdial_campaigns-campaign_name">
<BR>
<B>Campaign Name -</B> This is the description of the campaign, it must be between 6 and 40 characters in length.

<BR>
<A NAME="osdial_campaigns-campaign_description">
<BR>
<B>Campaign Description -</B> This is a memo field for the campaign, it is optional and can be a maximum of 255 characters in length.

<BR>
<A NAME="osdial_campaigns-campaign_changedate">
<BR>
<B>Campaign Change Date -</B> This is the last time that the settings for this campaign were modified in any way.

<BR>
<A NAME="osdial_campaigns-campaign_logindate">
<BR>
<B>Last Campaign Login Date -</B> This is the last time that an agent was logged into this campaign.

<BR>
<A NAME="osdial_campaigns-campaign_stats_refresh">
<BR>
<B>Campaign Stats Refresh -</B> This checkbox will allow you to force a OSDial stats refresh, even if the campaign is not active.

<BR>
<A NAME="osdial_campaigns-active">
<BR>
<B>Active -</B> This is where you set the campaign to Active or Inactive. If Inactive, noone can log into it.

<BR>
<A NAME="osdial_campaigns-park_ext">
<BR>
<B>Park Extension -</B> This is where you can customize the on-hold music for OSDial. Make sure the extension is in place in the extensions.conf and that it points to the filename below.

<BR>
<A NAME="osdial_campaigns-park_file_name">
<BR>
<B>Park File Name -</B> This is where you can customize the on-hold music for OSDial. Make sure the filename is 10 characters in length or less and that the file is in place in the /var/lib/asterisk/sounds directory.

<BR>
<A NAME="osdial_campaigns-web_form_address">
<BR>
<B>Web Form -</B> This is where you can set the custom web page that will be opened when the user clicks on one of the WEB FORM buttons.

<BR>
<A NAME="osdial_campaigns-web_form_extwindow">
<BR>
<B>Web Form in External Window -</B> "Y" will display the WebForm in an external window, "N" will display in a frame in the agent screen.

<BR>
<A NAME="osdial_campaigns-web_form2_extwindow">
<BR>
<B>Web Form 2 in External Window -</B> "Y" will display the WebForm2 in an external window, "N" will display in a frame in the agent screen.

<BR>
<A NAME="osdial_campaigns-allow_closers">
<BR>
<B>Allow Closers -</B> This is where you can set whether the users of this campaign will have the option to send the call to a closer.

<BR>
<A NAME="osdial_campaigns-default_xfer_group">
<BR>
<B>Default Transfer Group -</B> This field is the default In-Group that will be automatically selected when the agent goes to the transfer-conference frame in their agent interface.

<BR>
<A NAME="osdial_campaigns-xfer_groups">
<BR>
<B>Allowed Transfer Groups -</B> With these checkbox listings you can select the groups that agents in this campaign can transfer calls to. Allow Closers must be enabled for this option to show up.

<BR>
<A NAME="osdial_campaigns-campaign_allow_inbound">
<BR>
<B>Allow Inbound and Blended -</B> This is where you can set whether the users of this campaign will have the option to take inbound calls with this campaign. If you want to do blended inbound and outbound then this must be set to Y. If you only want to do outbound dialing on this campaign set this to N. Default is N.

<BR>
<A NAME="osdial_campaigns-dial_status">
<BR>
<B>Dial Status -</B> This is where you set the statuses that you are wanting to dial on within the lists that are active for the campaign below. To add another status to dial, select it from the drop-down list and click ADD. To remove one of the dial statuses, click on the REMOVE link next to the status you want to remove.

<BR>
<A NAME="osdial_campaigns-lead_order">
<BR>
<B>List Order -</B> This menu is where you select how the leads that match the statuses selected above will be put in the lead hopper:
 <BR> &nbsp; - DOWN: select the first leads loaded into the osdial_list table
 <BR> &nbsp; - UP: select the last leads loaded into the osdial_list table
 <BR> &nbsp; - UP PHONE: select the highest phone number and works its way down
 <BR> &nbsp; - DOWN PHONE: select the lowest phone number and works its way up
 <BR> &nbsp; - UP LAST NAME: starts with last names starting with Z and works its way down
 <BR> &nbsp; - DOWN LAST NAME: starts with last names starting with A and works its way up
 <BR> &nbsp; - UP COUNT: starts with most called leads and works its way down
 <BR> &nbsp; - DOWN COUNT: starts with least called leads and works its way up
 <BR> &nbsp; - DOWN COUNT 2nd NEW: starts with least called leads and works its way up inserting a NEW lead in every other lead - Must NOT have NEW selected in the dial statuses
 <BR> &nbsp; - DOWN COUNT 3nd NEW: starts with least called leads and works its way up inserting a NEW lead in every third lead - Must NOT have NEW selected in the dial statuses
 <BR> &nbsp; - DOWN COUNT 4th NEW: starts with least called leads and works its way up inserting a NEW lead in every forth lead - Must NOT have NEW selected in the dial statuses

<BR>
<A NAME="osdial_campaigns-hopper_level">
<BR>
<B>Hopper Level -</B> This is how many leads the VDhopper script tries to keep in the osdial_hopper table for this campaign. If running VDhopper script every minute, make this slightly greater than the number of leads you go through in a minute.

<BR>
<A NAME="osdial_campaigns-lead_filter_id">
<BR>
<B>Lead Filter -</B> This is a method of filtering your leads using a fragment of a SQL query. Use this feature with caution, it is easy to stop dialing accidentally with the slightest alteration to the SQL statement. Default is NONE.

<BR>
<A NAME="osdial_campaigns-force_reset_hopper">
<BR>
<B>Force Reset of Hopper -</B> This allows you to wipe out the hopper contents upon form submission. It should be filled again when the VDhopper script runs.

<BR>
<A NAME="osdial_campaigns-dial_method">
<BR>
<B>Dial Method -</B> This field is the way to define how dialing is to take place. If MANUAL then the auto_dial_level will be locked at 0 unless Dial Method is changed. If RATIO then the normal dialing a number of lines for Active agents. ADAPT_HARD_LIMIT will dial predictively up to the dropped percentage and then not allow aggressive dialing once the drop limit is reached until the percentage goes down again. ADAPT_TAPERED allows for running over the dropped percentage in the first half of the shift -as defined by call_time selected for campaign- and gets more strict as the shift goes on. ADAPT_AVERAGE tries to maintain an average or the dropped percentage not imposing hard limits as aggressively as the other two methods. You cannot change the Auto Dial Level if you are in any of the ADAPT dial methods. Only the Dialer can change the dial level when in predictive dialing mode.

<BR>
<A NAME="osdial_campaigns-auto_dial_level">
<BR>
<B>Auto Dial Level -</B> This is where you set how many lines OSDial should use per active agent. zero 0 means auto dialing is off and the agents will click to dial each number. Otherwise OSDial will keep dialing lines equal to active agents multiplied by the dial level to arrive at how many lines this campaign on each server should allow. The ADAPT OVERRIDE checkbox allows you to force a new dial level even though the dial method is in an ADAPT mode. This is useful if there is a dramatic shift in the quality of leads and you want to drastically change the dial_level manually.

<BR>
<A NAME="osdial_campaigns-available_only_ratio_tally">
<BR>
<B>Available Only Tally -</B> This field if set to Y will leave out INCALL and QUEUE status agents when calculating the number of calls to dial when not in MANUAL dial mode. Default is N.

<BR>
<A NAME="osdial_campaigns-adaptive_dropped_percentage">
<BR>
<B>Drop Percentage Limit -</B> This field is where you set the limit of the percentage of dropped calls you would like while using an adaptive-predictive dial method, not MANUAL or RATIO.

<BR>
<A NAME="osdial_campaigns-adaptive_maximum_level">
<BR>
<B>Maximum Adapt Dial Level -</B> This field is where you set the limit of the limit to the numbr of lines you would like dialed per agent while using an adaptive-predictive dial method, not MANUAL or RATIO. This number can be higher than the Auto Dial Level if your hardware will support it. Value must be a positive number greater than one and can have decimal places Default 3.0.

<BR>
<A NAME="osdial_campaigns-adaptive_latest_server_time">
<BR>
<B>Latest Server Time -</B> This field is only used by the ADAPT_TAPERED dial method. You should enter in the hour and minute that you will stop calling on this campaign, 2100 would mean that you will stop dialing this campaign at 9PM server time. This allows the Tapered algorithm to decide how aggressively to dial by how long you have until you will be finished calling.

<BR>
<A NAME="osdial_campaigns-adaptive_intensity">
<BR>
<B>Adapt Intensity Modifier -</B> This field is used to adjust the predictive intensity either higher or lower. The higher a positive number you select, the greater the dialer will increase the call pacing when it goes up and the slower the dialer will decrease the call pacing when it goes down. The lower the negative number you select here, the slower the dialer will increase the call pacing and the faster the dialer will lower the call pacing when it goes down. Default is 0. This field is not used by the MANUAL or RATIO dial methods.

<BR>
<A NAME="osdial_campaigns-adaptive_dl_diff_target">
<BR>
<B>Dial Level Difference Target -</B> This field is used to define whether you want to target having a specific number of agents waiting for calls or calls waiting for agents. For example if you would always like to have on average one agent free to take calls immediately you would set this to -1, if you would like to target always having one call on hold waiting for an agent you would set this to 1. Default is 0. This field is not used by the MANUAL or RATIO dial methods.

<BR>
<A NAME="osdial_campaigns-concurrent_transfers">
<BR>
<B>Concurrent Transfers -</B> This setting is used to define the number of calls that can be sent to agents at the same time. It is recommended that this setting is left at AUTO. This field is not used by the MANUAL dial method.

<BR>
<A NAME="osdial_campaigns-auto_alt_dial">
<BR>
<B>Auto Alt-Number Dialing -</B> This setting is used to automatically dial alternate number fields while dialing in the RATIO and ADAPT dial methods when there is no contact at the main phone number for a lead, the NA, B, DC and N statuses. This setting is not used by the MANUAL dial method.

<BR>
<A NAME="osdial_campaigns-next_agent_call">
<BR>
<B>Next Agent Call -</B> This determines which agent receives the next call that is available:
 <BR> &nbsp; - random: orders by the random update value in the osdial_live_agents table
 <BR> &nbsp; - oldest_call_start: orders by the last time an agent was sent a call. Results in agents receiving about the same number of calls overall.
 <BR> &nbsp; - oldest_call_finish: orders by the last time an agent finished a call. AKA agent waiting longest receives first call.
 <BR> &nbsp; - overall_user_level: orders by the user_level of the agent as defined in the osdial_users table a higher user_level will receive more calls.
 <BR> &nbsp; - campaign_rank: orders by the rank given to the agent for the campaign. Highest to Lowest.
 <BR> &nbsp; - fewest_calls: orders by the number of calls received by an agent for that specific inbound group. Least calls first.

<BR>
<A NAME="osdial_campaigns-campaign_call_time">
<BR>
<B>Campaign Call Time -</B> This is where you set during which hours you would like to dial, as determined by the local time in the area <b>where you are calling from</b>. This is controlled by area code and is adjusted for Daylight Savings time if applicable.  This is essentially an automated on/off switch based on time for the campaign and has no effect on the times you are calling into in various time-zones.

<BR>
<A NAME="osdial_campaigns-local_call_time">
<BR>
<B>Local Call Time -</B> This is where you set during which hours you would like to dial, as determined by the local time in the area <b>in which you are calling</b>. This is controlled by area code and is adjusted for Daylight Savings time if applicable. General Guidelines in the USA for Business to Business is 9am to 5pm and Business to Consumer calls is 9am to 9pm.

<BR>
<A NAME="osdial_campaigns-dial_timeout">
<BR>
<B>Dial Timeout -</B> If defined, calls that would normally hang up after the timeout defined in extensions.conf would instead timeout at this amount of seconds if it is less than the extensions.conf timeout. This allows for quickly changing dial timeouts from server to server and limiting the effects to a single campaign. If you are having a lot of Answering Machine or Voicemail calls you may want to try changing this value to between 21-26 and see if results improve.

<BR>
<A NAME="osdial_campaigns-preview_force_dial_time">
<BR>
<B>Preview Force Dial Time -</B> If this is set to a number greater than 0, then when in manual-preview dial mode, the call will be placed after the number of seconds set in this field as elapsed with the user needing to click DIAL LEAD.

<BR>
<A NAME="osdial_campaigns-manualpreview_default">
<BR>
<B>Manual Preview Default -</B> When set to "Y", and dial method is "MANUAL", preview dialing will be selected by default.

<BR>
<A NAME="osdial_campaigns-dial_prefix">
<BR>
<B>Dial Prefix -</B> This field allows for more easily changing a path of dialing to go out through a different method without doing a reload in Asterisk. Default is 9 based upon a 91NXXNXXXXXX in the dial plan - extensions.conf.

<BR>
<A NAME="osdial_campaigns-omit_phone_code">
<BR>
<B>Omit Phone Code -</B> This field allows you to leave out the phone_code field while dialing within OSDial. For instance if you are dialing in the UK from the UK you would have 44 in as your phone_code field for all leads, but you just want to dial 10 digits in your dial plan extensions.conf to place calls instead of 44 then 10 digits. Default is N.

<BR>
<A NAME="osdial_campaigns-campaign_cid">
<BR>
<B>Campaign CallerID -</B> This field allows for the sending of a custom callerid number on the outbound calls. This is the number that would show up on the callerid of the person you are calling. The default is UNKNOWN. If you are using T1 or E1s to dial out this option is only available if you are using PRIs - ISDN T1s or E1s - that have the custom callerid feature turned on, this will not work with Robbed-bit service -RBS- circuits. This will also work through most VOIP -SIP or IAX trunks- providers that allow dynamic outbound callerID. The custom callerID only applies to calls placed for the OSDial campaign directly, any 3rd party calls or transfers will not send the custom callerID. NOTE: Sometimes putting UNKNOWN or PRIVATE in the field will yield the sending of your default callerID number by your carrier with the calls. You may want to test this and put 0000000000 in the callerid field instead if you do not want to send you CallerID.

<BR>
<A NAME="osdial_campaigns-campaign_vdad_exten">
<BR>
<B>Campaign OSDial extension -</B> This field allows for a custom OSDial transfer extension. This allows you to use different VDADtransfer...agi scripts depending upon your campaign. The default transfer AGI - exten 8365 agi-VDADtransfer.agi - just immediately sends the calls on to agents as soon as they are picked up. An additional sample political survey AGI is also now included - 8366 agi-VDADtransferSURVEY.agi - that plays a message to the called person and allows them to make a choice by pressing buttons - effectively pre-screening the lead - . Please note that except for surveys, political calls and charities this form of calling is illegal in the United States.

<BR>
<A NAME="osdial_campaigns-campaign_rec_exten">
<BR>
<B>Campaign Rec extension -</B> This field allows for a custom recording extension to be used with OSDial. This allows you to use different extensions depending upon how long you want to allow a maximum recording and what type of codec you want to record in. The default exten is 8309 which if you follow the SCRATCH_INSTALL examples will record in the WAV format for upto one hour. Another option included in the examples is 8310 which will record in GSM format for upto one hour.

<BR>
<A NAME="osdial_campaigns-campaign_recording">
<BR>
<B>Campaign Recording -</B> This menu allows you to choose what level of recording is allowed on this campaign. NEVER will disable recording on the client. ONDEMAND is the default and allows the agent to start and stop recording as needed. ALLCALLS will start recording on the client whenever a call is sent to an agent. ALLFORCE will start recording on the client whenever a call is sent to an agent giving the agent no option to stop recording. For ALLCALLS and ALLFORCE there is an option to use the Recording Delay to cut down on very short recordings and recude system load.

<BR>
<A NAME="osdial_campaigns-campaign_rec_filename">
<BR>
<B>Campaign Rec Filename -</B> This field allows you to customize the name of the recording when Campaign recording is ONDEMAND or ALLCALLS. The allowed variables are CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT. The default is FULLDATE_AGENT and would look like this 20051020-103108_6666. Another example is CAMPAIGN_TINYDATE_CUSTPHONE which would look like this TESTCAMP_51020103108_3125551212. 50 char max.

<BR>
<A NAME="osdial_campaigns-allcalls_delay">
<BR>
<B>Recording Delay -</B> For ALLCALLS and ALLFORCE recording only. This setting will delay the starting of the recording on all calls for the number of seconds specified in this field. Default is 0.

<BR>
<A NAME="osdial_campaigns-campaign_script">
<BR>
<B>Campaign Script -</B> This menu allows you to choose the script that will appear on the agents screen for this campaign. Select NONE to show no script for this campaign.

<BR>
<A NAME="osdial_campaigns-get_call_launch">
<BR>
<B>Get Call Launch -</B> This menu allows you to choose whether you want to auto-launch the web-form page in a separate window, auto-switch to the SCRIPT tab or do nothing when a call is sent to the agent for this campaign. 

<BR>
<A NAME="osdial_campaigns-allow_tab_switch">
<BR>
<B>Allow Tab Switch -</B> This menu allows you to choose whether you want to allow users to be able to switch between FORM and SCRIPT tabs.

<BR>
<A NAME="osdial_campaigns-am_message_exten">
<BR>
<B>Answering Machine Message -</B> This field is for entering in an extension to blind transfer calls to when the agent gets an answering machine and clicks on the Answering Machine Message button in the transfer conference frame. You must set this exten up in the dial plan - extensions.conf - and make sure it plays an audio file then hangs up. 

<BR>
<A NAME="osdial_campaigns-amd_send_to_vmx">
<BR>
<B>AMD send to vm exten -</B> This menu allows you to define whether a message is left on an answering machine when it is detected. the call will be immediately forwarded to the Answering-Machine-Message extension if AMD is active and it is determined that the call is an answering machine.

<BR>
<A NAME="osdial_campaigns-xferconf_a_dtmf">
<BR>
<B>Xfer-Conf DTMF -</B> These four fields allow for you to have two sets of Transfer Conference and DTMF presets. When the call or campaign is loaded, the agent.php script will show two buttons on the transfer-conference frame and auto-populate the number-to-dial and the send-dtmf fields when pressed. If you want to allow Consultative Transfers, a fronter to a closer, you can place CXFER as one of the number-to-dial presets and the proper dial string will be sent to do a Local Consultative Transfer, then the agent can just LEAVE-3WAY-CALL and move on to their next call. If you want to allow Blind transfers of customers to a OSDial AGI script for logging or an IVR, then place AXFER in the number-to-dial field. You can also specify an custom extension after the AXFER or CXFER, for instance if you want to do Internal Consultative transfers instead of Local you would put CXFER90009 in the number-to-dial field.

<BR>
<A NAME="osdial_campaigns-alt_number_dialing">
<BR>
<B>Agent Alt Num Dialing -</B> This option allows an agent to manually dial the alternate phone number or address3 field after the main number has been called.

<BR>
<A NAME="osdial_campaigns-scheduled_callbacks">
<BR>
<B>Scheduled Callbacks -</B> This option allows an agent to disposition a call as CALLBK and choose the data and time at which the lead will be re-activated.

<BR>
<A NAME="osdial_campaigns-drop_call_seconds">
<BR>
<B>Drop Call Seconds -</B> The number of seconds from the time the customer line is picked up until the call is considered a DROP, only applies to outbound calls.

<BR>
<A NAME="osdial_campaigns-voicemail_ext">
<BR>
<B>Voicemail -</B> If defined, calls that would normally DROP would instead be directed to this voicemail box to hear and leave a message.

<BR>
<A NAME="osdial_campaigns-safe_harbor_message">
<BR>
<B>Safe Harbor Message -</B> If set to Y will play a message to customer after the Drop Call Seconds timeout is reached without being transferred to an agent. This setting will override sending to a voicemail box if this is set to Y.

<BR>
<A NAME="osdial_campaigns-safe_harbor_exten">
<BR>
<B>Safe Harbor Exten -</B> This is the dial plan extension that the desired Safe Harbor audio file is located at on your server.

<BR>
<A NAME="osdial_campaigns-wrapup_seconds">
<BR>
<B>Wrap Up Seconds -</B> The number of seconds to force an agent to wait before allowing them to receive or dial another call. The timer begins as soon as an agent hangs up on their customer - or in the case of alternate number dialing when the agent finishes the lead - Default is 0 seconds. If the timer runs out before the agent has dispositioned the call, the agent still will NOT move on to the next call until they select a disposition.

<BR>
<A NAME="osdial_campaigns-wrapup_message">
<BR>
<B>Wrap Up Message -</B> This is a campaign-specific message to be displayed on the wrap up screen if wrap up seconds is set.

<BR>
<A NAME="osdial_campaigns-use_internal_dnc">
<BR>
<B>Use Internal DNC List -</B> This defines whether this campaign is to filter leads against the Internal DNC list. If it is set to Y, the hopper will look for each phone number in the DNC list before placing it in the hopper. If it is in the DNC list then it will change that lead status to DNCL so it cannot be dialed. Default is N.

<BR>
<A NAME="osdial_campaigns-closer_campaigns">
<BR>
<B>Allowed Inbound Groups -</B> For CLOSER campaigns only. Here is where you select the inbound groups you want agents in this CLOSER campaign to be able to take calls from. It is important for BLENDED inbound-outbound campaigns only to select the inbound groups that are used for agents in this campaign. The calls coming into the inbound groups selected here will be counted as active calls for a blended campaign even if all agents in the campaign are not logged in to receive calls from all of those selected inbound groups.

<BR>
<A NAME="osdial_campaigns-agent_pause_codes_active">
<BR>
<B>Agent Pause Codes Active -</B> Allows agents to select a pause code when they click on the PAUSE button in agent.php. Pause codes are definable per campaign at the bottom of the campaign view detail screen and they are stored in the osdial_agent_log table. Default is N.

<BR>
<A NAME="osdial_campaigns-disable_alter_custdata">
<BR>
<B>Disable Alter Customer Data -</B> If set to Y, does not change any of the customer data record when an agent dispositions the call. Default is N.

<BR>
<A NAME="osdial_campaigns-no_hopper_leads_logins">
<BR>
<B>Allow No-Hopper-Leads Logins -</B> If set to Y, allows agents to login to the campaign even if there are no leads loaded into the hopper for that campaign. This function is not needed in CLOSER-type campaigns. Default is N.

<BR>
<A NAME="osdial_campaigns-list_order_mix">
<BR>
<B>List Order Mix -</B> Overrides the Lead Order and Dial Status fields. Will use the List and status parameters for the selected List Mix entry in the List Mix sub section instead. Default is DISABLED.

<BR>
<A NAME="osdial_campaigns-vcl_id">
<BR>
<B>List Mix ID -</B> ID of the list mix. Must be from 2-20 characters in length with no spaces or other special punctuation.

<BR>
<A NAME="osdial_campaigns-vcl_name">
<BR>
<B>List Mix Name -</B> Descriptive name of the list mix. Must be from 2-50 characters in length.

<BR>
<A NAME="osdial_campaigns-list_mix_container">
<BR>
<B>List Mix Detail -</B> The composition of the List Mix entry. Contains the List ID, mix order, percentages and statuses that make up this List Mix. The percentages always have to add up to 100, and the lists all have to be active and set to the campaign for the order mix entry to be Activated.

<BR>
<A NAME="osdial_campaigns-mix_method">
<BR>
<B>List Mix Method -</B> The method of mixing all of the parts of the List Mix Detail together. EVEN_MIX will mix leads from each part interleaved with the other parts, like this 1,2,3,1,2,3,1,2,3. IN_ORDER will put the leads in the order in which they are listed in the List Mix Detail screen 1,1,1,2,2,2,3,3,3. RANDOM will put them in RANDOM order 1,3,2,1,1,3,2,1,3. Default is IN_ORDER.

<BR>
<A NAME="osdial_campaigns-manual_dial_list_id">
<BR>
<B>Manual Dial List ID -</B> The default list_id to be used when an agent placces a manual call and a new lead record is created in osdial_list. Default is 999. This field can contain digits only.

<BR>
<A NAME="osdial_campaigns-answers_per_hour_limit">
<BR>
<B>Answers per hour limit -</B> This option sets an upper limit on the number of answers the dialer has in an hour.  0 to disable.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_LISTS TABLE</FONT></B><BR><BR>
<A NAME="osdial_lists-list_id">
<BR>
<B>List ID -</B> This is the numerical name of the list, it is not editable after initial submission, must contain only numbers and must be between 2 and 8 characters in length. Must be a number greater than 100.

<BR>
<A NAME="osdial_lists-list_name">
<BR>
<B>List Name -</B> This is the description of the list, it must be between 2 and 20 characters in length.

<BR>
<A NAME="osdial_lists-list_description">
<BR>
<B>List Description -</B> This is the memo field for the list, it is optional.

<BR>
<A NAME="osdial_lists-list_changedate">
<BR>
<B>List Change Date -</B> This is the last time that the settings for this list were modified in any way.

<BR>
<A NAME="osdial_lists-list_lastcalldate">
<BR>
<B>List Last Call Date -</B> This is the last time that lead was dialed from this list.

<BR>
<A NAME="osdial_lists-campaign_id">
<BR>
<B>Campaign -</B> This is the campaign that this list belongs to. A list can only be dialed on a single campaign at one time.

<BR>
<A NAME="osdial_lists-active">
<BR>
<B>Active -</B> This defines whether the list is to be dialed on or not.

<BR>
<A NAME="osdial_lists-reset_list">
<BR>
<B>Reset Lead-Called-Status for this list -</B> This resets all leads in this list to N for "not called since last reset" and means that any lead can now be called if it is the right status as defined in the campaign screen.

<BR>
<A NAME="osdial_list-dnc">
<BR>
<B>OSDial DNC List -</B> This Do Not Call list contains every lead that has been set to a status of DNC in the system. Through the LISTS - ADD NUMBER TO DNC page you are able to manually add a number to this list so that it will not be called by campaigns that use the internal DNC list.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_INBOUND_GROUPS TABLE</FONT></B><BR><BR>
<A NAME="osdial_inbound_groups-group_id">
<BR>
<B>Group ID -</B> This is the short name of the inbound group, it is not editable after initial submission, must not contain any spaces and must be between 2 and 20 characters in length.

<BR>
<A NAME="osdial_inbound_groups-group_name">
<BR>
<B>Group Name -</B> This is the description of the group, it must be between 2 and 30 characters in length. Cannot include dashes, plusses or spaces .

<BR>
<A NAME="osdial_inbound_groups-group_color">
<BR>
<B>Group Color -</B> This is the color that displays in the OSDial client app when a call comes in on this group. It must be between 2 and 7 characters long. If this is a hex color definition you must remember to put a # at the beginning of the string or OSDial will not work properly.

<BR>
<A NAME="osdial_inbound_groups-active">
<BR>
<B>Active -</B> This determines whether this group show up in the selection box when a OSDial agent logs in.

<BR>
<A NAME="osdial_inbound_groups-web_form_address">
<BR>
<B>Web Form -</B> This is the custom address that clicking on one of the WEB FORM buttons in OSDial will take you to for calls that come in on this group.

<BR>
<A NAME="osdial_inbound_groups-web_form_extwindow">
<BR>
<B>Web Form External -</B> "Y" will display the WebForm in an external window, "N" will display in an OSDial frame on the agent screen.

<BR>
<A NAME="osdial_inbound_groups-next_agent_call">
<BR>
<B>Next Agent Call -</B> This determines which agent receives the next call that is available:
 <BR> &nbsp; - random: orders by the random update value in the osdial_live_agents table
 <BR> &nbsp; - oldest_call_start: orders by the last time an agent was sent a call. Results in agents receiving about the same number of calls overall.
 <BR> &nbsp; - oldest_call_finish: orders by the last time an agent finished a call. AKA agent waiting longest receives first call.
 <BR> &nbsp; - overall_user_level: orders by the user_level of the agent as defined in the osdial_users table a higher user_level will receive more calls.
 <BR> &nbsp; - inbound_group_rank: orders by the rank given to the agent for the specific inbound group. Highest to Lowest.
 <BR> &nbsp; - fewest_calls: orders by the number of calls received by an agent for that specific inbound group. Least calls first.
 <BR> &nbsp; - campaign_rank: orders by the rank given to the agent for the campaign. Highest to Lowest.
 <BR> &nbsp; - fewest_calls_campaign: orders by the number of calls received by an agent for the campaign. Least calls first.
<BR>
<A NAME="osdial_inbound_groups-fronter_display">
<BR>
<B>Fronter Display -</B> This field determines whether the inbound OSDial agent would have the fronter name - if there is one - displayed in the Status field when the call comes to the agent.

<BR>
<A NAME="osdial_inbound_groups-ingroup_script">
<BR>
<B>Campaign Script -</B> This menu allows you to choose the script that will appear on the agents screen for this campaign. Select NONE to show no script for this campaign.

<BR>
<A NAME="osdial_inbound_groups-get_call_launch">
<BR>
<B>Get Call Launch -</B> This menu allows you to choose whether you want to auto-launch the web-form page in a separate window, auto-switch to the SCRIPT tab or do nothing when a call is sent to the agent for this campaign. 

<BR>
<A NAME="osdial_inbound_groups-allow_tab_switch">
<BR>
<B>Allow Tab Switch -</B> This menu allows you to choose whether you want to allow users to be able to switch between FORM and SCRIPT tabs.

<BR>
<A NAME="osdial_inbound_groups-xferconf_a_dtmf">
<BR>
<B>Xfer-Conf DTMF -</B> These four fields allow for you to have two sets of Transfer Conference and DTMF presets. When the call or campaign is loaded, the agent.php script will show two buttons on the transfer-conference frame and auto-populate the number-to-dial and the send-dtmf fields when pressed. If you want to allow Consultative Transfers, a fronter to a closer, you can place CXFER as one of the number-to-dial presets and the proper dial string will be sent to do a Local Consultative Transfer, then the agent can just LEAVE-3WAY-CALL and move on to their next call. If you want to allow Blind transfers of customers to a OSDial AGI script for logging or an IVR, then place AXFER in the number-to-dial field. You can also specify an custom extension after the AXFER or CXFER, for instance if you want to do Internal Consultative transfers instead of Local you would put CXFER90009 in the number-to-dial field.

<BR>
<A NAME="osdial_inbound_groups-drop_call_seconds">
<BR>
<B>Drop Call Seconds -</B> The number of seconds from the time the customer line is picked up until the call is considered a DROP, only applies to outbound calls.

<BR>
<A NAME="osdial_inbound_groups-voicemail_ext">
<BR>
<B>Voicemail -</B> If defined, calls that would normally DROP would instead be directed to this voicemail box to hear and leave a message.

<BR>
<A NAME="osdial_inbound_groups-drop_message">
<BR>
<B>Drop Message -</B> If set to Y will play a message to customer after the Drop Call Seconds timeout is reached without being transferred to an agent. This setting will override sending to a voicemail box if this is set to Y.

<BR>
<A NAME="osdial_inbound_groups-drop_exten">
<BR>
<B>Drop Exten -</B> This is the dial plan extension that the desired Dropped call audio file is located at on your server.

<BR>
<A NAME="osdial_inbound_groups-call_time_id">
<BR>
<B>Call Time -</B> This is the call time scheme to use for this inbound group. Keep in mind that the time is based on the server time. Default is 24hours.

<BR>
<A NAME="osdial_inbound_groups-after_hours_action">
<BR>
<B>After Hours Action -</B> The action to perform if it is after hours as defined in the call time for this inbound group. HANGUP will immediately hangup the call, MESSASGE will play the file in the After Hours Message Filenam field, EXTENSION will send the call to the After Hours Extension in the dialplan and VOICEMAIL will send the call to the voicemail box listed in the After Hours Voicemail field. Default is MESSAGE.

<BR>
<A NAME="osdial_inbound_groups-after_hours_message_filename">
<BR>
<B>After Hours Message Filename -</B> The audio file located on the server to be played if the Action is set to MESSAGE. Default is vm-goodbye

<BR>
<A NAME="osdial_inbound_groups-after_hours_exten">
<BR>
<B>After Hours Extension -</B> The dialplan extension to send the call to if the Action is set to EXTENSION. Default is 8300.

<BR>
<A NAME="osdial_inbound_groups-after_hours_voicemail">
<BR>
<B>After Hours Voicemail -</B> The voicemail box to send the call to if the Action is set to VOICEMAIL.

<BR>
<A NAME="osdial_inbound_groups-welcome_message_filename">
<BR>
<B>Welcome Message Filename -</B> The audio file located on the server to be played when the call comes in. If set to ---NONE--- then no message will be played. Default is ---NONE---

<BR>
<A NAME="osdial_inbound_groups-moh_context">
<BR>
<B>Music On Hold Context -</B> The music on hold context to use when the customer is placed on hold. Default is default.

<BR>
<A NAME="osdial_inbound_groups-onhold_prompt_filename">
<BR>
<B>On Hold Prompt Filename -</B> The audio file located on the server to be played at a regular interval when the customer is on hold. Default is generic_hold. This audio file MUST be 9 seconds or less in length.

<BR>
<A NAME="osdial_inbound_groups-prompt_interval">
<BR>
<B>On Hold Prompt Interval -</B> The length of time in seconds to wait before playing the on hold prompt. Default is 60.

<BR>
<A NAME="osdial_inbound_groups-agent_alert_exten">
<BR>
<B>Agent Alert Extension -</B> The extension to send into the agent session to announce that a call is coming to the agent. This extension should have a Playback of an audio file. To not use this function set this to X. Default is X.

<BR>
<A NAME="osdial_inbound_groups-agent_alert_delay">
<BR>
<B>Agent Alert Delay -</B> The length of time in milliseconds to wait before sending the call to the agent after playing the on Agent Alert Extension. Default is 1000.

<BR>
<A NAME="osdial_inbound_groups-default_xfer_group">
<BR>
<B>Default Transfer Group -</B> This field is the default In-Group that will be automatically selected when the agent goes to the transfer-conference frame in their agent interface.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_REMOTE_AGENTS TABLE</FONT></B><BR><BR>
<A NAME="osdial_remote_agents-user_start">
<BR>
<B>Agent ID Start -</B> This is the starting Agent ID that is used when the remote agent entries are inserted into the system. If the Number of Lines is set higher than 1, this number is incremented by one until each line has an entry. Make sure you create a new OSDial agent account with a user level of 4 or great if you want them to be able to use the vdremote.php page for remote web access of this account.

<BR>
<A NAME="osdial_remote_agents-number_of_lines">
<BR>
<B>Number of Lines -</B> This defines how many remote agent entries the system creates, and determines how many lines it thinks it can safely send to the number below.

<BR>
<A NAME="osdial_remote_agents-server_ip">
<BR>
<B>Server IP -</B> A remote agent entry is only good for one specific server, here is where you select which server you want.

<BR>
<A NAME="osdial_remote_agents-conf_exten">
<BR>
<B>External Extension -</B> This is the number that you want the calls forwarded to. Make sure that it is a full dial plan number and that if you need a 9 at the beginning you put it in here. Test by dialing this number from a phone on the system.

<BR>
<A NAME="osdial_remote_agents-status">
<BR>
<B>Status -</B> Here is where you turn the remote agent on and off. As soon as the agent is Active the system assumes that it can send calls to it. It may take up to 30 seconds once you change the status to Inactive to stop receiving calls.

<BR>
<A NAME="osdial_remote_agents-campaign_id">
<BR>
<B>Campaign -</B> Here is where you select the campaign that these remote agents will be logged into. Inbound needs to use the CLOSER campaign and select the inbound campaigns below that you want to receive calls from.

<BR>
<A NAME="osdial_remote_agents-closer_campaigns">
<BR>
<B>Inbound Groups -</B> Here is where you select the inbound groups you want to receive calls from if you have selected the CLOSER campaign.


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGN_LISTS</FONT></B><BR><BR>
<A NAME="osdial_campaign_lists">
<BR>
The lists within this campaign are listed here, whether they are active is denoted by the Y or N and you can go to the list screen by clicking on the list ID in the first column.


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGN_STATUSES TABLE</FONT></B><BR><BR>
<A NAME="osdial_campaign_statuses">
<BR>
Through the use of custom campaign statuses, you can have statuses that only exist for a specific campaign. The Status must be 1-8 characters in length, the description must be 2-30 characters in length and Selectable defines whether it shows up in OSDial as a disposition. The human_answered field is used when calculating the drop percentage, or abandon rate. Setting human_answered to Y will use this status when counting the human-answered calls. The Category option allows you to group several statuses into a catogy that can be used for statistical analysis.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGN_HOTKEYS TABLE</FONT></B><BR><BR>
<A NAME="osdial_campaign_hotkeys">
<BR>
Through the use of custom campaign hot keys, agents that use the OSDial web-client can hang up and disposition calls just by pressing a single key on their keyboard.</B> There are two special HotKey options that you can use in conjunction with Alternate Phone number dialing, ALTPH2 - Alternate Phone Hot Dial and ADDR3-----Address3 Hot Dial allow an agent to use a hotkey to hang up their call, stay on the same lead, and dial another contact number from that lead. 





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_LEAD_RECYCLE TABLE</FONT></B><BR><BR>
<A NAME="osdial_lead_recycle">
<BR>
Through the use of lead recycling, you can call specific statuses of leads again at a specified interval without resetting the entire list. Lead recycling is campaign-specific and does not have to be a selected dialable status in your campaign. The attempt delay field is the number of seconds until the lead can be placed back in the hopper, this number must be at least 120 seconds. The attempt maximum field is the maximum number of times that a lead of this status can be attempted before the list needs to be reset, this number can be from 1 to 10. You can activate and deactivate a lead recycle entry with the provided links.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial AUTO ALT DIAL STATUSES</FONT></B><BR><BR>
<A NAME="osdial_auto_alt_dial_statuses">
<BR>
If the Auto Alt-Number Dialing field is set, then the leads that are dispositioned under these auto alt dial statuses will have their alt_phone and-or address3 fields dialed after any of these no-answer statuses are set.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial AGENT PAUSE CODES</FONT></B><BR><BR>
<A NAME="osdial_pause_codes">
<BR>
If the Agent Pause Codes Active field is set to active then the agents will be able to select from these pause codes when they click on the PAUSE button on their screens. This data is then stored in the OSDial agent log. The Pause code must contain only letters and numbers and be less than 7 characters long. The pause code name can be no longer than 30 characters.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_USER_GROUPS TABLE</FONT></B><BR><BR>
<A NAME="osdial_user_groups-user_group">
<BR>
<B>User Group -</B> This is the short name of a OSDial User group, try not to use any spaces or punctuation for this field. max 20 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_user_groups-group_name">
<BR>
<B>Group Name -</B> This is the description of the OSDial user group max of 40 characters.

<BR>
<A NAME="osdial_user_groups-allowed_campaigns">
<BR>
<B>Allowed Campaigns -</B> This is a selectable list of Campaigns to which members of this user group can log in to. The ALL-CAMPAIGNS option allows the users in this group to see and log in to any campaign on the system.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_SCRIPTS TABLE</FONT></B><BR><BR>
<A NAME="osdial_scripts-script_id">
<BR>
<B>Script ID -</B> This is the short name of a OSDial Script. This needs to be a unique identifier. Try not to use any spaces or punctuation for this field. max 10 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_scripts-script_name">
<B>Script Name -</B> This is the title of a OSDial Script. This is a short summary of the script. max 50 characters, minimum of 2 characters. There should be no spaces or punctuation of any kind in theis field.

<BR>
<A NAME="osdial_scripts-script_comments">
<B>Script Comments -</B> This is where you can place comments for a OSDial Script such as -changed to free upgrade on Sept 23-.  max 255 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_scripts-script_text">
<B>Script Text -</B> This is where you place the content of a OSDial Script. Minimum of 2 characters. You can have customer information be auto-populated in this script using "--A--field--B--" where field is one of the following fieldnames: vendor_lead_code, source_id, list_id, gmt_offset_now, called_since_last_reset, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, custom1, custom2, comments, lead_id, campaign, phone_login, group, channel_group, SQLdate, epoch, uniqueid, customer_zap_channel, server_ip, SIPexten, session_id. For example, this sentence would print the persons name in it----<BR><BR>  Hello, can I speak with --A--first_name--B-- --A--last_name--B-- please? Well hello --A--title--B-- --A--last_name--B-- how are you today?<BR><BR> This would read----<BR><BR>Hello, can I speak with John Doe please? Well hello Mr. Doe how are you today?<BR><BR> You can also use an iframe to load a separate window within the SCRIPT tab, here is an example with prepopulated variables:

<DIV style="height:200px;width:400px;background:white;overflow:scroll;font-size:12px;font-family:sans-serif;" id=iframe_example>
&#60;iframe src="http://localhost/test_OSDial_output.php?lead_id=--A--lead_id--B--&#38;vendor_id=--A--vendor_lead_code--B--&#38;list_id=--A--list_id--B--&#38;gmt_offset_now=--A--gmt_offset_now--B--&#38;phone_code=--A--phone_code--B--&#38;phone_number=--A--phone_number--B--&#38;title=--A--title--B--&#38;first_name=--A--first_name--B--&#38;middle_initial=--A--middle_initial--B--&#38;last_name=--A--last_name--B--&#38;address1=--A--address1--B--&#38;address2=--A--address2--B--&#38;address3=--A--address3--B--&#38;city=--A--city--B--&#38;state=--A--state--B--&#38;province=--A--province--B--&#38;postal_code=--A--postal_code--B--&#38;country_code=--A--country_code--B--&#38;gender=--A--gender--B--&#38;date_of_birth=--A--date_of_birth--B--&#38;alt_phone=--A--alt_phone--B--&#38;email=--A--email--B--&#38;custom1=--A--custom1--B--&#38;custom2=--A--custom2--B--&#38;comments=--A--comments--B--&#38;user=--A--user--B--&#38;campaign=--A--campaign--B--&#38;phone_login=--A--phone_login--B--&#38;fronter=--A--fronter--B--&#38;closer=--A--user--B--&#38;group=--A--group--B--&#38;channel_group=--A--group--B--&#38;SQLdate=--A--SQLdate--B--&#38;epoch=--A--epoch--B--&#38;uniqueid=--A--uniqueid--B--&#38;customer_zap_channel=--A--customer_zap_channel--B--&#38;server_ip=--A--server_ip--B--&#38;SIPexten=--A--SIPexten--B--&#38;session_id=--A--session_id--B--&#38;phone=--A--phone--B--" style="width:580;height:290;background-color:transparent;" scrolling="auto" frameborder="0" allowtransparency="true" id="popupFrame" name="popupFrame" width="460" height="290" STYLE="z-index:17"&#62;
&#60;/iframe&#62;
</DIV>

<BR>
<A NAME="osdial_scripts-active">
<BR>
<B>Active -</B> This determines whether this script can be selected to be used by a campaign.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_LEAD_FILTERS TABLE</FONT></B><BR><BR>
<A NAME="osdial_lead_filters-lead_filter_id">
<BR>
<B>Filter ID -</B> This is the short name of a OSDial Lead Filter. This needs to be a unique identifier. Do not use any spaces or punctuation for this field. max 10 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_lead_filters-lead_filter_name">
<B>Filter Name -</B> This is a more descriptive name of the Filter. This is a short summary of the filter. max 30 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_lead_filters-lead_filter_comments">
<B>Filter Comments -</B> This is where you can place comments for a OSDial Filter such as -calls all California leads-.  max 255 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_lead_filters-lead_filter_sql">
<B>Filter SQL -</B> This is where you place the SQL query fragment that you want to filter by. do not begin or end with an AND, that will be added by the hopper cron script automatically. an example SQL query that would work here is- called_count > 4 and called_count < 8 -.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CALL TIMES TABLE</FONT></B><BR><BR>
<A NAME="osdial_call_times-call_time_id">
<BR>
<B>Call Time ID -</B> This is the short name of a OSDial Call Time Definition. This needs to be a unique identifier. Do not use any spaces or punctuation for this field. max 10 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_call_times-call_time_name">
<B>Call Time Name -</B> This is a more descriptive name of the Call Time Definition. This is a short summary of the Call Time definition. max 30 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_call_times-call_time_comments">
<B>Call Time Comments -</B> This is where you can place comments for a OSDial Call Time Definition such as -10am to 4pm with extra call state restrictions-.  max 255 characters.

<BR>
<A NAME="osdial_call_times-ct_default_start">
<B>Default Start and Stop Times -</B> This is the default time that calling will be allowed to be started or stopped within this call time definition if the day-of-the-week start time is not defined. 0 is midnight. To prevent calling completely set this field to 2400 and set the Default Stop time to 2400. To allow calling 24 hours a day set the start time to 0 and the stop time to 2400.

<BR>
<A NAME="osdial_call_times-ct_sunday_start">
<B>Weekday Start and Stop Times -</B> These are the custom times per day that can be set for the call time definition. same rules apply as with the Default start and stop times.

<BR>
<A NAME="osdial_call_times-ct_state_call_times">
<B>State Call Time Definitions -</B> This is the list of State specific call time definitions that are followed in this Call Time Definition.

<BR>
<A NAME="osdial_call_times-state_call_time_state">
<B>State Call Time State -</B> This is the two letter code for the state that this calling time definition is for. For this to be in effect the local call time that is set in the campaign must have this state call time record in it as well as all of the leads having two letter state codes in them.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial LIST LOADER FUNCTIONALITY</FONT></B><BR><BR>
<A NAME="osdial_list_loader">
<BR>
The OSDial basic web-based lead loader is designed simply to take a lead file - up to 8MB in size - that is either tab or pipe delimited and load it into the osdial_list table. The lead loader allows for field choosing and TXT- Plain Text, CSV- Comma Separated Values and XLS- Excel file formats. The lead loader does not do data validation, but it does allow you to check for duplicates in itself, within the campaign or within the entire system. Also, make sure that you have created the list that these leads are to be under so that you can use them. Here is a list of the fields in their proper order for the lead files:
	<OL>
	<LI>Vendor Lead Code - shows up in the Vendor ID field of the GUI
	<LI>Source Code - internal use only for admins and DBAs
	<LI>List ID - the list number that these leads will show up under
	<LI>Phone Code - the prefix for the phone number - 1 for US, 01144 for UK, 01161 for AUS, etc
	<LI>Phone Number - must be at least 8 digits long
	<LI>Title - title of the customer - Mr. Ms. Mrs, etc...
	<LI>First Name
	<LI>Middle Initial
	<LI>Last Name
	<LI>Address Line 1
	<LI>Address Line 2
	<LI>Address Line 3
	<LI>City
	<LI>State - limited to 2 characters
	<LI>Province
	<LI>Postal Code
	<LI>Country
	<LI>Gender
	<LI>Date of Birth
	<LI>Alternate Phone Number
	<LI>Email Address
	<LI>Custom1
	<LI>Comments
	</OL>

<BR>NOTES: The Excel Lead loader functionality is enabled by a series of perl scripts and needs to have a properly configured /etc/osdial.conf file in place on the web server. Also, a couple perl modules must be loaded for it to work as well - OLE-Storage_Lite and Spreadsheet-ParseExcel. You can check for runtime errors in these by looking at your apache error_log file. Also, for duplication checks against gampaign lists, the list that has new leads going into it does need to be created in the system before you start to load the leads.




<BR><BR><BR><BR>






<B><FONT SIZE=3>PHONES TABLE</FONT></B><BR><BR>
<A NAME="phones-extension">
<BR>
<B>Phone extension -</B> This field is where you put the phones name as it appears to Asterisk not including the protocol or slash at the beginning. For Example: for the SIP phone SIP/test101 the Phone extension would be test101. Also, for IAX2 phones make sure you use the full phones name: IAX2/IAXphone1@IAXphone1 would be IAXphone1@IAXphone1. For Zap phones make sure you put the full channel: Zap/25-1 would be 25-1.  Another note, make sure you set the Protocol below correctly for your type of phone.

<BR>
<A NAME="phones-dialplan_number">
<BR>
<B>Dial Plan Number -</B> This field is for the number you dial to have the phone ring. This number is defined in the extensions.conf file of your Asterisk server

<BR>
<A NAME="phones-voicemail_id">
<BR>
<B>Voicemail Box -</B> This field is for the voicemail box that the messages go to for the user of this phone. We use this to check for voicemail messages and for the user to be able to use the VOICEMAIL button on OSDial app.

<BR>
<A NAME="phones-outbound_cid">
<BR>
<B>Outbound CallerID -</B> This field is where you would enter the callerID number that you would like to appear on outbound calls placed form the OSDial web-client. This does not work on RBS, non-PRI, T1/E1s.

<BR>
<A NAME="phones-phone_ip">
<BR>
<B>Phone IP address -</B> This field is for the phone's IP address if it is a VOIP phone. This is an optional field

<BR>
<A NAME="phones-computer_ip">
<BR>
<B>Computer IP address -</B> This field is for the user's computer IP address. This is an optional field

<BR>
<A NAME="phones-server_ip">
<BR>
<B>Server IP -</B> This menu is where you select which server the phone is active on.

<BR>
<A NAME="phones-login">
<BR>
<B>Login -</B> The login used for the phone user to login to the client applications.

<BR>
<A NAME="phones-pass">
<BR>
<B>Password -</B>  The password used for the phone user to login to the client applications.

<BR>
<A NAME="phones-status">
<BR>
<B>Status -</B> The status of the phone in the system, ACTIVE and ADMIN allow for GUI clients to work. ADMIN allows access to this administrative web site. All other statuses do not allow GUI or Admin web access.

<BR>
<A NAME="phones-active">
<BR>
<B>Active Account -</B> Whether the phone is active to put it in the list in the GUI client.

<BR>
<A NAME="phones-phone_type">
<BR>
<B>Phone Type -</B> Purely for administrative notes.

<BR>
<A NAME="phones-fullname">
<BR>
<B>Full Name -</B> Used by the GUIclient in the list of active phones.

<BR>
<A NAME="phones-company">
<BR>
<B>Company -</B> Purely for administrative notes.

<BR>
<A NAME="phones-picture">
<BR>
<B>Picture -</B> Not yet Implemented.

<BR>
<A NAME="phones-messages">
<BR>
<B>New Messages -</B> Number of new voicemail messages for this phone on the Asterisk server.

<BR>
<A NAME="phones-old_messages">
<BR>
<B>Old Messages -</B> Number of old voicemail messages for this phone on the Asterisk server.

<BR>
<A NAME="phones-protocol">
<BR>
<B>Client Protocol -</B> The protocol that the phone uses to connect to the Asterisk server: SIP, IAX2, Zap . Also, there is EXTERNAL for remote dial numbers or speed dial numbers that you want to list as phones.

<BR>
<A NAME="phones-local_gmt">
<BR>
<B>Local GMT -</B> The difference from Greenwich Mean time, or ZULU time where the phone is located. DO NOT ADJUST FOR DAYLIGHT SAVINGS TIME. This is used by the OSDial campaign to accurately display the time and customer time.

<BR>
<A NAME="phones-ASTmgrUSERNAME">
<BR>
<B>Manager Login -</B> This is the login that the GUI clients for this phone will use to access the Database where the server data resides.

<BR>
<A NAME="phones-ASTmgrSECRET">
<BR>
<B>Manager Secret -</B> This is the password that the GUI clients for this phone will use to access the Database where the server data resides.

<BR>
<A NAME="phones-login_user">
<BR>
<B>OSDial Default Agent -</B> This is to place a default value in the OSDial agent field whenever this phone user opens the OSDial client app. Leave blank for no agent.

<BR>
<A NAME="phones-login_pass">
<BR>
<B>OSDial Default Pass -</B> This is to place a default value in the OSDial password field whenever this phone user opens the OSDial client app. Leave blank for no pass.

<BR>
<A NAME="phones-login_campaign">
<BR>
<B>OSDial Default Campaign -</B> This is to place a default value in the OSDial campaign field whenever this phone user opens the OSDial client app. Leave blank for no campaign.

<BR>
<A NAME="phones-park_on_extension">
<BR>
<B>Park Exten -</B> This is the default Parking extension for the client apps. Verify that a different one works before you change this.

<BR>
<A NAME="phones-conf_on_extension">
<BR>
<B>Conf Exten -</B> This is the default Conference park extension for the client apps. Verify that a different one works before you change this.

<BR>
<A NAME="phones-OSDial_park_on_extension">
<BR>
<B>OSDial Park Exten -</B> This is the default Parking extension for OSDial client app. Verify that a different one works before you change this.

<BR>
<A NAME="phones-OSDial_park_on_filename">
<BR>
<B>OSDial Park File -</B> This is the default OSDial park extension file name for the client apps. Verify that a different one works before you change this. limited to 10 characters.

<BR>
<A NAME="phones-monitor_prefix">
<BR>
<B>Monitor Prefix -</B> This is the dial plan prefix for monitoring of Zap channels automatically within the OSDial app. Only change according to the extensions.conf ZapBarge extensions records.

<BR>
<A NAME="phones-recording_exten">
<BR>
<B>Recording Exten -</B> This is the dial plan extension for the recording extension that is used to drop into meetme conferences to record them. It usually lasts upto one hour if not stopped. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-voicemail_exten">
<BR>
<B>VMAIL Main Exten -</B> This is the dial plan extension going to check your voicemail. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-voicemail_dump_exten">
<BR>
<B>VMAIL Dump Exten -</B> This is the dial plan prefix used to send calls directly to a user's voicemail from a live call in the OSDial app. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-ext_context">
<BR>
<B>Exten Context -</B> This is the dial plan context that this phone primarily uses. It is assumed that all numbers dialed by the client apps are using this context so it is a good idea to make sure this is the most wide context possible. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-dtmf_send_extension">
<BR>
<B>DTMF send Channel -</B> This is the channel string used to send DTMF sounds into meetme conferences from the client apps. Verify the exten and context with the extensions.conf file.

<BR>
<A NAME="phones-call_out_number_group">
<BR>
<B>Outbound Call Group -</B> This is the channel group that outbound calls from this phone are placed out of. There are a couple routines in the client apps that use this. For Zap channels you want to use something like Zap/g2 , for IAX2 trunks you would want to use the full IAX prefix like IAX2/OSDtest1:secret@10.10.10.15:4569. Verify the trunks with the extensions.conf file, it is usually what you have defined as the TRUNK global variable at the top of the file.

<BR>
<A NAME="phones-client_browser">
<BR>
<B>Browser Location -</B> This is applicable to only UNIX/LINUX clients, the absolute path to Mozilla or Firefox browser on the machine. verify this by launching it manually.

<BR>
<A NAME="phones-install_directory">
<BR>
<B>Install Directory -</B> This is the place where the OSDial scripts are located on your machine. For Win32 it should be something like C:\AST_OSD and for UNIX it should be something like /usr/local/perl_TK. verify this manually.

<BR>
<A NAME="phones-local_web_callerID_URL">
<BR>
<B>CallerID URL -</B> This is the web address of the page used to do custom callerID lookups. default testing address is: http://localhost/test_callerid_output.php

<BR>
<A NAME="phones-OSDial_web_URL">
<BR>
<B>OSDial Default URL -</B> This is the web address of the page used to do custom OSDial Web Form queries. default testing address is: http://localhost/test_OSDial_output.php

<BR>
<A NAME="phones-AGI_call_logging_enabled">
<BR>
<B>Call Logging -</B> This is set to true if the call_log.agi file is in place in the extensions.conf file for all outbound and hang up 'h' extensions to log all calls. This should always be 1 because it is manditory for many OSDial features to work properly.

<BR>
<A NAME="phones-user_switching_enabled">
<BR>
<B>Agent Switching -</B> Set to true to allow agent to switch to another agent account. NOTE: If agent switches they can initiate recording on the new agent's phone conversation

<BR>
<A NAME="phones-conferencing_enabled">
<BR>
<B>Conferencing -</B> Set to true to allow user to start conference calls with upto six external lines.

<BR>
<A NAME="phones-admin_hangup_enabled">
<BR>
<B>Admin Hang Up -</B> Set to true to allow user to be able to hang up any line at will through OSDial. Good idea only to enable this for Admin users.

<BR>
<A NAME="phones-admin_hijack_enabled">
<BR>
<B>Admin Hijack -</B> Set to true to allow user to be able to grab and redirect to their extension any line at will through OSDial. Good idea only to enable this for Admin users. But is very useful for Managers.

<BR>
<A NAME="phones-admin_monitor_enabled">
<BR>
<B>Admin Monitor -</B> Set to true to allow user to be able to grab and redirect to their extension any line at will through OSDial. Good idea only to enable this for Admin users. But is very useful for Managers and as a training tool.

<BR>
<A NAME="phones-call_parking_enabled">
<BR>
<B>Call Park -</B> Set to true to allow user to be able to park calls on OSDial hold to be picked up by any other OSDial user on the system. Calls stay on hold for upto a half hour then hang up. Usually enabled for all.

<BR>
<A NAME="phones-updater_check_enabled">
<BR>
<B>Updater Check -</B> Set to true to display a popup warning that the updater time has not changed in 20 seconds. Useful for Admin users.

<BR>
<A NAME="phones-AFLogging_enabled">
<BR>
<B>AF Logging -</B> Set to true to log many actions of OSDial usage to a text file on the user's computer.

<BR>
<A NAME="phones-QUEUE_ACTION_enabled">
<BR>
<B>Queue Enabled -</B> Set to true to have client apps use the Asterisk Central Queue system. Required for OSDial and recommended for all users.

<BR>
<A NAME="phones-CallerID_popup_enabled">
<BR>
<B>CallerID Popup -</B> Set to true to allow for numbers defined in the extensions.conf file to send CallerID popup screens to OSDial users.

<BR>
<A NAME="phones-voicemail_button_enabled">
<BR>
<B>VMail Button -</B> Set to true to display the VOICEMAIL button and the messages count display on OSDial.

<BR>
<A NAME="phones-enable_fast_refresh">
<BR>
<B>Fast Refresh -</B> Set to true to enable a new rate of refresh of call information for the OSDial. Default disabled rate is 1000 ms ,1 second. Can increase system load if you lower this number.

<BR>
<A NAME="phones-fast_refresh_rate">
<BR>
<B>Fast Refresh Rate -</B> in milliseconds. Only used if Fast Refresh is enabled. Default disabled rate is 1000 ms ,1 second. Can increase system load if you lower this number.

<BR>
<A NAME="phones-enable_persistant_mysql">
<BR>
<B>Persistant MySQL -</B> If enabled the OSDial connection will remain connected instead of connecting every second. Useful if you have a fast refresh rate set. It will increase the number of connections on your MySQL machine.

<BR>
<A NAME="phones-auto_dial_next_number">
<BR>
<B>Auto Dial Next Number -</B> If enabled the OSDial client will dial the next number on the list automatically upon disposition of a call unless they selected to "Stop Dialing" on the disposition screen.

<BR>
<A NAME="phones-VDstop_rec_after_each_call">
<BR>
<B>Stop Rec after each call -</B> If enabled the OSDial client will stop whatever recording is going on after each call has been dispositioned. Useful if you are doing a lot of recording or you are using a web form to trigger recording.

<BR>
<A NAME="phones-enable_sipsak_messages">
<BR>
<B>Enable SIPSAK Messages -</B> If enabled the server will send messages to the SIP phone to display on the phone LCD display when logged into OSDial. Feature only works with SIP phones and requires sipsak application to be installed on the web server. Default is 0.

<BR>
<A NAME="phones-DBX_server">
<BR>
<B>DBX Server -</B> The MySQL database server that this user should be connecting to.

<BR>
<A NAME="phones-DBX_database">
<BR>
<B>DBX Database -</B> The MySQL database that this user should be connecting to. Default is asterisk.

<BR>
<A NAME="phones-DBX_user">
<BR>
<B>DBX User -</B> The MySQL user login that this user should be using when connecting. Default is cron.

<BR>
<A NAME="phones-DBX_pass">
<BR>
<B>DBX Pass -</B> The MySQL user password that this user should be using when connecting. Default is 1234.

<BR>
<A NAME="phones-DBX_port">
<BR>
<B>DBX Port -</B> The MySQL TCP port that this user should be using when connecting. Default is 3306.

<BR>
<A NAME="phones-DBY_server">
<BR>
<B>DBY Server -</B> The MySQL database server that this user should be connecting to. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_database">
<BR>
<B>DBY Database -</B> The MySQL database that this user should be connecting to. Default is asterisk. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_user">
<BR>
<B>DBY User -</B> The MySQL user login that this user should be using when connecting. Default is cron. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_pass">
<BR>
<B>DBY Pass -</B> The MySQL user password that this user should be using when connecting. Default is 1234. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_port">
<BR>
<B>DBY Port -</B> The MySQL TCP port that this user should be using when connecting. Default is 3306. Secondary server, not used currently.


<BR><BR><BR><BR>

<B><FONT SIZE=3>SERVERS TABLE</FONT></B><BR><BR>
<A NAME="servers-server_id">
<BR>
<B>Server ID -</B> This field is where you put the Asterisk servers name, doesnt have to be an official domain sub, just a nickname to identify the server to Admin users.

<BR>
<A NAME="servers-server_description">
<BR>
<B>Server Description -</B> The field where you use a small phrase to describe the Asterisk server.

<BR>
<A NAME="servers-server_ip">
<BR>
<B>Server IP Address -</B> The field where you put the Network IP address of the Asterisk server.

<BR>
<A NAME="servers-active">
<BR>
<B>Active -</B> Set whether the Asterisk server is active or inactive.

<BR>
<A NAME="servers-asterisk_version">
<BR>
<B>Asterisk Version -</B> Set the version of Asterisk that you have installed on this server. Examples: '1.2', '1.0.8', '1.0.7', 'CVS_HEAD', 'REALLY OLD', etc... This is used because versions 1.0.8 and 1.0.9 have a different method of dealing with Local/ channels, a bug that has been fixed in CVS v1.0, and need to be treated differently when handling their Local/ channels. Also, current CVS_HEAD and the 1.2 release tree uses different manager and command output so it must be treated differently as well.

<BR>
<A NAME="servers-max_osdial_trunks">
<BR>
<B>Max OSDial Trunks -</B> This field will determine the maximum number of lines that the OSDial auto-dialer will attempt to call on this server. If you want to dedicate two full PRI T1s to dialing on a server then you would set this to 46. Default is 96.

<BR>
<A NAME="servers-telnet_host">
<BR>
<B>Telnet Host -</B> This is the address or name of the Asterisk server and is how the manager applications connect to it from where they are running. If they are running on the Asterisk server, then the default of 'localhost' is fine.

<BR>
<A NAME="servers-telnet_port">
<BR>
<B>Telnet Port -</B> This is the port of the Asterisk server Manager connection and is how the manager applications connect to it from where they are running. The default of '5038' is fine for a standard install.

<BR>
<A NAME="servers-ASTmgrUSERNAME">
<BR>
<B>Manager User -</B> The username or login used to connect genericly to the Asterisk server manager. Default is 'cron'

<BR>
<A NAME="servers-ASTmgrSECRET">
<BR>
<B>Manager Secret -</B> The secret or password used to connect genericly to the Asterisk server manager. Default is '1234'

<BR>
<A NAME="servers-ASTmgrUSERNAMEupdate">
<BR>
<B>Manager Update User -</B> The username or login used to connect to the Asterisk server manager optimized for the Update scripts. Default is 'updatecron' and assumes the same secret as the generic user.

<BR>
<A NAME="servers-ASTmgrUSERNAMElisten">
<BR>
<B>Manager Listen User -</B> The username or login used to connect to the Asterisk server manager optimized for scripts that only listen for output. Default is 'listencron' and assumes the same secret as the generic user.

<BR>
<A NAME="servers-ASTmgrUSERNAMEsend">
<BR>
<B>Manager Send User -</B> The username or login used to connect to the Asterisk server manager optimized for scripts that only send Actions to the manager. Default is 'sendcron' and assumes the same secret as the generic user.

<BR>
<A NAME="servers-local_gmt">
<BR>
<B>Server GMT offset -</B> The difference in hours from GMT time not adjusted for Daylight-Savings-Time of the server. Default is '-5'

<BR>
<A NAME="servers-voicemail_dump_exten">
<BR>
<B>VMail Dump Exten -</B> The extension prefix used on this server to send calls directly through agc to a specific voicemail box. Default is '85026666666666'

<BR>
<A NAME="servers-answer_transfer_agent">
<BR>
<B>OSDial AD extension -</B> The default extension if none is present in the campaign to send calls to for OSDial auto dialing. Default is '8365'

<BR>
<A NAME="servers-ext_context">
<BR>
<B>Default Context -</B> The default dial plan context used for scripts that operate for this server. Default is 'default'

<BR>
<A NAME="servers-sys_perf_log">
<BR>
<B>System Performance -</B> Setting this option to Y will enable logging of system performance stats for the server machine including system load, system processes and Asterisk channels in use. Default is N.

<BR>
<A NAME="servers-vd_server_logs">
<BR>
<B>Server Logs -</B> Setting this option to Y will enable logging of all OSDial related scripts to their text log files. Setting this to N will stop writing logs to files for these processes, also the screen logging of asterisk will be disabled if this is set to N when Asterisk is started. Default is Y.

<BR>
<A NAME="servers-agi_output">
<BR>
<B>AGI Output -</B> Setting this option to NONE will disable output from all OSDial related AGI scripts. Setting this to STDERR will send the AGI output to the Asterisk CLI. Setting this to FILE will send the output to a file in the logs directory. Setting this to BOTH will send output to both the Asterisk CLI and a log file. Default is FILE.

<BR>
<A NAME="servers-osdial_balance_active">
<BR>
<B>OSDial Balance Dialing -</B> Setting this field to Y will allow the server to place balance calls for campaigns in OSDial so that the defined dial level can be met even if there are no agents logged into that campaign on this server. Default is N.

<BR>
<A NAME="servers-balance_trunks_offlimits">
<BR>
<B>OSDial Balance Offlimits -</B> This setting defines the number of trunks to not allow OSDial balance dialing to use. For example if you have 40 max OSDial trunks and balance offlimits is set to 10 you will only be able to use 30 trunk lines for OSDial balance dialing. Default is 0.


<BR><BR><BR><BR>

<B><FONT SIZE=3>CONFERENCES TABLE</FONT></B><BR><BR>
<A NAME="conferences-conf_exten">
<BR>
<B>Conference Number -</B> This field is where you put the meetme conference dialpna number. It is also recommended that the meetme number in meetme.conf matches this number for each entry. This is for the conferences in OSDial and is used for leave-3way-call functionality in OSDial.

<BR>
<A NAME="conferences-server_ip">
<BR>
<B>Server IP -</B> The menu where you select the Asterisk server that this conference will be on.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_SERVER_TRUNKS TABLE</FONT></B><BR><BR>
<A NAME="osdial_server_trunks">
<BR>
OSDial Server Trunks allows you to restrict the outgoing lines that are used on this server for campaign dialing on a per-campaign basis. You have the option to reserve a specific number of lines to be used by only one campaign as well as allowing that campaign to run over its reserved lines into whatever lines remain open, as long at the total lines used by OSDial on this server is less than the Max OSDial Trunks setting. Not having any of these records will allow the campaign that dials the line first to have as many lines as it can get under the Max OSDial Trunks setting.





<BR><BR><BR><BR>

<B><FONT SIZE=3>SYSTEM_SETTINGS TABLE</FONT></B><BR><BR>
<A NAME="settings-use_non_latin">
<BR>
<B>Use Non-Latin -</B> This option allows you to default the web display script to use UTF8 characters and not do any latin-character-family regular expression filtering or display formatting. Default is 0.

<BR>
<A NAME="settings-webroot_writable">
<BR>
<B>Webroot Writable -</B> This setting allows you to define whether temp files and authentication files should be placed in the webroot on your web server. Default is 1.

<BR>
<A NAME="settings-enable_queuemetrics_logging">
<BR>
<B>Enable QueueMetrics Logging -</B> This setting allows you to define whether OSDial will insert log entries into the queue_log database table as Asterisk Queues activity does. QueueMetrics is a standalone, closed-source statistical analysis program. You must have QueueMetrics already installed and configured before enabling this feature. Default is 0.

<BR>
<A NAME="settings-queuemetrics_server_ip">
<BR>
<B>QueueMetrics Server IP -</B> This is the IP address of the database for your QueueMetrics installation.

<BR>
<A NAME="settings-queuemetrics_dbname">
<BR>
<B>QueueMetrics Database Name -</B> This is the database name for your QueueMetrics database.

<BR>
<A NAME="settings-queuemetrics_login">
<BR>
<B>QueueMetrics Database Login -</B> This is the user name used to log in to your QueueMetrics database.

<BR>
<A NAME="settings-queuemetrics_pass">
<BR>
<B>QueueMetrics Database Password -</B> This is the password used to log in to your QueueMetrics database.

<BR>
<A NAME="settings-queuemetrics_url">
<BR>
<B>QueueMetrics URL -</B> This is the URL or web site address used to get to your QueueMetrics installation.

<BR>
<A NAME="settings-queuemetrics_log_id">
<BR>
<B>QueueMetrics Log ID -</B> This is the server ID that all OSDial logs going into the QueueMetrics database will use as an identifier for each record.

<BR>
<A NAME="settings-queuemetrics_eq_prepend">
<BR>
<B>QueueMetrics EnterQueue Prepend -</B> This field is used to allow for prepending of one of the osdial_list data fields in front of the phone number of the customer for customized QueueMetrics reports. Default is NONE to not populate anything.

<BR>
<A NAME="settings-osdial_agent_disable">
<BR>
<B>OSDial Agent Disable Display -</B> This field is used to select when to show an agent when their session has been disabled by the system, a manager action or by an external measure. The NOT_ACTIVE setting will disable the message on the agents screen. The LIVE_AGENT setting will only display the disabled message when the agents osdial_auto_calls record has been removed, such as during a force logout or emergency logout. 

<BR>
<A NAME="settings-allow_sipsak_messages">
<BR>
<B>Allow SIPSAK Messages -</B> If set to 1, this will allow the phones table setting to work properly, the server will send messages to the SIP phone to display on the phone LCD display when logged into OSDial. This feature only works with SIP phones and requires sipsak application to be installed on the web server. Default is 0. 

<BR>
<A NAME="settings-admin_home_url">
<BR>
<B>Admin Home URL -</B> This is the URL or web site address that you will go to if you click on the HOME link at the top of the admin.php page.

<BR>
<A NAME="settings-enable_agc_xfer_log">
<BR>
<B>Enable Agent Transfer Logfile -</B> This option will log to a text logfile on the webserver every time a call is transferred to an agent. Default is 0, disabled.


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_Statuses Table</FONT></B><BR><BR>
<A NAME="osdial_statuses">
<BR>
Through the use of system statuses, you can have statuses that exist for campaign and in-group. The Status must be 1-6 characters in length, the description must be 2-30 characters in length and Selectable defines whether it shows up in OSDial as an agent disposition. The human_answered field is used when calculating the drop percentage, or abandon rate. Setting human_answered to Y will use this status when counting the human-answered calls. The Category option allows you to group several statuses into a catogy that can be used for statistical analysis.</B>


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_Status_Categories Table</FONT></B><BR><BR>
<A NAME="osdial_status_categories">
<BR>
Through the use of system status categories, you can group together statuses to allow for statistical analysis on a group of statuses. The Category ID must be 2-20 characters in length with no spaces, the name must be 2-50 characters in length, the description is optional and Time On OSDial Display defines whether that status will be one of the upto 4 statuses that can be calculated and displayed on the Time On OSDial Real-Time report.</B>




<BR><BR><BR><BR><BR><BR><BR><BR>
<BR><BR><BR><BR><BR><BR><BR><BR>

</TD></TR></TABLE></BODY></HTML>
<?
exit;

#### END HELP SCREENS
}

?>
