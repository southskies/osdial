<?php
require('admin/include/display.php');
?>
<html>
<head><title>Choose Login:</title></head>
<body>
<script language="javascript">
	// OSDial font function.
	//   id = html id
	//   phrase = text to display
	//   krnon:
	//     true = use kerning
	//     false = turn kerning off
	//     numeric = positive or negative number to add to kerning
	function osdfont(id,phrase,krnon) {
		var let = new Array();
		var krn = new Array();
		var images = '/osdial/images/';
		if (krnon == undefined)
			krnon = 1;

		let['a'] = 'a.png'; let['b'] = 'b.png'; let['c'] = 'c.png'; let['d'] = 'd.png'; let['e'] = 'e.png'; let['f'] = 'f.png'; let['g'] = 'g.png'; let['h'] = 'h.png'; let['i'] = 'i.png';
		krn['a'] =      -9; krn['b'] =      -9; krn['c'] =      -9; krn['d'] =      -8; krn['e'] =      -9; krn['f'] =      -9; krn['g'] =      -8; krn['h'] =      -8; krn['i'] =      -9;
		let['j'] = 'j.png'; let['k'] = 'k.png'; let['l'] = 'l.png'; let['m'] = 'm.png'; let['n'] = 'n.png'; let['o'] = 'o.png'; let['p'] = 'p.png'; let['q'] = 'q.png'; let['r'] = 'r.png';
		krn['j'] =      -7; krn['k'] =      -9; krn['l'] =      -8; krn['m'] =      -8; krn['n'] =      -9; krn['o'] =      -8; krn['p'] =      -9; krn['q'] =      -8; krn['r'] =     -10;
		let['s'] = 's.png'; let['t'] = 't.png'; let['u'] = 'u.png'; let['v'] = 'v.png'; let['w'] = 'w.png'; let['x'] = 'x.png'; let['y'] = 'y.png'; let['z'] = 'z.png';
		krn['s'] =      -9; krn['t'] =      -8; krn['u'] =      -9; krn['v'] =     -10; krn['w'] =     -11; krn['x'] =     -10; krn['y'] =      -9; krn['z'] =      -8;

		let['A'] = 'A.png'; let['B'] = 'B.png'; let['C'] = 'C.png'; let['D'] = 'D.png'; let['E'] = 'E.png'; let['F'] = 'F.png'; let['G'] = 'G.png'; let['H'] = 'H.png'; let['I'] = 'I.png';
		krn['A'] =      -6; krn['B'] =      -9; krn['C'] =      -9; krn['D'] =      -8; krn['E'] =      -8; krn['F'] =      -9; krn['G'] =      -8; krn['H'] =      -7; krn['I'] =      -8;
		let['J'] = 'J.png'; let['K'] = 'K.png'; let['L'] = 'L.png'; let['M'] = 'M.png'; let['N'] = 'N.png'; let['O'] = 'O.png'; let['P'] = 'P.png'; let['Q'] = 'Q.png'; let['R'] = 'R.png';
		krn['J'] =      -8; krn['K'] =     -10; krn['L'] =      -9; krn['M'] =      -7; krn['N'] =      -8; krn['O'] =      -8; krn['P'] =      -9; krn['Q'] =      -9; krn['R'] =     -10;
		let['S'] = 'S.png'; let['T'] = 'T.png'; let['U'] = 'U.png'; let['V'] = 'V.png'; let['W'] = 'W.png'; let['X'] = 'X.png'; let['Y'] = 'Y.png'; let['Z'] = 'Z.png';
		krn['S'] =     -10; krn['T'] =     -10; krn['U'] =      -9; krn['V'] =     -10; krn['W'] =     -11; krn['X'] =     -11; krn['Y'] =     -11; krn['Z'] =      -8;

		let['0'] = '0.png'; let['1'] = '1.png'; let['2'] = '2.png'; let['3'] = '3.png'; let['4'] = '4.png'; let['5'] = '5.png'; let['6'] = '6.png'; let['7'] = '7.png'; let['8'] = '8.png'; let['9'] = '9.png';
		krn['0'] =      -9; krn['1'] =      -9; krn['2'] =      -9; krn['3'] =      -9; krn['4'] =      -9; krn['5'] =      -9; krn['6'] =      -9; krn['7'] =      -9; krn['8'] =      -9; krn['9'] =      -9;

		let['&'] = 'ampersand.png'; let['.'] = 'period.png'; let['@'] = 'at.png'; let['!'] = 'exclamation.png'; let['-'] = 'hyphen.png'; let[' '] = 'space.png'; let[','] = 'comma.png'; let[':'] = 'colon.png';
		krn['&'] =              -9; krn['.'] =           -9; krn['@'] =       -9; krn['!'] =                -7; krn['-'] =           -9; krn[' '] =          -7; krn[','] =          -7; krn[':'] =          -7;

		var osdhtml = '';
		var lstkrn = 0;
		for (var l in phrase.split("")) {
			if (let[phrase[l]]) {
				osdhtml += '<img style="margin-left: ' + lstkrn + 'px;" border="0" src="' + images + let[phrase[l]] + '">';
				if (krnon)
					lstkrn = krn[phrase[l]] - krnon;
			}
		}
		document.getElementById(id).innerHTML = osdhtml;
	}
</script>
<br><br><br>

<table align=center border=0 align=center width=518 height=368 background=/osdial/images/osdial-bg.png>
<tr>
	<td valign=top>
	
		<table border=0 align=center>
		<tr>
			<td height=180 width=360 align=center valign=middle colspan=2>
				<div id="company"></div>
				<script>
                    comp = '<?= $user_company ?>';
                    klen = 1;
                    if (comp.length >= 20)
                        klen = 2;
					osdfont('company',comp,klen);
				</script>
				<!-- <img src=/osdial/images/defaultCompany.png alt="Testing" width=298 height=30> -->
			</td>
		</tr>
		<tr>
			<td align=center>
				&nbsp;&nbsp;&nbsp;<map name="mapAL">
					<a href=/osdial/agent
						OnMouseOver="agent.src='/osdial/images/AgentLoginDn.png'" 
						OnMouseOut="agent.src='/osdial/images/AgentLoginUp.png'" 
						usemap="#mapAL">
					<img src="/osdial/images/AgentLoginUp.png" width=117 height=26 BORDER=0 NAME="agent"></A>
				</map>
			</td>
			<td align=center>
				<map name="mapCL">
					<a href=/osdial/admin/admin.php?ADD=10 
						OnMouseOver="control.src='/osdial/images/ControlLoginDn.png'" 
						OnMouseOut="control.src='/osdial/images/ControlLoginUp.png'" 
						usemap="#mapCL">
					<img src="/osdial/images/ControlLoginUp.png" width=129 height=26 BORDER=0 NAME="control"></A>&nbsp;&nbsp;&nbsp;
				</map>
			</td>
		</tr>
		</table>
		
	</td>
</tr>
</table>

</body>
</html>
