[Form]

method = post
message = "Add users from the selected team and role to the selected newsletter. <a href='/index/usradm-browse-action?list=users'>Back</a>"

[team]

type = select
alt = Team
setValues = "eval: sitemailer2_get_teams ()"

[role]

type = select
alt = Role
setValues = "eval: sitemailer2_get_roles ()"

[newsletter]

type = select
alt = Add to Newsletter

[submit_button]

type = submit
setValues = Save
extra = "onclick=`this.value = 'Please wait...'`"
