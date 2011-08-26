; <?php /*

[Collection]

name = sitepoll_poll
app = sitepoll
display = Polls
singular = Poll
key_field = id
title_field = title
is_versioned = yes

[Source]

name = Database

[Store]

name = Database

[browse:title]

header = Question
width = 28%
filter_import = sitepoll.Filters
filter = sitepoll_filter_title

[browse:date_added]

header = Date Added
width = 18%
filter_import = sitepoll.Filters
filter = sitepoll_filter_date

[browse:votes]

header = Votes
width = 10%
align = right
virtual = sitepoll_virtual_votes

[browse:enable_comments]

header = Comments
width = 10%
align = right
virtual = sitepoll_virtual_enable_comments

[browse:sitellite_status]

header = Status
width = 12%
filter = ucwords

[browse:sitellite_access]

header = Access Level
width = 12%
filter = ucwords

[facet:title]

type = text
display = Text
fields = "title, option_1, option_2, option_3, option_4, option_5, option_6, option_7, option_8, option_9, option_10, option_11, option_12"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

[facet:section]

type = select
display = Section
values = "loader_call ('saf.GUI.Menu', 'menu_get_sections')"

[hint:title]

alt = Question
rule 1 = not empty, You must enter a question.
extra = "size='50'"

[hint:option_1]

extra = "size='45'"

[hint:option_2]

extra = "size='45'"

[hint:option_3]

extra = "size='45'"

[hint:option_4]

extra = "size='45'"

[hint:option_5]

extra = "size='45'"

[hint:option_6]

extra = "size='45'"

[hint:option_7]

extra = "size='45'"

[hint:option_8]

extra = "size='45'"

[hint:option_9]

extra = "size='45'"

[hint:option_10]

extra = "size='45'"

[hint:option_11]

extra = "size='45'"

[hint:option_12]

extra = "size='45'"

[hint:sections]

type = multiple
size = 5
alt = Display in Sections
setValues = "eval: array_merge (array ('' => '- ALL -'), loader_call ('saf.GUI.Menu', 'menu_get_sections'))"

[hint:date_added]

type = hidden
setDefault = "eval: date ('Y-m-d H:i:s')"

[hint:enable_comments]

type = select
setValues = "eval: array ('yes' => 'Yes', 'no' => 'No')"
default_value = "no"

[hint:sitellite_status]

type = status
alt = Status
defalut_value = draft

[hint:sitellite_access]

type = access
alt = Access Level
default_value = public

[hint:sitellite_startdate]

type			= calendar
alt				= "Publish On (If Status is `Queued`)"
nullable		= yes
showsTime		= yes
displayFormat	= "datetime"

[hint:sitellite_expirydate]

type			= calendar
alt				= "Archive On (If Status is `Approved`)"
nullable		= true
showsTime		= true
displayFormat	= "datetime"

[hint:sitellite_owner]

type = owner
alt = Created By

[hint:sitellite_team]

type = team
alt = Owned by Team

; */ ?>