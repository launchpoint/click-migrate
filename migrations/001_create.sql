CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `last_migration` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;