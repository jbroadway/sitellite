; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

[Database]

connection_name         = mysqlweb

driver                  = MySQL

hostname                = localhost

database                = DBNAME

username                = USER

password                = PASS

persistent              = Off

[Site]

domain                  = DOMAIN

usedocroot              = Off

docroot                 = Off

prefix                  = Off

secure                  = Off

safpath                 = saf

remove_index            = On

new_pages_under_current = Off

[Server]

default_handler         = index

default_handler_type    = document

default_template_set    = default

default_template        = default

error_handler           = sitellite/error

error_handler_type      = box

send_version_header     = On

default_app             = webapp

debug                   = Off

compress_output         = Off

error_reporting         = "E_ALL ^ E_NOTICE"

xsendfile               = readfile

[I18n]

directory               = inc/lang

negotiate               = url

serialize               = plain

[Emailing]

mta                     = mail

template                = default.spt

from_name               = Sitellite OpenSource

from_email              = info@sitellite.org

use_html                = On

use_nomail              = Off

overrule_from           = On

[Messaging]

return_address          = Off

email_server            = Off

email_port              = 110

email_username          = Off

email_password          = Off

jabber                  = Off

jabber_server           = Off

jabber_port             = 5222

jabber_username         = Off

jabber_password         = Off

[Services]

recaptcha_public_key    = Off

recaptcha_private_key   = Off

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>