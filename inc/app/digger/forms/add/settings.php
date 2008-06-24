; <?php /*

[Form]
error_mode = all
message = "Step 2 - Story Details"

[category]
type = hidden

[link]
type = text
alt = Story Link
setDefault = "http://"
extra = "size='30'"
rule 1 = "not empty, Please specify the story link."
rule 2 = "not is 'http://', Please specify the story link."

[title]
type = text
alt = Story Title
rule 1 = "not empty, Please specify the story title."
extra = "size='30'"

[description]
type = textarea
alt = "Description (Max 500 Characters)"
rule 1 = "not empty, Please specify the story description."
rule 2 = "length '500-', Your description exceeds 500 characters.  Please shorten your description."

[submit_button]
type = submit
setValues = Submit Story

; */ ?>
