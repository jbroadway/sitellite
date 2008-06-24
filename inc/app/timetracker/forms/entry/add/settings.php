; <?php /*

[Form]

[project]

type			= hidden

[proj_name]

type			= info
alt				= Project

[description]

type			= textarea
cols			= 50
rows			= 5
labelPosition	= left

[users]

type			= multiple
size			= 5

[started]

type			= calendar
showsTime		= true
format			= "%Y-%m-%d %H:%M:%S"
displayFormat	= "%a, %e %b, %Y - %l:%M%P"
rule 1			= not empty, You must specify a date/time that you started.

[ended]

alt				= Finished
type			= calendar
showsTime		= true
format			= "%Y-%m-%d %H:%M:%S"
displayFormat	= "%a, %e %b, %Y - %l:%M%P"
rule 1			= not empty, You must specify a date/time that you finished.

[submit_button]

type			= msubmit
button 1		= "Add"
button 2		= "Cancel"

; */ ?>