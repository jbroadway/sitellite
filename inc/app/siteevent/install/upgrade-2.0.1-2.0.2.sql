# 1. back up your siteevent_audience data because you'll need to re-import it

# 2. re-create siteevent_audience
drop table if exists siteevent_audience;
CREATE TABLE siteevent_audience (
	id int not null auto_increment primary key,
	name char(72) not null
);

# 3. new fields in siteevent_event
ALTER TABLE siteevent_event CHANGE COLUMN audience audience char(32) NOT NULL AFTER category;
ALTER TABLE siteevent_event_sv CHANGE COLUMN audience audience char(32) NOT NULL AFTER category;
ALTER TABLE siteevent_event ADD COLUMN sponsor char(72) not null AFTER contact_url;
ALTER TABLE siteevent_event_sv ADD COLUMN sponsor char(72) not null AFTER contact_url;
ALTER TABLE siteevent_event ADD COLUMN rsvp char(72) not null AFTER sponsor;
ALTER TABLE siteevent_event_sv ADD COLUMN rsvp char(72) not null AFTER sponsor;
ALTER TABLE siteevent_event ADD COLUMN public enum('yes','no') not null default 'no' AFTER rsvp;
ALTER TABLE siteevent_event_sv ADD COLUMN public enum('yes','no') not null default 'no' AFTER rsvp;
ALTER TABLE siteevent_event ADD COLUMN media enum('yes','no') not null default 'no' AFTER public;
ALTER TABLE siteevent_event_sv ADD COLUMN media enum('yes','no') not null default 'no' AFTER public;
