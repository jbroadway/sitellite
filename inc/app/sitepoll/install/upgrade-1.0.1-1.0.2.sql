alter table sitepoll_poll_sv modify sv_action enum('created','modified','republished','replaced','restored','deleted','updated') not null default 'created';
