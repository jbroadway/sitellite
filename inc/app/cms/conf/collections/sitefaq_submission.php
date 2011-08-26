; <?php /*

[Collection]

name			= sitefaq_submission
app				= sitefaq
display			= FAQ Submissions
singular		= FAQ Submission
key_field		= id
title_field		= question
title_field_name= Question
summary_field	= answer
body_field		= answer
edit			= form:sitefaq/edit
order_by		= ts
sorting_order	= desc

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Blank

[browse:add_faq]

header			= ""
width			= 4%
align			= center
filter_import	= sitefaq.Filters
virtual			= sitefaq_virtual_add_faq

[browse:question]

header			= Question
length			= 40
width			= 22%

[browse:ts]

header			= Submitted On
filter_import	= sitefaq.Filters
filter			= sitefaq_filter_date_time
width			= 24%

[browse:assigned_to]

header			= Assigned To
width			= 14%

[browse:answer]

header			= Answer
length			= 40
width			= 26%

[facet:question]

display			= Text
type			= text
fields			= "question, answer"

[facet:assigned_to]

display			= Assigned To
type			= select
values			= "eval (CLOSE_TAG . OPEN_TAG . ' loader_import (`sitefaq.Filters`); return sitefaq_facet_assigned_to ()' . CLOSE_TAG)"

[facet:age]

display			= Age
type			= select
values			= "eval (CLOSE_TAG . OPEN_TAG . ' loader_import (`sitefaq.Filters`); return sitefaq_facet_age ()' . CLOSE_TAG)"

[hint:question]

alt				= Question
type			= textarea
rows			= 2
labelPosition	= left

[hint:category]

type			= selector
table			= sitefaq_category
key				= name

[hint:answer]

alt				= Answer
rows			= 12
labelPosition	= left

[hint:ts]

alt				= Submitted On
type			= info

[hint:assigned_to]

type			= select

[hint:age]

type			= info

[hint:ip]

type			= info
alt				= IP Address

[hint:url]

type			= info
alt				= Web Site

[hint:name]

type			= info

[hint:email]

type			= info

[hint:member_id]

type			= info
alt				= Member ID

[hint:sitellite_status]

type			= status
alt				= Status

[hint:sitellite_access]

type			= access
alt				= Access Level

[hint:sitellite_owner]

type			= owner
alt				= Created By

[hint:sitellite_team]

type			= team
alt				= Owned by Team

; */ ?>