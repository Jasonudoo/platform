DROP TABLE IF EXISTS `#__schorder_address`;
CREATE TABLE IF NOT EXISTS `#__schorder_address` (
	`address_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`address_user_id` int(11) unsigned NOT NULL DEFAULT '0',
	`address_title` varchar(255) DEFAULT NULL,
	`address_firstname` varchar(255) DEFAULT NULL,
	`address_middle_name` varchar(255) DEFAULT NULL,
	`address_lastname` varchar(255) DEFAULT NULL,
	`address_company` varchar(255) DEFAULT NULL,
	`address_street` varchar(255) DEFAULT NULL,
	`address_street2` varchar(255) NOT NULL DEFAULT '',
	`address_post_code` varchar(255) DEFAULT NULL,
	`address_city` varchar(255) DEFAULT NULL,
	`address_telephone` varchar(255) DEFAULT NULL,
	`address_telephone2` varchar(255) DEFAULT NULL,
	`address_fax` varchar(255) DEFAULT NULL,
	`address_state` varchar(255) DEFAULT NULL,
	`address_country` varchar(255) DEFAULT NULL,
	`address_published` tinyint(4) NOT NULL DEFAULT '1',
	`address_vat` varchar(255) DEFAULT NULL,
	`address_default` tinyint(4) NOT NULL DEFAULT '0',
	`created_on` datetime DEFAULT NULL,
	`modified_on` datetime DEFAULT NULL,
	`created_by` int(11) UNSIGNED NOT NULL,
	`modified_by` int(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`address_id`),
	KEY `IDX_address_user_id` (`address_user_id`)
) ENGINE=MyISAM COMMENT='member address information' /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

DROP TABLE IF EXISTS `#__schorder_config`;
CREATE TABLE IF NOT EXISTS `#__schorder_config` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`config_namekey` varchar(200) NOT NULL,
	`config_value` text NOT NULL,
	`config_default` text NOT NULL,
	`created_on` datetime DEFAULT NULL,
	`modified_on` datetime DEFAULT NULL,
	`created_by` int(11) UNSIGNED NOT NULL,
	`modified_by` int(11) UNSIGNED NOT NULL,	
	PRIMARY KEY (`id`),
	UNIQUE KEY `IDX_name` (`config_namekey`)
) ENGINE=MyISAM COMMENT='configuration for schedule order' /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

DROP TABLE IF EXISTS `#__schorder_shipping`;
CREATE TABLE IF NOT EXISTS `#__schorder_shipping` (
	`shipping_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`shipping_type` varchar(255) NOT NULL DEFAULT 'manual',
	`shipping_zone_namekey` varchar(255) NOT NULL,
	`shipping_tax_id` int(10) unsigned NOT NULL DEFAULT '0',
	`shipping_price` decimal(17,5) NOT NULL DEFAULT '0.00000',
	`shipping_currency_id` int(10) unsigned NOT NULL DEFAULT '0',
	`shipping_name` varchar(255) NOT NULL,
	`shipping_description` text NOT NULL,
	`shipping_published` tinyint(4) NOT NULL DEFAULT '1',
	`shipping_ordering` int(10) unsigned NOT NULL DEFAULT '0',
	`shipping_params` text NOT NULL,
	`shipping_images` varchar(255) NOT NULL DEFAULT '',
	`shipping_access` varchar(255) NOT NULL DEFAULT 'all',
   `created_by` int(11) unsigned NOT NULL DEFAULT '0',
   `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
   `created_on` datetime DEFAULT NULL,
   `modified_on` datetime DEFAULT NULL,	
	PRIMARY KEY (`shipping_id`)
) ENGINE=MyISAM COMMENT='the shipping information' /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;