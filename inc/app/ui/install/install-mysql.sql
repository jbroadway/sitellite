
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
