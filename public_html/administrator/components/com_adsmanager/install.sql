CREATE TABLE IF NOT EXISTS `#__adsmanager_ads` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category` int(10) unsigned default '0',
  `userid` int(10) unsigned default NULL,
  `name` text,
  `images` text,
  `ad_zip` text,
  `ad_city` text,
  `ad_phone` text,
  `email` text,
  `ad_kindof` text,
  `ad_headline` text,
  `ad_text` text,
  `ad_state` text,
  `ad_price` text,
  `date_created` datetime default NULL,
  `date_modified` datetime default NULL,
  `date_recall` date default NULL,
  `expiration_date` date default NULL,
  `publication_date` DATETIME default '0000-00-00 00:00:00',
  `recall_mail_sent` tinyint(1) default '0',
  `views` int(10) unsigned default '0',
  `published` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;
         
CREATE TABLE IF NOT EXISTS `#__adsmanager_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` int(10) unsigned default '0',
  `name` varchar(50) default NULL,
  `description` text,
  `metadata_description` text,
  `metadata_keywords` text,
  `ordering` int(11) default '0',
  `published` tinyint(1) default '1',
  `limitads` int(11) default '-1',
  `usergroupsread` text,
  `usergroupswrite` text,
  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;
         
CREATE TABLE IF NOT EXISTS `#__adsmanager_adcat` (
  `adid` int(10) unsigned NOT NULL ,
  `catid` int(10) unsigned NOT NULL ,
  PRIMARY KEY  (`adid`,`catid`)
) DEFAULT CHARACTER SET utf8;
         
 CREATE TABLE IF NOT EXISTS `#__adsmanager_config` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` text NOT NULL,
  `ads_per_page` int(10) unsigned NOT NULL default '20',
  `max_image_size` int(10) unsigned NOT NULL default '102400',
  `max_width` int(4) NOT NULL default '450',
  `max_height` int(4) NOT NULL default '300',
  `max_width_t` int(4) NOT NULL default '150',
  `max_height_t` int(4) NOT NULL default '100',
  `root_allowed` tinyint(4) NOT NULL default '1',
  `nb_images` int(4) NOT NULL default '2',
  `show_contact` tinyint(4) NOT NULL default '1',
  `send_email_on_new` tinyint(4) NOT NULL default '1',
  `send_email_on_update` tinyint(4) NOT NULL default '1',
  `auto_publish` tinyint(4) NOT NULL default '1',
  `tag` text NOT NULL,
  `fronttext` text NOT NULL,
  `comprofiler` tinyint(4) NOT NULL default '0',
  `email_display` tinyint(4) NOT NULL default '0',
  `rules_text` text NOT NULL,
  `display_expand` tinyint(4) NOT NULL default '1',
  `display_last` tinyint(4) NOT NULL default '2',
  `display_fullname` tinyint(4) NOT NULL default '2',
  `expiration` tinyint(1) NOT NULL default '1',
  `ad_duration` int(4) NOT NULL default '30',
  `recall` tinyint(1) NOT NULL default '1',
  `recall_time` int(4) NOT NULL default '7',
  `recall_text` text NOT NULL,
  `image_display` varchar(50) NOT NULL default 'default',
  `cat_max_width` int(4) NOT NULL default '150',
  `cat_max_height` int(4) NOT NULL default '150',
  `cat_max_width_t` int(4) NOT NULL default '30',
  `cat_max_height_t` int(4) NOT NULL default '30',
  `submission_type` int(4) NOT NULL default '30',
  `nb_ads_by_user` int(4) NOT NULL default '-1',
  `allow_attachement` tinyint(1) NOT NULL default '0',
  `allow_contact_by_pms` tinyint(1) NOT NULL default '0',
  `show_rss` tinyint(1) NOT NULL default '0',
  `nbcats` int(4) NOT NULL default '1',
  `show_new` tinyint(1) NOT NULL default '1',
  `nbdays_new` int(10) NOT NULL default '5',
  `show_hot` tinyint(1) NOT NULL default '1',
  `nbhits` int(10) NOT NULL default '100',
  `bannedwords` TEXT DEFAULT NULL,
  `replaceword` TEXT DEFAULT NULL,
  `after_expiration` TEXT DEFAULT NULL,
  `archive_catid` int(10) NOT NULL default '1',

  `metadata_description` text,
  `metadata_keywords` text,
  `autocomplete` tinyint(1) default '0',
  `jquery` tinyint(1) default '1',
  `jqueryui` tinyint(1) default '1',

  `disable_post` tinyint(1) NOT NULL default '0',

  `nb_last_cols` int(10) NOT NULL default '3',
  `nb_last_rows` int(10) NOT NULL default '1',

  `display_general_menu` tinyint(1) NOT NULL default '1',
  `display_list_sort` tinyint(1) NOT NULL default '1',
  `display_list_search` tinyint(1) NOT NULL default '1',
  `display_inner_pathway` tinyint(1) NOT NULL default '1',
  `display_front` tinyint(1) NOT NULL default '1',

  `send_email_on_new_to_user` tinyint(4) NOT NULL default '1',
  `send_email_on_update_to_user` tinyint(4) NOT NULL default '0',
  `send_email_on_validation_to_user` tinyint(4) NOT NULL default '1',
  `new_text` text NOT NULL,
  `update_text` text NOT NULL,
  `admin_new_text` text NOT NULL,
  `admin_update_text` text NOT NULL,
  `waiting_validation_text` text NOT NULL,
  `validation_text` text NOT NULL,
  `expiration_text` text NOT NULL,
  `new_subject` text NOT NULL,
  `update_subject` text NOT NULL,
  `admin_new_subject` text NOT NULL,
  `admin_update_subject` text NOT NULL,
  `waiting_validation_subject` text NOT NULL,
  `validation_subject` text NOT NULL,
  `expiration_subject` text NOT NULL,
  `recall_subject` text NOT NULL,
  `params` text DEFAULT NULL,
  `special` text DEFAULT NULL,
  
   PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__adsmanager_profile` (
  `userid` int(11) NOT NULL default '0',
  `name` text NOT NULL,
  `ad_city` text NOT NULL,
  `email` text NOT NULL,
  `ad_zip` text NOT NULL,
  `ad_phone` text NOT NULL,
  PRIMARY KEY  (`userid`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__adsmanager_positions` (
	`id` tinyint(4) NOT NULL auto_increment,
	`name` text NOT NULL,
	`title` text NOT NULL,
	PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE  IF NOT EXISTS `#__adsmanager_fields` (
  `fieldid` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `display_title` tinyint(1) NOT NULL default '0',
  `description` mediumtext NOT NULL,
  `type` varchar(50) NOT NULL default '',
  `maxlength` int(11) default NULL,
  `size` int(11) default NULL,
  `required` tinyint(4) default '0',
  `ordering` int(11) default NULL,
  `cols` int(11) default NULL,
  `rows` int(11) default NULL,
  `link_text` varchar( 255 ) NOT NULL DEFAULT '', 
  `link_image` varchar( 255 ) NOT NULL DEFAULT '', 
  `columnid` int(11) NOT NULL default '-1',
  `columnorder` int(11) NOT NULL default '0',
  `pos` tinyint(4) NOT NULL default '1',
  `posorder` tinyint(4) NOT NULL default '1',
  `profile` tinyint(1) NOT NULL default '0',
  `cb_field` int(11) NOT NULL default '-1',
  `cbfieldvalues` int(11) NOT NULL default '-1',
  `editable` tinyint(1) NOT NULL default '1',
  `searchable` tinyint(1) NOT NULL default '1',
  `sort` tinyint(1) NOT NULL default '0',
  `sort_direction` varchar(4) NOT NULL default 'DESC',
  `catsid` TEXT NOT NULL, 
  `options` TEXT NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`fieldid`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE  IF NOT EXISTS `#__adsmanager_field_values` (
  `fieldvalueid` int(11) NOT NULL auto_increment,
  `fieldid` int(11) NOT NULL default '0',
  `fieldtitle` varchar(50) NOT NULL default '',
  `fieldvalue` VARCHAR( 50 ) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL default '0',
  `sys` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`fieldvalueid`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__adsmanager_columns` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `catsid` TEXT NOT NULL, 
  `published` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__adsmanager_pending_ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `date` date NOT NULL,
  `content` text NOT NULL,
  `contentid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `#__adsmanager_positions` VALUES (1, 'top', 'ADSMANAGER_POSITION_TOP');	
INSERT IGNORE INTO `#__adsmanager_positions` VALUES (2, 'subtitle', 'ADSMANAGER_POSITION_SUBTITLE');
INSERT IGNORE INTO `#__adsmanager_positions` VALUES (3, 'description', 'ADSMANAGER_POSITION_DESCRIPTION');
INSERT IGNORE INTO `#__adsmanager_positions` VALUES (4, 'description2', 'ADSMANAGER_POSITION_DESCRIPTION2');
INSERT IGNORE INTO `#__adsmanager_positions` VALUES (5, 'contact', 'ADSMANAGER_POSITION_CONTACT');
INSERT IGNORE INTO `#__adsmanager_positions` VALUES (6, 'description3', 'ADSMANAGER_POSITION_DESCRIPTION3');
