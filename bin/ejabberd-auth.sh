#!/bin/bash
#Created by F0ruD A <fzerorubigd@gmail.com>

# For test, first run this as ejabberd user. app/ejabberd folder and app/config/databases.xml
# must be readable by ejabberd user. 
# do not create log file, let program create that with proper permision.

# Absolute path to this script, e.g. /home/user/bin/foo.sh
## Mac readlink is old. so we can not use readlink
pushd "$(dirname "$0")" > /dev/null
# Absolute path this script is in, thus /home/user/bin
## Find the real path using PHP (a little cheat :) ) 
SCRIPTPATH=`php -r "echo __DIR__;"`

php "$SCRIPTPATH/../app/ejabberd/Run.php"
popd > /dev/null
