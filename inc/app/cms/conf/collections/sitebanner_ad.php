; <?php /*

[Collection]

name = sitebanner_ad
app	= sitebanner
display = Banner Ads
singular = Banner Ad
key_field = id
title_field = name

[Source]

name = Database

[Store]

name = Blank

[browse:stats]

header = ""
width = 5%
align = center
filter_import = sitebanner.Filters
virtual = sitebanner_virtual_stats

[browse:name]

header = Name
width = 18%

[browse:client]

header = Client
width = 25%
filter_import = sitebanner.Filters
filter = sitebanner_filter_client

[browse:impressions]

header = Impressions
width = 8%
align = right
filter_import = sitebanner.Filters
filter = sitebanner_filter_impressions

[browse:purchased]

header = Remaining
width = 8%
align = right
filter_import = sitebanner.Filters
filter = sitebanner_filter_purchased

[browse:clicks]

header = Clicks
width = 8%
align = right
filter_import = sitebanner.Filters
virtual = sitebanner_virtual_clicks

[browse:clicks_percent]

header = %
width = 8%
align = right
filter_import = sitebanner.Filters
virtual = sitebanner_virtual_clicks_percent

[browse:active]

header = Active
width = 8%
filter = ucwords

[facet:client]

type = select
display = Client
values = "db_shift_array ('select distinct client from sitebanner_ad order by client asc')"

[facet:active]

type = select
display = Active
values = "array ('yes' => 'Yes', 'no' => 'No')"

[facet:section]

type = select
display = Section
values = "loader_call ('sitebanner.MenuSections', 'menu_get_sections')"

[hint:name]

alt = Ad Name
rule 1 = not empty, You must enter a name for your ad.

[hint:purchased]

type = text
alt = "Purchased Impressions (-1 for unlimited)"

[hint:impressions]

type = hidden

[hint:url]

alt = URL
extra = "size='40'"

[hint:description]

type = textarea
cols = 40
rows = 2
labelPosition = left

[hint:client]

type = select
setValues = "eval: db_pairs ('select username, concat(role, ` - `, lastname, ` `, firstname, ` (`, username, `)`) as name from sitellite_user order by name asc')"

[hint:display_url]

alt = Alternate Text
extra = "size='40'"

[hint:file]

type = sitebanner.Widgets.Shapeshifter

[hint:section]

type = multiple
size = 5
alt = Display in Sections
setValues = "eval: array_merge (array ('' => '- ALL -'), loader_call ('sitebanner.MenuSections', 'menu_get_sections'))"

[hint:position]

type = selector
alt = Screen Position
table = sitebanner_position
key = name

[hint:active]

alt = "Is Active?"

[hint:format]

setValues = "eval: array ('external' => 'External Link', 'adsense' => 'Google(TM) AdSense', 'html' => 'HTML', 'image' => 'Image', 'text' => 'Text')"
extra = "onchange='this.form.submit ()'";

[hint:target]

setValues = "eval: array ('blank' => 'New Window', 'parent' => 'Parent Frame', 'self' => 'Same Frame', 'top' => 'Top Frame')"

; */ ?>