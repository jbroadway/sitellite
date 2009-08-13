
--
-- Structure de la table `sitellite_tag`
--

CREATE TABLE `sitellite_tag` (
  `set` varchar(48) NOT NULL,
  `tag` varchar(48) NOT NULL,
  `item` int(10) unsigned NOT NULL,
  `sitellite_owner` varchar(48) NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`set`,`tag`,`item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sitellite_tag_item`
--

CREATE TABLE `sitellite_tag_item` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `set` varchar(48) NOT NULL,
  `url` varchar(256) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` tinytext NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `sitellite_owner` varchar(48) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sitellite_user` (`sitellite_owner`),
  KEY `set` (`set`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
