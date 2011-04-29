#!/bin/sh

script_dir=`dirname $0`
cd $script_dir

pid_file=../data/mailer.pid

if test -s "$pid_file"
then
	mailer_pid=`cat $pid_file`
	echo "Killing Mailer with pid $mailer_pid"
	kill $mailer_pid

	sleep 1
	rm $pid_file
else
	echo "No Mailer pid file found.  Looked for $pid_file"
fi

pid_file=../data/recurring.pid

if test -s "$pid_file"
then
	recurring_pid=`cat $pid_file`
	echo "Killing Recurring with pid $recurring_pid"
	kill $recurring_pid

	sleep 1
	rm $pid_file
else
	echo "No Recurring pid file found.  Looked for $pid_file"
fi
