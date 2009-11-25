<?php ?>
<html>
<head><title>Lead #<?=$_GET['lead_id'] ?></title></head>
<body>
<br><br>
<center>
<h3>Lead #<?=$_GET['lead_id'] ?></h3>
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
