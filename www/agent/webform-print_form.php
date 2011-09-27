<?php

require_once("dbconnect.php");
require_once("functions.php");

# Get fields from GET/POST.
$fields = Array();
foreach ($_GET as $k => $v) {
    $fields[$k] = $v;
}
foreach ($_POST as $k => $v) {
    $fields[$k] = $v;
}

# Some additional formatting.
$fields['DATE'] = date('m-d-Y');
$fields['date_of_birth'] = OSDsubstr($fields['date_of_birth'],5) . '-' . OSDsubstr($fields['date_of_birth'],0,4);
$fields['phone_number'] = OSDsubstr($fields['phone_number'],0,3) . '-' . OSDsubstr($fields['phone_number'],3,3) . '-' . OSDsubstr($fields['phone_number'],6,4);
$fields['alt_phone'] = OSDsubstr($fields['alt_phone'],0,3) . '-' . OSDsubstr($fields['alt_phone'],3,3) . '-' . OSDsubstr($fields['alt_phone'],6,4);


# Set the print flag if statuses is blank or dispo is in statuses.
$print = 0;
if (!isset($fields['statuses']) or empty($fields['statuses'])) {
    $print = 0;
} else {
    foreach(explode(",",$fields['statuses']) as $status) {
        if ($status == $fields['dispo']) {
            $print = 1;
        }
    }
}

# if template is blank or not found, add some dummy html and turn
# printing off, else open file and do substitution.
$html="";
if (!isset($fields['template']) or empty($fields['template']) or !file_exists($fields['template'])) {
    $print = 0;
    $html .= "<html>\n";
    $html .= "<body>\n";
    $html .= "<b>Error, template is not set or is not found<b>\n";
    $html .= "</body>\n";
    $html .= "</html>\n";
} else {
    $htmlfile = file($fields['template']);
    foreach ($htmlfile as $htmlline) {
        $html .= $htmlline;
    }
    foreach ($fields as $k => $v) {
	if (empty($v)) {
		$v = "&nbsp;";
	}
        $html = OSDpreg_replace('/\[\[' . $k . '\]\]/',$v,$html);
    }
    $html = OSDpreg_replace('/\[\[[a-z0-9](.*)\]\]/iU','&nbsp;',$html);
    #$html = OSDpreg_replace('/\[\[\S(.*)\]\]/i','&nbsp;',$html);
}



# Add script to close and/or print window automatically.
$script = "\n\n<script language=\"javascript\">\n";
if ($print) {
	$script .= "window.print();\n";
}
#$script .= "window.close();\n";
$script .= "</script>\n\n";
$script .= "</body>\n";
$html = OSDpreg_replace('/\<\/body\>/',$script,$html);



# Display html and exit...
echo $html;
exit;

?>
