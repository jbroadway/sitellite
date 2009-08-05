alter table sitellite_news add column external char(128) not null after summary;
alter table sitellite_news_sv add column external char(128) not null after summary;

