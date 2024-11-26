#!/bin/sh
cd `dirname $0`
content="deny from all"
echo "$content" >docker/class/.htaccess
echo "$content" >docker/template/.htaccess
echo "$content" >docker/database/.htaccess
echo "$content" >docker/vendor/.htaccess
find ./ -type f -name ".htaccess" | xargs chmod 660
find ./ -type f -name ".htaccess" | xargs chown www-data:www-data
