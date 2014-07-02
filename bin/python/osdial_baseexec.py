#!/usr/bin/python
"""

Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>

osdial_baseexec - A program stub which imports a python module by
                  the name of "baseexec" and starts the imported
                  program by calling user_main from that module.
                  Python automatically compiles all imported modules
                  into bytecode, hence the reason for this stub.
"""
import os, sys, re
if __name__ == '__main__':
    sys.path.insert(0, '%s/python' % os.path.dirname(os.path.realpath(__file__)))
    try:
        libname = re.sub('^osdial_','',os.path.basename(__file__))
        thislib = __import__(libname)
        thislib.user_main(sys.argv[1:])
    except KeyboardInterrupt, e:
        print >> sys.stderr, "\n\nExiting on user cancel."
        sys.exit(1)
