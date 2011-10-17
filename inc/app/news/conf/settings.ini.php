; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

[tab1]

type                    = tab

title                   = Main

value                   = Off

[news_name]

alt                     = Main page title

extra                   = "size='25'"

value                   = News Stories

[sections]

alt                     = Use sections

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = Off

[limit]

alt                     = Number of stories per page

extra                   = "size='10'"

value                   = 10

[frontpage_limit]

alt                     = Number of stories on front page

extra                   = "size='10'"

value                   = 5

[comments]

alt                     = Allow comments

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = On

[comments_email]

alt                     = "Send comments to (email)"

extra                   = "size='25'"

value                   = Off

[comments_security]

alt                     = Use CAPTCHA on comments

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = On

[comments_blog_style]

alt                     = Blog-style comment display

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = On

[submissions]

alt                     = "Story submissions (email, optional)"

extra                   = "size='25'"

value                   = Off

[template]

alt                     = Template

type                    = cms.Widget.Templates

value                   = Off

[page_alias]

alt                     = Alias of a page

type                    = pagebrowser.Widget.Pagebrowser

value                   = Off

[tab2]

type                    = tab

title                   = Thumbnails

value                   = Off

[fixed_thumbnail_size]

alt                     = "Thumbnail size fixed?"

type                    = select

setValues               = "eval: array ('yes' => 'Yes', 'no' => 'No')"

value                   = yes

[align_thumbnails]

alt                     = Align thumbnails

type                    = select

setValues               = "eval: array ('left' => 'Left', 'right' => 'Right')"

value                   = left

[thumb_width]

type                    = text

alt                     = Thumb max width px

value                   = 80

extra                   = "size=`4`"

[thumb_height]

type                    = text

alt                     = Thumb max height px

value                   = 80

extra                   = "size=`4`"

[default_thumbnail]

type                    = imagechooser

alt                     = Default Thumbnail Image

extra                   = "size=`30`"

value                   = "pix/arrow.desc.gif"

rule 0                  = not empty

[tab-end]

type                    = tab

value                   = Off

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>