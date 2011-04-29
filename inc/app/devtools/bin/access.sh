#!/bin/sh

ARGS=2
E_BADARGS=65

test $# -ne $ARGS && echo "Usage: `basename $0` ACCESS-LEVEL ACTION(on|off)" && exit $E_BADARGS

cat <<END > access.php
; <?php /*

; These are your box access rules
sitellite_access = $1
sitellite_status = approved
sitellite_action = $2

; */ ?>
END

echo "Your access file is ready, sir."
