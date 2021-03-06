<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009-2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009-2013  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
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
# 090410-1118 - Rename of Remote/Off-Hook Agent to External Agent.


# Default menu
if (!isset($ADD))   {$ADD=0;}
if (!isset($sh))    {$sh='';}

# Users
if ($ADD==0)			{$hh='users';		$title = "Users List";}
if ($ADD=="1")			{$hh='users';		$title = "Add New User";}
if ($ADD=="1A")			{$hh='users';		$title = "Copy User";}
if ($ADD=="2")			{$hh='users';		$title = "New User Addition";}
if ($ADD=="2A")			{$hh='users';		$title = "New Copied User Addition";}
if ($ADD==3)			{$hh='users';		$title = "Modify User";}
if ($ADD=="4A")			{$hh='users';		$title = "Modify User - Admin";}
if ($ADD=="4B")			{$hh='users';		$title = "Modify User - Admin";}
if ($ADD==4)			{$hh='users';		$title = "Modify User";}
if ($ADD==5)			{$hh='users';		$title = "Delete User";}
if ($ADD==6)			{$hh='users';		$title = "Delete User";}
if ($ADD==8)			{$hh='users';		$title = "CallBacks Within Agent";}
if ($ADD==9)			{$hh='users';		$title = "Lead Allocation";}
if ($ADD==550)			{$hh='users';		$title = "Search Form";}
if ($ADD==551)			{$hh='users';		$title = "SEARCH PHONES";}
if ($ADD==660)			{$hh='users';		$title = "Search Results";}
if ($ADD==661)			{$hh='users';		$title = "SEARCH PHONES RESULTS";}

# Campaigns - Misc
if ($ADD==73)			{$hh='campaigns';	$title = "Dialable Lead Count";}
if ($ADD==30)			{$hh='campaigns';	$title = "Campaign Not Allowed";}

# Campaigns - List
if ($ADD==10)			{$hh='campaigns';	$sh='list';	$title = "Campaigns";}
if ($ADD==81)			{$hh='campaigns';	$sh='list';	$title = "CallBacks Within Campaign";}

# Campaigns - Status
if ($ADD==22)			{$hh='campaigns';	$sh='status';	$title = "New Campaign Status Addition";}
if ($ADD==42)			{$hh='campaigns';	$sh='status';	$title = "Modify Campaign Status";}
if ($ADD==32)			{$hh='campaigns';	$sh='status';	$title = "Campaign Statuses";}

# Campaigns - hotkeys
if ($ADD==23)			{$hh='campaigns';	$sh='hotkey';	$title = "New Campaign HotKey Addition";}
if ($ADD==43)			{$hh='campaigns';	$sh='hotkey';	$title = "Modify Campaign HotKey";}
if ($ADD==33)			{$hh='campaigns';	$sh='hotkey';	$title = "Campaign HotKeys";}

# Campaigns - recycle
if ($ADD==25)			{$hh='campaigns';	$sh='recycle';	$title = "New Campaign Lead Recycle Addition";}
if ($ADD==45)			{$hh='campaigns';	$sh='recycle';	$title = "Modify Campaign Lead Recycle";}
if ($ADD==65)			{$hh='campaigns';	$sh='recycle';	$title = "Delete Lead Recycle";}
if ($ADD==35)			{$hh='campaigns';	$sh='recycle';	$title = "Campaign Lead Recycle Entries";}

# Campaigns - autoalt
if ($ADD==26)			{$hh='campaigns';	$sh='autoalt';	$title = "New Auto Alt Dial Status";}
if ($ADD==66)			{$hh='campaigns';	$sh='autoalt';	$title = "Delete Auto Alt Dial Status";}
if ($ADD==36)			{$hh='campaigns';	$sh='autoalt';	$title = "Campaign Auto Alt Dial Statuses";}

