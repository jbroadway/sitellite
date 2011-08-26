#!/bin/sh

export PATH=/usr/local/bin:/usr/bin:/bin

script_dir=`dirname $0`
cd $script_dir

cd ../../../../

php -f index sitemailer2-bouncer-action &

sleep 2
bouncer_pid=`ps wux | grep 'php -f index sitemailer2-bouncer-action' | grep -v grep | awk '{ print $2; exit }'`

echo $bouncer_pid > inc/app/sitemailer2/data/bouncer.pid
