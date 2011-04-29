#!/bin/sh

# start the server in the background

export PATH=/usr/local/bin:/usr/bin:/bin

script_dir=`dirname $0`
cd $script_dir

cd ../../../../

php -f index sitemailer2-mailer-action &

sleep 2
mailer_pid=`ps wux | grep 'php -f index sitemailer2-mailer-action' | grep -v grep | awk '{ print $2; exit }'`

echo $mailer_pid > inc/app/sitemailer2/data/mailer.pid

php -f index sitemailer2-recurring-action &

sleep 2
recurring_pid=`ps wux | grep 'php -f index sitemailer2-recurring-action' | grep -v grep | awk '{ print $2; exit }'`

echo $recurring_pid > inc/app/sitemailer2/data/recurring.pid