# Campaigns - pause
if ($ADD==27)			{$hh='campaigns';	$sh='pause';	$title = "New Agent Pause Code";}
if ($ADD==47)			{$hh='campaigns';	$sh='pause';	$title = "Modify Agent Pause Code";}
if ($ADD==67)			{$hh='campaigns';	$sh='pause';	$title = "Delete Agent Pause Code";}
if ($ADD==37)			{$hh='campaigns';	$sh='pause';	$title = "Campaign Agent Pause Codes";}

# Campaigns - cid areacode mappings
if ($ADD=="2ca")               {$hh='campaigns';   $sh='cid_areacode'; $title = "New CID Areacode Mapping";}
if ($ADD=="4ca")               {$hh='campaigns';   $sh='cid_areacode'; $title = "Modify CID Areacode Mapping";}
if ($ADD=="5ca")               {$hh='campaigns';   $sh='cid_areacode'; $title = "Configm Delete CID Areacode Mapping";}
if ($ADD=="6ca")               {$hh='campaigns';   $sh='cid_areacode'; $title = "Delete CID Areacode Mapping";}
if ($ADD=="3ca" and $SUB!=2)   {$hh='campaigns';   $sh='cid_areacode'; $title = "Campaign CID Areacode Mapping";}
if ($ADD=="3ca" and $SUB==2)   {$hh='campaigns';   $sh='cid_areacode'; $title = "Modify Campaign - Detail - $campaign_id - CID Areacode Mappings";}

# Campaigns - email blacklist
if ($ADD=="2eb")               {$hh='campaigns';   $sh='email_blacklist'; $title = "New Email Blacklist";}
if ($ADD=="4eb")               {$hh='campaigns';   $sh='email_blacklist'; $title = "Modify Email Blacklist";}
if ($ADD=="5eb")               {$hh='campaigns';   $sh='email_blacklist'; $title = "Configm Delete Email Blacklist";}
if ($ADD=="6eb")               {$hh='campaigns';   $sh='email_blacklist'; $title = "Delete Email Blacklist";}
if ($ADD=="3eb" and $SUB!=2)   {$hh='campaigns';   $sh='email_blacklist'; $title = "Campaign Email Blacklist";}
if ($ADD=="3eb" and $SUB==2)   {$hh='campaigns';   $sh='email_blacklist'; $title = "Modify Campaign - Detail - $campaign_id - Email Blacklist";}

# Campaigns - fields
if ($ADD=="1form")	    {$hh='campaigns';	$sh='fields';	$title = "Add New Form";}
if ($ADD=="2form")	    {$hh='campaigns';	$sh='fields';	$title = "New Additional Form";}
if ($ADD=="2fields")	{$hh='campaigns';	$sh='fields';	$title = "New Additional Field";}
if ($ADD=="3fields")	{$hh='campaigns';	$sh='fields';	$title = "Campaign Additional Field Entries";}  
if ($ADD=="4form")	    {$hh='campaigns';	$sh='fields';	$title = "Modify Form";}
if ($ADD=="4fields")	{$hh='campaigns';	$sh='fields';	$title = "Modify Field";}
if ($ADD=="5form")	    {$hh='campaigns';	$sh='fields';	$title = "Delete Form Confirmation";}
if ($ADD=="6form")	    {$hh='campaigns';	$sh='fields';	$title = "Delete Form";}
if ($ADD=="6fields")	{$hh='campaigns';	$sh='fields';	$title = "Delete Field";}
# Campaigns - NEW additional fields
if ($ADD==71)			{$hh='campaigns';	$sh='fields';	$title = "Campaign Additional Field Entries";} 
if ($ADD==72)		    {$hh='campaigns';	$sh='fields';	$title = "Modify Form";}
# 73 already exists in campaigns

