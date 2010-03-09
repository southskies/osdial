<?php
#
# Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

# Complete rewrite of VD authorization.

# NEW LOG['user_level'] style.
$LOG = Array();

$failexit=0;
$fps = "";
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");


if ($force_logout) {
	if(strlen($PHP_AUTH_USER) > 0 or strlen($PHP_AUTH_PW) > 0) {
		Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
	}
	echo "<script language=\"javascript\">\n";
	echo "window.location = '$system_settings[admin_home_url]';\n";
	echo "</script>\n";
    $fps = "OSDIAL|FORCELOGOUT|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
    $failexit=1;

} else {
    # Get the authed user.
    $LOG = get_first_record($link, 'osdial_users', '*', sprintf("user='%s' and pass='%s'",mres($PHP_AUTH_USER),mres($PHP_AUTH_PW)) );
    $auth=count($LOG);

    if(strlen($PHP_AUTH_USER) < 2 or strlen($PHP_AUTH_PW) < 2 or $auth < 1 and $LOG['user_level'] > 7) {
        Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
        Header("HTTP/1.0 401 Unauthorized");
        if (!preg_match('/wget/i',$browser)) $fps = "OSDIAL|BADAUTH|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
        $failexit=1;
    } elseif($auth > 0) {
        # And array of the allowed campagins
        $LOGacA = Array();
        $LOGagA = Array();
        $LOGaiA = Array();
        # allowed_campaigns in SQL form
        $LOGacSQL = '';
        $LOGagSQL = '';
        $LOGaiSQL = '';

        # Setup multicomp
        $LOG['multicomp'] = 0;
        $LOG['multicomp_admin'] = 0;
        $LOG['multicomp_user'] = 0;
        $LOG['company_prefix'] = '';
        $LOG['company_id'] = 0;
        if ($system_settings['enable_multicompany'] == 1) {
            $LOG['multicomp'] = 1;
            if ($system_settings['multicompany_admin'] == $LOG['user']) {
                $LOG['multicomp_admin'] = 1;
            } elseif (strlen($LOG['user']) > 5) {
                $LOG['company_prefix'] = substr($LOG['user'],0,3);
                $LOG['company_id'] = ((substr($LOG['user'],0,3) * 1) - 100);
                $LOG['multicomp_user'] = 1;
            }
            $LOG['companies'] = get_krh($link, 'osdial_companies', '*','','','');
            $LOG['companiesRE'] = '/';
            foreach ($LOG['companies'] as $comp) {
                $LOG['companiesRE'] .= '^' . (($comp['id'] * 1) + 100) . '|';
            }
            $LOG['companiesRE'] = rtrim($LOG['companiesRE'],'|') . '/';

            $LOG['company'] = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",mres($LOG['company_id'])) );
            # Set banner title.
            $user_company = $LOG['company']['name'];
        }

        # Get the allowed campaigns.
        $ug = get_first_record($link, 'osdial_user_groups', '*', sprintf("user_group='%s'",mres($LOG['user_group'])) );

        $LOGac = $ug['allowed_campaigns'];
        if (strlen($LOGac)> 1) {
            if (preg_match('/\-ALL\-CAMPAIGNS\-/',$LOGac)) {
                $LOG['allowed_campaignsALL'] = 1;
                # Pack all the valid UGs
                $ugs = get_krh($link, 'osdial_user_groups', 'user_group','',sprintf("user_group LIKE '%s___%%'",$LOG['company_prefix']),'');
                foreach ($ugs as $ugg) {
                    $LOGagA[] = $ugg['user_group'];
                }
                # Now go and get all campaigns...
                $camps = get_krh($link, 'osdial_campaigns', 'campaign_id','',sprintf("campaign_id LIKE '%s___%%'",$LOG['company_prefix']),'');
                foreach ($camps as $camp) {
                    $LOGacA[] = $camp['campaign_id'];
                }
            } else {
                $LOG['allowed_campaignsALL'] = 0;
                $LOGagA[] = $LOG['user_group'];
                foreach (explode(' ',$LOGac) as $c) {
                    if (strlen(rtrim($c,'-')) > 0) $LOGacA[] = $c;
                }
            }
            $ingrps = get_krh($link, 'osdial_inbound_groups', 'group_id','',sprintf("group_id LIKE '%s___%%'",$LOG['company_prefix']),'');
            foreach ($ingrps as $ingrp) {
               $LOGaiA[] = $ingrp['group_id'];
            }
            foreach ($LOGacA as $c) {
                $LOGacSQL .= "'" . mres($c) . "',";
            }
            $LOGacSQL = '(' . rtrim($LOGacSQL, ',') . ')';
            foreach ($LOGagA as $g) {
                $LOGagSQL .= "'" . mres($g) . "',";
            }
            $LOGagSQL = '(' . rtrim($LOGagSQL, ',') . ')';
            foreach ($LOGaiA as $i) {
                $LOGaiSQL .= "'" . mres($i) . "',";
            }
            $LOGaiSQL = '(' . rtrim($LOGaiSQL, ',') . ')';
        }

        # Array of allowed campaigns for user.
        $LOG['allowed_campaigns'] = $LOGacA;
        $LOG['allowed_usergroups'] = $LOGagA;
        $LOG['allowed_ingroups'] = $LOGaiA;

        # Joined string (:) of allowed campaigns for user.
        $LOG['allowed_campaignsSTR'] = ":" . implode(":",$LOGacA) . ":";
        $LOG['allowed_usergroupsSTR'] = ":" . implode(":",$LOGagA) . ":";
        $LOG['allowed_ingroupsSTR'] = ":" . implode(":",$LOGaiA) . ":";

        # A SQL format you might use with an IN.  ie "('C1','C2','...')"
        $LOG['allowed_campaignsSQL'] = $LOGacSQL;
        $LOG['allowed_usergroupsSQL'] = $LOGagSQL;
        $LOG['allowed_ingroupsSQL'] = $LOGaiSQL;



        # Finally for historical and compaitibility reasons, we will map them out to straight vars, ie $LOGuser_level instead of $LOG['user_level']
        foreach ($LOG as $k => $v ) {
            if ($k != "") eval("\$LOG" . $k . " = \$LOG['" . $k . "'];");
        }
        flush();
        $LOGallowed_campaigns = $LOG['allowed_campaignsSTR'];

        $fps = "OSDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|" . $LOG['full_name'] . "|" . $LOG['allowed_campaignsSTR'] . "|\n";

    } else {
        $fps = "OSDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
    }
}

# Log error at end...
if ($fps != "" and $WeBRooTWritablE > 0) {
    $fp = fopen($WeBServeRRooT . "/admin/project_auth_entries.txt", "a");
    fwrite ($fp, $fps);
    fclose($fp);
}
if ($failexit==1) exit;

?>
