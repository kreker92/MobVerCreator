<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

class AdsmanagerTableField extends JTable
{
   var $fieldid=null;
   var $name=null;
   var $description=null;
   var $title=null;
   var $display_title=null;
   var $type=null;
   var $maxlength=null;
   var $size=null;
   var $required=null;
   var $ordering=null;
   var $cols=null;
   var $rows=null;
   var $link_text = null;
   var $link_image = null;
   var $columnid    =null;
   var $columnorder =null;
   var $pos      = null;
   var $posorder = null;
   var $profile = null;
   var $cb_field = null;
   var $cbfieldvalues = null;
   var $editable = null;
   var $searchable = null;
   var $sort = null;
   var $sort_direction = null;
   var $catsid = null;
   var $published = 1;
			
    function __construct(&$db)
    {
    	parent::__construct( '#__adsmanager_fields', 'fieldid', $db );
    }
    
    function saveContent($post,$plugins)
    {
    	if(version_compare(JVERSION, '2.5', '>=') && version_compare(JVERSION, '3.0', '<') ) {
    		if (JError::$legacy)
    			$tmp_legacy = true;
    		else
    			$tmp_legacy = false;
    	
    		JError::$legacy = false;
    	}
    	
		$fieldNames  = @$post['vNames'];
		$fieldValues = @$post['vValues'];
		$fieldImagesSelect = @$post['vSelectImages'];
		$fieldImagesValues = @$post['vImagesValues'];
		
		$j=1;
		if($this->fieldid > 0) {
			/*$this->_db->setQuery( "DELETE FROM #__adsmanager_field_values"
				. " WHERE fieldid='".(int)$this->fieldid."'" );*/
			$this->_db->setQuery( "SELECT * FROM #__adsmanager_field_values"
					. " WHERE fieldid='".(int)$this->fieldid."'" );
			$existingvalues = $this->_db->loadObjectList('fieldvalue');
		} else {
			$this->_db->setQuery( "SELECT MAX(fieldid) FROM #__adsmanager_fields");
			$maxID=$this->_db->loadResult();
			$this->fieldid=$maxID;
			$existingvalues = array();
		}
		
		$options_tag = $this->type;
		
		
		//To erase previous values in case of update
		$listnewvalues = array();
	
		switch($this->type) {
			case "select":
		    case "multiselect":
			case "radio":
			case "multicheckbox":
			case "price":
			case "number":
				$options_tag = "multiple";
				$j=0;$i=0;
				while(isset($fieldNames[$i])){
					$fieldName  = trim($fieldNames[$i]);
					$fieldValue = trim($fieldValues[$i]);
					$i++;
					
					if ($fieldName!=null) {
						$obj = new stdClass();
						$obj->fieldtitle = $fieldName;
						$obj->fieldvalue = $fieldValue;
						$obj->ordering = $j;
						$obj->fieldid = $this->fieldid;
						$listnewvalues[] = $this->_db->Quote($fieldValue);
						
						if (isset($existingvalues[$fieldValue])) {
							$obj->fieldvalueid = $existingvalues[$fieldValue]->fieldvalueid;
							$this->_db->updateObject('#__adsmanager_field_values',$obj,'fieldvalueid');
						} else {
							$this->_db->insertObject('#__adsmanager_field_values',$obj);
						}
						$j++;
					}
				}
				break;
			case 'radioimage':
			case 'multicheckboximage':
				$j=0;$i=0;
				while(isset($fieldImagesSelect[$i])){
					$fieldName  = trim($fieldImagesSelect[$i]);
					$fieldValue = trim($fieldImagesValues[$i]);
					$i++;
					
					if ($fieldName !=null) {
						$obj = new stdClass();
						$obj->fieldtitle = $fieldName;
						$obj->fieldvalue = $fieldValue;
						$obj->ordering = $j;
						$obj->fieldid = $this->fieldid;
						$listnewvalues[] = $this->_db->Quote($fieldValue);
						
						if (isset($existingvalues[$fieldValue])) {
							$obj->fieldvalueid = $existingvalues[$fieldValue]->fieldvalueid;
							$this->_db->updateObject('#__adsmanager_field_values',$obj,'fieldvalueid');
						} else {
							$this->_db->insertObject('#__adsmanager_field_values',$obj);
						}
						$j++;
					}
				}
				break;
		}
		
		//If they was previous values then erase old values no more present
		if (count($listnewvalues) >0 && count($existingvalues) > 0) {
			$this->_db->setQuery("DELETE FROM #__adsmanager_field_values 
							      WHERE fieldid = ".$this->fieldid." 
					              AND fieldvalue NOT IN (".implode(',',$listnewvalues).")");
			$this->_db->query();
			
		}
		
		$field_catsid = $post['field_catsid'];
		if (!is_array($field_catsid))
			$field_catsid = array();
			
		$field_catsid = ",".implode(',', $field_catsid).",";
		if ($field_catsid != "")
		{
			$query = "UPDATE #__adsmanager_fields SET catsid =".$this->_db->Quote($field_catsid)." WHERE fieldid=".(int)$this->fieldid;
			$this->_db->setQuery( $query);
			$this->_db->query();
		}
		
		
		if (isset($plugins["$this->type"]))
		{
			$options = $plugins["$this->type"]->saveFieldOptions($this);
			if ($options == null) {
				$options = array();
			}
		} else {
			$options = array();
		}
		
		$data = JRequest::get( 'post' );
		foreach($data as $key => $d) {
			if (strpos($key,"options_{$options_tag}_") === 0) {
				$k = substr($key,strlen("options_{$options_tag}_"));
				$options[$k] = $d;
			}
			if (strpos($key,"options_common_") === 0) {
				$k = substr($key,strlen("options_common_"));
				$options[$k] = $d;
			}
            if (strpos($key,"options_{$this->type}_") === 0) {
				$k = substr($key,strlen("options_{$this->type}_"));
				$options[$k] = $d;
			}
		}
		$data = new stdClass();
		$data->fieldid = $this->fieldid;
		$data->options = json_encode($options);
		
		//var_dump($options);exit();
			
		$this->_db->updateObject("#__adsmanager_fields",$data, "fieldid");	
		
		$app = JFactory::getApplication();
		
		//Update Ad Fields
	    $this->_db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$this->_db->getPrefix()."adsmanager_ads' AND column_name='{$this->name}'");
	    
	    $exist = $this->_db->loadResult();
		if (!$exist) {
			$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_ads ADD `$this->name` TEXT NOT NULL");
			try {
				$result = $this->_db->query();
			} catch(Exception $e) {}
	    }
	    
	    if ($this->profile == 1)
	    {
			//Update Profile Fields
			$this->_db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$this->_db->getPrefix()."adsmanager_profile' AND column_name='{$this->name}'");
			$exist = $this->_db->loadResult();
			if (!$exist) {
				$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_profile ADD `$this->name` TEXT NOT NULL");
				try {
					$result = $this->_db->query();
				} catch(Exception $e) {}
			}
		}
		else
		{
			//Update Profile Fields
			$this->_db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$this->_db->getPrefix()."adsmanager_profile' AND column_name='{$this->name}'");
			$exist = $this->_db->loadResult();
			if ($exist) {
				$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_profile DROP `$this->name`");
				try {
					$result = $this->_db->query();
				} catch(Exception $e) {}
			}
		}
		
		if(version_compare(JVERSION, '2.5', '>=') && version_compare(JVERSION, '3.0', '<') ) {
			JError::$legacy = $tmp_legacy;
		}
    }
    
    function deleteContent($id)
    {
    	$app = JFactory::getApplication();
    	
		$this->_db->setQuery("SELECT name FROM #__adsmanager_fields WHERE fieldid = ".(int)$id);
		$name = $this->_db->loadResult();
	
		if(($name == "name")||($name == "email")||($name == "ad_text")||($name == "ad_headline"))
		{
			$app->redirect( 'index.php?option=com_adsmanager&c=fields', JText::_('ADSMANAGER_ERROR_SYSTEM_FIELD'),'message' );
			return;
		}
		
		//Update Ad Fields
		$this->_db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$this->_db->getPrefix()."adsmanager_ads' AND column_name='$name'");
		$exist = $this->_db->loadResult();
		if ($exist) {
			$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_ads DROP `$name`");
			$result = $this->_db->query();
		}
			
		//Update Profile Fields
		$this->_db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$this->_db->getPrefix()."adsmanager_profile' AND column_name='$name'");
		$exist = $this->_db->loadResult();
		if ($exist) {
			$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_profile DROP `$name`");
			$result = $this->_db->query();
		}
		
		$this->_db->setQuery("DELETE FROM #__adsmanager_fields WHERE fieldid = ".(int)$id);
		$this->_db->query();
		
		$this->_db->setQuery("DELETE FROM #__adsmanager_field_values WHERE fieldid = ".(int)$id);
    }
}