# Campaigns - fields
if ($ADD=="1menu")		{$hh='campaigns';	$sh='ivr';	$title = "Add New In/Out IVR Menu";}
if ($ADD=="1keys")		{$hh='campaigns';	$sh='ivr';	$title = "Add New In/Out IVR Key";}
if ($ADD=="2keys")		{$hh='campaigns';	$sh='ivr';	$title = "New Key Entry Form";}
if ($ADD=="4menu")		{$hh='campaigns';	$sh='ivr';	$title = "Modify IVR";}
if ($ADD=="4keys")		{$hh='campaigns';	$sh='ivr';	$title = "Modify Key";}
if ($ADD=="6keys")		{$hh='campaigns';	$sh='ivr';	$title = "Delete Key";}
if ($ADD=="3menu")		{$hh='campaigns';	$sh='ivr';	$title = "In/Out IVR Modification Form";}
if ($ADD=="3keys")		{$hh='campaigns';	$sh='ivr';	$title = "Key Modification Form";}

# Campaigns - dialstat
if ($ADD==28)			{$hh='campaigns';	$sh='dialstat';	$title = "Campaign Dial Status Added";}
if ($ADD==68)			{$hh='campaigns';	$sh='dialstat';	$title = "Campaign Dial Status Removed";}
if ($ADD==38)			{$hh='campaigns';	$sh='dialstat';	$title = "Campaign Dial Statuses";}

# Campaigns - listmix
if ($ADD==29)			{$hh='campaigns';	$sh='listmix';	$title = "Campaign List Mix Added";}
if ($ADD==49)			{$hh='campaigns';	$sh='listmix';	$title = "Modify Campaign List Mix";}
if ($ADD==69)			{$hh='campaigns';	$sh='listmix';	$title = "Campaign List Mix Removed";}
if ($ADD==39)			{$hh='campaigns';	$sh='listmix';	$title = "Campaign List Mixes";}

# Campaigns - basic
if ($ADD==44)			{$hh='campaigns';	$sh='basic';	$title = "Modify Campaign - Basic View";}
if ($ADD==34)			{$hh='campaigns';	$sh='basic';	$title = "Modify Campaign - Basic View";    $ADD="31";}

# Campaigns - detail
if ($ADD==11)			{$hh='campaigns';	$sh='detail';	$title = "Add New Campaign";}
if ($ADD==12)			{$hh='campaigns';	$sh='detail';	$title = "Copy Campaign";}
if ($ADD==20)			{$hh='campaigns';	$sh='detail';	$title = "New Copied Campaign Addition";}
if ($ADD==21)			{$hh='campaigns';	$sh='detail';	$title = "New Campaign Addition";}
if ($ADD==41)			{$hh='campaigns';	$sh='detail';	$title = "Modify Campaign";}
if ($ADD==51)			{$hh='campaigns';	$sh='detail';	$title = "Delete Campaign";}
if ($ADD==52)			{$hh='campaigns';	$sh='detail';	$title = "Logout Agents";}
if ($ADD==53)			{$hh='campaigns';	$sh='detail';	$title = "Emergency VDAC Jam Clear";}
if ($ADD==61)			{$hh='campaigns';	$sh='detail';	$title = "Delete Campaign";}
if ($ADD==62)			{$hh='campaigns';	$sh='detail';	$title = "Logout Agents";}
if ($ADD==63)			{$hh='campaigns';	$sh='detail';	$title = "Emergency VDAC Jam Clear";}
//if ($ADD=='3fields')	{$hh='campaigns';	$sh='detail';	$title = "Campaign Additional Field Entries";}  // $sh changed from 'fields' to 'detail'
if ($ADD==31) {
	$hh='campaigns';	$sh='detail';	$title = "Modify Campaign - Detail - $campaign_id";
	if ($SUB==22)	{$title .= " - Statuses";}
	if ($SUB==23)	{$title .= " - HotKeys";}
	if ($SUB==25)	{$title .= " - Lead Recycle Entries";}
	if ($SUB==26)	{$title .= " - Auto Alt Dial Statuses";}
	if ($SUB==27)	{$title .= " - Agent Pause Codes";}
	if ($SUB==29)	{$title .= " - List Mixes";}
}


