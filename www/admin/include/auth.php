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

# If $osdial_skip_auth is not set, this script will attempt to use Basic authentication.
# If $osdial_skip_auth is set, it is assumed that the autorization is occuring elsewhere...like the API.
if (!isset($osdial_skip_auth)) {
    $failexit=0;
    $fps = "";
    $date = date("r");
    $ip = getenv("REMOTE_ADDR");
    $browser = getenv("HTTP_USER_AGENT");

    if ($force_logout) {
        $_SESSION = array();
        foreach ($_COOKIE as $k => $v) {
            setcookie($k,'',time()-42000);
        }
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();

	    if(OSDstrlen($PHP_AUTH_USER) > 0 or OSDstrlen($PHP_AUTH_PW) > 0) {
		    Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
		    Header("HTTP/1.0 401 Unauthorized");
	    }
	    echo "<script language=\"javascript\">\n";
	    echo "window.location = '$config[settings][admin_home_url]';\n";
	    echo "</script>\n";
        $fps = "OSDIAL|FORCELOGOUT|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
        $failexit=1;


    } else {
        $LOG = osdial_authenticate($PHP_AUTH_USER, $PHP_AUTH_PW);
        if(OSDstrlen($PHP_AUTH_USER) < 2 or OSDstrlen($PHP_AUTH_PW) < 2 or $LOG['error'] or $LOG['user_level'] < 8) {
            Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
            Header("HTTP/1.0 401 Unauthorized");
            if (!OSDpreg_match('/wget/i',$browser)) $fps = "OSDIAL|BADAUTH|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
            $failexit=1;
            if ($LOG['error']) {
                if ($LOG['error_type'] == 'COMPANY_NOT_ACTIVE') {
                    $fps = "OSDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|COMPANY_" . $LOG['company']['status'] . "||\n";
                    echo "<a href=\"$PHP_SELF?force_logout=1\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>Logout</a></font><br><br>\n";
                    echo "<font color=red>Error, Company is currently marked " . $LOG['company']['status'] . ".</font>";
                } elseif ($LOG['error_type'] == 'LOGIN_FAILED') {
                    $fps = "OSDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
                }
            }
        } else {
            # Login Success, save some session data in the browser.
            $tsid = session_id();
            if(empty($tsid)) session_start();
        }
    }

    if ($failexit==0) $fps = "OSDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|" . $LOG['full_name'] . "|" . $LOG['allowed_campaignsSTR'] . "|\n";
    # Log error at end...
    if (!empty($fps) and $WeBRooTWritablE > 0) {
        $fp = fopen($WeBServeRRooT . "/admin/project_auth_entries.txt", "a");
        fwrite ($fp, $fps);
        fclose($fp);
    }
    if ($failexit==1) exit;
}




