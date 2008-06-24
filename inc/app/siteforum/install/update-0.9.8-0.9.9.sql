alter table siteforum_topic add column sitellite_owner char(48) not null;
alter table siteforum_topic add column sitellite_team char(48) not null;
alter table siteforum_topic add index (sitellite_owner, sitellite_team);
alter table siteforum_post add column sitellite_owner char(48) not null;
alter table siteforum_post add column sitellite_team char(48) not null;
alter table siteforum_post add index (sitellite_owner, sitellite_team);
