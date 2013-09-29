# XboxAPI_Bot



## SQL

    CREATE TABLE IF NOT EXISTS `settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `value` varchar(255) CHARACTER SET utf8 NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
    
    INSERT INTO `settings` (`id`, `value`) VALUES (1, '1');
    
    CREATE TABLE IF NOT EXISTS `tweets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `date` datetime NOT NULL,
        `tweet_id` varchar(255) CHARACTER SET utf8 NOT NULL,
        `screen_name` varchar(255) CHARACTER SET utf8 NOT NULL,
        `gamertag` varchar(255) CHARACTER SET utf8 NOT NULL,
        `attempts` int(11) NOT NULL,
        `status` int(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;