; <?php /*

[Product]

table = siteshop_product
pkey = id
permissions = on
multilingual = on
import = siteshop.CustomProduct
extends = CustomProduct

[Category]

table = siteshop_category
pkey = id
import = siteshop.CustomCategory
extends = CustomCategory

[Order]

table = siteshop_order
pkey = id
import = siteshop.CustomOrder
extends = CustomOrder

[Option]

table = siteshop_option
pkey = id
import = siteshop.CustomOption
extends = CustomOption

[ProductOption]
table = siteshop_product_option
pkey = id


[rel:Option:ProductOption]
type = 1x
ProductOption field = option_id
cascade = on

[OptionType]

table = siteshop_option_type
pkey = id

[rel:Product:Option]
type = xx
join_table = siteshop_product_option
Product field = product_id
Option field = option_id

[Sale]

table = siteshop_sale
pkey = id
import = siteshop.CustomSale
extends = CustomSale

[Promo]

table = siteshop_promo_code
pkey = id
import = siteshop.CustomPromo
extends = CustomPromo

[CheckoutOffer]

table = siteshop_checkout_offer
pkey = id

[rel:Product:Category]

type = xx
join_table = siteshop_product_category
Product field = product_id
Category field = category_id

[rel:Order:Product]

type = xx
join_table = siteshop_order_product
Order field = order_id
Product field = product_id

[rel:Sale:Product]

type = xx
join_table = siteshop_sale_product
Sale field = sale_id
Product field = product_id

; */ ?>