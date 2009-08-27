function box_open (link, box_id) {
   $('#' + box_id).slideDown ();
   $(link).html ('{intl Hide Help}');
   $(link).bind ('click', {
       id: box_id
   }, function (e) {
       $(this).unbind ();
       return box_close (this, e.data.id);
   });
   $(link).blur ();
   return false;
}

function box_close (link, box_id) {
   $('#' + box_id).slideUp ();
   $(link).html ('{intl Show Help}');
   $(link).bind ('click', {
       id: box_id
   }, function (e) {
       $(this).unbind ();
       return box_open (this, e.data.id);
   });
   $(link).blur ();
   return false;
}
