<?php
require_once('admin/include/dbconnect.php');
require_once('admin/include/functions.php');
require_once('admin/include/variables.php');
$template=$config['settings']['admin_template'];
if (empty($template)) $template='default';
require_once('admin/templates/' . $template . '/display.php');
$AdmVer=substr($admin_version,0,3);
$browser = getenv("HTTP_USER_AGENT");
if (!preg_match('/wget/i',$browser)) {
    echo "<html>\n";
    echo "<head>\n";
    echo "    <title>Choose Login:</title>\n";
    echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"admin/templates/$template/styles.css\" media=\"screen\">\n";
    echo "    <!-- <link rel=\"stylesheet\" type=\"text/css\" href=\"admin/templates/default/styles.css\" media=\"screen\"> -->\n";
    echo "</head>\n";
    echo "<body>\n";

    echo "    <!-- <table align=\"center\" frame=\"border\" align=\"center\" width=\"660\" height=\"500\" cellpadding=\"0\" cellspacing=\"0\" background=\"admin/templates/$template/images/Xosdial-bg.png\"> -->\n";
    echo "    <table align=\"center\" frame=\"border\" align=\"center\" width=\"660\" height=\"500\" cellpadding=\"0\" cellspacing=\"0\" class=\"homepagebg\">\n";
    echo "        <tr>\n";
    echo "            <td colspan=\"3\">\n";
		
    echo "                <table border=\"0\" align=\"center\" width=\"90%\" cellpadding=\"0\" cellspacing=\"0\">\n";
    echo "                    <tr>\n";
    echo "                        <td align=\"center\" colspan=\"2\" valign=\"top\" height=\"180\">\n";
    echo "                            <div id=\"company\" style=\"margin-top:35px;\"></div>\n";
    echo "                            <!--<script>\n";
    $c = $config['settings']['company_name'];
    if (strlen($c) < 22) {
        $fontclass='homepagecompany';
    } else {
        $fontclass='homepagecompanysmall';
    }
    echo "                            </script>-->\n";
    echo "                            <div class=\"$fontclass\">$c</div>\n";
    echo "                        </td>\n";
    echo "                    </tr>\n";
    echo "                    <tr valign=\"top\">\n";
    echo "                        <td align=\"center\" class=\"homepage\" width=\"50%\">\n";
    echo "                            <span><a href=\"agent\">Agent Login</a></span>\n";
    echo "                        </td>\n";
    echo "                        <td align=\"center\" class=\"homepage\">\n";
    echo "                            <span><a href=\"admin/admin.php?ADD=10\">Admin Login</a></span>\n";
    echo "                        </td>\n";
    echo "                    </tr>\n";
    echo "                </table>\n";
			
    echo "            </td>\n";
    echo "        </tr>\n";
    echo "        <tr height=\"50\"><td colspan=\"2\">&nbsp;</td></tr>\n";
    echo "        <tr>\n";
    echo "            <td class=\"homepagelogo\">\n";
    echo "                <a href=\"http://callcentersg.com/licenses.php\" target=\"_blank\" title=\"Click for License Information\"><img class=\"homepagelogo\" src=\"admin/templates/default/images/osdial-logo.gif\" height=\"100\"></a>\n";
    echo "            </td>\n";

    echo "            <td width=\"170\" align=\"right\" valign=\"bottom\">\n";
    echo "                <div class=\"homepagever\"><font style=\"font-size:18pt;\">V</font>$AdmVer</div>\n";
    echo "            </td>\n";
    echo "        </tr>\n";
    echo "    </table>\n";
} 

echo "</body>\n";
echo "</html>\n";


?>
