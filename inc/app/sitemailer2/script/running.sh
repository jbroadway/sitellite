#!/bin/sh

# To check for sitemailer's wellbeing every 10 minutes (recommended), 
# put the following line in your crontab:
#    0,10,20,30,40,50 * * * *   /path/to/sitellite/inc/app/sitemailer2/script/running.sh
# And if you don't want to get email from crontab put the following 
# in your crontab:
#    0,10,20,30,40,50 * * * *   /path/to/sitellite/inc/app/sitemailer2/script/running.sh
#

export PATH=/bin:/usr/bin:/usr/local/bin

script_dir=`dirname $0`
cd $script_dir

pid_file=../data/mailer.pid
mailer_pid=`ps wux | grep 'php -f index sitemailer2-mailer-action' | grep -v grep | awk '{ print $2; exit }'`

if test -s "$pid_file"
then
	real_pid=`ps wux | grep 'php -f index sitemailer2-mailer-action' | grep -v grep | awk '{ print $2; exit }'`

	if [ $real_pid ]
	then
		# SiteSearch is running
		echo "Mailer is Running."
	else
		echo "No Mailer daemon found.  Attempting to start Mailer daemon..."
        ./stop.sh
		./start.sh
	fi
else
	echo "No Mailer pid file found.  Looked for $pid_file"
	echo "Attempting to start Mailer daemon..."
    ./stop.sh
	./start.sh
fi

pid_file=../data/recurring.pid
recurring_pid=`ps wux | grep 'php -f index sitemailer2-recurring-action' | grep -v grep | awk '{ print $2; exit }'`

if test -s "$pid_file"
then
	real_pid=`ps wux | grep 'php -f index sitemailer2-recurring-action' | grep -v grep | awk '{ print $2; exit }'`

	if [ $real_pid ]
	then
		# SiteSearch is running
		echo "Recurring is Running."
	else
		echo "No Recurring daemon found.  Attempting to start Recurring daemon..."
        ./stop.sh
		./start.sh
	fi
else
	echo "No Recurring pid file found.  Looked for $pid_file"
	echo "Attempting to start Recurring daemon..."
    ./stop.sh
    ./start.sh
fi

pid_file=../data/bouncer.pid
bouncer_pid=`ps wux | grep 'php -f index sitemailer2-bouncer-action' | grep -v grep | awk '{ print $2; exit }'`

if test -s "$pid_file"
then
	real_pid=`ps wux | grep 'php -f index sitemailer2-bouncer-action' | grep -v grep | awk '{ print $2; exit }'`

	if [ $real_pid ]
	then
		# SiteSearch is running
		echo "Bouncer Running."
	else
		echo "No Bouncer found.  Attempting to start Bouncer..."
		./start_bouncer.sh
	fi
else
	echo "No Bouncer pid file found.  Looked for $pid_file"
	echo "Attempting to start Bouncer..."
	./start_bouncer.sh
fi