# Lists
if ($ADD==112)			{$ADD=999999;   $SUB=27;    $hh='reports';	$sh="reports";    $title = "Lead Search - Basic";}
if ($ADD==1122)			{$ADD=999999;   $SUB=26;    $hh='reports';	$sh="reports"; $title = "Lead Search - Advanced";}
if ($ADD==100)			{$hh='lists';	$title = "Lists";}
if ($ADD==111)			{$hh='lists';	$title = "Add New List";}
if ($ADD==1121)			{$hh='lists';	$sh="modify_lead";     $title = "Lead Modification";}
if ($ADD==121)			{$hh='lists';	$sh="dnc";             $title = "Do-Not-Call Management";}
if ($ADD==131)			{$hh='lists';	$sh="export";          $title = "Export Leads";}
if ($ADD==122)			{$hh='lists';	$sh="list_loader";     $title = "Load New Leads";}
if ($ADD==211)			{$hh='lists';	$title = "New List Addition";}
if ($ADD==411)			{$hh='lists';	$title = "Modify List";}
if ($ADD==511)			{$hh='lists';	$title = "Delete List";}
if ($ADD==611)			{$hh='lists';	$title = "Delete List";}
if ($ADD==811)			{$hh='lists';	$title = "CallBacks Within List";}
if ($ADD==311)			{$hh='lists';	$title = "Modify List";}

# Lead Transfers
if ($ADD=='10lx')			{$hh='lists';	$sh='transfers';       $title = "Lead Transfer Policies";}
if ($ADD=='41lx')			{$hh='lists';	$sh='transfers';       $title = "Modify Lead Transfer Policy";}
if ($ADD=='31lx')			{$hh='lists';	$sh='transfers';       $title = "Modify Lead Transfer Policy";}

# Ingroups
if ($ADD==1000)			{$hh='ingroups';	$title = "In-Groups";}
if ($ADD==1001)			{$hh='ingroups';	$title = "In-Groups A2A";}
if ($ADD==1111)			{$hh='ingroups';	$title = "Add New In-Group";}
if ($ADD==1211)			{$hh='ingroups';	$title = "Copy In-Group";}
if ($ADD==2111)			{$hh='ingroups';	$title = "New In-Group Addition";}
if ($ADD==2011)			{$hh='ingroups';	$title = "New Copied In-Group Addition";}
if ($ADD==4111)			{$hh='ingroups';	$title = "Modify In-Group";}
if ($ADD==5111)			{$hh='ingroups';	$title = "Delete In-Group";}
if ($ADD==6111)			{$hh='ingroups';	$title = "Delete In-Group";}
if ($ADD==3111)			{$hh='ingroups';	$title = "Modify In-Group";}

# User Groups
if ($ADD==8111)			{$hh='usergroups';	$title = "CallBacks Within User Group";}
if ($ADD==100000)		{$hh='usergroups';	$title = "User Groups";}
if ($ADD==111111)		{$hh='usergroups';	$title = "Add New Users Group";}
if ($ADD==211111)		{$hh='usergroups';	$title = "New Users Group Addition";}
if ($ADD==411111)		{$hh='usergroups';	$title = "Modify Users Groups";}
if ($ADD==511111)		{$hh='usergroups';	$title = "Delete Users Group";}
if ($ADD==611111)		{$hh='usergroups';	$title = "Delete Users Group";}
if ($ADD==311111)		{$hh='usergroups';	$title = "Modify Users Groups";}

