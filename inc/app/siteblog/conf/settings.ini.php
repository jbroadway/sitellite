; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

[blog_name]

alt                     = Blog Name

value                   = My Blog

[comments_security]

type                    = select

alt                     = Use CAPTCHA for comments

setValues               = "eval: array ('1' => 'Yes', '0' => 'No')"

value                   = On

[akismet_key]

alt                     = Akismet.com key for comment spam filtering

value                   = Off

[template]

alt                     = Default Template

type                    = cms.Widget.Templates

value                   = Off

[page_alias]

alt                     = Alias of a page

type                    = pagebrowser.Widget.Pagebrowser

value                   = Off

[limit]

alt                     = Posts per page

extra                   = "size=`10`"

value                   = 10

[split_body]

type                    = select

alt                     = Mark post summary end by <hr /> tag

setValues               = "eval: array ('1' => 'Yes', '0' => 'No')"

value                   = On

[sharethis]

alt                     = ShareThis.com embed code

type                    = textarea

labelPosition           = left

value                   = Off

[twitter]

alt                     = "Twitter Username (auto-post to Twitter)"

extra                   = "autocomplete=`off`"

value                   = Off

[twitter_pass]

alt                     = Twitter Password

type                    = password

extra                   = "autocomplete=`off`"

value                   = Off

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>