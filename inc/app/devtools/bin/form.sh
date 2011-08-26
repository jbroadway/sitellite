#!/bin/sh

ARGS=4
E_BADARGS=65

test $# -ne $ARGS && echo "Usage: `basename $0` APPNAME FORMNAME <on|off|none> DESCRIPTION" && exit $E_BADARGS

APP=`echo $1 | perl -e 'print ucfirst (<STDIN>)'`
FORM=`echo $2 | perl -e 'print ucfirst (<STDIN>)'`

mkdir $2
cd $2

cat <<END > index.php
<?php

class ${APP}${FORM}Form extends MailForm {
	function ${APP}${FORM}Form () {
		parent::MailForm ();

		\$this->parseSettings ('inc/app/$1/forms/$2/settings.php');
	}

	function onSubmit (\$vals) {
		// your handler code goes here
	}
}

?>
END

if [ $3 = "on" ]
then
cat <<END > access.php
; <?php /*

; These are your box access rules
sitellite_access = public
sitellite_status = approved
sitellite_action = $3

; */ ?>
END
else if [ $3 = "off" ]
then
cat <<END > access.php
; <?php /*

; These are your box access rules
sitellite_access = public
sitellite_status = approved
sitellite_action = $3

; */ ?>
END
fi
fi

cat <<END > settings.php
; <?php /*

[Form]

name            = $2
description     = $4
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1

; your form definition goes here

; */ ?>
END

cd ..

echo "Your form is ready, sir."
