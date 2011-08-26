; <?php /*

[Collection]

name = realty_listing
app = realty
display = Property Listings
singular = Listing

key_field = id
key_field_name = ID

title_field = headline
title_field_name = Headline

body_field = description

is_versioned = no

order_by = ts
sorting_order = desc

add = form:realty/add
edit = form:realty/edit

[Source]

name = Database

[Store]

name = Blank

[browse:headline]

header = Headline

[browse:price]

header = Asking Price
filter_import = realty.Filters
filter = realty_filter_price
align = right

[browse:status]

header = Status
filter = ucwords
align = center

[browse:property_type]

header = Property Type
filter = ucwords
align = center

[browse:house_size]

header = House Size

[browse:lot_size]

header = Lot Size

[browse:gross_taxes]

header = Gross Taxes

[facet:headline]

display = Text
type = text
fields = headline, description

[facet:status]

display = Status
type = select
values = "array ('active','sold','archived')"

[facet:property_type]

display = Property Type
type = select
values = "array ('residential','commercial')"

[hint:headline]

extra = "size=`40`"

[hint:price]

alt = Asking Price

[hint:house_size]

alt = House Size / Business Type

[hint:lot_size]

alt = Lot Size / Building Type

[hint:gross_taxes]

alt = Gross Taxes / For Lease

[hint:summary]

type = textarea
rows = 3
cols = 40
labelPosition = left

[hint:photo1]

type = file
alt = "Photo 1 (.jpg)"
clear = yes

[hint:photo2]

type = file
alt = "Photo 2 (.jpg)"
clear = yes

[hint:photo3]

type = file
alt = "Photo 3 (.jpg)"
clear = yes

[hint:photo4]

type = file
alt = "Photo 4 (.jpg)"
clear = yes

[hint:photo5]

type = file
alt = "Photo 5 (.jpg)"
clear = yes

[hint:photo6]

type = file
alt = "Photo 6 (.jpg)"
clear = yes

[hint:photo7]

type = file
alt = "Photo 7 (.jpg)"
clear = yes

[hint:photo8]

type = file
alt = "Photo 8 (.jpg)"
clear = yes

[hint:ts]

alt = Listing Date
type = calendar
setValue = "eval: date ('Y-m-d')"

[hint:status]

alt = Listing Status

[hint:description]

type = xed.Widget.Xeditor

; */ ?>
