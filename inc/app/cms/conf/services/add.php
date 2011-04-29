; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

[service:cms_services_cacheclear]

name                    = Reset Entire Page Cache

handler                 = "box:cms/services/cacheclear"

[service:cms_services_notice]

name                    = Document Workflow Notifier

handler                 = "box:cms/services/notice"

[service:cms_services_webhooks]

name                    = Web Hooks

handler                 = "box:cms/services/webhooks"

[service:multilingual_services_translation]

name                    = Translator Workflow Notifier

handler                 = "box:multilingual/services/translation"

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>