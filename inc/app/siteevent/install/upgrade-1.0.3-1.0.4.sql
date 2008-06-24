alter table siteevent_event add column recurring enum('no','daily','weekly','monthly','yearly') not null default 'no' after details;
alter table siteevent_event_sv add column recurring enum('no','daily','weekly','monthly','yearly') not null default 'no' after details;
alter table siteevent_event add index (recurring);
