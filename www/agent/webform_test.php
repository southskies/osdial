<?php
header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');
?>
<html>
<head><title>Lead #<?php echo $_GET['lead_id']; ?></title></head>
<body>
<br><br>
<center>
<h3>Lead #<?php echo $_GET['lead_id']; ?></h3>
<table>
<tr>
<th>Field</th>
<th>Value</th>
</tr>
<?php

foreach ($_GET as $k => $v) {
    echo "<tr>";
    echo "<td>$k</td>";
    echo "<td><input type=textbox size=40 value=\"$v\"></td>";
    echo "</tr>\n";
}

?>
</table>
</center>
</body>
</html>
