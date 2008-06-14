# upgrade 4.0.3 -> 4.0.4
#
# after running this update, on the command-line run:
#
# php -f index scheduler-app scanner
#
# this will syncronize and correct the revision tables.
#

alter table sitellite_user change username username varchar(48) not null;

alter table sitellite_page_sv add column sv_deleted enum('yes','no') default 'no' after sv_changelog;
alter table sitellite_page_sv add column sv_current enum('yes','no') default 'yes' after sv_deleted;
alter table sitellite_page_sv add index (sv_deleted,sv_current);

alter table sitellite_sidebar_sv add column sv_deleted enum('yes','no') default 'no' after sv_changelog;
alter table sitellite_sidebar_sv add column sv_current enum('yes','no') default 'yes' after sv_deleted;
alter table sitellite_sidebar_sv add index (sv_deleted,sv_current);

alter table sitellite_undo_sv add column sv_deleted enum('yes','no') default 'no' after sv_changelog;
alter table sitellite_undo_sv add column sv_current enum('yes','no') default 'yes' after sv_deleted;
alter table sitellite_undo_sv add index (sv_deleted,sv_current);
