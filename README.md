# XboxAPI_Bot

PHP script for replying to online requests sent on Twitter to @XboxAPI_Bot

## How

Simply Tweet the bot, and it will check the online status of the Xbox gamertag you specify. Use the following format:

    @XboxAPI_Bot isonline Major Nelson
    
The bot will reply to your tweet and tell you whether the gamer is online or offline. If offline, the bot will recheck until the gamer comes online and will tweet when this happens.

## Installation

You'll need to create a `config.php` from the `config-sample.php` file and set up the following tables:

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

The script is required to be run via CRON every minute

    * * * * * /path/to/php /path/to/script/index.php