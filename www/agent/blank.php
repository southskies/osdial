<?php
header('Cache-Control: public, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() + 2592000)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');
?>
<html>
<head>
<title>Blank Page</title>
</head>
<body bgcolor=white>
</body>
</html>
