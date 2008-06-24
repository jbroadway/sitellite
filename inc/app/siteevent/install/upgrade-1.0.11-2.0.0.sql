CREATE TABLE siteevent_audience (
	name char(72) not null primary key
);

ALTER TABLE siteevent_event ADD COLUMN audience CHAR(72) NOT NULL AFTER category;
ALTER TABLE siteevent_event_sv ADD COLUMN audience CHAR(72) NOT NULL AFTER category;
ALTER TABLE siteevent_event ADD INDEX (audience);
