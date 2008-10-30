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
append = "<br/><pre>Example: Sunday, Monday, Tuesday, Wednesday, Thusday, Friday, Saturday</pre>"

[shortdays]

type = text
alt = "Days of week (3 letters form)"
rule 1 = "func 'dates_rule_shdays', You must enter 7 comma separated values, begining with Sun (3 letters each value)"
extra = "size=`40`"
append = "<br/><pre>Example: Sun, Mon, Tue, Wed, Thu, Fri, Sat</pre>"

[months]

type = text
alt = Months
rule 1 = "func 'dates_rule_months', You must enter 12 comma separated values"
extra = "size=`80`"
append = "<br/><pre>Example: January, February, March, April, May, June, July, August, September...</pre>"

[shortmonths]

type = text
alt = "Months (3 letters form)"
rule 1 = "func 'dates_rule_shmonths', You must enter 12 comma separated values (3 letters each value)"
extra = "size=`60`"
append = "<br/><pre>Example: Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec</pre>"

[antepost]

type = text
alt = AM / PM
rule 1 = "func 'dates_rule_antepost', You must enter 2 comma separated values"
append = "<br/><pre>Example: am, pm</pre>"

[suffixes]

type = text
alt = Ordinal suffixes
rule 1 = "func 'dates_rule_suffixes', You must enter 4 comma separated values"
append = "<br/><pre>Example: st, nd, rd, th</pre>"


[submit_button]

type = msubmit
button 1 = Save
button 2 = Cancel

; */ ?>
