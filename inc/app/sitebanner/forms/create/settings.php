; <?php /*

[Form]

error_mode = all

[name]

type = text
alt = Banner Name

[file]

type = text
alt = Image URL
default_value = "http://"

[display_url]

type = text
alt = Alternate Text

[url]

type = text
alt = Web Site Address
default_value = "http://"

[section]

type = multiple
alt = Desired Sections
setValues = "eval: menu_get_sections ()"
size = 5

[position]

type = select
alt = Screen Position
setValues = "eval: assocify (db_shift_array ('select * from sitebanner_position'))"

[description]

type = textarea
alt = Additional Comments
cols = 40
rows = 5

[submit_button]

type = submit
setValues = Submit

; */ ?>