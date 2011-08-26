ALTER TABLE `sitellite_sidebar` CHANGE `alias` `alias` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `sitellite_sidebar_sv` CHANGE `alias` `alias` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `sitellite_property_set` CHANGE `data_value` `data_value` TEXT NOT NULL;
ALTER TABLE `sitellite_property_set` CHANGE `collection` `collection` VARCHAR( 84 ) NOT NULL;
ALTER TABLE `sitellite_property_set` CHANGE `entity` `entity` VARCHAR( 84 ) NOT NULL;
ALTER TABLE `sitellite_property_set` CHANGE `property` `property` VARCHAR( 84 ) NOT NULL;
