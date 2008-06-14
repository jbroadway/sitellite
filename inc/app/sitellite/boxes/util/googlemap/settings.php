[Meta]

name = Google Map
description = "Embed a Google Map into your web page"

[key]

type = text
alt = Google Maps API Key
setDefault = "eval: db_shift ('select data_value from sitellite_property_set where collection = `sitellite_util_googlemap` and entity = `api` and property = `key`')"
rule 1 = "not empty, You must register for a free Google Maps API key to use this box, which you can do <a href=`http://www.google.com/apis/maps/signup.html` target=`_blank`>here</a>"

[address]

type = text
rule 1 = not empty, You must enter an address.

[city]

type = text
rule 1 = not empty, You must enter a city.

[state]

type = text
alt = State/Province
rule 1 = not empty, You must enter a state.

[country]

type = text
