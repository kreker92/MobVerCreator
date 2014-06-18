<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
$paths = JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelSearchmodule extends TModel
{
	var $_conf;
	
	function getSearchModuleConfiguration() {
    	if ($this->_conf)
    		return $this->_conf;
    	else {
    		$this->_db->setQuery( "SELECT * FROM #__adsmanager_searchmodule_config");
			$this->_conf = $this->_db->loadObject();
			$params = json_decode($this->_conf->params);
			if ($params != null) {
				foreach($params as $name => $value) {
					$this->_conf->$name = $value;
				}
			}
			if (isset($this->_conf->simple_fields) && $this->_conf->simple_fields != "") {
				$this->_conf->simple_fields = explode(",",$this->_conf->simple_fields);
			} else {
				$this->_conf->simple_fields = array();
			}
			if (isset($this->_conf->advanced_fields) && $this->_conf->advanced_fields != "") {
				$this->_conf->advanced_fields = explode(",",$this->_conf->advanced_fields);
			} else {
				$this->_conf->advanced_fields = array();
			}
			return $this->_conf;
    	}
    }
    
    function getSearchFields($type="simple") {
    	$this->getSearchModuleConfiguration();
    	if ($type == "advanced") {
    		$fields = $this->_conf->advanced_fields;
    	} else {
    		$fields = $this->_conf->simple_fields;
    	}
        if($fields[0] == '') {
            $fields = array();
        }
    	if (count($fields) == 0) {
    		return array();
    	} else {
    		$sql = "SELECT * FROM #__adsmanager_fields WHERE fieldid IN (".implode(',',$fields).")";
    		$this->_db->setQuery($sql);
    		$temp = $this->_db->loadObjectList("fieldid");
    		$results = array();
    		foreach($fields as $id) {
    			if (isset($temp[$id])) {
    				$temp[$id]->options = json_decode($temp[$id]->options);
    				$results[] = $temp[$id];
    			}
    		}
    		return $results;
    	}
    }
}