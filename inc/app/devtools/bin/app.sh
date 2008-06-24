#!/bin/sh

ARGS=1
E_BADARGS=65

test $# -ne $ARGS && echo "Usage: `basename $0` APPNAME" && exit $E_BADARGS

mkdir $1
cd $1

mkdir html
mkdir lib
mkdir boxes
mkdir conf
mkdir forms
mkdir docs
mkdir data
mkdir pix
mkdir install
mkdir boxes/index
mkdir boxes/admin

echo "Your installation instructions go here" > install/INSTALL

echo "# Your database schema goes here" > install/install-mysql.sql

cat <<END > conf/config.ini.php
; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS

; This is your app config file.  Most fields are optional, and those that
; are not so self explanatory have a comment above them.

; Only app_name among all these is actually required, but all are recommended.
app_name		= MyApp
description		= About MyApp...
author			= Me
copyright		= "Copyright (C) 2004, Me Inc."
license			= "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version			= "0.1 alpha $Id: app.sh,v 1.2 2004/02/20 19:36:14 lux Exp $"

; These allow you to call your app via the /index/myapp-app syntax.
default_handler		= index
default_handler_type	= box

; Comment these lines out if your app doesn't have an administrative UI to it.
admin_handler		= admin
admin_handler_type	= box

; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>
END

cat <<END > conf/properties.php
<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

?>
END

cat <<END > boxes/index/index.php
<?php

// your app begins here

?>
END

cat <<END > boxes/index/access.php
; <?php /*

sitellite_access	= public
sitellite_status	= approved
sitellite_action	= on

; */ ?>
END

cat <<END > boxes/admin/index.php
<?php

// your admin UI begins here

?>
END

cat <<END > boxes/admin/access.php
; <?php /*

sitellite_access	= public
sitellite_status	= approved
sitellite_action	= on
sitellite_template_set = admin

; */ ?>
END

cd ..

echo "Your app is ready, sir."