function osdial_authenticate($user, $password) {
    global $link;
    $LOG = get_first_record($link, 'osdial_users', '*', sprintf("user='%s' AND pass='%s'",mres($user),mres($password)) );
    $auth=count($LOG);
    $LOG['error'] = 0;
    if ($auth > 0) {
        # And array of the allowed campagins
        $LOGacA = Array();
        $LOGagA = Array();
        $LOGaiA = Array();
        $LOGasA = Array();
        $LOGaeA = Array();
        # allowed_campaigns in SQL form
        $LOGacSQL = "'',";
        $LOGagSQL = "'',";
        $LOGaiSQL = "'',";
        $LOGasSQL = "'',";
        $LOGaeSQL = "'',";

        # Setup multicomp
        $LOG['multicomp'] = 0;
        $LOG['multicomp_admin'] = 0;
        $LOG['multicomp_user'] = 0;
        $LOG['company_prefix'] = '';
        $LOG['company_id'] = 0;
        if ($config['settings']['enable_multicompany'] == 1 and file_exists($WeBServeRRooT . convert_uudecode("H+V%D;6EN+VEN8VQU9&4O8V]N=&5N=\"]A9&UI;B]C;VUP86YY+G!H<```\n`"))) {
            $LOG['multicomp'] = 1;
            if ($config['settings']['multicompany_admin'] == $LOG['user']) {
                $LOG['multicomp_admin'] = 1;
            } elseif (OSDstrlen($LOG['user']) > 5) {
                $LOG['company_prefix'] = OSDsubstr($LOG['user'],0,3);
                $LOG['company_id'] = ((OSDsubstr($LOG['user'],0,3) * 1) - 100);
                $LOG['multicomp_user'] = 1;
                $LOG['company'] = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",mres($LOG['company_id'])) );
                # Set banner title.
                $config['settings']['company_name'] = $LOG['company']['name'];
                if ($LOG['company']['status'] != 'ACTIVE') {
                    $LOG['error']=1;
                    $LOG['error_type']='COMPANY_NOT_ACTIVE';
                }
            }
            $LOG['companies'] = get_krh($link, 'osdial_companies', '*','','','');
            $LOG['companiesRE'] = '/';
            if (is_array($LOG['companies'])) {
                foreach ($LOG['companies'] as $comp) {
                    $LOG['companiesRE'] .= '^' . (($comp['id'] * 1) + 100) . '|';
                }
            }
            $LOG['companiesRE'] = rtrim($LOG['companiesRE'],'|') . '/';
        }

        # Get the allowed campaigns.
        $ug = get_first_record($link, 'osdial_user_groups', '*', sprintf("user_group='%s'",mres($LOG['user_group'])) );
        if (is_array($ug)) {
            foreach ($ug as $k => $v) {
                if (OSDpreg_match('/^view|^export/',$k)) $LOG[$k] = $v;
            }
        }

        $LOGac = $ug['allowed_campaigns'];
        $LOGas = $ug['allowed_scripts'];
        $LOGae = $ug['allowed_email_templates'];
        $LOGai = $ug['allowed_ingroups'];
        if (OSDstrlen($LOGac)> 1) {
            if (OSDpreg_match('/\-ALL\-CAMPAIGNS\-/',$LOGac)) {
                $LOG['allowed_campaignsALL'] = 1;
                # Pack all the valid UGs
                $ugs = get_krh($link, 'osdial_user_groups', 'user_group','',sprintf("user_group LIKE '%s__%%'",$LOG['company_prefix']),'');
                if (is_array($ugs)) {
                    foreach ($ugs as $ugg) {
                        $LOGagA[] = $ugg['user_group'];
                    }
                }
                # Now go and get all campaigns...
                $camps = get_krh($link, 'osdial_campaigns', 'campaign_id','',sprintf("campaign_id LIKE '%s__%%'",$LOG['company_prefix']),'');
                if (is_array($camps)) {
                    foreach ($camps as $camp) {
                        $LOGacA[] = $camp['campaign_id'];
                    }
                }
                if ($LOG['user_level'] > 8 and $LOG['multicomp'] == 0) {
                    $LOGacA[] = 'PBX-IN';
                    $LOGacA[] = 'PBX-OUT';
                }
            } else {
                $LOG['allowed_campaignsALL'] = 0;
                $LOGagA[] = $LOG['user_group'];
                $tmpacs = explode(' ',$LOGac);
                if (is_array($tmpacs)) {
                    foreach ($tmpacs as $c) {
                        if (OSDstrlen(rtrim($c,'-')) > 0) $LOGacA[] = $c;
                    }
                }
            }
            if (OSDstrlen($LOGai)> 1) {
                if (OSDpreg_match('/\-ALL\-INGROUPS\-/',$LOGai)) {
                    $LOG['allowed_ingroupsALL'] = 1;
                    # Pack all the valid InGroups
                    $ois = get_krh($link, 'osdial_inbound_groups', 'group_id','',sprintf("group_id LIKE '%s__%%'",$LOG['company_prefix']),'');
                    if (is_array($ois)) {
                        foreach ($ois as $oi) {
                            $LOGaiA[] = $oi['group_id'];
                        }
                    }
                } else {
                    $LOG['allowed_ingroupsALL'] = 0;
                    $tmpais = explode(' ',$LOGai);
                    if (is_array($tmpais)) {
                        foreach ($tmpais as $i) {
                            if (OSDstrlen(rtrim($i,'-')) > 0) $LOGaiA[] = $i;
                        }
                    }
                }
            }
            if (OSDstrlen($LOGas)> 1) {
                if (OSDpreg_match('/\-ALL\-SCRIPTS\-/',$LOGas)) {
                    $LOG['allowed_scriptsALL'] = 1;
                    # Pack all the valid Scripts
                    $oss = get_krh($link, 'osdial_scripts', 'script_id','','','');
                    if (is_array($oss)) {
                        foreach ($oss as $os) {
                            $LOGasA[] = $os['script_id'];
                        }
                    }
                } else {
                    $LOG['allowed_scriptsALL'] = 0;
                    $tmpass = explode(' ',$LOGas);
                    if (is_array($tmpass)) {
                        foreach ($tmpass as $s) {
                            if (OSDstrlen(rtrim($s,'-')) > 0) $LOGasA[] = $s;
                        }
                    }
                }
            }
            if (OSDstrlen($LOGae)> 1) {
                if (OSDpreg_match('/\-ALL\-EMAIL\-TEMPLATES\-/',$LOGae)) {
                    $LOG['allowed_email_templatesALL'] = 1;
                    # Pack all the valid Scripts
                    $oets = get_krh($link, 'osdial_email_templates', 'et_id','','','');
                    if (is_array($oets)) {
                        foreach ($oets as $oet) {
                            $LOGaeA[] = $oet['et_id'];
                        }
                    }
                } else {
                    $LOG['allowed_email_templatesALL'] = 0;
                    $tmpaes = explode(' ',$LOGae);
                    if (is_array($tmpaes)) {
                        foreach ($tmpaes as $e) {
                            if (OSDstrlen(rtrim($e,'-')) > 0) $LOGaeA[] = $e;
                        }
                    }
                }
            }

            # Allowed Campaigns SQL
            if (is_array($LOGacA)) {
                foreach ($LOGacA as $c) {
                    $LOGacSQL .= "'" . mres($c) . "',";
                }
            }
            $LOGacSQL = '(' . rtrim($LOGacSQL, ',') . ')';
            # Allowed Usergroup SQL
            if (is_array($LOGagA)) {
                foreach ($LOGagA as $g) {
                    $LOGagSQL .= "'" . mres($g) . "',";
                }
            }
            $LOGagSQL = '(' . rtrim($LOGagSQL, ',') . ')';
            # Allowed InGroups SQL
            if (is_array($LOGaiA)) {
                foreach ($LOGaiA as $i) {
                    $LOGaiSQL .= "'" . mres($i) . "',";
                }
            }
            $LOGaiSQL = '(' . rtrim($LOGaiSQL, ',') . ')';
            # Allowed Scripts SQL
            if (is_array($LOGasA)) {
                foreach ($LOGasA as $s) {
                    $LOGasSQL .= "'" . mres($s) . "',";
                }
            }
            $LOGasSQL = '(' . rtrim($LOGasSQL, ',') . ')';
            # Allowed Email Templates SQL
            if (is_array($LOGaeA)) {
                foreach ($LOGaeA as $e) {
                    $LOGaeSQL .= "'" . mres($e) . "',";
                }
            }
            $LOGaeSQL = '(' . rtrim($LOGaeSQL, ',') . ')';
        }

        # Array of allowed campaigns for user.
        $LOG['allowed_campaigns'] = $LOGacA;
        $LOG['allowed_usergroups'] = $LOGagA;
        $LOG['allowed_ingroups'] = $LOGaiA;
        $LOG['allowed_scripts'] = $LOGasA;
        $LOG['allowed_email_templates'] = $LOGaeA;

        # Joined string (:) of allowed campaigns for user.
        $LOG['allowed_campaignsSTR'] = ":" . implode(":",$LOGacA) . ":";
        $LOG['allowed_usergroupsSTR'] = ":" . implode(":",$LOGagA) . ":";
        $LOG['allowed_ingroupsSTR'] = ":" . implode(":",$LOGaiA) . ":";
        $LOG['allowed_scriptsSTR'] = ":" . implode(":",$LOGasA) . ":";
        $LOG['allowed_email_templatesSTR'] = ":" . implode(":",$LOGaeA) . ":";

        # A SQL format you might use with an IN.  ie "('C1','C2','...')"
        $LOG['allowed_campaignsSQL'] = $LOGacSQL;
        $LOG['allowed_usergroupsSQL'] = $LOGagSQL;
        $LOG['allowed_ingroupsSQL'] = $LOGaiSQL;
        $LOG['allowed_scriptsSQL'] = $LOGasSQL;
        $LOG['allowed_email_templatesSQL'] = $LOGaeSQL;
    } else {
        $LOG['error']=1;
        $LOG['error_type']='LOGIN_FAILED';
    }
    return $LOG;
}


?>
