<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelConfiguration extends TModel
{
	var $_conf;
	
	function getConfiguration() {
    	if ($this->_conf)
    		return $this->_conf;
    	else {
    		$this->_db->setQuery( "SELECT * FROM #__adsmanager_config");
			$this->_conf = $this->_db->loadObject();
			$params = json_decode($this->_conf->params);
			if ($params != null) {
				foreach($params as $name => $value) {
					$this->_conf->$name = $value;
					if($name == 'max_width_m' && $value == '/')
                        $this->_conf->$name = 300;
                    if($name == 'max_height_m' && $value == '/')
                        $this->_conf->$name = 200;
				}
			}
			if (!isset($this->_conf->display_nb_categories_per_row)) {
				$this->_conf->display_nb_categories_per_row = 3;
			}
			if (!isset($this->_conf->globalfilter_user)) {
				$this->_conf->globalfilter_user = 1;
			}
			if (!isset($this->_conf->globalfilter_fieldname)) {
				$this->_conf->globalfilter_fieldname = "";
			}	
			
			return $this->_conf;
    	}
    }
}