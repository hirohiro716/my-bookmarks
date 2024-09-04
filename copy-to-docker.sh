#!/bin/bash
currentDirectory=`pwd`
dockerDirectory=$currentDirectory/../../docker/my-bookmarks
if [ ! -d $dockerDirectory ]; then
    echo "Cannot find docker directory."
    exit 1
fi
rsync -r -u $currentDirectory/www/ $dockerDirectory/ --exclude 'vendor' --exclude 'vendor/' --exclude 'database.db'
find $dockerDirectory/ -type f -print0 | xargs -0 chmod 666 2>/dev/null
find $dockerDirectory/ -type d -print0 | xargs -0 chmod 777 2>/dev/null
