; <?php /*

[Form]

error_mode = all

[lang]

type = hidden

[charset]

type = hidden

[default]

type = hidden

[name]

type = text
alt = "Name"
rule 1 = not empty, You must specify a name
rule 2 = "func 'dates_rule_unique_name', This format name already exists"

[format_string]

type = text
alt = "Format string"
rule 1 = not empty, Format string cannot be empty

[submit_button]

type = msubmit
button 1 = Save
button 2 = Cancel

; */ ?>
