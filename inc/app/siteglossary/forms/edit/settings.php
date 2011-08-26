; <?php /*

[Form]

extra = "onsubmit=`xed_copy_value (this, 'body')`"

[_key]

type = hidden

[_collection]

type = hidden

[_return]

type = hidden

[changelog]

type = hidden

[word]

type = text
alt = Term

[category]

type = selector
alt = Category
table = siteglossary_category
key = name

[description]

type = textarea
alt = Summary
rows = 2
extra = " maxlength='80'"
labelPosition = left

[section]

type = section
title = Definition

[body]

type = xed.Widget.Xeditor

[submit_button]

type			= msubmit
button 0		= Create
button 1		= Save and continue
button 2		= Cancel

; */ ?>