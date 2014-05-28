DROP TABLE IF EXISTS `#__securitycheck`;
CREATE TABLE `#__securitycheck` (
`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`Product` VARCHAR(35) NOT NULL,
`Type` VARCHAR(35),
`Installedversion` VARCHAR(30) DEFAULT '---',
`Vulnerable` VARCHAR(10) NOT NULL DEFAULT 'No',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__securitycheck_db`;
CREATE TABLE `#__securitycheck_db` (
`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`Product` VARCHAR(35) NOT NULL,
`Type` VARCHAR(35),
`Vulnerableversion` VARCHAR(10) DEFAULT '---',
`modvulnversion` VARCHAR(2) DEFAULT '==',
`Joomlaversion` VARCHAR(10) DEFAULT 'Notdefined',
`modvulnjoomla` VARCHAR(2) DEFAULT '==',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
INSERT INTO `#__securitycheck_db` (`product`,`type`,`vulnerableversion`,`modvulnversion`,`Joomlaversion`,`modvulnjoomla`) VALUES 
('Joomla!','core','3.0.0','==','3.0.0','=='),
('com_fss','component','1.9.1.1447','<=','3.0.0','>='),
('com_commedia','component','3.1','<=','3.0.0','>='),
('Joomla!','core','3.0.1','<=','3.0.1','<='),
('com_jnews','component','7.9.1','<','3.0.0','>='),
('com_bch','component','---','==','3.0.0','>='),
('com_aclassif','component','---','==','3.0.0','>='),
('com_rsfiles','component','1.0.0 Rev 11','==','3.0.0','>='),
('Joomla!','core','3.0.2','<=','3.0.0','>='),
('com_jnews','component','8.0.1','<=','3.0.0','>='),
('com_attachments','component','3.1.1','<','3.0.0','>='),
('Joomla!','core','3.1.4','<=','3.0.0','>='),
('com_sectionex','component','2.5.96','<=','3.0.0','>='),
('com_joomsport','component','1.7.1','<','3.0.0','>='),
('Joomla!','core','3.1.5','<=','3.0.0','>='),
('com_flexicontent','component','2.1.3','<=','3.0.0','>='),
('com_mijosearch','component','2.0.1','<=','3.0.0','>='),
('com_acesearch','component','3.0','==','3.0.0','>='),
('com_melody','component','1.6.25','<=','3.0.0','>='),
('com_sexypolling','component','1.0.8','<=','3.0.0','>='),
('com_komento','component','1.7.2','<=','3.0.0','>='),
('com_community','component','2.6','==','3.0.0','>='),
('Joomla!','core','3.2.2','<=','3.0.0','>='),
('com_youtubegallery','component','3.4.0','==','3.0.0','>='),
('com_pbbooking','component','2.4','==','3.0.0','>='),
('com_extplorer','component','2.1.3','==','3.0.0','>='),
('com_freichat','component','3.5','<=','3.0.0','>='),
('com_multicalendar','component','4.0.2','==','3.0.0','>='),
('com_kunena','component','3.0.4','==','3.0.0','>=');

CREATE TABLE IF NOT EXISTS `#__securitycheck_logs` (
`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ip` VARCHAR(35) NOT NULL,
`time` DATETIME NOT NULL,
`tag_description` VARCHAR(50),
`description` VARCHAR(300) NOT NULL,
`type` VARCHAR(50),
`uri` VARCHAR(100),
`component` VARCHAR(150) DEFAULT '---',
`marked` TINYINT(1) DEFAULT 0,
`original_string` VARCHAR(300),
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__securitycheck_file_permissions`;

DROP TABLE IF EXISTS `#__securitycheck_file_manager`;
CREATE TABLE IF NOT EXISTS `#__securitycheck_file_manager` (
`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`last_check` DATETIME,
`files_scanned` INT(10) DEFAULT 0,
`files_with_incorrect_permissions` INT(10) DEFAULT 0,
`estado` VARCHAR(40) DEFAULT 'IN_PROGRESS',
`estado_clear_data` VARCHAR(40) DEFAULT 'DELETING_ENTRIES',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
INSERT INTO `#__securitycheck_file_manager` (`estado`,`estado_clear_data`) VALUES 
('ENDED','DELETING_ENTRIES');

DROP TABLE IF EXISTS `#__securitycheck_storage`;
CREATE TABLE IF NOT EXISTS `#__securitycheck_storage` (
`storage_key` varchar(255) NOT NULL,
`storage_value` longtext NOT NULL,
PRIMARY KEY (`storage_key`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;