[Form]

method = post
error_mode = all
message = * Required fields.

[id]

type = hidden

[firstname]

type = text
alt = First Name*
rule 1 = not empty, You must enter your first name.    

[lastname]

type = text
alt = Last Name*
rule 1 = not empty, You must enter your last name.

[email]

type = text
alt = Email Address*
;rule 1 = not empty, You must enter your email address.
;rule 2 = email, Your email address does not appear to be valid.
;rule 3 = "unique `petition_signature/email`, You may only sign once. We found your email address already in the signature list."

[address]

type = text
alt = Mailing Address

[city]

type = text
alt = City or Town*
rule 1 = not empty, You must enter your city or town.

[province]

type = select
alt = Province/Territory*
setValues = "eval: appconf ('provinces')"
rule 1 = not empty, You must select your province or territory.

[country]

type = select
alt = Country*
setValues = "eval: appconf ('countries')"
setDefault = "eval: appconf ('default_country')"
rule 1 = not empty, You must select your country.

[postal_code]

type = text
alt = Zip/Postal Code

[secure]

type = security
alt = Security Test
verify_method = phpcaptcha

[submit_button]

type = submit
setValues = Submit now
