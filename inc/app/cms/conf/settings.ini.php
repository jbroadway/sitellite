; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

[activity_log_items]

alt                     = Number of items to keep into activity log

type                    = select

setValues               = "eval:array (0=>'ALL', 7=>'One week', 31=>'One month', 92=>'Three months', 182=>'Six months', 365=>'One year', 1095=>'Three years')"

value                   = 182

[cms_revisions_items]

alt                     = Number of document versions to keep

type                    = select

setValues               = "eval:array (0=>'ALL', 2=>2, 5=>5, 10=>10, 20=>20, 50=>50)"

value                   = 20

[cms_webhooks]

alt                     = "Web Hooks (Sitellite will POST to these URLs on workflow events)"

type                    = textarea

value                   = ""

extra                   = "style=`width: 100%`"

[cms_webhooks_auth]

alt                     = Web Hooks Authorization Key

type                    = text

value                   = ""

extra                   = "size=`30`"

append                  = " <a href=`#` onclick=`var f=function(){var c='01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';var r='';for(var i=0;i<32;i++){s=Math.floor(Math.random()*c.length);r+=c.substring(s,s+1);}return r;};document.getElementById('cms_webhooks_auth').value=f()`>Auto-Generate</a>"

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>