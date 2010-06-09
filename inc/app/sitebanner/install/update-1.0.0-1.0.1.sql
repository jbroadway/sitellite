alter table sitebanner_ad add column target enum('parent','self','top','blank') not null default 'top' after url;
alter table sitebanner_ad add column format enum('image','html','text','external','adsense') not null default 'image' after target;
alter table sitebanner_ad change column file file text not null;
alter table sitebanner_ad add index (format);
