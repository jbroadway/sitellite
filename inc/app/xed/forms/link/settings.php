[Form]

title = Add Link

[ifname]

type = hidden

[href]

type = xed.Widget.Linker
alt = Link

[target]

type = select
setValues = "eval: array ('' => intl_get ('None'), '_blank' => intl_get ('New Window'), '_top' => intl_get ('Top Frame'))"

[submit_button]

type = msubmit
button 1 = OK
button 2 = "Cancel, onclick='window.close (); return false'"
