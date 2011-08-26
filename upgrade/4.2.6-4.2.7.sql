alter table sitellite_page_sv modify sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created';
alter table sitellite_news_sv modify sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created';
alter table sitellite_filesystem_sv modify sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created';
alter table sitellite_sidebar_sv modify sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created';
alter table sitellite_undo_sv modify sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created';
