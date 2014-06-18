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
class AdsmanagerModelColumn extends TModel
{
	function getColumn($id)
	{
		$query = "SELECT * FROM #__adsmanager_columns WHERE id = ".(int)$id;
		$this->_db->setQuery($query);	 
		$column = $this->_db->loadObject();
		return $column;
	}
	
    function getColumns($catid,$all=false) {
    	$this->_db->setQuery("SELECT c.* ".
							"FROM #__adsmanager_columns as c ".
							"ORDER BY c.ordering ");
    	$columns = $this->_db->loadObjectList();
    	if ($all == true)
    		return $columns;
    		
    	$col = array();
    		
    	if (isset($columns))
		{
			$licz=0;
			
			
			foreach ($columns as $c ) {
				
				if ($c->catsid == ",-1,")				
					array_push( $col, $c );
				else 
				{	
					if ($catid != null) {
						$find = ",".$catid.",";
						if (strstr($c->catsid, $find))
							array_push( $col, $c );
					}
				}
			}
			unset($columns);
		}		
		$columns = $col;
		return $columns;
    }
    
    function cleanFieldsByColumn() {
    	$this->_db->setQuery('UPDATE #__adsmanager_fields SET columnid = -1');
    	$this->_db->query();
    }
    
    function setColumn($id,$title,$listfields) {
    	$obj = new stdClass();
    	$obj->id = $id;
    	$obj->name = $title;
    	$this->_db->updateObject('#__adsmanager_columns',$obj,'id');
    	 
    	$list = explode(',',$listfields);
    	foreach($list as $key => $fieldid) {
    		$obj = new stdClass();
    		$obj->fieldid = $fieldid;
    		$obj->columnorder = $key;
    		$obj->columnid = $id;
    		$this->_db->updateObject('#__adsmanager_fields',$obj,'fieldid');
    	}
    }
}