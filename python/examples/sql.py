#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys
from osdial import OSDial

osdial = OSDial()

for var in osdial.vars:
    sys.stderr.write('%s  =  %s\n' % (var, osdial.vars[var]))

#sys.stderr.write('%s  =  %s\n' % ('PATHconf', osdial.sql.osdial.PATHconf))
#sys.stderr.write('%s  =  %s\n' % ('PATHconf', osdial.agi.osdial.PATHconf))

test = osdial.sql().execute("SELECT * FROM phones;")
rows = osdial.sql().fetchall()

sys.stderr.write('%s\n' % test)
for row in rows:
    sys.stderr.write('%s\n' % (row['dialplan_number']))

#agi = osdial.agi()
#agi.answer()
