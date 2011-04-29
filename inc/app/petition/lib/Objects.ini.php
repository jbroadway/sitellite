; <?php /*

[Petition]

table = petition
pkey = id
permissions = on

[Signature]

table = petition_signature
pkey = id

[rel:Petition:Signature]

type = 1x
Signature field = petition_id
cascade = on

; */ ?>