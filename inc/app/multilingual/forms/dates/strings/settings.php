; <?php /*

[Form]

error_mode = all

[lang]

type = hidden

[charset]

type = hidden

[days]

type = text
alt = Days of week
rule 1 = "func 'dates_rule_days', You must enter 7 comma separated values, begining with Sunday"
extra = "size=`80`"

[shortdays]

type = text
alt = "Days of week (3 letters form)"
rule 1 = "func 'dates_rule_shdays', You must enter 7 comma separated values, begining with Sun (3 letters each value)"
extra = "size=`40`"

[months]

type = text
alt = Months
rule 1 = "func 'dates_rule_months', You must enter 12 comma separated values"
extra = "size=`80`"

[shortmonths]

type = text
alt = "Months (3 letters form)"
rule 1 = "func 'dates_rule_shmonths', You must enter 12 comma separated values (3 letters each value)"
extra = "size=`60`"

[antepost]

type = text
alt = AM / PM
rule 1 = "func 'dates_rule_antepost', You must enter 2 comma separated values"

[suffixes]

type = text
alt = Ordinal suffixes
rule 1 = "func 'dates_rule_suffixes', You must enter 4 comma separated values"

[submit_button]

type = msubmit
button 1 = Save
button 2 = Cancel

; */ ?>
