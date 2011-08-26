alter table sitellite_user add column public enum('yes','no') not null default 'no';
alter table sitellite_user add column profile text not null;
alter table sitellite_user add column sig text not null;
alter table sitellite_user add column registered datetime not null;
alter table sitellite_user add column modified timestamp not null;
alter table sitellite_user add index (public, registered, modified);