# Scripts
if ($ADD==1000000)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Scripts";}
if ($ADD==1111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Add New Script";}
if ($ADD==2111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "New Script Addition";}
if ($ADD==4111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Modify Script";}
if ($ADD==5111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Delete Script";}
if ($ADD==6111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Delete Script";}
if ($ADD==7111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Preview Script";}
if ($ADD==3111111)		{$hh='templates';	$sh='scripts';	$ssh='scripts';		$title = "Modify Script";}


# Email Templates
if ($ADD=="0email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Email Templates";}
if ($ADD=="1email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Add New Email Template";}
if ($ADD=="2email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "New Email Template Addition";}
if ($ADD=="4email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Modify Email Template";}
if ($ADD=="5email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Delete Email Template";}
if ($ADD=="6email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Delete Email Template";}
if ($ADD=="7email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Preview Email Template";}
if ($ADD=="3email")		{$hh='templates';	$sh='scripts';	$ssh='email_templates';    $title = "Modify Email Template";}

# Filters
if ($ADD==10000000)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "Filters";}
if ($ADD==11111111)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "Add New Filter";}
if ($ADD==21111111)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "New Filter Addition";}
if ($ADD==41111111)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "Modify Filter";}
if ($ADD==51111111)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "Delete Filter";}
if ($ADD==61111111)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "Delete Filter";}
if ($ADD==31111111)		{$hh='templates';	$sh='filters';	$ssh='filters';		$title = "Modify Filter";}

