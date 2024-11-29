#!/bin/bash
cd `dirname $0`
operationalDirectory="www/"
if [ -d docker ]; then
    if [ ${EUID:-${UID}} != 0 ]; then
        echo "Please run with Root."
        exit 1
    fi
    operationalDirectory="docker/"
fi
content="deny from all"
echo "$content" >${operationalDirectory}class/.htaccess
echo "$content" >${operationalDirectory}template/.htaccess
echo "$content" >${operationalDirectory}database/.htaccess
echo "$content" >${operationalDirectory}vendor/.htaccess
find ./ -type f -name ".htaccess" | xargs chmod 660
if [ -d docker ]; then
    find $operationalDirectory -type f -name ".htaccess" | xargs chown www-data:www-data
fi
