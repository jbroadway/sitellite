[Form]

method = post
message = "Add form submission contacts from the selected group to the selected newsletter. <a href='/index/cms-browse-action?collection=sitellite_form_submission'>Back</a>"

[group]

type = select
alt = Form Submission Group

[newsletter]

type = select
alt = Add to Newsletter

[submit_button]

type = submit
setValues = Save
extra = "onclick=`this.value = 'Please wait...'`"