# Admin - media
if ($ADD=="10media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "MEDIA LIST";}
if ($ADD=="11media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "ADD NEW MEDIA";}
if ($ADD=="21media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "ADDING NEW MEDIA";}
if ($ADD=="41media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "MODIFY MEDIA";}
if ($ADD=="51media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "DELETE MEDIA";}
if ($ADD=="61media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "DELETE MEDIA";}
if ($ADD=="31media")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "MODIFY MEDIA";}

# Admin - media - tts scripts
if ($ADD=="10tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "TTS LIST";}
if ($ADD=="11tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "ADD NEW TTS";}
if ($ADD=="21tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "ADDING NEW TTS";}
if ($ADD=="41tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "MODIFY TTS";}
if ($ADD=="51tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "DELETE TTS";}
if ($ADD=="61tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "DELETE TTS";}
if ($ADD=="31tts")		{$hh='templates';	$sh='admin';	$ssh='media';	$title = "MODIFY TTS";}


# Admin - carrier
if (OSDpreg_match('/carrier$/',$ADD)) {
    $hh='admin';
    $sh='carriers';
    $ttype='CARRIER';
    if ($SUB==2) $ttype='CARRIER';
    if ($SUB==3) $ttype='CARRIER SERVER OPTIONS';
    if ($SUB==4) $ttype='CARRIER DIDS';

    if ($ADD=="1carrier") $title = "ADD NEW $ttype";
    if ($ADD=="2carrier") $title = "ADDING NEW $ttype";
    if ($ADD=="4carrier") $title = "MODIFIED $ttype";
    if ($ADD=="5carrier") $title = "DELETE $ttype";
    if ($ADD=="6carrier") $title = "DELETE $ttype";
    if ($ADD=="3carrier") $title = "MODIFY $ttype";
    if ($ADD=="3carrier" and $SUB<=1) $title = "CARRIER LIST";
}


# Admin - times
if ($ADD==100000000)		{$hh='admin';	$sh='times';	$title = "Call Times";}
if ($ADD==111111111)		{$hh='admin';	$sh='times';	$title = "Add New Call Time";}
if ($ADD==211111111)		{$hh='admin';	$sh='times';	$title = "New Call Time Addition";}
if ($ADD==411111111)		{$hh='admin';	$sh='times';	$title = "Modify Call Time";}
if ($ADD==511111111)		{$hh='admin';	$sh='times';	$title = "Delete Call Time";}
if ($ADD==611111111)		{$hh='admin';	$sh='times';	$title = "Delete Call Time";}
if ($ADD==311111111)		{$hh='admin';	$sh='times';	$title = "Modify Call Time";}
if ($ADD==321111111)		{$hh='admin';	$sh='times';	$title = "Modify Call Time State Definitions List";}
if ($ADD==1000000000)		{$hh='admin';	$sh='times';	$title = "State Call Times";}
if ($ADD==1111111111)		{$hh='admin';	$sh='times';	$title = "Add New State Call Time";}
if ($ADD==2111111111)		{$hh='admin';	$sh='times';	$title = "New State Call Time Addition";}
if ($ADD==4111111111)		{$hh='admin';	$sh='times';	$title = "Modify State Call Time";}
if ($ADD==5111111111)		{$hh='admin';	$sh='times';	$title = "Delete State Call Time";}
if ($ADD==6111111111)		{$hh='admin';	$sh='times';	$title = "Delete State Call Time";}
if ($ADD==3111111111)		{$hh='admin';	$sh='times';	$title = "Modify State Call Time";}

# Admin - phones
if ($ADD==10000000000)		{$hh='admin';	$sh='phones';	$title = "PHONE LIST";}
if ($ADD==11111111111)		{$hh='admin';	$sh='phones';	$title = "ADD NEW PHONE";}
if ($ADD==21111111111)		{$hh='admin';	$sh='phones';	$title = "ADDING NEW PHONE";}
if ($ADD==41111111111)		{$hh='admin';	$sh='phones';	$title = "MODIFY PHONE";}
if ($ADD==51111111111)		{$hh='admin';	$sh='phones';	$title = "DELETE PHONE";}
if ($ADD==61111111111)		{$hh='admin';	$sh='phones';	$title = "DELETE PHONE";}
if ($ADD==31111111111)		{$hh='admin';	$sh='phones';	$title = "MODIFY PHONE";}

# Admin - extensions
if ($ADD=="10exten")		{$hh='admin';	$sh='extensions';	$title = "EXTENSION LIST";}
if ($ADD=="11exten")		{$hh='admin';	$sh='extensions';	$title = "ADD NEW EXTENSION";}
if ($ADD=="21exten")		{$hh='admin';	$sh='extensions';	$title = "ADDING NEW EXTENSION";}
if ($ADD=="41exten")		{$hh='admin';	$sh='extensions';	$title = "MODIFY EXTENSION";}
if ($ADD=="51exten")		{$hh='admin';	$sh='extensions';	$title = "DELETE EXTENSION";}
if ($ADD=="61exten")		{$hh='admin';	$sh='extensions';	$title = "DELETE EXTENSION";}
if ($ADD=="31exten")		{$hh='admin';	$sh='extensions';	$title = "MODIFY EXTENSION";}

# Admin - companies
if ($ADD=="10comp")		{$hh='admin';	$sh='company';	$title = "COMPANY LIST";}
if ($ADD=="11comp")		{$hh='admin';	$sh='company';	$title = "ADD NEW COMPANY";}
if ($ADD=="21comp")		{$hh='admin';	$sh='company';	$title = "ADDING NEW COMPANY";}
if ($ADD=="41comp")		{$hh='admin';	$sh='company';	$title = "MODIFY COMPANY";}
if ($ADD=="51comp")		{$hh='admin';	$sh='company';	$title = "DELETE COMPANY";}
if ($ADD=="61comp")		{$hh='admin';	$sh='company';	$title = "DELETE COMPANY";}
if ($ADD=="31comp")		{$hh='admin';	$sh='company';	$title = "MODIFY COMPANY";}

# Admin - Server
if ($ADD==100000000000)		{$hh='admin';	$sh='server';	$title = "SERVER LIST";}
if ($ADD==111111111111)		{$hh='admin';	$sh='server';	$title = "ADD NEW SERVER";}
if ($ADD==211111111111)		{$hh='admin';	$sh='server';	$title = "ADDING NEW SERVER";}
if ($ADD==221111111111)		{$hh='admin';	$sh='server';	$title = "ADDING NEW SERVER $t1 TRUNK RECORD";}
if ($ADD==411111111111)		{$hh='admin';	$sh='server';	$title = "MODIFY SERVER";}
if ($ADD==421111111111)		{$hh='admin';	$sh='server';	$title = "MODIFY SERVER $t1 TRUNK RECORD";}
if ($ADD==511111111111)		{$hh='admin';	$sh='server';	$title = "DELETE SERVER";}
if ($ADD==611111111111)		{$hh='admin';	$sh='server';	$title = "DELETE SERVER";}
if ($ADD==621111111111)		{$hh='admin';	$sh='server';	$title = "DELETE SERVER $t1 TRUNK RECORD";}
if ($ADD==311111111111)		{$hh='admin';	$sh='server';	$title = "MODIFY SERVER";}
if ($ADD==499111111111111)	{$hh='admin';	$sh='server';	$title = "MODIFY ARCHIVE SERVER SETTINGS";}
if ($ADD==499211111111111)	{$hh='admin';	$sh='server';	$title = "MODIFY QC SERVER SETTINGS";}
if ($ADD==499911111111111)	{$hh='admin';	$sh='server';	$title = "MODIFY DNC DATABASE SETTINGS";}
if ($ADD==699211111111111)	{$hh='admin';	$sh='server';	$title = "DELETE QC SERVER SETTINGS";}
if ($ADD==399111111111111)	{$hh='admin';	$sh='server';	$title = "MODIFY ARCHIVE SERVER SETTINGS";}
if ($ADD==399211111111111)	{$hh='admin';	$sh='server';	$title = "MODIFY QC SERVER SETTINGS";}
if ($ADD==399911111111111)	{$hh='admin';	$sh='server';	$title = "MODIFY DNC DATABASE SETTINGS";}

# Admin - conferences
if ($ADD==1000000000000)	{$hh='admin';	$sh='conference';	$title = "CONFERENCE LIST";}
if ($ADD==1111111111111)	{$hh='admin';	$sh='conference';	$title = "ADD NEW CONFERENCE";}
if ($ADD==2111111111111)	{$hh='admin';	$sh='conference';	$title = "ADDING NEW CONFERENCE";}
if ($ADD==4111111111111)	{$hh='admin';	$sh='conference';	$title = "MODIFY CONFERENCE";}
if ($ADD==5111111111111)	{$hh='admin';	$sh='conference';	$title = "DELETE CONFERENCE";}
if ($ADD==6111111111111)	{$hh='admin';	$sh='conference';	$title = "DELETE CONFERENCE";}
if ($ADD==3111111111111)	{$hh='admin';	$sh='conference';	$title = "MODIFY CONFERENCE";}
if ($ADD==10000000000000)	{$hh='admin';	$sh='conference';	$title = "$t1 CONFERENCE LIST";}
if ($ADD==11111111111111)	{$hh='admin';	$sh='conference';	$title = "ADD NEW $t1 CONFERENCE";}
if ($ADD==21111111111111)	{$hh='admin';	$sh='conference';	$title = "ADDING NEW $t1 CONFERENCE";}
if ($ADD==41111111111111)	{$hh='admin';	$sh='conference';	$title = "MODIFY $t1 CONFERENCE";}
if ($ADD==51111111111111)	{$hh='admin';	$sh='conference';	$title = "DELETE $t1 CONFERENCE";}
if ($ADD==61111111111111)	{$hh='admin';	$sh='conference';	$title = "DELETE $t1 CONFERENCE";}
if ($ADD==31111111111111)	{$hh='admin';	$sh='conference';	$title = "MODIFY $t1 CONFERENCE";}

# Remote/External/Auto Agents
if ($ADD==10000)		{$hh='admin';	$sh='remoteagent';	$title = "External Agents";}
if ($ADD==11111)		{$hh='admin';	$sh='remoteagent';	$title = "Add New External Agents";}
if ($ADD==21111)		{$hh='admin';	$sh='remoteagent';	$title = "New External Agents Addition";}
if ($ADD==41111)		{$hh='admin';	$sh='remoteagent';	$title = "Modify External Agents";}
if ($ADD==51111)		{$hh='admin';	$sh='remoteagent';	$title = "Delete External Agents";}
if ($ADD==61111)		{$hh='admin';	$sh='remoteagent';	$title = "Delete External Agents";}
if ($ADD==31111)		{$hh='admin';	$sh='remoteagent';	$title = "Modify External Agents";}

# Admin - settings
if ($ADD==411111111111111)	{$hh='admin';	$sh='settings';	$title = "MODIFY $t1 SYSTEM SETTINGS";}
if ($ADD==311111111111111)	{$hh='admin';	$sh='settings';	$title = "MODIFY $t1 SYSTEM SETTINGS";}

# admin - status
if ($ADD==221111111111111)	{$hh='admin';	$sh='status';	$title = "ADDING $t1 SYSTEM STATUSES";}
if ($ADD==231111111111111)	{$hh='admin';	$sh='status';	$title = "ADDING $t1 STATUS CATEGORY";}
if ($ADD==421111111111111)	{$hh='admin';	$sh='status';	$title = "MODIFY $t1 SYSTEM STATUSES";}
if ($ADD==431111111111111)	{$hh='admin';	$sh='status';	$title = "MODIFY $t1 STATUS CATEGORIES";}
if ($ADD==321111111111111)	{$hh='admin';	$sh='status';	$title = "MODIFY $t1 SYSTEM STATUSES";}
if ($ADD==331111111111111)	{$hh='admin';	$sh='status';	$title = "MODIFY $t1 STATUS CATEGORY";}

# Reports
if ($ADD==99999)		{$hh='reports';		$title = "HELP";}
if ($ADD==999999)		{$hh='reports';                     $title = "REPORTS";
    if ($SUB==1)		{$hh='users';       $sh='iframe';   $title = "REPORTS";}
    if ($SUB==2)		{$hh='campaigns';   $sh='iframe';   $title = "REPORTS";}
    if ($SUB==3)		{$hh='lists';       $sh='iframe';   $title = "REPORTS";}
    if ($SUB==4)		{$hh='scripts';     $sh='iframe';   $title = "REPORTS";}
    if ($SUB==5)		{$hh='filters';     $sh='iframe';   $title = "REPORTS";}
    if ($SUB==6)		{$hh='ingroups';    $sh='iframe';   $title = "REPORTS";}
    if ($SUB==7)		{$hh='usergroups';  $sh='iframe';   $title = "REPORTS";}
    if ($SUB==8)		{$hh='remoteagent'; $sh='iframe';   $title = "REPORTS";}
    if ($SUB==9)		{$hh='reports';     $sh='iframe';   $title = "REPORTS";}
    if ($SUB==10)		{$hh='admin';       $sh='iframe';   $title = "REPORTS";}
    if ($SUB==11)		{$hh='reports';     $sh='reports';  $title = "REPORTS - Real Time Summary"; $useOAC=1; }
    if ($SUB==12)		{$hh='reports';     $sh='reports';  $title = "REPORTS - Real Time Detail"; $useOAC=1; }
    if ($SUB==13)       {$hh='campaigns';   $sh='realtime'; $title = "REPORTS - Real Time Summary"; $useOAC=1; }
    if ($SUB==14)       {$hh='campaigns';   $sh='realtime'; $title = "REPORTS - Real Time Detail"; $useOAC=1; }
    if ($SUB==15)		{$hh='reports';     $sh='reports';  $title = "REPORTS - Call Stats";}
    if ($SUB==16)		{$hh='reports';     $sh='reports';  $title = "REPORTS - List Cost";}
    if ($SUB==17)		{$hh='reports';     $sh='reports';  $title = "REPORTS - List Performance and Analysis";}
}


$content = 'include/content/';
if ($hh == 'templates') {
    $content .= $sh . '/' . $ssh;
} else {
    $content .= $hh . '/';
    //if ($sh == '' || ($hh == 'campaigns' && ($sh == 'basic' || $sh == 'detail' || $sh == 'list'))) {
    if ($sh == '' || ($hh == 'campaigns' && ($sh == 'detail' || $sh == 'list'))) {
        $content .= $hh;
    } else {
        $content .= $sh;
    }
}
$content .= '.php';

?>
