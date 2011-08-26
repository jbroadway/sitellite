; <?php /*

[Form]

error_mode = all
formhelp = on

[Database]

type = section
title = Database

[Database_connection_name]

type = hidden

[Database_driver]

type = hidden

[Database_hostname]

type = text
alt = Hostname
formhelp = "The name of the database host server.  If it is the same as the web server, often 'localhost' is fine."

[Database_database]

type = text
alt = Database Name
formhelp = "The name of your database on the database server."

[Database_username]

type = text
alt = Username
formhelp = "The username you must supply to the database server in order to connect to it."

[Database_password]

type = password
alt = Password
formhelp = "The password you must supply to the database server in order to connect to it."

[Database_persistent]

type = select
alt = Connection Persistence
setValues = "eval: array ('0' => 'No', '1' => 'Yes')"
formhelp = "Specifies whether or not to use persistent connections when connecting to your database.  Usually, it is best to leave this off."

[Site]

type = section
title = Site

[Site_domain]

type = text
alt = Domain Name
formhelp = "The domain name of the site, used by the scheduler to refer to in command-line mode where no domain is used in the script request."

[Site_usedocroot]

type = select
alt = Use Document Root Setting
setValues = "eval: array ('0' => 'No', '1' => 'Yes')"
formhelp = "Whether to use the document root above, or the auto-discovered value.  This setting is usually only used in the event that the auto-discovery feature fails to determine the correct value."

[Site_docroot]

type = text
alt = Document Root
formhelp = "The full directory path to the web site directory.  Note: This is the filesystem path and not the web-visible path.  If set to empty, the value will be auto-discovered.  Please note that if the 'Use Document Root' setting above is set to 'No', this value will be ignored."

[Site_prefix]

type = text
alt = URL Prefix
formhelp = "Sets the path in the URL to Sitellite.  Note: This hsould NOT include a trailing slash (ie. /mydir not /mydir/) and also should not include the /index or anything after.  Also note that if the 'Use Document Root' setting above is set to 'No', this value will be ignored."

[Site_secure]

type = select
alt = "Secure Socket Layer (SSL)"
setValues = "eval: array ('0' => 'No', '1' => 'Yes')"
formhelp = "If your site sits behind a Secure Socket Layer (SSL), then set this to 'Yes'.  Sitellite will set the protocol to 'http' or 'https' depending on this value."

[Site_safpath]

type = hidden

[Site_remove_index]

type = select
alt = "Remove '/index/' from URLs"
setValues = "eval: array ('0' => 'No', '1' => 'Yes')"

[Site_new_pages_under_current]

type = select
alt = "Default parent for new pages (Web View)"
setValues = "eval: array ('0' => intl_get ('Root'), '1' => intl_get ('Below current page'), '-1' => intl_get ('Same level as current page'))"

[Server]

type = section
title = Server

[Server_default_handler]

type = text
alt = Default Handler
formhelp = "This is the default request handler for when no specific request is made (ie. a request for / or /index)."

[Server_default_handler_type]

type = select
alt = Default Handler Type
setValues = "eval: array ('document' => 'Web Page', 'box' => 'Box', 'form' => 'Form')"
formhelp = "This is the type of the default request handler."

[Server_default_template_set]

type = text
alt = Default Template Set
formhelp = "This is the default template theme used ot render pages.  By using this setting, you can add your own template themes, install multiple themes in the same Sitellite installation, by adding sets instead of changing the existing ones."

[Server_default_template]

type = hidden

[Server_error_handler]

type = text
alt = Error Handler
formhelp = "This is the name of the error handler, when an invalid request is made."

[Server_error_handler_type]

type = select
alt = Error Handler Type
setValues = "eval: array ('document' => 'Web Page', 'box' => 'Box', 'form' => 'Form')"
formhelp = "This is the type of the error handler."

[Server_send_version_header]

type = select
alt = Sitellite HTTP Header
setValues = "eval: array ('1' => 'Yes', '' => 'No')"
formhelp = "This determines whether Sitellite will send an 'X-Powered-By: Sitellite' HTTP header with page requests.  The default is 'Yes', which shouldn't be a problem, but some may prefer to turn it off as one extra security-through-obscurity measure."

[Server_default_app]

type = text
alt = Default App
formhelp = "This sets the default app to look in for boxes, forms, settings, etc.  The default 'webapp' is where you can place your custom code for a single web site.  However, if you are looking to create reusable apps, it is best to create a new app directory structure and not use the default in that case."

[Server_debug]

type = hidden

[Server_compress_output]

type = select
alt = "Compress Output (gzip)"
setValues = "eval: array ('1' => 'Yes', '' => 'No')"
formhelp = "This setting enables gzip encoding of pages sent to visitors should their browsers and the server support it.  This can help reduce bandwidth usage as well as page load time."

[Server_error_reporting]

type = hidden

[Server_xsendfile]

type = select
alt = "X-SendFile Compatibility"
setValues = "eval: array ('readfile' => 'Default (PHP)', 'lighttpd' => 'X-SendFile (Apache/Lighttpd)', 'nginx' => 'X-Accel-Redirect (nginx)')"
formhelp = "This allows Web Files downloads to be served directly by the web server, which is more efficient than serving through PHP. Please note that some web server configuration changes may be required for this setting."

[I18n]

type = section
title = Internationalization

[I18n_directory]

type = text
alt = Directory
formhelp = "Directory where language files are stored."

[I18n_negotiate]

type = select
alt = Determine language from
setValues = "eval: array ('http' => 'Browser settings', 'cookie' => 'Session cookie', 'url' => 'Page URLs (e.g. /fr/)')"
formhelp = "The method of determining which language to use for each visitor.  See saf.I18n::negotiate() in the API references for more information."

[I18n_serialize]

type = hidden

[Emailing]

type = section
title = Sending Email messages

[Emailing_mta]

type = select
alt = Mail Transfer Agent
setValues = "eval: array ('smtp' => 'SMTP', 'sendmail' => 'Sendmail' , 'qmail' => 'QMail' , 'mail' => intl_get('Default PHP Mail') )"
formhelp = "This sets the Mail Transfer Agent to use when sending messages."
; mta_mail , mta_qmail , mta_sendmail config params

[Emailing_template]

type = text
alt = "Default template (.spt) file"
formhelp = "This sets the default email letter template (to be found in /inc/html/mail directory"

[Emailing_from_name]

type = text
alt = "Default FROM: name"
formhelp = "From whom is the email message send"

[Emailing_from_email]

type = text
alt = "Default FROM: email address"
formhelp = "Which email address will be used for sending email messages"

[Emailing_use_html]

type = select
alt = Use HTML
setValues = "eval: array ('1' => 'Yes', '' => 'No')"
formhelp = "Usage of HTML in email messages"

[Emailing_use_nomail]

type = select
alt = Use nomail list
formhelp = Use list with addresses that have specified not to want e-mail from your site
setValues = "eval: array ('1' => 'Yes', '' => 'No')"

[Emailing_overrule_from]

type = select
alt = "Overrule FROM:"
formhelp = Select this option if you want all e-mails from your site to be send from the address specified above
setValues = "eval: array ('1' => 'Yes', '' => 'No')"

[Messaging]

type = section
title = Messaging

[Messaging_return_address]

type = text
alt = Email Address
formhelp = "This is the email address used by Sitellite to send and receive messages emails.  It is the email address recipients will see in the 'From' field of emails."

[Messaging_email_server]

type = text
alt = Email Server
formhelp = "The email server to check for incoming email.  This must be a POP3 email server.  Ordinarily, simply setting up a dedicated email account for Sitellite on your existing mail server is sufficient to enable this functionality."

[Messaging_email_port]

type = text
alt = Email Server Port
formhelp = "The port of the POP3 mail server.  The default is usually correct."

[Messaging_email_username]

type = text
alt = Email Server Username
formhelp = "The username to access the email account designated to Sitellite."

[Messaging_email_password]

type = text
alt = Email Server Password
formhelp = "The password to access the email account designated to Sitellite."

[Messaging_jabber]

type = select
alt = Jabber Instant Messaging
setValues = "eval: array ('' => 'No', '1' => 'Yes')"
formhelp = "Specifies whether to enable Jabber message forwarding and receiving."

[Messaging_jabber_server]

type = text
alt = Jabber Server
formhelp = "The Jabber server to check for incoming instant messages.  This must be a Jabber server."

[Messaging_jabber_port]

type = text
alt = Jabber Server Port
formhelp = "The port of the Jabber server.  The default is usually correct."

[Messaging_jabber_username]

type = text
alt = Jabber Server Username
formhelp = "The username to access the Jabber account designated to Sitellite."

[Messaging_jabber_password]

type = text
alt = Jabber Server Password
formhelp = "The password to access the Jabber account designated to Sitellite."

[Services]

type = section
title = "3rd-Party Services"

[Services_recaptcha_public_key]

type = text
alt = "reCaptcha.net Public Key"

[Services_recaptcha_private_key]

type = text
alt = "reCaptcha.net Private Key"

[submit_button]

type = msubmit
button 1 = Save
button 2 = "Cancel, onclick=`window.location.href = 'cms-cpanel-action'; return false`"

; */ ?>