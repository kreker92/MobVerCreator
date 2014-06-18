<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelPosition extends TModel
{
    function getPositions() {
    	$this->_db->setQuery( "SELECT * FROM #__adsmanager_positions WHERE 1 " );

		$positions = $this->_db->loadObjectList();
		return $positions;
    }
    
    function cleanFieldsByPosition() {
    	$this->_db->setQuery('UPDATE #__adsmanager_fields SET pos = -1');
    	$this->_db->query();
    }
    
    function setPosition($id,$title,$listfields) {
    	$obj = new stdClass();
    	$obj->id = $id;
    	$obj->title = $title;
    	$this->_db->updateObject('#__adsmanager_positions',$obj,'id');
    	
    	$list = explode(',',$listfields);
    	foreach($list as $key => $fieldid) {
    		$obj = new stdClass();
    		$obj->fieldid = $fieldid;
    		$obj->posorder = $key;
    		$obj->pos = $id;
    		$this->_db->updateObject('#__adsmanager_fields',$obj,'fieldid');
    	}
    }
}