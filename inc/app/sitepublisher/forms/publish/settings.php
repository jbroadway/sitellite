; <?php /*

[Form]

message = Enter the path to a folder and SitePublisher will generate a static version of your web site in that location.
error_mode = all

[folder]

alt = Publish to Folder
type = text
rule 1 = "func `sitepublisher_rule_folder`, The folder you have specified either does not exist or is not writeable by Sitellite."
extra = "size=`40`"

[extension]

alt = File Extensions
type = select
setValues = "eval: appconf ('extensions')"

[submit_button]

type = msubmit
button 1 = Publish
button 2 = Cancel

; */ ?>