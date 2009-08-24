-- --------------------------------------------------------

--
-- Structure de la table `ui_comment`
--

CREATE TABLE `ui_comment` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `item_group` (`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ui_rating`
--

CREATE TABLE `ui_rating` (
  `user` varchar(48) NOT NULL,
  `item` varchar(128) NOT NULL,
  `group` varchar(32) NOT NULL,
  `rating` int(11) default NULL,
  PRIMARY KEY  (`user`,`item`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
