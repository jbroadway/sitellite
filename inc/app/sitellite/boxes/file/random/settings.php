; <?php /*

[Meta]

name            = random
description     = Displays a random file from the specified folder path.
author          = Lux <john.luxford@gmail.com>
license         = "http://www.opensource.org/licenses/index.php#GPL"
version         = 0.1
parameters      = 

[path]

type			= text
alt				= The path to the folder to choose from.
rule 1			= not empty, You must include a folder path.

[ext]

type			= text
alt				= A comma-separated list of permitted file extensions.

[img]

type			= text
alt				= Set this to 'no' to return only the file name/path (default returns a full img tag around it).

; */ ?>