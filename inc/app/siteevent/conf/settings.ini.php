; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

[siteevent_title]

alt                     = Calendar title

extra                   = "size='25'"

value                   = Events Calendar

[submissions]

alt                     = "Submissions (email)"

extra                   = "size='25'"

value                   = Off

[default_view]

alt                     = Default view

type                    = select

setValues               = "eval: array ('day' => 'Day', 'list' => 'List', 'month' => 'Month', 'week' => 'Week')"

value                   = month

[template]

alt                     = Default Template

type                    = cms.Widget.Templates

value                   = wide

[template_calendar]

alt                     = Calendar Template

type                    = hidden

value                   = Off

[page_alias]

alt                     = Alias of a page

type                    = pagebrowser.Widget.Pagebrowser

value                   = Off

[css_location]

alt                     = Custom CSS Stylesheet

extra                   = "size='40'"

value                   = "{site/prefix}/inc/app/siteevent/html/style.css"

[default_city]

alt                     = Default City for Submissions

value                   = Off

[default_province]

alt                     = Default State/Province

value                   = Off

[default_country]

alt                     = Default Country

value                   = Off

[ical_links]

alt                     = Show iCal Links

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = On

[rss_links]

alt                     = Show RSS Links

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = On

[rss_title]

alt                     = RSS Feed Title

extra                   = "size='25'"

value                   = "{site/domain} Events"

[rss_description]

alt                     = RSS Feed Description

extra                   = "size='40'"

value                   = "Event listings from {site/domain}"

[google_maps]

alt                     = Show Google Maps Links

type                    = select

setValues               = "eval: array ('' => 'No', '1' => 'Yes')"

value                   = On

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>