#!/bin/sh
cd `dirname $0`
content="deny from all"
echo "$content" >.htaccess
echo "$content" >www/class/.htaccess
echo "$content" >www/template/.htaccess
echo "$content" >www/database/.htaccess
echo "$content" >www/vendor/.htaccess
find ./ -type f -name ".htaccess" | xargs chmod o-rxw
