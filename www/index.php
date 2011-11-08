<?php
require_once('admin/include/dbconnect.php');
require_once('admin/include/functions.php');
require_once('admin/include/variables.php');
$template=$system_settings['admin_template'];
if (empty($template)) $template='default';
require_once('admin/templates/' . $template . '/display.php');
?>
<html>
<head><title>Choose Login:</title></head>
<body>
<?php
    $browser = getenv("HTTP_USER_AGENT");
    if (!preg_match('/wget/i',$browser)) {
?>

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
        var images = 'admin/templates/<?php echo $template; ?>/images/';
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
        var preload = new Image();
        var letcnt = 0;
        for (var l in phrase.split("")) {
            if (let[phrase[l]]) {
                if (phrase[l] == " " && letcnt > 14) {
                    osdhtml += '<br>';
                    lastkrn=0;
                    letcnt=0;
                } else {
                    preload.src = images + let[phrase[l]];
                    osdhtml += '<img style="margin-left: ' + lstkrn + 'px;" border="0" src="' + images + let[phrase[l]] + '">';
                    if (krnon)
                        lstkrn = krn[phrase[l]] - krnon;
                }
                letcnt++;
            }
        }
        document.getElementById(id).innerHTML = osdhtml;
    }
</script>
<?php } ?>
<br><br><br>

<table align=center border=0 align=center width=546 height=381 cellpadding=0 cellspacing=0 background="admin/templates/<?php echo $template; ?>/images/osdial-bg.png"> 
<tr>
    <td>
    
        <table border=0 align=center width=546 height=331 cellpadding=0 cellspacing=0>
        <tr>
            <td align=center colspan=2 valign=middle height=140>
                <div id="company"></div>
                <script>
<?php
        $c = $system_settings['company_name'];
        $klen = 2;
        if (strlen($c) < 20 or (strlen($c) >= 20 && preg_match('/............... /',$c))) {
                $klen = 1;
        }
        echo "osdfont('company','$c',$klen);\n";
?>
                </script>
                <!-- <img src=admin/templates/<?php echo $template; ?>/images/defaultCompany.png alt="Testing" width=298 height=30> -->
            </td>
        </tr>
        <tr valign=top>
            <td align=center>
                &nbsp;&nbsp;&nbsp;<map name="mapAL">
                    <a href=agent
                        OnMouseOver="agent.src='admin/templates/<?php echo $template; ?>/images/AgentLoginDn.png'" 
                        OnMouseOut="agent.src='admin/templates/<?php echo $template; ?>/images/AgentLoginUp.png'" 
                        usemap="#mapAL">
                    <img src="admin/templates/<?php echo $template; ?>/images/AgentLoginUp.png" WIDTH=150 HEIGHT=30 BORDER=0 NAME="agent"></A>
                </map>
            </td>
            <td align=center>
                <map name="mapCL">
                    <a href=admin/admin.php?ADD=10 
                        OnMouseOver="control.src='admin/templates/<?php echo $template; ?>/images/ManagerLoginDn.png'" 
                        OnMouseOut="control.src='admin/templates/<?php echo $template; ?>/images/ManagerLoginUp.png'" 
                        usemap="#mapCL">
                    <img src="admin/templates/<?php echo $template; ?>/images/ManagerLoginUp.png" WIDTH=190 HEIGHT=30 BORDER=0 NAME="control"></A>
                </map>
            </td>
        </tr>
        </table>
        
    </td>
</tr>
</table>

</body>
</html>
