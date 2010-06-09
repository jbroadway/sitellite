alter table sitelinks_item add column sitellite_owner char(48) not null;
alter table sitelinks_item add column sitellite_team char(48) not null;
alter table sitelinks_item add index (sitellite_owner, sitellite_team);
alter table sitelinks_item_sv add column sitellite_owner char(48) not null;
alter table sitelinks_item_sv add column sitellite_team char(48) not null;
alter table sitelinks_item_sv add index (sitellite_owner, sitellite_team);
