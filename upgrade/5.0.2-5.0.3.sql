alter table sitellite_news add column external char(128) not null after summary;
alter table sitellite_news_sv add column external char(128) not null after summary;

CREATE TABLE `ui_comment` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `website` varchar(256) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `item_group` (`item`,`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `ui_rating` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `rating` int(11) default NULL,
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `ui_review` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

UPDATE sitellite_page SET body=replace(body,
	'border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_page_sv SET body=replace(body,
	'border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news SET body=replace(body,
	'border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news_sv SET body=replace(body,
	'border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post SET body=replace(body,
	'border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post_sv SET body=replace(body,
	'border: 0px none; background-image: url(' + xed_prefix + '/inc/app/xed/pix/box-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_page SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_page_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE sitellite_news_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);

UPDATE siteblog_post_sv SET body=replace(body,
	'border: 0px none; background-image: url(/inc/app/xed/pix/form-bg.jpg); background-repeat: no-repeat; width: 528px; height: 63px; font-weight: normal; padding: 23px 10px 0px 85px;',
	'background-color: #b7c3cf; -moz-border-radius: 10px; min-height: 20px; width: 575px; font-weight: normal; padding: 15px 15px 15px 15px;'
);
