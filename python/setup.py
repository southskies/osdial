#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

from distutils.core import setup

try :
    from osdial._version  import VERSION
except :
    VERSION = None

licenses = ( 'Python Software Foundation License'
    , 'GNU Library or Lesser General Public License (LGPL)'
    )

setup \
    ( name = 'osdial'
    , version = VERSION
    , description = 'A Python Interface to OSDial'
    , author = 'Lott Caskey'
    , author_email = 'lottcaskey@gmail.com'
    , maintainer = 'Lott Caskey'
    , maintainer_email = 'lottcaskey@gmail.com'
    , url = 'http://www.osdial.com/'
    , packages = ['osdial']
    , license = ', '.join (licenses)
    , platforms = 'Any'
    )
