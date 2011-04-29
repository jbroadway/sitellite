alter table sitellite_page add column include_in_search enum('yes','no') not null default 'yes' after include;
alter table sitellite_page_sv add column include_in_search enum('yes','no') not null default 'yes' after include;

alter table sitellite_filesystem_sv change column sv_revision sv_revision datetime not null;
alter table sitellite_news_sv change column sv_revision sv_revision datetime not null;
alter table sitellite_page_sv change column sv_revision sv_revision datetime not null;
alter table sitellite_sidebar_sv change column sv_revision sv_revision datetime not null;
alter table sitellite_undo_sv change column sv_revision sv_revision datetime not null;

alter table sitellite_page change body body mediumtext not null;
alter table sitellite_page_sv change body body mediumtext not null;

# if an error occurs on the following lines, it is safe to ignore it.  it simply means
# that you don't have these add-ons installed.

alter table siteblog_post_sv change column sv_revision sv_revision datetime not null;
alter table siteevent_event_sv change column sv_revision sv_revision datetime not null;
alter table sitelinks_item_sv change column sv_revision sv_revision datetime not null;
