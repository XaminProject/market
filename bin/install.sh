#!/bin/bash

function detect(){
  type -P $1  || { echo "Require $1 but not installed. Aborting." >&2; exit 1; }
}

detect php
detect curl
## Mac readlink is old. so we can not use readlink
case $0 in
     /*) BIN_DIR=`dirname $0` ;;
     *) BIN_DIR=`pwd`/`dirname $0` ;;
esac

cd "$BIN_DIR"
## Find the real path using PHP (a little cheat :) ) 
BIN_DIR=`php -r "echo __DIR__;"`

## First things first, install composer
if [ ! -f $BIN_DIR/composer.phar ]; then
	echo "Installing Composer..."
	curl -s https://getcomposer.org/installer | php

    ## Composer install
	pushd $BIN_DIR/../
	php $BIN_DIR/composer.phar install
	popd
fi
## Check for pub folder, if there is leave it be if not create a new one
if [ ! -d "$BIN_DIR/../pub" ]; then
	mkdir "$BIN_DIR/../pub"
    ## Agavi public create
	pushd $BIN_DIR/../
	sh $BIN_DIR/agavi public-create
	popd
fi

echo "Add this aliases to use with this project : "
echo "xagavi='sh $BIN_DIR/agavi'"
echo "xcomposer='php $BIN_DIR/composer.phar'"
echo "All done"

