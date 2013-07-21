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
require_once("dbconnect.php");
require_once("functions.php");
require_once("variables.php");

$use_basic_auth=($config['settings']['use_old_admin_auth']*1);
$sess_login=get_variable('sess_login');

# If $osdial_skip_auth is not set, this script will attempt to use Basic authentication.
# If $osdial_skip_auth is set, it is assumed that the autorization is occuring elsewhere...like the API.
if (!isset($osdial_skip_auth)) {
    $failexit=0;
    $fps = "";
    $date = date("r");
    $ip = getenv("REMOTE_ADDR");
    $browser = getenv("HTTP_USER_AGENT");


    if ($force_logout) {
	    if($use_basic_auth and (OSDstrlen($PHP_AUTH_USER) > 0 or OSDstrlen($PHP_AUTH_PW) > 0)) {
		    Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
		    Header("HTTP/1.0 401 Unauthorized");
	        echo "<script language=\"javascript\">\n";
	        echo "window.location = '".$config['settings']['admin_home_url']."';\n";
	        echo "</script>\n";
        } else {

            # When logout is clicked, clear the session.
            unset($_SESSION[KEY]);
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            session_destroy();
            header( "location: ".$config['settings']['admin_home_url'] );
	    }
        $fps = "OSDIAL|FORCELOGOUT|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|||\n";
        $failexit=1;


    } else {

        # If not basic auth, test to see if we have auth yet, if not flag for login page.
        if ($use_base_auth==0) {
            if (!isset($_SESSION[KEY]['valid'])) {
	            if(OSDstrlen($PHP_AUTH_USER) > 0 or OSDstrlen($PHP_AUTH_PW) > 0) {
                    $sess_login=0;
                } else {
                    $sess_login=1;
                }
            }
        }

        # Basic Auth is really a one-step process, so we take care of it in the following condition.
        # For session based auth, we display the initial login page and EXIT.
        if ($use_basic_auth or $sess_login) {
            if ($use_basic_auth) {
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
                }
            } else {

                # This session variable holds any error strings we want to pass on to the user.
                $message = $_SESSION[KEY]['message'];
                unset($_SESSION[KEY]['message']);

                $jskf = "var jskf = function(evt) { var key; if (evt.keyCode) { key = evt.keyCode; } else if (typeof(e.which) != 'undefined') { key = evt.which; } if (key == 13) {document.getElementById('PHP_AUTH_PW').focus(); return false;}};";
                $jskd = "var jskd = function(evt) { var key; if (evt.keyCode) { key = evt.keyCode; } else if (typeof(e.which) != 'undefined') { key = evt.which; } if (key == 13) {document.osdial_login.submit(); return false;}};";
                # Session Based Login Form, The form must go to PHP_SELF and have a destination ADD.
                echo "<html>\n";
                echo "  <head>\n";
                echo "    <meta name=\"Copyright\" content=\"&copy; 2013 Lott Caskey\">\n";
                echo "    <meta name=\"Robots\" content=\"none\">\n";
                echo "    <meta name=\"Version\" content=\"SVN_Version/SVN_Build\">\n";
                echo "    <!-- VERSION: SVN_Version     BUILD: SVN_Build -->\n";
                echo "    <title>OSDial: Agent & Campaign Management - System Administration - Login</title>\n";
                echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"../agent/templates/default/styles.css\" media=\"screen\">\n";
                echo "  </head>\n";
                echo "  <body bgcolor=white name=osdial>\n";
                echo "    <form name=osdial_login id=osdial_login action=\"$PHP_SELF\" method=post autocomplete=off>\n";
                echo "      <input type=hidden name=ADD value=\"10\">\n";
                echo "      <input type=hidden name=SUB value=\"\">\n";
                echo "      <input type=hidden name=DB value=\"\">\n";
                echo "      <div class=containera>\n";
                echo "        <div class=acrosslogin2>\n";
                echo "          <table align=center width=500 cellpadding=0 cellspacing=0 border=0>\n";
                echo "            <tr><td align=center colspan=4>&nbsp;</td></tr>\n";
                echo "            <tr>\n";
                echo "              <td align=left>&nbsp;&nbsp;</td>\n";
                echo "              <td align=center colspan=2><font color=#1C4754><b>OSDial</b><br><br>- Agent & Campaign Management -<br>- System Administration -</font></td>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "            </tr>\n";
                echo "            <tr height='30px'><td align=left colspan=4><font size=1>&nbsp;</font></td></tr>\n";
                echo "            <tr>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "              <td align=right><font color=#1C4754>Username:&nbsp;</font></td>\n";
                echo "              <td align=left><input type=text id=PHP_AUTH_USER name=PHP_AUTH_USER size=20 maxlength=30></td>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "            </tr>\n";
                echo "            <rt>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "              <td align=right><font color=#1C4754>Password:&nbsp;</font></td>\n";
                echo "              <td align=left><input type=password id=PHP_AUTH_PW name=PHP_AUTH_PW size=20 maxlength=30></td>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "            </tr>\n";
                echo "            <tr height='60px'><td colspan=4><center><font size=2 color=red><b>$message</b></font></center></td></tr>\n";
                echo "            <tr>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "              <td align=center colspan=2><input class=submit type=button onclick=\"document.osdial_login.submit(); return false;\" name=SUBMIT value=Submit></td>\n";
                echo "              <td align=left><font size=1>&nbsp;</font></td>\n";
                echo "            </tr>\n";
                echo "            <tr>\n";
                echo "              <td align=right colspan=4 class=rbborder><font size=1><br>&nbsp;Version: SVN_Version</font>&nbsp;</td>\n";
                echo "            </tr>\n";
                echo "          </table>\n";
                echo "        </div>\n";
                echo "      </div>\n";
                echo "    </form>\n";
                echo "    <script type=\"text/javascript\">\n";
                echo "      $jskf\n";
                echo "      $jskd\n";
                echo "      document.getElementById('PHP_AUTH_USER').onkeydown=jskf;\n";
                echo "      document.getElementById('PHP_AUTH_PW').onkeydown=jskd;\n";
                echo "    </script>\n";
                echo "  </body>\n";
                echo "</html>\n";
                exit;
            }

        } else {

            if (!isset($_SESSION[KEY]['valid'])) {
                # This is Phase 2 of session based auth, we do not yet have auth need to perform the test with the received creds.
                $tmpLOG = osdial_authenticate($PHP_AUTH_USER, $PHP_AUTH_PW);

                if(OSDstrlen($PHP_AUTH_USER) < 2 or OSDstrlen($PHP_AUTH_PW) < 2 or $tmpLOG['error'] or $tmpLOG['user_level'] < 8) {
                    if ($tmpLOG['error']) {
                        # Indicate an auth engine error has occurred.
                        $_SESSION[KEY]['message'] = 'Auth Engine Reported: '.$tmpLOG['error'];
                    } elseif ($tmpLOG['user_level']<8) {
                        # They need higher permissions if they want in.
                        $_SESSION[KEY]['message'] = "We're sorry, users with a permissions level below 8 are not allowing in the management application.";
                    } else {
                        # Auth failed, reload login page,
                        $_SESSION[KEY]['message'] = "Authentication Failed!";
                    }
                    # This header line immediately causes the browser to loop back over to the login form.
                    header( "location: $PHP_SELF?sess_login=1" );

                } else {
                    # Login Success, WOOOO, now save some session data in the browser.
                    $_SESSION[KEY]['valid'] = 1;
                    $_SESSION[KEY]['last_update'] = time();
                    $_SESSION[KEY]['PHP_AUTH_USER'] = $PHP_AUTH_USER;
                    $_SESSION[KEY]['PHP_AUTH_PW'] = $PHP_AUTH_PW;
                    $_SESSION[KEY]['LOG'] = $tmpLOG;
                    $_SESSION[KEY]['LOG_time'] = time();
                    $LOG =& $_SESSION[KEY]['LOG'];
                }

            } else {

                if (time()>$_SESSION[KEY]['last_update']+600) {
                    # Expires session after 10 minutes of inactivity.
                    $_SESSION = array();
                    $_SESSION[KEY]['message'] = "Session Expired!";
                    # User is immediately redirected to the login page.
                    header( "location: $PHP_SELF?sess_login=1" );

                } else {

                    # This is the Success zone!!!  The account has auth, and it is not expired. Time for maintenance!
                    # Step1, Update the contents of LOG with a fresh auth if older than 60 seconds.
                    if (time()>$_SESSION[KEY]['LOG_time']+60) {
                        $_SESSION[KEY]['LOG'] = osdial_authenticate($_SESSION[KEY]['PHP_AUTH_USER'], $_SESSION[KEY]['PHP_AUTH_PW']);
                        $_SESSION[KEY]['LOG_time'] = time();
                    }
                    # Adjust our update times and make $LOG available to the program.
                    $LOG =& $_SESSION[KEY]['LOG'];
                    $_SESSION[KEY]['last_update'] = time();
                }

            }
        }
    }

    if ($failexit==0) $fps = "OSDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|" . $LOG['full_name'] . "|" . $LOG['allowed_campaignsSTR'] . "|\n";
    # Log error at end...
    if (!empty($fps) and $WeBRooTWritablE > 0) {
        $fp = fopen($config['PATHweb'] . "/admin/project_auth_entries.txt", "a");
        fwrite ($fp, $fps);
        fclose($fp);
    }
    if ($failexit==1) exit;
}




function osdial_authenticate($user, $password) {
    global $link;
    global $config;
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
        if ($config['settings']['enable_multicompany'] == 1 and file_exists($config['PATHweb'] . convert_uudecode("H+V%D;6EN+VEN8VQU9&4O8V]N=&5N=\"]A9&UI;B]C;VUP86YY+G!H<```\n`"))) {
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
                    if ($LOG['multicomp']>0) {
                        if ($LOG['company']['acct_method'] != 'NONE' and $LOG['company']['acct_method'] != '') {
                            if ($LOG['company']['status'] == 'SUSPENDED') {
                                $LOG['error']=0;
                                $LOG['error_type']='';
                            }
                        }
                    }
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
                    $oss = get_krh($link, 'osdial_scripts', 'script_id','',sprintf("script_id LIKE '%s__%%'",$LOG['company_prefix']),'');
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
                    $oets = get_krh($link, 'osdial_email_templates', 'et_id','',sprintf("et_id LIKE '%s__%%'",$LOG['company_prefix']),'');
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
