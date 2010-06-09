; <?php /*

[Meta]

name            = determine
description     = Returns the first file from the specified list that exists.
author          = Lux <john.luxford@gmail.com>
license         = "http://www.opensource.org/licenses/index.php#GPL"
version         = 0.1
parameters      = 

[list]

type			= text
alt				= A comma-separated list of files.
rule 1			= not empty, You must include a file list.

[path]

type			= text
alt				= An optional folder path, in case they are all in the same location.

[img]

type			= text
alt				= Set this to 'no' to return only the file name/path (default returns a full img tag around it).

; */ ?>