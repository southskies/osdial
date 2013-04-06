<?php
require_once('admin/include/dbconnect.php');
require_once('admin/include/functions.php');
require_once('admin/include/variables.php');
$template=$config['settings']['admin_template'];
if (empty($template)) $template='default';
require_once('admin/templates/' . $template . '/display.php');

?>
<html>
<head>
	<title>Choose Login:</title>
<!-- 	<link rel="stylesheet" type="text/css" href="admin/templates/<?php echo $template; ?>/styles.css" media="screen"> -->
	<link rel="stylesheet" type="text/css" href="admin/templates/default/styles.css" media="screen">

</head>
<body>
<?php
    $browser = getenv("HTTP_USER_AGENT");
    if (!preg_match('/wget/i',$browser)) {
?>


<?php } ?>


<!-- <table align=center frame=border align=center width=660 height=500 cellpadding=0 cellspacing=0 background="admin/templates/<?php echo $template; ?>/images/Xosdial-bg.png"> -->
<table align=center frame=border align=center width=660 height=500 cellpadding=0 cellspacing=0 class="homepagebg"> 
	<tr>
		<td colspan=3>
		
			<table border=0 align=center width=90% cellpadding=0 cellspacing=0>
			<tr>
				<td align=center colspan=2 valign=top height=180>
					<div id="company" style='margin-top:35px;'></div>
						<!--<script>
							<?php
							$c = $config['settings']['company_name'];
							$klen = 2;
 							if (strlen($c) < 20 or (strlen($c) >= 20 && preg_match('/............... /',$c))) {
 									$klen = 1;
 							}
							echo "osdfont('company','$c',$klen);\n";
							?>
						</script>-->
					
					<?php echo "<div class=homepagecompany>$c</div>"; ?>
				</td>
			</tr>
			<tr valign=top>
				<td align=center class=homepage width=50%><?php $c ?>
					<span><a href=agent>Agent Login</a></span>
				</td>
				<td align=center class=homepage>
					<span><a href=admin/admin.php?ADD=10>Admin Login</a></span>
				</td>
			</tr>
			</table>
			
		</td>
	</tr>
	<tr height=50><td colspan=2>&nbsp;</td></tr>
	<tr>
		<td>
			<img class=homepagelogo src='admin/templates/default/images/osdial-logo-remake-150.gif' height=100>
		</td>

		<td width=170 align=right valign=bottom>
			<div class=homepagever><font style='font-size:18pt;'>V</font>3.0</div>
		</td>
	</tr>
</table>

</body>
</html>
