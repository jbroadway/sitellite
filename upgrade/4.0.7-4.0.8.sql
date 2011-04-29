ALTER TABLE `sitellite_page` ADD COLUMN `nav_title` CHAR(128) NOT NULL AFTER `title`;
ALTER TABLE `sitellite_page_sv` ADD COLUMN `nav_title` CHAR(128) NOT NULL AFTER `title`;
ALTER TABLE sitellite_page CHANGE id id char(72) NOT NULL;
ALTER TABLE sitellite_page_sv CHANGE id id char(72) NOT NULL;
