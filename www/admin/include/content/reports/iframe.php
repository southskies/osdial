<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

 ?>

<script language="JavaScript">
<!--
function iFrameAutoResize(id){
    var newheight;
    if(document.getElementById(id)) {
        newheight=document.getElementById(id).contentWindow.document.height-20;
        document.getElementById(id).height=(newheight) + "px";
    }
}
//-->
</script>

<center>
<div style="overflow-x:hidden;overflow-y:scroll;height:520px;width:100%;float:right;" onscroll="iFrameAutoResize('mwiframe');">
<iframe src="<?php echo $iframe ?>" frameborder="0" width="100%" height="600px" marginwidth="0" marginheight="0" seamless="seamless" scrolling="no" id="mwiframe" onload="iFrameAutoResize('mwiframe');"></iframe>
</div>
</center>
