#!/bin/bash
#Created by F0ruD A <fzerorubigd@gmail.com>

# For test, first run this as ejabberd user. app/ejabberd folder and app/config/databases.xml
# must be readable by ejabberd user. 
# do not create log file, let program create that with proper permision.


# Absolute path to this script, e.g. /home/user/bin/foo.sh
SCRIPT=`readlink -f $0`
# Absolute path this script is in, thus /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`

php "$SCRIPTPATH/../app/ejabberd/Run.php"
