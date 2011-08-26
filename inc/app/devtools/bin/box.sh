#!/bin/sh

ARGS=3
E_BADARGS=65

test $# -ne $ARGS && echo "Usage: `basename $0` BOXNAME <on|off|none> DESCRIPTION" && exit $E_BADARGS

mkdir $1
cd $1

cat <<END > index.php
<?php

// your box code goes here

?>
END

if [ $2 = "on" ]
then
cat <<END > access.php
; <?php /*

; These are your box access rules
sitellite_access = public
sitellite_status = approved
sitellite_action = $2

; */ ?>
END
else if [ $2 = "off" ]
then
cat <<END > access.php
; <?php /*

; These are your box access rules
sitellite_access = public
sitellite_status = approved
sitellite_action = $2

; */ ?>
END
fi
fi

cat <<END > settings.php
; <?php /*

[Meta]

name            = $1
description     = $3
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1
parameters      = none

; */ ?>
END

cd ..

echo "Your box is ready, sir."
