; <?php /*
;
; This file contains a list of folders or external web sites
; you would like to index and make searchable as content types.
; These are optional, as the default is to index and search
; Sitellite content types themselves, not static files.
;
; The default scheduler task also doesn't parse these itself,
; however you can always write a simple directory parser in
; PHP and use the sitesearch.Extractor package to parse the
; files themselves.  This simply provides a standardized
; configuration format for you to do so with.
;
; Note that it is not possible to use Sitellite's access
; control here because these files would be accessed directly
; and not through Sitellite.
;
; Examples:
;
; [My Docs]
;
; folder     = /home/me/public_html/my_docs
; prefix     = /my_docs
; type       = folder
;
; [asdf.com]
;
; domain     = www.asdf.com
; type       = site
; sitellite  = no
;

;[SAF Docs]

;prefix			= /inc/app/cms/docs/api
;folder			= /Users/lux/Sites/dev.lo/inc/app/cms/docs/api
;type			= folder

;[demo.lo]
;
;domain			= www.demo.lo
;type			= site
;sitellite		= yes

; */ ?>