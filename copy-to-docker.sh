#!/bin/bash
currentDirectory=`pwd`
dockerDirectory=$currentDirectory/docker
if [ ! -d $dockerDirectory ]; then
    echo "Cannot find docker directory."
    exit 1
fi
rsync -r -u $currentDirectory/www/ $dockerDirectory/ --exclude 'vendor' --exclude 'vendor/' --exclude 'database.db'
if [ ! -e $dockerDirectory/database/database.db ]; then
    cp $currentDirectory/www/database/database.db $dockerDirectory/database/database.db
fi
if [ ! -d $dockerDirectory/template/compile ]; then
    mkdir $dockerDirectory/template/compile
fi
find $dockerDirectory/ -type f -print0 | xargs -0 chmod 660
find $dockerDirectory/ -type d -print0 | xargs -0 chmod 770
chown -R www-data:www-data $dockerDirectory/*
