<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TConf 
{
	static $_conf;
	
	static function getConfig() {
		if (!self::$_conf) {		
			include_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/configuration.php');
			$model = new AdsmanagerModelConfiguration();
			self::$_conf = $model->getConfiguration();
		}
		return self::$_conf;
	}
}